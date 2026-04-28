<?php
// ข้อมูลหลักสูตรทั้งหมดของหน้า "หลักสูตร"
$course = array(
    'title' => 'หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา',
    'quote' => 'ปรัชญา: "สารสนเทศสร้างปัญญา ปัญญาสร้างคุณค่าให้สังคม"',
    'program_code' => '25520091104002',
    'sections' => array(
        array(
            'title' => '1. ชื่อหลักสูตรและวุฒิการศึกษา',
            'items' => array(
                array('label' => 'ชื่อภาษาไทย', 'text' => 'ศิลปศาสตรบัณฑิต (ศศ.บ.) สาขาวิชาสารสนเทศศึกษา'),
                array('label' => 'ชื่อภาษาอังกฤษ', 'text' => 'Bachelor of Arts (Information Studies)'),
                array('label' => 'อักษรย่อ', 'text' => 'B.A. (Information Studies)'),
            ),
        ),
        array(
            'title' => '2. เจาะลึกวิชาเรียน (กลุ่มวิชาเอก)',
            'items' => array(
                array('text' => 'หลักสูตรแบ่งกลุ่มวิชาเพื่อให้ครอบคลุมการทำงานทั้งรูปแบบดั้งเดิมและเทคโนโลยีสมัยใหม่:', 'tag' => 'p'),
                array('label' => 'การจัดการสารสนเทศ', 'text' => 'เรียนรู้การวิเคราะห์ จัดหมวดหมู่ และจัดเก็บข้อมูลอย่างเป็นระบบ', 'indent' => true),
                array('label' => 'เทคโนโลยีสารสนเทศ', 'text' => 'การเขียนโปรแกรมเบื้องต้น การจัดการฐานข้อมูล และการพัฒนาเว็บไซต์', 'indent' => true),
                array('label' => 'การจัดการนวัตกรรมและคอนเทนต์', 'text' => 'การสร้างสรรค์เนื้อหาดิจิทัล การตลาดสารสนเทศ และการใช้ AI', 'indent' => true),
                array('label' => 'การบริการสารสนเทศ', 'text' => 'จิตวิทยาการบริการ และการถ่ายทอดความรู้', 'indent' => true),
            ),
        ),
        array(
            'title' => '3. จุดเด่นของสาขา',
            'items' => array(
                array('label' => 'ความร่วมมือกับภาคเอกชน', 'text' => 'มีวิทยากรจากวงการ Digital Agency และ Data Analyst'),
                array('label' => 'ทักษะที่หลากหลาย', 'text' => 'ผสมผสานศาสตร์มนุษย์และศาสตร์ข้อมูลเข้าด้วยกัน'),
                array('label' => 'ห้องปฏิบัติการ', 'text' => 'มีเครื่องมือและซอฟต์แวร์ที่ทันสมัยสำหรับการฝึกปฏิบัติจริง'),
            ),
        ),
        array(
            'title' => '4. แนวทางการประกอบอาชีพ',
            'items' => array(
                array('text' => 'นักจัดการสารสนเทศและข้อมูล (Information Manager)'),
                array('text' => 'นักจดหมายเหตุ หรือ บรรณารักษ์ยุคใหม่ (Modern Librarian)'),
                array('text' => 'Content Creator และ Digital Content Manager'),
                array('text' => 'Data Curator และ Knowledge Manager ในองค์กรชั้นนำ'),
            ),
        ),
        array(
            'title' => '5. เกณฑ์การคัดเลือก ปี 2569 (TCAS)',
            'items' => array(
                array('label' => 'ภาคปกติ', 'text' => 'รอบที่ 1 (Portfolio) 10 คน | รอบที่ 3 (Admission) 40 คน'),
                array('label' => 'ภาคพิเศษ', 'text' => 'รอบที่ 3 (Admission) 40 คน'),
                array('text' => '*พิจารณาจาก GPAX และคะแนน TGAT / A-Level ตามเกณฑ์ของมหาวิทยาลัย', 'class' => 'course-note'),
            ),
        ),
    ),
    'document_url' => 'https://drive.google.com/file/d/1YNrk2CEBeDCe-GRXs-C_T1rvzxw6g_L2/view',
    'document_label' => 'รายละเอียดหลักสูตรฉบับปรับปรุง พ.ศ.2565',
);
?>

<div class="card">
    <h4><?php echo e($course['title']); ?></h4>

    <div class="course-quote"><?php echo e($course['quote']); ?></div>

    <div class="course-section">
        <div class="course-item"><strong>รหัสหลักสูตร:</strong> <?php echo e($course['program_code']); ?></div>
    </div>

    <?php // วนแสดงหมวดเนื้อหาของหลักสูตรทีละหัวข้อ ?>
    <?php foreach ($course['sections'] as $section): ?>
        <div class="course-section">
            <h3><?php echo e($section['title']); ?></h3>

            <?php // วนแสดงรายละเอียดในแต่ละหมวดย่อย ?>
            <?php foreach ($section['items'] as $item): ?>
                <?php
                // รวม class เพิ่มเติมตามประเภทข้อมูลที่ต้องการแสดง
                $classes = array('course-item');
                if (!empty($item['indent'])) {
                    $classes[] = 'text-indent';
                }
                if (!empty($item['class'])) {
                    $classes[] = $item['class'];
                }
                ?>

                <?php if (($item['tag'] ?? '') === 'p'): ?>
                    <?php // ข้อความที่ต้องการแสดงเป็นย่อหน้าเต็ม ?>
                    <p class="<?php echo e(implode(' ', $classes)); ?>"><?php echo e($item['text']); ?></p>
                <?php else: ?>
                    <?php // ข้อความทั่วไปที่อาจมี label นำหน้า ?>
                    <div class="<?php echo e(implode(' ', $classes)); ?>">
                        <?php if (!empty($item['label'])): ?>
                            <strong><?php echo e($item['label']); ?>:</strong>
                        <?php endif; ?>
                        <?php echo e($item['text']); ?>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <a href="<?php echo e($course['document_url']); ?>" target="_blank" class="course-link"><?php echo e($course['document_label']); ?></a>
</div>

<?php require __DIR__ . '/../includes/contact-footer.php'; ?>
