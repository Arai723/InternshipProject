<?php
// ข้อมูลข่าวและประชาสัมพันธ์ที่ต้องการโชว์ในหน้า PR
$prPosts = array(
    array(
        'image_url' => '',
        'image_alt' => '',
        'placeholder' => 'ไม่มีรูปภาพ',
        'date' => '6 กุมภาพันธ์ 2569',
        'tags' => '#IS111 #สารสนเทศศึกษา #SWU',
        'title' => 'IS111 การรู้สารสนเทศและรู้เท่าทันสื่อ',
        'text' => 'กิจกรรมโต้วาที ภาคเรียนที่ 1/2568 หลักสูตรศิลปศาสตรบัณฑิต สาขาวิชาสารสนเทศศึกษา คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ',
        'link' => 'https://is.hu.swu.ac.th/is111/',
    ),
    array(
        'image_url' => 'https://is.hu.swu.ac.th/wp-content/uploads/2025/02/GDCatalog2-1140x458.jpg',
        'image_alt' => 'Government Data Catalog Day 2024',
        'placeholder' => '',
        'date' => '21 กุมภาพันธ์ 2568',
        'tags' => '#GDCatalog #OpenData #SWU #สารสนเทศศึกษา',
        'title' => 'Government Data Catalog Day 2024: Insights and Impact',
        'text' => 'คณาจารย์หลักสูตรสารสนเทศศึกษา เข้าร่วมงานประชุมสัมมนา Government Data Catalog Day 2024 และได้รับรางวัลหน่วยงานด้านการศึกษาที่ใช้ข้อมูลสาธารณะจากภาครัฐดีเด่น',
        'link' => 'https://is.hu.swu.ac.th/gdcatalog/',
    ),
    array(
        'image_url' => 'https://hu.swu.ac.th/Portals/5/EasyDNNNews/12571/600600p2228EDNmainimg-1611.jpg',
        'image_alt' => 'โครงการพัฒนาระบบสารสนเทศ',
        'placeholder' => '',
        'date' => '16 มีนาคม 2569',
        'tags' => '#HumanitiesSWU #DigitalTransformation #Automation #CyberSecurity',
        'title' => 'โครงการ "พัฒนาระบบสารสนเทศเพื่อการบริหารจัดการองค์กร"',
        'text' => 'คณะมนุษยศาสตร์ มหาวิทยาลัยศรีนครินทรวิโรฒ อบรมเชิงปฏิบัติการด้าน Automation, Workflow และ Cyber Security เพื่อเสริมทักษะการใช้เทคโนโลยีในการบริหารจัดการงาน',
        'link' => 'https://hu.swu.ac.th/news/โครงการ-พัฒนาระบบสารสนเทศเพื่อการบริหารจัดการองค์กร',
    ),
);
?>

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
        <?php // วนแสดงโพสต์ข่าวทีละรายการ ?>
        <?php foreach ($prPosts as $post): ?>
            <div class="pr-post">
                <div class="pr-post-thumb">
                    <?php // ถ้ามีรูปก็แสดงรูป ถ้าไม่มีก็ใช้ข้อความแทน ?>
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

<?php require __DIR__ . '/../includes/contact-footer.php'; ?>
