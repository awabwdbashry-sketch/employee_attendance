<?php require_once __DIR__.'/../includes/layout.php';require_login();header('Content-Type: application/json; charset=utf-8');$u=(int)$_SESSION['uid'];
if($_SERVER['REQUEST_METHOD']==='POST'){csrf_check($_POST['csrf']??'');
 if(($_POST['do']??'')==='all')$pdo->prepare('UPDATE notifications SET is_read=1 WHERE user_id=?')->execute([$u]);else $pdo->prepare('UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?')->execute([(int)$_POST['id'],$u]);}
$s=$pdo->prepare('SELECT id,message,is_read,created_at FROM notifications WHERE user_id=? ORDER BY id DESC LIMIT 8');$s->execute([$u]);
echo json_encode(['unread'=>unread(),'items'=>$s->fetchAll()],JSON_UNESCAPED_UNICODE);
