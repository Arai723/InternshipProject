<?php

$errorMsg = '';
$formMsg = null;
$loginForm = array(
    'role' => 'student',
    'username' => '',
);

if (isset($_GET['logout'])) {
    session_destroy();
    redirectTo();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return;
}

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

if (isset($_POST['update_status'])) {
    if (!isLoggedIn() || currentUserRole() !== 'staff') {
        redirectTo(array('page' => 'internship'));
    }

    updateRequestStatus($conn, $_POST['req_id'] ?? 0, $_POST['new_status'] ?? 0);
    redirectTo(array('page' => 'internship', 'staff_updated' => 1));
}

if (isset($_POST['teacher_action'])) {
    if (!isLoggedIn() || currentUserRole() !== 'teacher') {
        redirectTo(array('page' => 'internship'));
    }

    reviewTeacherRequest($conn, $_POST['req_id'] ?? 0, $_POST['teacher_action'] ?? '');
    redirectTo(array('page' => 'internship', 'teacher_updated' => 1));
}

