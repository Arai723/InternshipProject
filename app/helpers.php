<?php

function createDatabaseConnection(array $dbConfig)
{
    $connection = new mysqli(
        $dbConfig['host'],
        $dbConfig['user'],
        $dbConfig['pass'],
        $dbConfig['name'],
        $dbConfig['port']
    );

    if ($connection->connect_error) {
        die('Connection failed: ' . $connection->connect_error);
    }

    $connection->set_charset('utf8mb4');

    return $connection;
}

function t($key)
{
    global $appConfig;

    $lang = $appConfig['lang'];

    return $appConfig['texts'][$lang][$key] ?? $key;
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function siteContent($key = null, $default = array())
{
    global $siteContent;

    if ($key === null) {
        return $siteContent;
    }

    return array_key_exists($key, $siteContent) ? $siteContent[$key] : $default;
}

function sidebarPages()
{
    global $appConfig;

    return $appConfig['sidebar_pages'];
}

function statusMap()
{
    global $appConfig;

    return $appConfig['status_map'];
}

function contactInfo()
{
    global $appConfig;

    return $appConfig['contact'];
}

function homeUrl(array $params = array())
{
    $query = http_build_query($params);

    return 'home.php' . ($query !== '' ? '?' . $query : '');
}

function redirectTo(array $params = array())
{
    header('Location: ' . homeUrl($params));
    exit();
}

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function currentUserRole()
{
    return $_SESSION['role'] ?? null;
}

function currentUserName()
{
    return $_SESSION['user_name'] ?? '';
}

function currentUserId()
{
    return $_SESSION['user_id'] ?? '';
}

function resolveCurrentPage($page, array $allowedPages)
{
    return in_array($page, $allowedPages, true) ? $page : 'internship';
}

function pageViewFor($currentPage)
{
    $pageViews = array(
        'internship' => 'pages/internship',
        'request_form' => 'pages/request_form',
        'pr' => 'pages/pr',
        'about' => 'pages/about',
        'course' => 'pages/course',
        'teacher' => 'pages/teacher',
        'student' => 'pages/student',
    );

    return $pageViews[$currentPage] ?? 'pages/internship';
}

function getStatusBadge($statusCode)
{
    $statusMap = statusMap();
    $statusCode = (int) $statusCode;

    if (!array_key_exists($statusCode, $statusMap)) {
        return "<span class='badge-status status-0'>รอตรวจสอบ</span>";
    }

    $status = $statusMap[$statusCode];

    return "<span class='badge-status {$status['class']}'>" . e($status['th']) . '</span>';
}

function statusOptionLabel(array $statusData)
{
    return $statusData['option'] ?? $statusData['th'];
}

function instagramIconSvg()
{
    return '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"></circle></svg>';
}

function avatarFallbackUrl($name, $background, $size)
{
    return 'https://ui-avatars.com/api/?name=' . rawurlencode($name)
        . '&background=' . rawurlencode($background)
        . '&color=fff&size=' . (int) $size;
}

function render($view, array $data = array())
{
    extract($data, EXTR_SKIP);
    require PROJECT_ROOT . '/views/' . $view . '.php';
}

function authenticateUser(mysqli $connection, $role, $username, $password)
{
    $queries = array(
        'student' => 'SELECT stu_id AS id, stu_name AS name FROM student WHERE stu_id = ? AND stu_pass = ?',
        'teacher' => 'SELECT tea_id AS id, tea_name AS name FROM teacher WHERE tea_id = ? AND tea_pass = ?',
        'staff' => 'SELECT sff_id AS id, sff_name AS name FROM staff WHERE sff_id = ? AND sff_pass = ?',
    );

    if (!isset($queries[$role])) {
        return null;
    }

    $statement = $connection->prepare($queries[$role]);

    if (!$statement) {
        return null;
    }

    $statement->bind_param('ss', $username, $password);
    $statement->execute();
    $result = $statement->get_result();

    if (!$result || $result->num_rows === 0) {
        return null;
    }

    return $result->fetch_assoc();
}

function submitInternshipRequest(mysqli $connection, $studentId, $companyName, $startDate, $endDate)
{
    if ($companyName === '' || $startDate === '' || $endDate === '') {
        return array('type' => 'error', 'text' => 'กรุณากรอกข้อมูลให้ครบถ้วน');
    }

    if ($endDate < $startDate) {
        return array('type' => 'error', 'text' => 'วันสิ้นสุดต้องไม่น้อยกว่าวันเริ่มต้น');
    }

    $requestDate = date('Y-m-d');
    $statusNow = 1;
    $statement = $connection->prepare('INSERT INTO request (stu_id, com_name, req_date, start_date, end_date, status_now) VALUES (?, ?, ?, ?, ?, ?)');

    if (!$statement) {
        return array('type' => 'error', 'text' => 'Database error: ' . $connection->error);
    }

    $statement->bind_param('sssssi', $studentId, $companyName, $requestDate, $startDate, $endDate, $statusNow);

    if ($statement->execute()) {
        return array('type' => 'success', 'text' => 'ยื่นคำร้องสำเร็จ! สถานะ: รับเรื่องเข้าระบบแล้ว');
    }

    return array('type' => 'error', 'text' => 'Database error: ' . $connection->error);
}

function updateRequestStatus(mysqli $connection, $requestId, $newStatus)
{
    $statement = $connection->prepare('UPDATE request SET status_now = ? WHERE req_id = ?');

    if (!$statement) {
        return false;
    }

    $requestId = (int) $requestId;
    $newStatus = (int) $newStatus;
    $statement->bind_param('ii', $newStatus, $requestId);

    return $statement->execute();
}

function reviewTeacherRequest(mysqli $connection, $requestId, $action)
{
    $newStatus = $action === 'approve' ? 2 : 9;

    return updateRequestStatus($connection, $requestId, $newStatus);
}

function fetchRows($result)
{
    if (!$result) {
        return array();
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function fetchStudentRequests(mysqli $connection, $studentId)
{
    $statement = $connection->prepare(
        'SELECT req_id, com_name, start_date, end_date, req_date, status_now
         FROM request
         WHERE stu_id = ?
         ORDER BY req_id DESC'
    );

    if (!$statement) {
        return array();
    }

    $statement->bind_param('s', $studentId);
    $statement->execute();

    return fetchRows($statement->get_result());
}

function fetchAllRequests(mysqli $connection)
{
    $result = $connection->query('SELECT * FROM request ORDER BY req_id DESC');

    return fetchRows($result);
}

function fetchStudentsByYear(mysqli $connection, $year)
{
    $yearString = (string) $year;
    $statement = $connection->prepare('SELECT * FROM student WHERE stu_year = ? ORDER BY stu_id ASC');

    if (!$statement) {
        return array();
    }

    $statement->bind_param('s', $yearString);
    $statement->execute();

    return fetchRows($statement->get_result());
}

function collectStudentsByYear(mysqli $connection, $maxYear)
{
    $studentsByYear = array();

    for ($year = 1; $year <= $maxYear; $year++) {
        $studentsByYear[$year] = fetchStudentsByYear($connection, $year);
    }

    return $studentsByYear;
}

function preparePageData($currentPage, mysqli $connection, $formMsg = null)
{
    if ($currentPage === 'internship') {
        $role = currentUserRole();
        $data = array(
            'currentUserRole' => $role,
            'studentRequests' => array(),
            'requestRows' => array(),
            'showStaffUpdated' => isset($_GET['staff_updated']),
            'showTeacherUpdated' => isset($_GET['teacher_updated']),
        );

        if ($role === 'student') {
            $data['studentRequests'] = fetchStudentRequests($connection, currentUserId());
        }

        if ($role === 'staff' || $role === 'teacher') {
            $data['requestRows'] = fetchAllRequests($connection);
        }

        return $data;
    }

    if ($currentPage === 'request_form') {
        return array('formMsg' => $formMsg);
    }

    if ($currentPage === 'student') {
        return array('studentsByYear' => collectStudentsByYear($connection, 4));
    }

    return array();
}

