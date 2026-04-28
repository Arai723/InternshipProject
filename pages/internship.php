<?php // แจ้งเตือนเมื่อเจ้าหน้าที่อัปเดตสถานะสำเร็จ ?>
<?php if (!empty($showStaffUpdated)): ?>
    <div class="alert-success">✔ อัปเดตสถานะเรียบร้อยแล้ว</div>
<?php endif; ?>

<?php // แจ้งเตือนเมื่ออาจารย์บันทึกผลพิจารณาสำเร็จ ?>
<?php if (!empty($showTeacherUpdated)): ?>
    <div class="alert-success">✔ บันทึกผลการพิจารณาเรียบร้อยแล้ว</div>
<?php endif; ?>

<?php // มุมมองของนิสิต: ดูคำร้องของตัวเองและปุ่มไปกรอกคำร้อง ?>
<?php if (currentUserRole() === 'student'): ?>
    <div class="card">
        <div class="card-header-flex">
            <div>
                <h4>สถานะการฝึกงานของคุณ</h4>
                <p class="text-muted">ตรวจสอบความคืบหน้าแบบ Real-time</p>
            </div>
            <a href="<?php echo e(homeUrl(array('page' => 'request_form'))); ?>" class="btn-action primary"><?php echo e(t('btn_submit')); ?></a>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>รหัสคำร้อง</th>
                    <th>บริษัท / สถานประกอบการ</th>
                    <th>วันที่เริ่ม</th>
                    <th>วันที่สิ้นสุด</th>
                    <th>วันที่ยื่น</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php // ถ้ามีคำร้องให้แสดงเป็นตาราง ถ้าไม่มีก็แสดงข้อความว่าง ?>
                <?php if (!empty($studentRequests)): ?>
                    <?php foreach ($studentRequests as $request): ?>
                        <tr>
                            <td><?php echo e($request['req_id']); ?></td>
                            <td><?php echo e($request['com_name'] ?? '-'); ?></td>
                            <td><?php echo e($request['start_date'] ?? '-'); ?></td>
                            <td><?php echo e($request['end_date'] ?? '-'); ?></td>
                            <td><?php echo e($request['req_date']); ?></td>
                            <td><?php echo getStatusBadge($request['status_now']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center empty-state">ยังไม่มีข้อมูลคำร้องในระบบ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php require __DIR__ . '/../includes/contact-footer.php'; ?>
<?php // มุมมองของเจ้าหน้าที่: ดูคำร้องทั้งหมดและเปลี่ยนสถานะได้ ?>
<?php elseif (currentUserRole() === 'staff'): ?>
    <div class="card">
        <h4>จัดการข้อมูลคำร้องทั้งหมด</h4>
        <p class="text-muted">เลือกสถานะใหม่แล้วกด "บันทึก" เพื่ออัปเดตทีละรายการ</p>

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
                <?php // วนแสดงคำร้องทั้งหมดพร้อม dropdown สำหรับเปลี่ยนสถานะ ?>
                <?php if (!empty($requestRows)): ?>
                    <?php foreach ($requestRows as $row): ?>
                        <tr>
                            <td><?php echo e($row['req_id']); ?></td>
                            <td><?php echo e($row['stu_id']); ?></td>
                            <td><?php echo e($row['com_name'] ?? '-'); ?></td>
                            <td><?php echo e($row['req_date']); ?></td>
                            <td style="font-size: 12px;">
                                <?php echo e($row['start_date'] ?? '-'); ?><br>
                                <?php echo e($row['end_date'] ?? '-'); ?>
                            </td>
                            <td><?php echo getStatusBadge($row['status_now']); ?></td>
                            <td>
                                <form method="POST" action="<?php echo e(homeUrl(array('page' => 'internship'))); ?>" class="inline-form">
                                    <input type="hidden" name="req_id" value="<?php echo e($row['req_id']); ?>">
                                    <select name="new_status" class="status-dropdown">
                                        <?php // โหลดตัวเลือกสถานะจากค่ากลางใน config ?>
                                        <?php foreach (getStatusOptions() as $statusCode => $statusData): ?>
                                            <option value="<?php echo e((string) $statusCode); ?>" <?php echo (int) $row['status_now'] === (int) $statusCode ? 'selected' : ''; ?>>
                                                <?php echo e(statusOptionLabel($statusData)); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <button type="submit" name="update_status" class="btn-small save-btn">บันทึก</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center empty-state">ไม่มีข้อมูลในระบบ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php require __DIR__ . '/../includes/contact-footer.php'; ?>
<?php // มุมมองของอาจารย์: ตรวจคำร้องและกดอนุมัติหรือไม่อนุมัติ ?>
<?php elseif (currentUserRole() === 'teacher'): ?>
    <div class="card">
        <h4>รายการคำร้องรอการอนุมัติ / บันทึกผล</h4>

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
                <?php // แสดงคำร้องทั้งหมดเพื่อใช้พิจารณา ?>
                <?php if (!empty($requestRows)): ?>
                    <?php foreach ($requestRows as $row): ?>
                        <tr>
                            <td><?php echo e($row['req_id']); ?></td>
                            <td><?php echo e($row['stu_id']); ?></td>
                            <td><?php echo e($row['com_name'] ?? '-'); ?></td>
                            <td style="font-size: 12px;">
                                <?php echo e($row['start_date'] ?? '-'); ?><br>
                                <?php echo e($row['end_date'] ?? '-'); ?>
                            </td>
                            <td><?php echo e($row['req_date']); ?></td>
                            <td><?php echo getStatusBadge($row['status_now']); ?></td>
                            <td>
                                <?php // อนุมัติได้เฉพาะคำร้องที่ยังอยู่สถานะเริ่มต้น ?>
                                <?php if ((int) $row['status_now'] === 1): ?>
                                    <form method="POST" action="<?php echo e(homeUrl(array('page' => 'internship'))); ?>" class="inline-form">
                                        <input type="hidden" name="req_id" value="<?php echo e($row['req_id']); ?>">
                                        <button type="submit" name="teacher_action" value="approve" class="btn-small success">✔ อนุมัติ</button>
                                    </form>
                                    <form method="POST" action="<?php echo e(homeUrl(array('page' => 'internship'))); ?>" class="inline-form">
                                        <input type="hidden" name="req_id" value="<?php echo e($row['req_id']); ?>">
                                        <button type="submit" name="teacher_action" value="reject" class="btn-small danger">✘ ไม่อนุมัติ</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 12px;">ดำเนินการแล้ว</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center empty-state">ไม่มีข้อมูลในระบบ</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php require __DIR__ . '/../includes/contact-footer.php'; ?>
<?php else: ?>
    <?php // กันกรณี role ไม่ตรงกับที่ระบบรองรับ ?>
    <div class="card">
        <p>ไม่พบสิทธิ์การใช้งานของบัญชีนี้</p>
    </div>
<?php endif; ?>
