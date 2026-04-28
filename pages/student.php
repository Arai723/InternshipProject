<?php $studentsByYear = $studentsByYear ?? array(); ?>

<div class="card">
    <h4>ทำเนียบนิสิต (ชั้นปี 1-4)</h4>

    <?php foreach ($studentsByYear as $year => $students): ?>
        <h5 class="faculty-section-title">ชั้นปีที่ <?php echo e((string) $year); ?></h5>

        <?php if (!empty($students)): ?>
            <?php foreach ($students as $student): ?>
                <div class="faculty-item">
                    <img
                        src=""
                        alt=""
                        onerror="this.src='<?php echo e(avatarFallbackUrl($student['stu_name'], '555', 56)); ?>'"
                    >

                    <div class="faculty-info">
                        <div class="fname"><?php echo e($student['stu_name']); ?></div>
                        <div class="feng"><?php echo e($student['stu_id']); ?></div>
                        <div class="femail">Email: <?php echo e($student['stu_email']); ?></div>
                        <div class="femail">เบอร์โทร: <?php echo e($student['stu_tel'] ?? '-'); ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-muted">ไม่มีข้อมูลนิสิตชั้นปีนี้</p>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php require __DIR__ . '/../includes/contact-footer.php'; ?>
</div>
