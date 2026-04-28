<?php
// ข้อความแนะนำทีมผู้พัฒนาที่แสดงช่วงต้นหน้า
$aboutIntro = 'พวกเราทีมพัฒนาระบบ IS Internship จากนิสิตชั้นปีที่ 2 คณะมนุษยศาสตร์ สาขาวิชาสารสนเทศศึกษา มหาวิทยาลัยศรีนครินทรวิโรฒ<br>เพื่อรองรับการจัดการข้อมูลและติดตามสถานะการฝึกงานของนิสิตในสาขาเรา พวกเราหวังอย่างยิ่งว่า ระบบที่เราทำจะมีส่วนช่วยในการพัฒนาองกรณ์ไปในทางที่ดีขึ้น';

// ข้อมูลสมาชิกทีมสำหรับสร้างการ์ดแนะนำแต่ละคนในหน้าเกี่ยวกับเรา
$teamMembers = array(
    array(
        'image' => 'assets/images/chorfon.png',
        'fallback_name' => 'ช่อผกา',
        'fallback_bg' => 'dc2626',
        'nickname' => 'ช่อฝน · Chorfon',
        'name' => 'ช่อผกา มาลัยจรูญ',
        'student_id' => '67101010660',
        'instagram_url' => 'https://www.instagram.com/rainonfl0wers',
        'instagram_handle' => 'rainonfl0wers',
    ),
    array(
        'image' => 'assets/images/nine.png',
        'fallback_name' => 'ณฤเมธ',
        'fallback_bg' => '1e293b',
        'nickname' => 'นาย · Nine',
        'name' => 'ณฤเมธ นฤพัคโภคิน',
        'student_id' => '67101010662',
        'instagram_url' => 'https://www.instagram.com/nine_nalumate',
        'instagram_handle' => 'nine_nalumate',
    ),
    array(
        'image' => 'assets/images/ohm.png',
        'fallback_name' => 'พรธนาศักดิ์',
        'fallback_bg' => '003366',
        'nickname' => 'โอม · Ohm',
        'name' => 'พรธนาศักดิ์ คำฟัก',
        'student_id' => '67101010677',
        'instagram_url' => 'https://www.instagram.com/_ohmmygods',
        'instagram_handle' => '_ohmmygods',
    ),
    array(
        'image' => 'assets/images/mitr.png',
        'fallback_name' => 'รพีภัทร',
        'fallback_bg' => '0f766e',
        'nickname' => 'มิตร · Mitr',
        'name' => 'รพีภัทร เรืองทอง',
        'student_id' => '67101010687',
        'instagram_url' => 'https://www.instagram.com/normalmitr',
        'instagram_handle' => 'normalmitr',
    ),
    array(
        'image' => 'assets/images/sundae.png',
        'fallback_name' => 'อนุสรณ์',
        'fallback_bg' => 'a16207',
        'nickname' => 'ซันเด · Sundae',
        'name' => 'อนุสรณ์ เชียงหนุ้น',
        'student_id' => '67101010692',
        'instagram_url' => 'https://www.instagram.com/ddej.javu',
        'instagram_handle' => 'ddej.javu',
    ),
    array(
        'image' => 'assets/images/moowan.png',
        'fallback_name' => 'อิสรีย์',
        'fallback_bg' => 'b91c1c',
        'nickname' => 'หมูหวาน · Moowan',
        'name' => 'อิสรีย์ วัฒนเกษมวงศ์',
        'student_id' => '67101010695',
        'instagram_url' => 'https://www.instagram.com/isrmoowan',
        'instagram_handle' => 'isrmoowan',
    ),
);
?>

<div class="card">
    <h4>เกี่ยวกับเรา · About us</h4>
    <div class="about-intro"><?php echo $aboutIntro; ?></div>

    <div class="team-grid">
        <?php // วนแสดงข้อมูลสมาชิกทีละคนในรูปแบบการ์ด ?>
        <?php foreach ($teamMembers as $member): ?>
            <div class="team-card">
                <img
                    class="team-card-img"
                    src="<?php echo e($member['image']); ?>"
                    alt="<?php echo e($member['fallback_name']); ?>"
                    onerror="this.src='<?php echo e(avatarFallbackUrl($member['fallback_name'], $member['fallback_bg'], 300)); ?>'"
                >

                <div class="team-card-body">
                    <div class="team-card-nickname"><?php echo e($member['nickname']); ?></div>
                    <div class="team-card-name"><?php echo e($member['name']); ?></div>
                    <div class="team-card-id"><?php echo e($member['student_id']); ?></div>

                    <a href="<?php echo e($member['instagram_url']); ?>" target="_blank" class="team-card-ig">
                        <?php // ใช้ไอคอน SVG กลางเพื่อให้หน้าตาเหมือนกันทุกการ์ด ?>
                        <?php echo instagramIconSvg(); ?>
                        <?php echo e($member['instagram_handle']); ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require __DIR__ . '/../includes/contact-footer.php'; ?>
