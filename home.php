<?php

// เริ่มต้น session และเปิดการแสดง error เพื่อช่วยตอนพัฒนา
session_start();
ini_set('display_errors', '1');
error_reporting(E_ALL);

// โหลดค่าตั้งต้นและฟังก์ชันที่ใช้ร่วมกันทั้งระบบ
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

// เชื่อมต่อฐานข้อมูลและระบุหน้าปัจจุบันจาก query string
$conn = createDatabaseConnection($dbConfig);
$currentPage = resolveCurrentPage($_GET['page'] ?? 'internship', $allowedPages);

// ตัวแปรกลางสำหรับข้อความแจ้งเตือนและค่าจากฟอร์ม login
$errorMsg = '';
$formMsg = null;
$loginForm = array(
    'role' => 'student',
    'username' => '',
);

// จัดการ action ที่ส่งมาจากฟอร์มต่าง ๆ ก่อนเริ่มแสดงผล
require __DIR__ . '/includes/handle-actions.php';

// เตรียมตัวแปรข้อมูลที่แต่ละหน้าอาจเรียกใช้งาน
$studentRequests = array();
$requestRows = array();
$studentsByYear = array();
$showStaffUpdated = isset($_GET['staff_updated']);
$showTeacherUpdated = isset($_GET['teacher_updated']);

// ดึงข้อมูลเพิ่มตามหน้าที่ผู้ใช้กำลังเปิดอยู่
if (isLoggedIn()) {
    if ($currentPage === 'internship') {
        // นิสิตเห็นคำร้องของตัวเอง
        if (currentUserRole() === 'student') {
            $studentRequests = fetchStudentRequests($conn, currentUserId());
        }

        // เจ้าหน้าที่และอาจารย์เห็นคำร้องทั้งหมด
        if (currentUserRole() === 'staff' || currentUserRole() === 'teacher') {
            $requestRows = fetchAllRequests($conn);
        }
    }

    // หน้าแสดงรายชื่อนิสิตจะโหลดข้อมูลแยกตามชั้นปี
    if ($currentPage === 'student') {
        $studentsByYear = collectStudentsByYear($conn, 4);
    }
}

// เริ่มส่วนหัวของหน้าเว็บ
require __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
    // ถ้ายังไม่เข้าสู่ระบบ ให้แสดงหน้า login
    require __DIR__ . '/includes/login-form.php';
} else {
    ?>
    <div class="dashboard-page">
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>IS <span>Internship</span></h2>
                <p class="sub-logo">Information Studies SWU</p>
            </div>

            <ul class="sidebar-menu">
                <?php foreach ($sidebarPages as $page => $labelKey): ?>
                    <li>
                        <a href="<?php echo e(homeUrl(array('page' => $page))); ?>" class="<?php echo $currentPage === $page ? 'active' : ''; ?>">
                            <?php echo e(t($labelKey)); ?>
                        </a>
                    </li>
                <?php endforeach; ?>

                <li class="logout-link">
                    <a href="<?php echo e(homeUrl(array('logout' => 'true'))); ?>"><?php echo e(t('logout')); ?></a>
                </li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <h3>
                    <?php echo e(t('welcome')); ?>, <?php echo e(currentUserName()); ?>
                    <span class="badge role-<?php echo e((string) currentUserRole()); ?>">
                        (<?php echo e(t('role_' . currentUserRole())); ?>)
                    </span>
                </h3>
            </header>

            <div class="content-wrapper">
                <?php // โหลดเนื้อหาตามหน้าที่ผู้ใช้เลือกจากเมนู ?>
                <?php require __DIR__ . '/pages/' . $currentPage . '.php'; ?>
            </div>
        </main>
    </div>
    <?php
}

// ปิดท้ายหน้าเว็บ
require __DIR__ . '/includes/footer.php';
