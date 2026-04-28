<?php

// ค่าการเชื่อมต่อฐานข้อมูลของโปรเจกต์
$dbConfig = array(
    'host' => '127.0.0.1',
    'user' => 'root',
    'pass' => 'root1234',
    'name' => 'is_internships',
    'port' => 3306,
);

// ภาษาหลักที่ใช้ในระบบ
$lang = 'th';

// ข้อความกลางที่เรียกใช้ซ้ำในหลายตำแหน่ง
$texts = array(
    'th' => array(
        'system_title' => 'IS Internship',
        'system_desc' => 'ระบบจัดการข้อมูลการฝึกงาน สาขาสารสนเทศศึกษา (SWU)',
        'menu_intern' => 'ข้อมูลการฝึกงาน',
        'menu_pr' => 'ประชาสัมพันธ์สาขา',
        'menu_about' => 'เกี่ยวกับเรา',
        'menu_course' => 'หลักสูตร',
        'menu_teacher' => 'คณาจารย์',
        'menu_student' => 'นิสิตชั้นปีที่ 1-4',
        'logout' => 'ออกจากระบบ',
        'welcome' => 'ยินดีต้อนรับ',
        'btn_submit' => '+ ยื่นคำร้องขอฝึกงาน',
        'role_student' => 'นิสิต',
        'role_teacher' => 'อาจารย์',
        'role_staff' => 'เจ้าหน้าที่',
    ),
);

// รายชื่อหน้าที่อนุญาตให้เปิดผ่าน query string
$allowedPages = array(
    'internship',
    'request_form',
    'pr',
    'about',
    'course',
    'teacher',
    'student',
);

// รายการเมนูใน sidebar และ key ของข้อความที่ใช้แสดง
$sidebarPages = array(
    'internship' => 'menu_intern',
    'pr' => 'menu_pr',
    'course' => 'menu_course',
    'teacher' => 'menu_teacher',
    'student' => 'menu_student',
    'about' => 'menu_about',
);

// ข้อมูลสถานะของคำร้องสำหรับแสดงผลและใช้ใน dropdown
$statusMap = array(
    1 => array('th' => '1: รับเรื่องเข้าระบบ', 'option' => '1: รับเรื่องเข้าระบบ', 'class' => 'status-1'),
    2 => array('th' => '2: อาจารย์อนุมัติ', 'option' => '2: อ.ที่ปรึกษาอนุมัติ', 'class' => 'status-2'),
    3 => array('th' => '3: ออกใบส่งตัวแล้ว', 'option' => '3: ออกใบส่งตัวแล้ว', 'class' => 'status-3'),
    4 => array('th' => '4: ฝึกงานเสร็จสิ้น', 'option' => '4: ฝึกงานเสร็จสิ้น', 'class' => 'status-4'),
    9 => array('th' => '5: ยกเลิกเอกสาร', 'option' => '5: ยกเลิก', 'class' => 'status-5'),
);

// ข้อมูลติดต่อที่แสดงท้ายหน้าต่าง ๆ
$contactInfo = array(
    'title' => 'ติดต่อเรา',
    'lines' => array(
        'หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา',
        'คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ',
        '114 ซอยสุขุมวิท 23 แขวงคลองเตยเหนือ เขตวัฒนา กรุงเทพฯ 10110',
    ),
    'meta' => 'Email: is@g.swu.ac.th | โทร: 02 649-5000',
);
