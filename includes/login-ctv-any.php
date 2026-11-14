<?php
if (empty($_SESSION['username'])) {
    header("location: " . BASE_URL('Auth/Login'));
    exit();
}

$ctvUsername = $AZCODE->escape($_SESSION['username']);
$getUser = $AZCODE->get_row(
    "SELECT * FROM `users` WHERE `username` = '$ctvUsername'"
    . " AND (`ctv` = 1 OR `ctvacc` = 1) AND `banned` = 0"
);
if (!$getUser) {
    header("location: " . BASE_URL('Auth/Logout'));
    exit();
}
