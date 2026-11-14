<?php
require_once("../../config/config.php");
require_once("../../config/function.php");
require_once(__DIR__ . "/../../includes/login-admin.php");

header('Location: ' . BASE_URL('Admin/Home'));
exit;
