<?php

return array(
    'db' => array(
        'host' => '127.0.0.1',
        'user' => 'root',
        'pass' => 'root1234',
        'name' => 'is_internships',
        'port' => 3306,
    ),
    'lang' => 'th',
    'texts' => array(
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
    ),
    'allowed_pages' => array(
        'internship',
        'request_form',
        'pr',
        'about',
        'course',
        'teacher',
        'student',
    ),
    'sidebar_pages' => array(
        'internship' => 'menu_intern',
        'pr' => 'menu_pr',
        'course' => 'menu_course',
        'teacher' => 'menu_teacher',
        'student' => 'menu_student',
        'about' => 'menu_about',
    ),
    'status_map' => array(
        1 => array(
            'th' => '1: รับเรื่องเข้าระบบ',
            'option' => '1: รับเรื่องเข้าระบบ',
            'class' => 'status-1',
        ),
        2 => array(
            'th' => '2: อาจารย์อนุมัติ',
            'option' => '2: อ.ที่ปรึกษาอนุมัติ',
            'class' => 'status-2',
        ),
        3 => array(
            'th' => '3: ออกใบส่งตัวแล้ว',
            'option' => '3: ออกใบส่งตัวแล้ว',
            'class' => 'status-3',
        ),
        4 => array(
            'th' => '4: ฝึกงานเสร็จสิ้น',
            'option' => '4: ฝึกงานเสร็จสิ้น',
            'class' => 'status-4',
        ),
        9 => array(
            'th' => '9: ยกเลิกเอกสาร',
            'option' => '9: ยกเลิก',
            'class' => 'status-9',
        ),
    ),
    'contact' => array(
        'title' => 'ติดต่อเรา',
        'lines' => array(
            'หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา',
            'คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ',
            '114 ซอยสุขุมวิท 23 แขวงคลองเตยเหนือ เขตวัฒนา กรุงเทพฯ 10110',
        ),
        'meta' => 'Email: is@g.swu.ac.th | โทร: 02 649-5000',
    ),
);

