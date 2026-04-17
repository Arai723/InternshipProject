<?php
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

// ==========================================
// เชื่อมต่อ Database
// ==========================================
$host   = "127.0.0.1";
$user   = "root";
$pass   = "root1234";
$dbname = "is_internships";
$port   = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// ==========================================
// ระบบเปลี่ยนภาษา
// ==========================================
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];
    $page_param = isset($_GET['page']) ? "?page=" . $_GET['page'] : "";
    header("Location: home.php" . $page_param);
    exit();
}
$lang = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'th';

$texts = [
    'th' => [
        'system_title' => 'IS Internship',
        'system_desc'  => 'ระบบจัดการข้อมูลการฝึกงาน สาขาสารสนเทศศึกษา (SWU)',
        'menu_intern'  => 'ข้อมูลการฝึกงาน',
        'menu_pr'      => 'ประชาสัมพันธ์สาขา',
        'menu_course'  => 'หลักสูตร',
        'menu_teacher' => 'คณาจารย์',
        'menu_student' => 'นิสิตชั้นปีที่ 1-4',
        'logout'       => 'ออกจากระบบ',
        'welcome'      => 'ยินดีต้อนรับ',
        'btn_submit'   => '+ ยื่นคำร้องขอฝึกงาน',
        'role_student' => 'นิสิต',
        'role_teacher' => 'อาจารย์',
        'role_staff'   => 'เจ้าหน้าที่',
    ],
    'en' => [
        'system_title' => 'IS Internship',
        'system_desc'  => 'Information Studies Internship System (SWU)',
        'menu_intern'  => 'Internship Info',
        'menu_pr'      => 'News & PR',
        'menu_course'  => 'Curriculum',
        'menu_teacher' => 'Faculty',
        'menu_student' => 'Students',
        'logout'       => 'Logout',
        'welcome'      => 'Welcome',
        'btn_submit'   => '+ Submit Request',
        'role_student' => 'Student',
        'role_teacher' => 'Teacher',
        'role_staff'   => 'Staff',
    ]
];

function t($key) {
    global $texts, $lang;
    return $texts[$lang][$key] ?? $key;
}

// ==========================================
// Routing
// ==========================================
$current_page = isset($_GET['page']) ? $_GET['page'] : 'internship';

// ==========================================
// Logout
// ==========================================
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: home.php");
    exit();
}

// ==========================================
// Login
// ==========================================
$error_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $role     = $_POST['role'];
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($role == 'student') {
        $stmt = $conn->prepare("SELECT stu_id AS id, stu_name AS name FROM student WHERE stu_id = ? AND stu_pass = ?");
    } elseif ($role == 'teacher') {
        $stmt = $conn->prepare("SELECT tea_id AS id, tea_name AS name FROM teacher WHERE tea_id = ? AND tea_pass = ?");
    } elseif ($role == 'staff') {
        $stmt = $conn->prepare("SELECT sff_id AS id, sff_name AS name FROM staff WHERE sff_id = ? AND sff_pass = ?");
    }

    if (isset($stmt)) {
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $_SESSION['user_id']   = $row['id'];
            $_SESSION['user_name'] = $row['name'];
            $_SESSION['role']      = $role;
            header("Location: home.php");
            exit();
        } else {
            $error_msg = $lang == 'th' ? "ไอดีผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง" : "Invalid credentials";
        }
    }
}

// ==========================================
// [นิสิต] บันทึกคำร้องลง Database
// ==========================================
$form_msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_request'])) {
    $stu_id     = $_SESSION['user_id'];
    $com_name   = trim($_POST['com_name']);
    $start_date = trim($_POST['start_date']);
    $end_date   = trim($_POST['end_date']);
    $req_date   = date('Y-m-d');
    $status_now = 1;

    if (empty($com_name) || empty($start_date) || empty($end_date)) {
        $form_msg = ['type' => 'error', 'text' => $lang == 'th' ? 'กรุณากรอกข้อมูลให้ครบถ้วน' : 'Please fill in all fields'];
    } elseif ($end_date < $start_date) {
        $form_msg = ['type' => 'error', 'text' => $lang == 'th' ? 'วันสิ้นสุดต้องไม่น้อยกว่าวันเริ่มต้น' : 'End date must be after start date'];
    } else {
        $ins = $conn->prepare(
            "INSERT INTO request (stu_id, com_name, req_date, start_date, end_date, status_now)
             VALUES (?, ?, ?, ?, ?, ?)"
        );
        $ins->bind_param("sssssi", $stu_id, $com_name, $req_date, $start_date, $end_date, $status_now);
        if ($ins->execute()) {
            $form_msg = ['type' => 'success', 'text' => $lang == 'th'
                ? 'ยื่นคำร้องสำเร็จ! สถานะ: รับเรื่องเข้าระบบแล้ว'
                : 'Request submitted successfully!'];
        } else {
            $form_msg = ['type' => 'error', 'text' => 'Database error: ' . $conn->error];
        }
    }
}

// ==========================================
// [Staff] อัปเดตสถานะคำร้อง
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $req_id     = (int)$_POST['req_id'];
    $new_status = (int)$_POST['new_status'];
    $upd = $conn->prepare("UPDATE request SET status_now = ? WHERE req_id = ?");
    $upd->bind_param("ii", $new_status, $req_id);
    $upd->execute();
    header("Location: home.php?page=internship&staff_updated=1");
    exit();
}

// ==========================================
// [Teacher] อนุมัติ / ไม่อนุมัติ
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['teacher_action'])) {
    $req_id     = (int)$_POST['req_id'];
    $action     = $_POST['teacher_action'];
    $new_status = ($action == 'approve') ? 2 : 9;
    $upd = $conn->prepare("UPDATE request SET status_now = ? WHERE req_id = ?");
    $upd->bind_param("ii", $new_status, $req_id);
    $upd->execute();
    header("Location: home.php?page=internship&teacher_updated=1");
    exit();
}

// ==========================================
// ฟังก์ชันแปลงสถานะเป็น Badge HTML
// ==========================================
function getStatusBadge($status_code, $lang) {
    $status_map = [
        1 => ['th' => '1: รับเรื่องเข้าระบบ', 'en' => '1: Received',  'class' => 'status-1'],
        2 => ['th' => '2: อาจารย์อนุมัติ',    'en' => '2: Approved',  'class' => 'status-2'],
        3 => ['th' => '3: ออกใบส่งตัวแล้ว',   'en' => '3: Issued',    'class' => 'status-3'],
        4 => ['th' => '4: ฝึกงานเสร็จสิ้น',   'en' => '4: Completed', 'class' => 'status-4'],
        9 => ['th' => '9: ยกเลิกเอกสาร',      'en' => '9: Cancelled', 'class' => 'status-9'],
    ];
    $sc = (int)$status_code;
    if (!array_key_exists($sc, $status_map)) {
        return "<span class='badge-status status-0'>" . ($lang == 'th' ? 'รอตรวจสอบ' : 'Pending') . "</span>";
    }
    return "<span class='badge-status " . $status_map[$sc]['class'] . "'>" . $status_map[$sc][$lang] . "</span>";
}

// ==========================================
// ฟังก์ชัน Render ส่วน "ติดต่อเรา" (เหมือนในหน้าคณาจารย์)
// ==========================================
function renderContactFooter() {
    echo '
    <div class="faculty-footer">
        <h3>ติดต่อเรา</h3>
        <p>
            หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา<br>
            คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ<br>
            114 ซอยสุขุมวิท 23 แขวงคลองเตยเหนือ เขตวัฒนา กรุงเทพฯ 10110<br><br>
            Email: is@g.swu.ac.th | โทร: 02 649-5000
        </p>
    </div>';
}
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo t('system_title'); ?></title>
    <link rel="stylesheet" href="deco.css">
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        .alert-success { background:#d4edda; color:#155724; border:1px solid #c3e6cb; padding:10px 15px; border-radius:6px; margin-bottom:15px; }
        .alert-error   { background:#f8d7da; color:#721c24; border:1px solid #f5c6cb; padding:10px 15px; border-radius:6px; margin-bottom:15px; }
        .faculty-section-title { color:#003366; border-bottom:2px solid #ccc; padding-bottom:5px; margin-top:20px; margin-bottom:10px; }
        .faculty-item { background:#f9f9f9; padding:12px 15px; margin:10px 0; border-radius:8px; border-left:4px solid #003366; display:grid; grid-template-columns:56px 1fr; gap:15px; align-items:start; }
        .faculty-item img { width:56px; height:56px; border-radius:50%; object-fit:cover; }
        .faculty-info { display:grid; grid-template-columns:1fr 1fr 1fr; gap:4px 15px; align-items:center; }
        .faculty-info .fname  { font-weight:bold; font-size:15px; color:#222; grid-column: 1 / -1; }
        .faculty-info .feng   { color:#555; font-size:13px; }
        .faculty-info .femail { color:#0066cc; font-size:13px; }
        .faculty-footer { background:#003366; color:white; padding:15px 20px; border-radius:8px; margin-top:20px; font-size:14px; line-height:1.8; }
        .faculty-footer h3 { margin-top:0; }
        .inline-form { display:inline; }

        /* PR Post styles */
        .pr-post { background:#fff; border:1px solid #e2e8f0; border-radius:12px; margin-bottom:20px; overflow:hidden; box-shadow:0 1px 4px rgba(0,0,0,0.06); }
        .pr-post-header { display:flex; align-items:center; gap:12px; padding:14px 16px 0 16px; }
        .pr-post-avatar { width:40px; height:40px; border-radius:50%; background:#003366; display:flex; align-items:center; justify-content:center; color:#fff; font-weight:700; font-size:14px; flex-shrink:0; }
        .pr-post-account { font-weight:600; font-size:14px; color:#1e293b; }
        .pr-post-date { font-size:12px; color:#94a3b8; }
        .pr-post-img { width:100%; aspect-ratio:1/1; object-fit:cover; display:block; background:#e2e8f0; }
        .pr-post-img-placeholder { width:100%; aspect-ratio:1/1; background:#f1f5f9; display:flex; align-items:center; justify-content:center; color:#94a3b8; font-size:14px; }
        .pr-post-body { padding:14px 16px 16px 16px; }
        .pr-post-body .pr-tags { color:#003366; font-size:13px; font-weight:500; margin-bottom:6px; }
        .pr-post-body .pr-text { font-size:14px; color:#334155; line-height:1.7; white-space:pre-line; }
        .pr-post-body .pr-link { display:inline-block; margin-top:10px; color:#dc2626; font-size:13px; text-decoration:none; }
        .pr-post-body .pr-link:hover { text-decoration:underline; }
        .pr-grid { display:grid; grid-template-columns:1fr 1fr; gap:20px; }
        @media (max-width:700px) { .pr-grid { grid-template-columns:1fr; } .faculty-info { grid-template-columns:1fr; } }
    </style>
</head>
<body>

<div class="lang-bar">
    <a href="?<?php echo isset($_GET['page']) ? 'page='.$_GET['page'].'&' : ''; ?>lang=th" class="<?php echo $lang=='th'?'active':''; ?>">TH</a> |
    <a href="?<?php echo isset($_GET['page']) ? 'page='.$_GET['page'].'&' : ''; ?>lang=en" class="<?php echo $lang=='en'?'active':''; ?>">EN</a>
</div>

<?php if (!isset($_SESSION['user_id'])): ?>
<!-- ===== หน้า Login ===== -->
<div class="login-page">
    <div class="login-container">
        <div class="login-header">
            <div class="logo-text">IS</div>
            <h1>IS <span>Internship</span></h1>
            <p><?php echo t('system_desc'); ?></p>
        </div>
        <?php if ($error_msg): ?>
            <div class="alert-error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>
        <form method="POST" action="home.php" class="login-form">
            <div class="role-selector">
                <label><input type="radio" name="role" value="student" checked> <?php echo t('role_student'); ?></label>
                <label><input type="radio" name="role" value="teacher"> <?php echo t('role_teacher'); ?></label>
                <label><input type="radio" name="role" value="staff"> <?php echo t('role_staff'); ?></label>
            </div>
            <div class="form-group">
                <label><?php echo $lang=='th' ? 'รหัสประจำตัว' : 'ID Number'; ?></label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label><?php echo $lang=='th' ? 'รหัสผ่าน' : 'Password'; ?></label>
                <input type="password" name="password" required>
            </div>
            <button type="submit" name="login" class="btn-login"><?php echo $lang=='th' ? 'เข้าสู่ระบบ' : 'Login'; ?></button>
        </form>
    </div>
</div>

<?php else: ?>
<!-- ===== หน้า Dashboard ===== -->
<div class="dashboard-page">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>IS <span>Internship</span></h2>
            <p class="sub-logo">Information Studies SWU</p>
        </div>
        <ul class="sidebar-menu">
            <li><a href="?page=internship" class="<?php echo $current_page=='internship'?'active':''; ?>"><?php echo t('menu_intern'); ?></a></li>
            <li><a href="?page=pr"         class="<?php echo $current_page=='pr'        ?'active':''; ?>"><?php echo t('menu_pr'); ?></a></li>
            <li><a href="?page=course"     class="<?php echo $current_page=='course'    ?'active':''; ?>"><?php echo t('menu_course'); ?></a></li>
            <li><a href="?page=teacher"    class="<?php echo $current_page=='teacher'   ?'active':''; ?>"><?php echo t('menu_teacher'); ?></a></li>
            <li><a href="?page=student"    class="<?php echo $current_page=='student'   ?'active':''; ?>"><?php echo t('menu_student'); ?></a></li>
            <li class="logout-link"><a href="home.php?logout=true"><?php echo t('logout'); ?></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <h3>
                <?php echo t('welcome'); ?>, <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                <span class="badge role-<?php echo $_SESSION['role']; ?>">
                    (<?php echo t('role_'.$_SESSION['role']); ?>)
                </span>
            </h3>
        </header>

        <div class="content-wrapper">
        <?php switch ($current_page):

            // ==========================================
            // 1. หน้าข้อมูลการฝึกงาน
            // ==========================================
            case 'internship':
                if (isset($_GET['staff_updated'])): ?>
                    <div class="alert-success">✔ อัปเดตสถานะเรียบร้อยแล้ว</div>
                <?php endif;
                if (isset($_GET['teacher_updated'])): ?>
                    <div class="alert-success">✔ บันทึกผลการพิจารณาเรียบร้อยแล้ว</div>
                <?php endif;

                // ----- STUDENT -----
                if ($_SESSION['role'] == 'student'): ?>
                    <div class="card">
                        <div class="card-header-flex">
                            <div>
                                <h4><?php echo $lang=='th' ? 'สถานะการฝึกงานของคุณ' : 'Your Internship Status'; ?></h4>
                                <p class="text-muted"><?php echo $lang=='th' ? 'ตรวจสอบความคืบหน้าแบบ Real-time' : 'Track your status in real-time'; ?></p>
                            </div>
                            <a href="?page=request_form" class="btn-action primary"><?php echo t('btn_submit'); ?></a>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><?php echo $lang=='th' ? 'รหัสคำร้อง' : 'Doc No.'; ?></th>
                                    <th><?php echo $lang=='th' ? 'บริษัท / สถานประกอบการ' : 'Company'; ?></th>
                                    <th><?php echo $lang=='th' ? 'วันที่เริ่ม' : 'Start Date'; ?></th>
                                    <th><?php echo $lang=='th' ? 'วันที่สิ้นสุด' : 'End Date'; ?></th>
                                    <th><?php echo $lang=='th' ? 'วันที่ยื่น' : 'Submitted'; ?></th>
                                    <th><?php echo $lang=='th' ? 'สถานะ' : 'Status'; ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stu_id = $_SESSION['user_id'];
                                $stmt = $conn->prepare(
                                    "SELECT req_id, com_name, start_date, end_date, req_date, status_now
                                     FROM request
                                     WHERE stu_id = ?
                                     ORDER BY req_id DESC"
                                );
                                $stmt->bind_param("s", $stu_id);
                                $stmt->execute();
                                $req_result = $stmt->get_result();
                                if ($req_result && $req_result->num_rows > 0):
                                    while ($row = $req_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['req_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['com_name'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['start_date'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['end_date'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['req_date']); ?></td>
                                            <td><?php echo getStatusBadge($row['status_now'], $lang); ?></td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr><td colspan="6" class="text-center empty-state">
                                        <?php echo $lang=='th' ? 'ยังไม่มีข้อมูลคำร้องในระบบ' : 'No requests found'; ?>
                                    </td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php renderContactFooter(); ?>

                <?php
                // ----- STAFF -----
                elseif ($_SESSION['role'] == 'staff'): ?>
                    <div class="card">
                        <h4><?php echo $lang=='th' ? 'จัดการข้อมูลคำร้องทั้งหมด' : 'Manage All Requests'; ?></h4>
                        <p class="text-muted"><?php echo $lang=='th' ? 'เลือกสถานะใหม่แล้วกด "บันทึก" เพื่ออัปเดตทีละรายการ' : 'Select new status and click Save to update'; ?></p>

                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>รหัสคำร้อง</th>
                                    <th>รหัสนิสิต</th>
                                    <th>บริษัท</th>
                                    <th>วันที่ยื่น</th>
                                    <th>ช่วงฝึกงาน</th>
                                    <th>สถานะปัจจุบัน</th>
                                    <th>อัปเดตสถานะ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $req_result = $conn->query(
                                    "SELECT * FROM request ORDER BY req_id DESC"
                                );
                                if ($req_result && $req_result->num_rows > 0):
                                    while ($row = $req_result->fetch_assoc()):
                                        $cs = (int)$row['status_now']; ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['req_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['stu_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['com_name'] ?? '-'); ?></td>
                                            <td><?php echo htmlspecialchars($row['req_date']); ?></td>
                                            <td style="font-size:12px">
                                                <?php echo htmlspecialchars($row['start_date'] ?? '-'); ?><br>
                                                <?php echo htmlspecialchars($row['end_date'] ?? '-'); ?>
                                            </td>
                                            <td><?php echo getStatusBadge($cs, $lang); ?></td>
                                            <td>
                                                <form method="POST" action="home.php?page=internship" class="inline-form">
                                                    <input type="hidden" name="req_id" value="<?php echo (int)$row['req_id']; ?>">
                                                    <select name="new_status" class="status-dropdown">
                                                        <option value="1" <?php if($cs==1) echo 'selected'; ?>>1: รับเรื่องเข้าระบบ</option>
                                                        <option value="2" <?php if($cs==2) echo 'selected'; ?>>2: อ.ที่ปรึกษาอนุมัติ</option>
                                                        <option value="3" <?php if($cs==3) echo 'selected'; ?>>3: ออกใบส่งตัวแล้ว</option>
                                                        <option value="4" <?php if($cs==4) echo 'selected'; ?>>4: ฝึกงานเสร็จสิ้น</option>
                                                        <option value="9" <?php if($cs==9) echo 'selected'; ?>>9: ยกเลิก</option>
                                                    </select>
                                                    <button type="submit" name="update_status" class="btn-small save-btn">บันทึก</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr><td colspan="7" class="text-center empty-state">ไม่มีข้อมูลในระบบ</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php renderContactFooter(); ?>

                <?php
                // ----- TEACHER -----
                elseif ($_SESSION['role'] == 'teacher'): ?>
                    <div class="card">
                        <h4><?php echo $lang=='th' ? 'รายการคำร้องรอการอนุมัติ / บันทึกผล' : 'Pending Approvals / Record Results'; ?></h4>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>รหัสคำร้อง</th>
                                    <th>รหัสนิสิต</th>
                                    <th>บริษัท</th>
                                    <th>ช่วงฝึกงาน</th>
                                    <th>วันที่ยื่น</th>
                                    <th>สถานะปัจจุบัน</th>
                                    <th>พิจารณา</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $req_result = $conn->query(
                                    "SELECT * FROM request ORDER BY req_id DESC"
                                );
                                if ($req_result && $req_result->num_rows > 0):
                                    while ($row = $req_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['req_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['stu_id']); ?></td>
                                            <td><?php echo htmlspecialchars($row['com_name'] ?? '-'); ?></td>
                                            <td style="font-size:12px">
                                                <?php echo htmlspecialchars($row['start_date'] ?? '-'); ?><br>
                                                <?php echo htmlspecialchars($row['end_date'] ?? '-'); ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['req_date']); ?></td>
                                            <td><?php echo getStatusBadge($row['status_now'], $lang); ?></td>
                                            <td>
                                                <?php if ((int)$row['status_now'] == 1): ?>
                                                    <form method="POST" action="home.php?page=internship" class="inline-form">
                                                        <input type="hidden" name="req_id" value="<?php echo (int)$row['req_id']; ?>">
                                                        <button type="submit" name="teacher_action" value="approve" class="btn-small success">✔ อนุมัติ</button>
                                                    </form>
                                                    <form method="POST" action="home.php?page=internship" class="inline-form">
                                                        <input type="hidden" name="req_id" value="<?php echo (int)$row['req_id']; ?>">
                                                        <button type="submit" name="teacher_action" value="reject" class="btn-small danger">✘ ไม่อนุมัติ</button>
                                                    </form>
                                                <?php else: ?>
                                                    <span style="color:#999;font-size:12px;">ดำเนินการแล้ว</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile;
                                else: ?>
                                    <tr><td colspan="7" class="text-center empty-state">ไม่มีข้อมูลในระบบ</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php renderContactFooter(); ?>
                <?php endif;
                break;

            // ==========================================
            // 2. ฟอร์มยื่นคำร้อง (เฉพาะนิสิต)
            // ==========================================
            case 'request_form':
                if ($_SESSION['role'] != 'student') {
                    echo "<div class='card'><p>เฉพาะนิสิตเท่านั้นที่สามารถเข้าถึงหน้านี้ได้</p></div>";
                    renderContactFooter();
                    break;
                } ?>
                <div class="card">
                    <div class="card-header-flex">
                        <h4><?php echo $lang=='th' ? 'แบบฟอร์มยื่นคำร้องขอฝึกงาน' : 'Internship Request Form'; ?></h4>
                        <a href="?page=internship" class="btn-small text-only"><?php echo $lang=='th' ? 'กลับไปหน้าข้อมูล' : 'Back to Info'; ?></a>
                    </div>
                    <hr class="divider">

                    <?php if ($form_msg): ?>
                        <div class="alert-<?php echo $form_msg['type']; ?>"><?php echo htmlspecialchars($form_msg['text']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="home.php?page=request_form" class="submit-form">
                        <div class="form-row">
                            <div class="form-group-half">
                                <label><?php echo $lang=='th' ? 'รหัสนิสิต' : 'Student ID'; ?></label>
                                <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>" readonly class="readonly-input">
                            </div>
                            <div class="form-group-half">
                                <label><?php echo $lang=='th' ? 'ชื่อ-นามสกุล' : 'Full Name'; ?></label>
                                <input type="text" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" readonly class="readonly-input">
                            </div>
                        </div>

                        <div class="form-group">
                            <label><?php echo $lang=='th' ? 'ชื่อบริษัท / สถานประกอบการ' : 'Company Name'; ?></label>
                            <input type="text" name="com_name"
                                   value="<?php echo isset($_POST['com_name']) ? htmlspecialchars($_POST['com_name']) : ''; ?>"
                                   placeholder="<?php echo $lang=='th' ? 'เช่น บริษัท ABC จำกัด' : 'e.g. ABC Co., Ltd.'; ?>" required>
                        </div>

                        <div class="form-row">
                            <div class="form-group-half">
                                <label><?php echo $lang=='th' ? 'วันที่เริ่มฝึกงาน' : 'Start Date'; ?></label>
                                <input type="date" name="start_date"
                                       value="<?php echo isset($_POST['start_date']) ? htmlspecialchars($_POST['start_date']) : ''; ?>" required>
                            </div>
                            <div class="form-group-half">
                                <label><?php echo $lang=='th' ? 'วันที่สิ้นสุดการฝึกงาน' : 'End Date'; ?></label>
                                <input type="date" name="end_date"
                                       value="<?php echo isset($_POST['end_date']) ? htmlspecialchars($_POST['end_date']) : ''; ?>" required>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" name="submit_request" class="btn-action primary">
                                <?php echo $lang=='th' ? 'บันทึกข้อมูล' : 'Submit'; ?>
                            </button>
                        </div>
                    </form>
                </div>
                <?php renderContactFooter();
                break;

            // ==========================================
            // 3. ประชาสัมพันธ์
            // ==========================================
            case 'pr': ?>
                <div class="card info-card">
                    <h4><?php echo $lang=='th' ? 'ข่าวสารและประชาสัมพันธ์ สาขาสารสนเทศศึกษา' : 'IS SWU News & PR'; ?></h4>
                    <p class="text-muted"><?php echo $lang=='th' ? 'ติดตามกิจกรรม ประกาศการฝึกงาน และความเคลื่อนไหวของสาขาได้ตามช่องทางด้านล่างนี้' : 'Follow our official channels for the latest updates.'; ?></p>
                    <ul class="clean-list">
                        <li>Instagram: <a href="https://www.instagram.com/is.hmswu/" target="_blank">@is.hmswu</a></li>
                        <li>Website: <a href="https://is.hu.swu.ac.th/" target="_blank">is.hu.swu.ac.th</a></li>
                    </ul>
                </div>

                <!-- PR Posts แบบ IG -->
                <div class="pr-grid">

                    <!-- โพสต์ที่ 1: IS111 กิจกรรมโต้วาที -->
                    <div class="pr-post">
                        <div class="pr-post-header">
                            <div class="pr-post-avatar">IS</div>
                            <div>
                                <div class="pr-post-account">is.hmswu</div>
                                <div class="pr-post-date">6 กุมภาพันธ์ 2569</div>
                            </div>
                            </div>
                        <div div class="is111img">
                            <img src="" alt="">
                        </div>
                        <div class="pr-post-body">
                            <div class="pr-tags">#IS111 #สารสนเทศศึกษา #SWU</div>
                            <div class="pr-text">📚 <strong>IS111 การรู้สารสนเทศและรู้เท่าทันสื่อ</strong>

กิจกรรมโต้วาที ภาคเรียนที่ 1/2568
หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา
คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ</div>
                            <a href="https://is.hu.swu.ac.th/is111/" target="_blank" class="pr-link">→ อ่านเพิ่มเติม</a>
                        </div>
                    </div>

                    <!-- โพสต์ที่ 2: Government Data Catalog Day 2024 -->
                    <div class="pr-post">
                        <div class="pr-post-header">
                            <div class="pr-post-avatar">IS</div>
                            <div>
                                <div class="pr-post-account">is.hmswu</div>
                                <div class="pr-post-date">21 กุมภาพันธ์ 2568</div>
                            </div>
                        </div>
                        <div class="img2">
                        <img src="https://is.hu.swu.ac.th/wp-content/uploads/2025/02/GDCatalog2-1140x458.jpg"
                             alt="Government Data Catalog Day 2024" class="pr-post-img">
                        </div>
                        <div class="pr-post-body">
                            <div class="pr-tags">#GDCatalog #OpenData #SWU #สารสนเทศศึกษา</div>
                            <div class="pr-text">🏆 <strong>Government Data Catalog Day 2024: Insights and Impact</strong>

เมื่อวันพุธที่ 19 กุมภาพันธ์ 2568 คณาจารย์หลักสูตรสารสนเทศศึกษา คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ เข้าร่วมงานประชุมสัมมนา Government Data Catalog Day 2024

🎖️ สาขาวิชาสารสนเทศศึกษา มศว ได้รับรางวัลหน่วยงานทางด้านการศึกษาที่ใช้ข้อมูลสาธารณะจากภาครัฐดีเด่น</div>
                            <a href="https://is.hu.swu.ac.th/gdcatalog/" target="_blank" class="pr-link">→ อ่านเพิ่มเติม</a>
                        </div>
                    </div>

                </div>

                <!-- โพสต์ที่ 3: พัฒนาระบบสารสนเทศ (เต็มความกว้าง) -->
                <div class="pr-post">
                    <div class="pr-post-header">
                        <div class="pr-post-avatar">IS</div>
                        <div>
                            <div class="pr-post-account">is.hmswu</div>
                            <div class="pr-post-date">วันจันทร์ที่ 16 มีนาคม 2569</div>
                        </div>
                    </div>
                <div class="Img111">
                    <img src="https://hu.swu.ac.th/Portals/5/EasyDNNNews/12571/600600p2228EDNmainimg-1611.jpg" alt="img111">
                </div>
                    <div class="pr-post-body">
                        <div class="pr-tags">#HumanitiesSWU #DigitalTransformation #Automation #CyberSecurity</div>
                        <div class="pr-text">🖥️ <strong>โครงการ "พัฒนาระบบสารสนเทศเพื่อการบริหารจัดการองค์กร"</strong>
คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ
📍 ห้อง 38-0301 ชั้น 3 อาคาร 38 คณะมนุษยศาสตร์

โดยได้รับเกียรติจาก ผู้ช่วยศาสตราจารย์ ดร.อัญชลี จันทร์เสม คณบดีคณะมนุษยศาสตร์ กล่าวเปิดโครงการ

ภายในกิจกรรมมีการอบรมเชิงปฏิบัติการเกี่ยวกับ Automation, Workflow และ Cyber Security เพื่อเสริมทักษะการใช้เทคโนโลยีในการบริหารจัดการงานและการใช้ระบบสารสนเทศอย่างปลอดภัย

ขอขอบพระคุณวิทยากรจากสาขาวิชาสารสนเทศศึกษา ได้แก่
🖥️ อาจารย์ ดร.ดิษฐ์ สุทธิวงศ์
🖥️ ผู้ช่วยศาสตราจารย์ ดร.วิภากร วัฒนสินธุ์
🖥️ ผู้ช่วยศาสตราจารย์ ดร.ดุษฎี สีวังคำ
🖥️ อาจารย์ ดร.โชคธำรงค์ จงจอหอ
🖥️ อาจารย์ ดร.ฐิติ อติชาติชยากร

ที่ได้ร่วมถ่ายทอดความรู้และประสบการณ์แก่บุคลากรของคณะ เพื่อสนับสนุนการพัฒนาองค์กรสู่ Digital Organization</div>
                    </div>
                </div>

                <?php renderContactFooter();
                break;

            // ==========================================
            // 4. หลักสูตร (แบบไม่มีจุด Bullet Points)
            // ==========================================
            case 'course': ?>
                <style>
                    /* CSS เฉพาะส่วนเนื้อหาหลักสูตร */
                    .course-section { margin-bottom: 25px; }
                    .course-section h3 { 
                        color: #003366; 
                        font-size: 1.25rem; 
                        margin-bottom: 12px; 
                        border-bottom: 1px solid #eee; 
                        padding-bottom: 5px;
                    }
                    .course-item { 
                        margin-bottom: 10px; 
                        padding-left: 5px;
                        line-height: 1.6;
                    }
                    .course-item strong { color: #333; }
                    .text-indent { padding-left: 20px; }
                    .course-quote { 
                        font-style: italic; 
                        color: #555; 
                        background: #fcfcfc; 
                        padding: 15px; 
                        border-left: 4px solid #003366;
                        margin-bottom: 25px;
                    }
                </style>

                <div class="card">
                    <h4><?php echo $lang=='th' ? 'หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา' : 'B.A. Program in Information Studies'; ?></h4>
                    
                    <div class="course-quote">
                        <?php echo $lang=='th' ? 'ปรัชญา: "สารสนเทศสร้างปัญญา ปัญญาสร้างคุณค่าให้สังคม"' : 'Philosophy: "Information creates wisdom, wisdom creates value for society"'; ?>
                    </div>

                    <?php if ($lang == 'th'): ?>
                        <div class="course-section">
                            <div class="course-item"><strong>รหัสหลักสูตร:</strong> 25520091104002</div>
                        </div>

                        <div class="course-section">
                            <h3>1. ชื่อหลักสูตรและวุฒิการศึกษา</h3>
                            <div class="course-item"><strong>ชื่อภาษาไทย:</strong> ศิลปศาสตรบัณฑิต (ศศ.บ.) สาขาวิชาสารสนเทศศึกษา</div>
                            <div class="course-item"><strong>ชื่อภาษาอังกฤษ:</strong> Bachelor of Arts (Information Studies)</div>
                            <div class="course-item"><strong>อักษรย่อ:</strong> B.A. (Information Studies)</div>
                        </div>

                        <div class="course-section">
                            <h3>2. เจาะลึกวิชาเรียน (กลุ่มวิชาเอก)</h3>
                            <p class="course-item">หลักสูตรแบ่งกลุ่มวิชาเพื่อให้ครอบคลุมการทำงานทั้งรูปแบบดั้งเดิมและเทคโนโลยีสมัยใหม่:</p>
                            <div class="course-item text-indent"><strong>การจัดการสารสนเทศ:</strong> เรียนรู้การวิเคราะห์ จัดหมวดหมู่ และจัดเก็บข้อมูลอย่างเป็นระบบ</div>
                            <div class="course-item text-indent"><strong>เทคโนโลยีสารสนเทศ:</strong> การเขียนโปรแกรมเบื้องต้น การจัดการฐานข้อมูล และการพัฒนาเว็บไซต์</div>
                            <div class="course-item text-indent"><strong>การจัดการนวัตกรรมและคอนเทนต์:</strong> การสร้างสรรค์เนื้อหาดิจิทัล การตลาดสารสนเทศ และการใช้ AI</div>
                            <div class="course-item text-indent"><strong>การบริการสารสนเทศ:</strong> จิตวิทยาการบริการ และการถ่ายทอดความรู้</div>
                        </div>

                        <div class="course-section">
                            <h3>3. จุดเด่นของสาขา</h3>
                            <div class="course-item"><strong>ความร่วมมือกับภาคเอกชน:</strong> มีวิทยากรจากวงการ Digital Agency และ Data Analyst</div>
                            <div class="course-item"><strong>ทักษะที่หลากหลาย:</strong> ผสมผสานศาสตร์มนุษย์และศาสตร์ข้อมูลเข้าด้วยกัน</div>
                            <div class="course-item"><strong>ห้องปฏิบัติการ:</strong> มีเครื่องมือและซอฟต์แวร์ที่ทันสมัยสำหรับการฝึกปฏิบัติจริง</div>
                        </div>

                        <div class="course-section">
                            <h3>4. แนวทางการประกอบอาชีพ</h3>
                            <div class="course-item">นักจัดการสารสนเทศและข้อมูล (Information Manager)</div>
                            <div class="course-item">นักจดหมายเหตุ หรือ บรรณารักษ์ยุคใหม่ (Modern Librarian)</div>
                            <div class="course-item">Content Creator และ Digital Content Manager</div>
                            <div class="course-item">Data Curator และ Knowledge Manager ในองค์กรชั้นนำ</div>
                        </div>

                        <div class="course-section">
                            <h3>5. เกณฑ์การคัดเลือก ปี 2569 (TCAS)</h3>
                            <div class="course-item"><strong>ภาคปกติ:</strong> รอบที่ 1 (Portfolio) 10 คน | รอบที่ 3 (Admission) 40 คน</div>
                            <div class="course-item"><strong>ภาคพิเศษ:</strong> รอบที่ 3 (Admission) 40 คน</div>
                            <div class="course-item" style="font-size: 0.9rem; color: #777; margin-top: 10px;">
                                *พิจารณาจาก GPAX และคะแนน TGAT / A-Level ตามเกณฑ์ของมหาวิทยาลัย
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="course-section">
                            <h3>Program Overview</h3>
                            <div class="course-item">Focused on producing graduates skilled in information management and digital transformation.</div>
                            <div class="course-item"><strong>Core Areas:</strong> IT, Data Analytics, and Content Creation.</div>
                        </div>
                    <?php endif; ?>
                </div>
                <?php renderContactFooter();
                break;

            // ==========================================
            // 5. คณาจารย์
            // ==========================================
            case 'teacher': ?>
                <div class="card">
                    <h4><?php echo $lang == 'th' ? 'คณาจารย์ประจำหลักสูตร' : 'Faculty Members'; ?></h4>
                    <h5 class="faculty-section-title"><?php echo $lang == 'th' ? 'คณะกรรมการหลักสูตร' : 'Curriculum Committee'; ?></h5>

                    <div class="faculty-item">
                        <img src="https://is.hu.swu.ac.th/wp-content/uploads/2021/01/Dit-scaled.jpg" alt="TEACHER1">
                        <div class="faculty-info">
                            <div class="fname">อาจารย์ ดร. ดิษฐ์ สุทธิวงศ์ <small>(ประธานกรรมการบริหารหลักสูตร)</small></div>
                            <div class="feng">Lecturer Dit Suthiwong, Ph.D.</div>
                            <div class="femail">Email: dit@g.swu.ac.th</div>
                        </div>
                    </div>

                    <div class="faculty-item">
                        <img src="https://is.hu.swu.ac.th/wp-content/uploads/2021/01/thiti-scaled.jpg" alt="">
                        <div class="faculty-info">
                            <div class="fname">อาจารย์ ดร. ฐิติ อติชาติชยากร <small>(เลขานุการหลักสูตร)</small></div>
                            <div class="feng">Lecturer Thiti Atichartchayakorn, Ph.D.</div>
                            <div class="femail">Email: thitik@g.swu.ac.th</div>
                        </div>
                    </div>

                    <div class="faculty-item">
                        <img src="https://is.hu.swu.ac.th/wp-content/uploads/2020/02/Vipakorn-683x1024.jpg" alt="">
                        <div class="faculty-info">
                            <div class="fname">ผู้ช่วยศาสตราจารย์ ดร. วิภากร วัฒนสินธุ์</div>
                            <div class="feng">Assistant Professor Vipakorn Vadhanasin, Ph.D., PMP, FHEA</div>
                            <div class="femail">Email: vipakorn@g.swu.ac.th</div>
                        </div>
                    </div>

                    <div class="faculty-item">
                        <img src="https://is.hu.swu.ac.th/wp-content/uploads/2025/02/Chokthamrong.jpg" alt="">
                        <div class="faculty-info">
                            <div class="fname">อาจารย์ ดร. โชคธำรงค์ จงจอหอ</div>
                            <div class="feng">Lecturer Chokthamrong Chongchorhor, Ph.D.</div>
                            <div class="femail">Email: chokthamrong@g.swu.ac.th</div>
                        </div>
                    </div>

                    <div class="faculty-item">
                        <img src="https://is.hu.swu.ac.th/wp-content/uploads/2025/11/Chotima.jpg" alt="">
                        <div class="faculty-info">
                            <div class="fname">อาจารย์โชติมา วัฒนะ</div>
                            <div class="feng">Lecturer Chotima Watana</div>
                            <div class="femail">Email: chotimaw@g.swu.ac.th</div>
                        </div>
                    </div>

                    <h5 class="faculty-section-title"><?php echo $lang == 'th' ? 'ผู้ช่วยศาสตราจารย์' : 'Instructors'; ?></h5>

                    <div class="faculty-item">
                        <img src="https://is.hu.swu.ac.th/wp-content/uploads/2020/02/Dussadee-683x1024.jpg" alt="">
                        <div class="faculty-info">
                            <div class="fname">ผู้ช่วยศาสตราจารย์ ดร. ดุษฎี สีวังคำ</div>
                            <div class="feng">Assistant Professor Dussadee Seewungkum, Ph.D.</div>
                            <div class="femail">Email: dussadee@g.swu.ac.th</div>
                        </div>
                    </div>

                    <div class="faculty-item">
                        <img src="https://is.hu.swu.ac.th/wp-content/uploads/2020/02/Sasipimol-683x1024.jpg" alt="">
                        <div class="faculty-info">
                            <div class="fname">ผู้ช่วยศาสตราจารย์ ดร. ศศิพิมล ประพินพงศกร</div>
                            <div class="feng">Assistant Professor Sasipimol Prapinpongsakorn, Ph.D., FHEA</div>
                            <div class="femail">Email: sasipimol@g.swu.ac.th</div>
                        </div>
                    </div>

                    <div class="faculty-item">
                        <img src="https://is.hu.swu.ac.th/wp-content/uploads/2020/02/Sumattra-683x1024.jpg" alt="">
                        <div class="faculty-info">
                            <div class="fname">อาจารย์ ดร. ศุมรรษตรา แสนวา</div>
                            <div class="feng">Lecturer Sumattra Saenwa, Ph.D., FHEA</div>
                            <div class="femail">Email: sumattra@g.swu.ac.th</div>
                        </div>
                    </div>

                    <div class="faculty-footer">
                        <h3>ติดต่อเรา</h3>
                        <p>
                            หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา<br>
                            คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ<br>
                            114 ซอยสุขุมวิท 23 แขวงคลองเตยเหนือ เขตวัฒนา กรุงเทพฯ 10110<br><br>
                            Email: is@g.swu.ac.th | โทร: 02 649-5000
                        </p>
                    </div>
                </div>
                <?php break;

            // ==========================================
            // 6. นิสิต (ดึงจาก DB แบ่งตามชั้นปี)
            // ==========================================
            case 'student': ?>
                <div class="card">
                    <h4><?php echo $lang=='th' ? 'ทำเนียบนิสิต (ชั้นปี 1-4)' : 'Student Directory (Year 1-4)'; ?></h4>
                    <?php for ($yr = 1; $yr <= 4; $yr++):
                        $yr_label = $lang=='th' ? "ชั้นปีที่ $yr" : "Year $yr";
                        $stu_stmt = $conn->prepare("SELECT * FROM student WHERE stu_year = ? ORDER BY stu_id ASC");
                        $yr_str   = (string)$yr;
                        $stu_stmt->bind_param("s", $yr_str);
                        $stu_stmt->execute();
                        $stu_result = $stu_stmt->get_result();
                    ?>
                        <h5 class="faculty-section-title"><?php echo $yr_label; ?></h5>
                        <?php if ($stu_result && $stu_result->num_rows > 0):
                            while ($s = $stu_result->fetch_assoc()): ?>
                                <div class="faculty-item">
                                    <img src="" alt=""
                                         onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($s['stu_name']); ?>&background=555&color=fff&size=56'">
                                    <div class="faculty-info">
                                        <div class="fname"><?php echo htmlspecialchars($s['stu_name']); ?></div>
                                        <div class="feng"><?php echo htmlspecialchars($s['stu_id']); ?></div>
                                        <div class="femail">Email: <?php echo htmlspecialchars($s['stu_email']); ?></div>
                                        <div class="femail">เบอร์โทร: <?php echo htmlspecialchars($s['stu_tel'] ?? '-'); ?></div>
                                    </div>
                                </div>
                            <?php endwhile;
                        else: ?>
                            <p class="text-muted"><?php echo $lang=='th' ? 'ไม่มีข้อมูลนิสิตชั้นปีนี้' : 'No students found for this year'; ?></p>
                        <?php endif;
                    endfor; ?>

                    <div class="faculty-footer">
                        <h3>ติดต่อเรา</h3>
                        <p>
                            หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา<br>
                            คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ<br>
                            114 ซอยสุขุมวิท 23 แขวงคลองเตยเหนือ เขตวัฒนา กรุงเทพฯ 10110<br><br>
                            Email: is@g.swu.ac.th | โทร: 02 649-5000
                        </p>
                    </div>
                </div>
                <?php break;

        endswitch; ?>
        </div>
    </main>
</div>
<?php endif; ?>

</body>
</html>