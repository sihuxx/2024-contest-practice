<?php
session_start();
date_default_timezone_set('Asia/Seoul');
require_once "../db.php";
require_once "../lib.php";
require_once "../router.php";
require_once "../controller/page.php";
router::handleRequest();