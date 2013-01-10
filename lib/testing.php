<?php
require_once('db.php');
require_once('../user.php');
$u = new User();
$u-> username = 'Timothy';
$u-> password = sha1('secret');
$u-> emailAddr = 'timothy@example.com';
$u-> save();
print_r($u);
?>