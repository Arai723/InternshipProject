<?php $course = siteContent('course', array()); ?>

<div class="card">
    <h4><?php echo e($course['title']); ?></h4>

    <div class="course-quote"><?php echo e($course['quote']); ?></div>

    <div class="course-section">
        <div class="course-item"><strong>รหัสหลักสูตร:</strong> <?php echo e($course['program_code']); ?></div>
    </div>

    <?php foreach ($course['sections'] as $section): ?>
        <div class="course-section">
            <h3><?php echo e($section['title']); ?></h3>

            <?php foreach ($section['items'] as $item): ?>
                <?php
                $classes = array('course-item');
                if (!empty($item['indent'])) {
                    $classes[] = 'text-indent';
                }
                if (!empty($item['class'])) {
                    $classes[] = $item['class'];
                }
                ?>

                <?php if (($item['tag'] ?? '') === 'p'): ?>
                    <p class="<?php echo e(implode(' ', $classes)); ?>"><?php echo e($item['text']); ?></p>
                <?php else: ?>
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

<?php render('partials/contact-footer'); ?>

