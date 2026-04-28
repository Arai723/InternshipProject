<?php
$teamMembers = siteContent('team_members', array());
$aboutIntro = siteContent('about_intro', '');
?>

<div class="card">
    <h4>เกี่ยวกับเรา · About us</h4>
    <div class="about-intro"><?php echo $aboutIntro; ?></div>

    <div class="team-grid">
        <?php foreach ($teamMembers as $member): ?>
            <div class="team-card">
                <img
                    class="team-card-img"
                    src="<?php echo e($member['image']); ?>"
                    alt="<?php echo e($member['fallback_name']); ?>"
                    onerror="this.src='<?php echo e(avatarFallbackUrl($member['fallback_name'], $member['fallback_bg'], 300)); ?>'"
                >

                <div class="team-card-body">
                    <div class="team-card-nickname"><?php echo e($member['nickname']); ?></div>
                    <div class="team-card-name"><?php echo e($member['name']); ?></div>
                    <div class="team-card-id"><?php echo e($member['student_id']); ?></div>

                    <a href="<?php echo e($member['instagram_url']); ?>" target="_blank" class="team-card-ig">
                        <?php echo instagramIconSvg(); ?>
                        <?php echo e($member['instagram_handle']); ?>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php render('partials/contact-footer'); ?>

