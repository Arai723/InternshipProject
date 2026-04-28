<?php
// จำค่าที่ผู้ใช้กรอกไว้เพื่อไม่ต้องเลือกใหม่เมื่อ login ไม่ผ่าน
$selectedRole = $loginForm['role'] ?? 'student';
$username = $loginForm['username'] ?? '';
?>
<div class="login-page">
    <div class="login-container">
        <div class="login-header">
            <div class="logo-text">IS</div>
            <h1>IS <span>Internship</span></h1>
            <p><?php echo e(t('system_desc')); ?></p>
        </div>

        <?php if (!empty($errorMsg)): ?>
            <?php // แสดงข้อความแจ้งเตือนเมื่อเข้าสู่ระบบไม่สำเร็จ ?>
            <div class="alert-error"><?php echo e($errorMsg); ?></div>
        <?php endif; ?>

        <?php // ฟอร์มเข้าสู่ระบบหลักของระบบ ?>
        <form method="POST" action="<?php echo e(homeUrl()); ?>" class="login-form">
            <div class="role-selector">
                <label><input type="radio" name="role" value="student" <?php echo $selectedRole === 'student' ? 'checked' : ''; ?>> <?php echo e(t('role_student')); ?></label>
                <label><input type="radio" name="role" value="teacher" <?php echo $selectedRole === 'teacher' ? 'checked' : ''; ?>> <?php echo e(t('role_teacher')); ?></label>
                <label><input type="radio" name="role" value="staff" <?php echo $selectedRole === 'staff' ? 'checked' : ''; ?>> <?php echo e(t('role_staff')); ?></label>
            </div>

            <div class="form-group">
                <label>รหัสประจำตัว</label>
                <input type="text" name="username" value="<?php echo e($username); ?>" required>
            </div>

            <div class="form-group">
                <label>รหัสผ่าน</label>
                <input type="password" name="password" required>
            </div>

            <button type="submit" name="login" class="btn-login">เข้าสู่ระบบ</button>
        </form>
    </div>
</div>
