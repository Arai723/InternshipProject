<?php
// ข้อมูลคณาจารย์ที่ใช้แสดงในหน้าหลักสูตร
$teacherSections = array(
    array(
        'title' => 'คณะกรรมการหลักสูตร',
        'members' => array(
            array(
                'image' => 'https://is.hu.swu.ac.th/wp-content/uploads/2021/01/Dit-scaled.jpg',
                'alt' => 'อ.ดร.ดิษฐ์',
                'name' => 'อาจารย์ ดร. ดิษฐ์ สุทธิวงศ์',
                'note' => 'ประธานกรรมการบริหารหลักสูตร',
                'english_name' => 'Lecturer Dit Suthiwong, Ph.D.',
                'email' => 'dit@g.swu.ac.th',
            ),
            array(
                'image' => 'https://is.hu.swu.ac.th/wp-content/uploads/2021/01/thiti-scaled.jpg',
                'alt' => 'อ.ดร.ฐิติ',
                'name' => 'อาจารย์ ดร. ฐิติ อติชาติชยากร',
                'note' => 'เลขานุการหลักสูตร',
                'english_name' => 'Lecturer Thiti Atichartchayakorn, Ph.D.',
                'email' => 'thitik@g.swu.ac.th',
            ),
            array(
                'image' => 'https://is.hu.swu.ac.th/wp-content/uploads/2020/02/Vipakorn-683x1024.jpg',
                'alt' => 'ผศ.ดร.วิภากร',
                'name' => 'ผู้ช่วยศาสตราจารย์ ดร. วิภากร วัฒนสินธุ์',
                'note' => '',
                'english_name' => 'Assistant Professor Vipakorn Vadhanasin, Ph.D., PMP, FHEA',
                'email' => 'vipakorn@g.swu.ac.th',
            ),
            array(
                'image' => 'https://is.hu.swu.ac.th/wp-content/uploads/2025/02/Chokthamrong.jpg',
                'alt' => 'อ.ดร.โชคธำรงค์',
                'name' => 'อาจารย์ ดร. โชคธำรงค์ จงจอหอ',
                'note' => '',
                'english_name' => 'Lecturer Chokthamrong Chongchorhor, Ph.D.',
                'email' => 'chokthamrong@g.swu.ac.th',
            ),
            array(
                'image' => 'https://is.hu.swu.ac.th/wp-content/uploads/2025/11/Chotima.jpg',
                'alt' => 'อ.โชติมา',
                'name' => 'อาจารย์โชติมา วัฒนะ',
                'note' => '',
                'english_name' => 'Lecturer Chotima Watana',
                'email' => 'chotimaw@g.swu.ac.th',
            ),
        ),
    ),
    array(
        'title' => 'ผู้ช่วยศาสตราจารย์',
        'members' => array(
            array(
                'image' => 'https://is.hu.swu.ac.th/wp-content/uploads/2020/02/Dussadee-683x1024.jpg',
                'alt' => 'ผศ.ดร.ดุษฎี',
                'name' => 'ผู้ช่วยศาสตราจารย์ ดร. ดุษฎี สีวังคำ',
                'note' => '',
                'english_name' => 'Assistant Professor Dussadee Seewungkum, Ph.D.',
                'email' => 'dussadee@g.swu.ac.th',
            ),
            array(
                'image' => 'https://is.hu.swu.ac.th/wp-content/uploads/2020/02/Sasipimol-683x1024.jpg',
                'alt' => 'ผศ.ดร.ศศิพิมล',
                'name' => 'ผู้ช่วยศาสตราจารย์ ดร. ศศิพิมล ประพินพงศกร',
                'note' => '',
                'english_name' => 'Assistant Professor Sasipimol Prapinpongsakorn, Ph.D., FHEA',
                'email' => 'sasipimol@g.swu.ac.th',
            ),
            array(
                'image' => 'https://is.hu.swu.ac.th/wp-content/uploads/2020/02/Sumattra-683x1024.jpg',
                'alt' => 'อ.ดร.ศุมรรษตรา',
                'name' => 'อาจารย์ ดร. ศุมรรษตรา แสนวา',
                'note' => '',
                'english_name' => 'Lecturer Sumattra Saenwa, Ph.D., FHEA',
                'email' => 'sumattra@g.swu.ac.th',
            ),
        ),
    ),
);
?>

<div class="card">
    <h4>คณาจารย์ประจำหลักสูตร</h4>

    <?php // วนตามหมวดของคณาจารย์ เช่น กรรมการหลักสูตร หรือผู้ช่วยศาสตราจารย์ ?>
    <?php foreach ($teacherSections as $section): ?>
        <h5 class="faculty-section-title"><?php echo e($section['title']); ?></h5>

        <?php // วนแสดงสมาชิกภายในแต่ละหมวด ?>
        <?php foreach ($section['members'] as $member): ?>
            <div class="faculty-item">
                <img src="<?php echo e($member['image']); ?>" alt="<?php echo e($member['alt']); ?>">

                <div class="faculty-info">
                    <div class="fname">
                        <?php echo e($member['name']); ?>
                        <?php if (!empty($member['note'])): ?>
                            <small>(<?php echo e($member['note']); ?>)</small>
                        <?php endif; ?>
                    </div>
                    <div class="feng"><?php echo e($member['english_name']); ?></div>
                    <div class="femail">Email: <?php echo e($member['email']); ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endforeach; ?>

    <?php require __DIR__ . '/../includes/contact-footer.php'; ?>
</div>
