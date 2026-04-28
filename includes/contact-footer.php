<?php
// ดึงข้อมูลติดต่อกลางมาแสดงซ้ำท้ายแต่ละหน้า
$contact = getContactInfo();
?>
<div class="faculty-footer">
    <h3><?php echo e($contact['title']); ?></h3>
    <p>
        <?php foreach ($contact['lines'] as $line): ?>
            <?php echo e($line); ?><br>
        <?php endforeach; ?>
        <br>
        <?php echo e($contact['meta']); ?>
    </p>
</div>
