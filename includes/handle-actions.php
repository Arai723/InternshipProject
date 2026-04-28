<?php

// ตัวแปรกลางสำหรับข้อความผิดพลาดและค่าฟอร์มที่ต้องจำไว้
$errorMsg = '';
$formMsg = null;
$loginForm = array(
    'role' => 'student',
    'username' => '',
);

// ออกจากระบบแล้วกลับไปหน้าแรก
if (isset($_GET['logout'])) {
    session_destroy();
    redirectTo();
}

// ถ้าไม่ใช่การส่งฟอร์มแบบ POST ก็ไม่ต้องทำ action ใด ๆ
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

// จัดการฟอร์มเข้าสู่ระบบ
if (isset($_POST['login'])) {
    $loginForm['role'] = trim($_POST['role'] ?? 'student');
    $loginForm['username'] = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $user = authenticateUser($conn, $loginForm['role'], $loginForm['username'], $password);

    if ($user) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $loginForm['role'];
        redirectTo();
    }

    $errorMsg = 'ไอดีผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง';
    return;
}

// จัดการฟอร์มยื่นคำร้องของนิสิต
if (isset($_POST['submit_request'])) {
    if (!isLoggedIn() || currentUserRole() !== 'student') {
        redirectTo();
    }

    $formMsg = submitInternshipRequest(
        $conn,
        currentUserId(),
        trim($_POST['com_name'] ?? ''),
        trim($_POST['start_date'] ?? ''),
        trim($_POST['end_date'] ?? '')
    );
    return;
}

// จัดการการอัปเดตสถานะจากฝั่งเจ้าหน้าที่
if (isset($_POST['update_status'])) {
    if (!isLoggedIn() || currentUserRole() !== 'staff') {
        redirectTo(array('page' => 'internship'));
    }

    updateRequestStatus($conn, $_POST['req_id'] ?? 0, $_POST['new_status'] ?? 0);
    redirectTo(array('page' => 'internship', 'staff_updated' => 1));
}

// จัดการการอนุมัติหรือไม่อนุมัติจากฝั่งอาจารย์
if (isset($_POST['teacher_action'])) {
    if (!isLoggedIn() || currentUserRole() !== 'teacher') {
        redirectTo(array('page' => 'internship'));
    }

    reviewTeacherRequest($conn, $_POST['req_id'] ?? 0, $_POST['teacher_action'] ?? '');
    redirectTo(array('page' => 'internship', 'teacher_updated' => 1));
}
