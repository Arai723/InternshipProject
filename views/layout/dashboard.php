<div class="dashboard-page">
    <aside class="sidebar">
        <div class="sidebar-header">
            <h2>IS <span>Internship</span></h2>
            <p class="sub-logo">Information Studies SWU</p>
        </div>

        <ul class="sidebar-menu">
            <?php foreach (sidebarPages() as $page => $labelKey): ?>
                <li>
                    <a href="<?php echo e(homeUrl(array('page' => $page))); ?>" class="<?php echo $currentPage === $page ? 'active' : ''; ?>">
                        <?php echo e(t($labelKey)); ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <li class="logout-link">
                <a href="<?php echo e(homeUrl(array('logout' => 'true'))); ?>">
                    <?php echo e(t('logout')); ?>
                </a>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <h3>
                <?php echo e(t('welcome')); ?>, <?php echo e(currentUserName()); ?>
                <span class="badge role-<?php echo e((string) currentUserRole()); ?>">
                    (<?php echo e(t('role_' . currentUserRole())); ?>)
                </span>
            </h3>
        </header>

        <div class="content-wrapper">
            <?php render($pageView, $pageData); ?>
        </div>
    </main>
</div>

