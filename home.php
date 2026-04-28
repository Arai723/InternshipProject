<?php

require_once __DIR__ . '/app/bootstrap.php';
require_once __DIR__ . '/app/actions.php';

$pageView = pageViewFor($currentPage);
$pageData = isLoggedIn() ? preparePageData($currentPage, $conn, $formMsg) : array();

render('layout/head');

if (!isLoggedIn()) {
    render('layout/login', array(
        'errorMsg' => $errorMsg,
        'loginForm' => $loginForm,
    ));
} else {
    render('layout/dashboard', array(
        'currentPage' => $currentPage,
        'pageView' => $pageView,
        'pageData' => $pageData,
    ));
}

render('layout/foot');

