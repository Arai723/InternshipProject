<?php $teacherSections = siteContent('teacher_sections', array()); ?>

<div class="card">
    <h4>คณาจารย์ประจำหลักสูตร</h4>

    <?php foreach ($teacherSections as $section): ?>
        <h5 class="faculty-section-title"><?php echo e($section['title']); ?></h5>

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

    <?php render('partials/contact-footer'); ?>
</div>

