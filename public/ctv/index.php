<?php
require_once(__DIR__ . "/../../config/config.php");
require_once(__DIR__ . "/../../config/function.php");
require_once(__DIR__ . "/../../includes/login-ctv.php");

header('Location: ' . BASE_URL('public/ctv/Products.php'));
exit();
