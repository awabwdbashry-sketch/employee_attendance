<?php require_once __DIR__.'/../includes/attendance.php';require_login();header('Content-Type: application/json; charset=utf-8');header('Cache-Control: no-store');
$id=(int)($_GET['id']??$_SESSION['eid']);
if($id!==(int)$_SESSION['eid']&&$_SESSION['role']!=='admin'){http_response_code(403);exit('{"ok":false}');}
$t=$pdo->prepare('SELECT token FROM employee_qr_tokens WHERE employee_id=? AND is_active=1 ORDER BY id DESC LIMIT 1');$t->execute([$id]);$tk=$t->fetchColumn();
if(!$tk){http_response_code(404);exit('{"ok":false}');}
echo json_encode(['ok'=>true,'url'=>make_qr_url($tk),'ttl'=>QR_WIN]);
