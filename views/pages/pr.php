<?php $prPosts = siteContent('pr_posts', array()); ?>

<div class="card info-card">
    <h4>ข่าวสารและประชาสัมพันธ์ สาขาสารสนเทศศึกษา</h4>
    <p class="text-muted">ติดตามกิจกรรม ประกาศการฝึกงาน และความเคลื่อนไหวของสาขาได้ตามช่องทางด้านล่างนี้</p>
    <ul class="clean-list">
        <li>Instagram: <a href="https://www.instagram.com/is.hmswu/" target="_blank">@is.hmswu</a></li>
        <li>Website: <a href="https://is.hu.swu.ac.th/" target="_blank">is.hu.swu.ac.th</a></li>
    </ul>
</div>

<div class="card" style="padding: 0; overflow: hidden;">
    <div class="pr-feed">
        <?php foreach ($prPosts as $post): ?>
            <div class="pr-post">
                <div class="pr-post-thumb">
                    <?php if (!empty($post['image_url'])): ?>
                        <img src="<?php echo e($post['image_url']); ?>" alt="<?php echo e($post['image_alt']); ?>">
                    <?php else: ?>
                        <div class="pr-post-thumb-placeholder"><?php echo e($post['placeholder']); ?></div>
                    <?php endif; ?>
                </div>

                <div class="pr-post-body">
                    <div class="pr-post-meta">
                        <div class="pr-post-avatar">IS</div>
                        <span class="pr-post-account">is.hmswu</span>
                        <span class="pr-post-date"><?php echo e($post['date']); ?></span>
                    </div>

                    <div class="pr-tags"><?php echo e($post['tags']); ?></div>
                    <div class="pr-post-title"><?php echo e($post['title']); ?></div>
                    <div class="pr-text"><?php echo e($post['text']); ?></div>
                    <a href="<?php echo e($post['link']); ?>" target="_blank" class="pr-link">อ่านเพิ่มเติม →</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php render('partials/contact-footer'); ?>

