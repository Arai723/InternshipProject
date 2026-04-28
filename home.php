<?php

session_start();
ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/functions.php';

$conn = createDatabaseConnection($dbConfig);
$currentPage = resolveCurrentPage($_GET['page'] ?? 'internship', $allowedPages);

$errorMsg = '';
$formMsg = null;
$loginForm = array(
    'role' => 'student',
    'username' => '',
);

require __DIR__ . '/includes/handle-actions.php';

$studentRequests = array();
$requestRows = array();
$studentsByYear = array();
$showStaffUpdated = isset($_GET['staff_updated']);
$showTeacherUpdated = isset($_GET['teacher_updated']);

if (isLoggedIn()) {
    if ($currentPage === 'internship') {
        if (currentUserRole() === 'student') {
            $studentRequests = fetchStudentRequests($conn, currentUserId());
        }

        if (currentUserRole() === 'staff' || currentUserRole() === 'teacher') {
            $requestRows = fetchAllRequests($conn);
        }
    }

    if ($currentPage === 'student') {
        $studentsByYear = collectStudentsByYear($conn, 4);
    }
}

require __DIR__ . '/includes/header.php';

if (!isLoggedIn()) {
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
                <?php require __DIR__ . '/pages/' . $currentPage . '.php'; ?>
            </div>
        </main>
    </div>
    <?php
}

require __DIR__ . '/includes/footer.php';
