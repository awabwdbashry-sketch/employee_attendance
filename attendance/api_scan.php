<?php
require_once __DIR__.'/../includes/functions.php';
header('Content-Type: application/json; charset=utf-8');
require_login();csrf_check($_POST['csrf']??'');
function out($a){exit(json_encode($a,JSON_UNESCAPED_UNICODE));}
$token=trim($_POST['token']??'');
if(!preg_match('/^[a-f0-9]{64}$/i',$token))out(['ok'=>false,'level'=>'error','msg'=>'❌ الـQR غير صالح']);
$s=$pdo->prepare('SELECT e.* FROM employee_qr_tokens q JOIN employees e ON e.id=q.employee_id WHERE q.token=? AND q.is_active=1');
$s->execute([$token]);$emp=$s->fetch();
if(!$emp||$emp['status']==='inactive')out(['ok'=>false,'level'=>'error','msg'=>'❌ الـQR غير صالح أو الموظف غير نشط']);
if($_SESSION['role']==='employee'&&(int)$emp['id']!==(int)$_SESSION['eid'])out(['ok'=>false,'level'=>'error','msg'=>'❌ لا يمكنك تسجيل حضور موظف آخر']);
if($_SESSION['role']==='manager'&&(int)$emp['id']!==(int)$_SESSION['eid']&&(int)$emp['manager_id']!==(int)$_SESSION['eid'])out(['ok'=>false,'level'=>'error','msg'=>'❌ هذا الموظف ليس ضمن فريقك']);
require_once __DIR__.'/../includes/attendance.php';
out(record_attendance($emp));
