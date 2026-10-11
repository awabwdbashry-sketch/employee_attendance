<?php require_once __DIR__.'/includes/functions.php';
if($_SERVER['REQUEST_METHOD']==='POST'&&hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){if(!empty($_SESSION['uid']))audit('logout');session_destroy();header('Location: login.php');exit;}
header('Location: '.(empty($_SESSION['uid'])?'login.php':'index.php'));
