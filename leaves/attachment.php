<?php require_once __DIR__.'/../includes/leave.php';require_login();
$s=$pdo->prepare('SELECT r.attachment,r.employee_id,e.manager_id FROM leave_requests r JOIN employees e ON e.id=r.employee_id WHERE r.id=?');$s->execute([(int)($_GET['id']??0)]);$r=$s->fetch();
$mine=$r&&(int)$r['employee_id']===(int)$_SESSION['eid'];
if(!$r||!$r['attachment']||!($mine||$_SESSION['role']==='admin'||($_SESSION['role']==='manager'&&(int)$r['manager_id']===(int)$_SESSION['eid']))){http_response_code(403);exit('غير مصرح.');}
$f=__DIR__.'/../uploads/leaves/'.basename($r['attachment']);$m=(new finfo(FILEINFO_MIME_TYPE))->file($f);
header('Content-Type: '.$m);header('X-Content-Type-Options: nosniff');readfile($f);
