<?php
// จำค่าที่ผู้ใช้กรอกไว้เมื่อฟอร์มยังส่งไม่ผ่าน
$formMsg = $formMsg ?? null;
$companyName = trim($_POST['com_name'] ?? '');
$startDate = trim($_POST['start_date'] ?? '');
$endDate = trim($_POST['end_date'] ?? '');
?>

<?php // หน้านี้อนุญาตเฉพาะนิสิตเท่านั้น ?>
<?php if (currentUserRole() !== 'student'): ?>
    <div class="card">
        <p>เฉพาะนิสิตเท่านั้นที่สามารถเข้าถึงหน้านี้ได้</p>
    </div>
    <?php require __DIR__ . '/../includes/contact-footer.php'; ?>
    <?php return; ?>
<?php endif; ?>

<div class="card">
    <div class="card-header-flex">
        <h4>แบบฟอร์มยื่นคำร้องขอฝึกงาน</h4>
        <a href="<?php echo e(homeUrl(array('page' => 'internship'))); ?>" class="btn-small text-only">กลับไปหน้าข้อมูล</a>
    </div>

    <hr class="divider">

    <?php // แสดงผลลัพธ์หลังส่งฟอร์ม เช่น สำเร็จหรือข้อมูลไม่ครบ ?>
    <?php if (!empty($formMsg)): ?>
        <div class="alert-<?php echo e($formMsg['type']); ?>"><?php echo e($formMsg['text']); ?></div>
    <?php endif; ?>

    <?php // ฟอร์มยื่นคำร้องฝึกงานของนิสิต ?>
    <form method="POST" action="<?php echo e(homeUrl(array('page' => 'request_form'))); ?>" class="submit-form">
        <div class="form-row">
            <div class="form-group-half">
                <label>รหัสนิสิต</label>
                <input type="text" value="<?php echo e(currentUserId()); ?>" readonly class="readonly-input">
            </div>
            <div class="form-group-half">
                <label>ชื่อ-นามสกุล</label>
                <input type="text" value="<?php echo e(currentUserName()); ?>" readonly class="readonly-input">
            </div>
        </div>

        <div class="form-group">
            <label>ชื่อบริษัท / สถานประกอบการ</label>
            <input type="text" name="com_name" value="<?php echo e($companyName); ?>" placeholder="เช่น บริษัท ABC จำกัด" required>
        </div>

        <div class="form-row">
            <div class="form-group-half">
                <label>วันที่เริ่มฝึกงาน</label>
                <input type="date" name="start_date" value="<?php echo e($startDate); ?>" required>
            </div>
            <div class="form-group-half">
                <label>วันที่สิ้นสุดการฝึกงาน</label>
                <input type="date" name="end_date" value="<?php echo e($endDate); ?>" required>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" name="submit_request" class="btn-action primary">บันทึกข้อมูล</button>
        </div>
    </form>
</div>

<?php require __DIR__ . '/../includes/contact-footer.php'; ?>
