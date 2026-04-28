<?php

// สร้างการเชื่อมต่อฐานข้อมูลและตั้งค่า charset ให้รองรับภาษาไทย
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

// ดึงข้อความตาม key ที่กำหนดจากชุดภาษา
function t($key)
{
    global $texts, $lang;

    return $texts[$lang][$key] ?? $key;
}

// ป้องกันอักขระพิเศษก่อนนำไปแสดงบนหน้าเว็บ
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// สร้าง URL กลับมาที่ home.php พร้อมพารามิเตอร์ที่ต้องการ
function homeUrl(array $params = array())
{
    $query = http_build_query($params);

    return 'home.php' . ($query !== '' ? '?' . $query : '');
}

// เปลี่ยนหน้าไปยังปลายทางที่กำหนดแล้วหยุดการทำงานต่อ
function redirectTo(array $params = array())
{
    header('Location: ' . homeUrl($params));
    exit();
}

// ตรวจว่าผู้ใช้เข้าสู่ระบบแล้วหรือยัง
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

// ดึง role ของผู้ใช้ปัจจุบัน
function currentUserRole()
{
    return $_SESSION['role'] ?? null;
}

// ดึงชื่อผู้ใช้ปัจจุบันจาก session
function currentUserName()
{
    return $_SESSION['user_name'] ?? '';
}

// ดึงรหัสผู้ใช้ปัจจุบันจาก session
function currentUserId()
{
    return $_SESSION['user_id'] ?? '';
}

// ตรวจว่าหน้าที่ขอมาอยู่ในรายการที่อนุญาตหรือไม่
function resolveCurrentPage($page, array $allowedPages)
{
    return in_array($page, $allowedPages, true) ? $page : 'internship';
}

// แปลงรหัสสถานะให้เป็น badge พร้อม class สี
function getStatusBadge($statusCode)
{
    global $statusMap;
    $statusCode = (int) $statusCode;

    if (!array_key_exists($statusCode, $statusMap)) {
        return "<span class='badge-status status-0'>รอตรวจสอบ</span>";
    }

    $status = $statusMap[$statusCode];

    return "<span class='badge-status {$status['class']}'>" . e($status['th']) . '</span>';
}

// เลือกข้อความที่ใช้แสดงใน dropdown สถานะ
function statusOptionLabel(array $statusData)
{
    return $statusData['option'] ?? $statusData['th'];
}

// ส่งรายการสถานะทั้งหมดกลับไปให้หน้าใช้งาน
function getStatusOptions()
{
    global $statusMap;

    return $statusMap;
}

// ส่งข้อมูลติดต่อกลับไปให้ส่วน footer ใช้งาน
function getContactInfo()
{
    global $contactInfo;

    return $contactInfo;
}

// ไอคอน Instagram แบบฝังในโค้ดเพื่อเรียกใช้ซ้ำ
function instagramIconSvg()
{
    return '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><circle cx="12" cy="12" r="4"></circle><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"></circle></svg>';
}

// สร้าง URL รูป avatar สำรองเมื่อไม่มีรูปจริง
function avatarFallbackUrl($name, $background, $size)
{
    return 'https://ui-avatars.com/api/?name=' . rawurlencode($name)
        . '&background=' . rawurlencode($background)
        . '&color=fff&size=' . (int) $size;
}

// ตรวจสอบข้อมูล login ตาม role ที่ผู้ใช้เลือก
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

// บันทึกคำร้องฝึกงานของนิสิตพร้อมตรวจข้อมูลพื้นฐาน
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

// อัปเดตสถานะของคำร้องตามรหัสคำร้องที่ระบุ
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

// เปลี่ยน action ของอาจารย์ให้เป็นรหัสสถานะจริงก่อนบันทึก
function reviewTeacherRequest(mysqli $connection, $requestId, $action)
{
    $newStatus = $action === 'approve' ? 2 : 9;

    return updateRequestStatus($connection, $requestId, $newStatus);
}

// แปลงผลลัพธ์จากฐานข้อมูลให้เป็น array ที่ใช้งานง่าย
function fetchRows($result)
{
    if (!$result) {
        return array();
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

// ดึงคำร้องทั้งหมดของนิสิตคนปัจจุบัน
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

// ดึงคำร้องทั้งหมดในระบบสำหรับ staff และ teacher
function fetchAllRequests(mysqli $connection)
{
    $result = $connection->query('SELECT * FROM request ORDER BY req_id DESC');

    return fetchRows($result);
}

// ดึงข้อมูลนิสิตตามชั้นปีที่ระบุ
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

// รวมข้อมูลนิสิตทุกชั้นปีไว้ในตัวแปรเดียว
function collectStudentsByYear(mysqli $connection, $maxYear)
{
    $studentsByYear = array();

    for ($year = 1; $year <= $maxYear; $year++) {
        $studentsByYear[$year] = fetchStudentsByYear($connection, $year);
    }

    return $studentsByYear;
}
