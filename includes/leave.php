<?php
require_once __DIR__.'/layout.php';
const LS=['pending'=>'قيد المراجعة','approved'=>'تمت الموافقة','rejected'=>'مرفوض','cancelled'=>'ملغي'];
function working_days(string $a,string $b):float{global $pdo;
 $wk=array_map('intval',array_filter(explode(',',(string)setting('weekend_days')),'strlen'));
 $hol=holiday_dates($a,$b);
 $n=0;for($t=strtotime($a);$t<=strtotime($b);$t+=86400){if(!in_array((int)date('w',$t),$wk,true)&&!in_array(date('Y-m-d',$t),$hol,true))$n++;}return $n;}
function notify(int $uid,string $msg){global $pdo;$pdo->prepare('INSERT INTO notifications(user_id,message) VALUES(?,?)')->execute([$uid,mb_substr($msg,0,255)]);}
function reviewer_user_ids(int $empId):array{global $pdo;
 $m=$pdo->prepare('SELECT u.id FROM employees e JOIN users u ON u.employee_id=e.manager_id AND u.is_active=1 WHERE e.id=?');$m->execute([$empId]);$r=$m->fetchAll(PDO::FETCH_COLUMN);
 return $r?:$pdo->query("SELECT id FROM users WHERE role='admin' AND is_active=1")->fetchAll(PDO::FETCH_COLUMN);}
function ensure_balance(int $emp,int $type,int $year){global $pdo;
 $pdo->prepare('INSERT IGNORE INTO leave_balances(employee_id,leave_type_id,year,total_days) SELECT ?,id,?,default_days FROM leave_types WHERE id=?')->execute([$emp,$year,$type]);}
function can_review(array $req):bool{ // $req يحتوي employee_id و manager_id
 if((int)$req['employee_id']===(int)$_SESSION['eid'])return false;
 if($_SESSION['role']==='admin')return true;
 return $_SESSION['role']==='manager'&&(int)$req['manager_id']===(int)$_SESSION['eid'];}
function leave_attachment(array $f):?string{
 if(($f['error']??4)===4)return null;
 if($f['error']!==0||$f['size']>3*1024*1024)throw new Exception('حجم المرفق يجب ألا يتجاوز 3 ميغابايت.');
 $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
 $ext=['application/pdf'=>'pdf','image/jpeg'=>'jpg','image/png'=>'png'][$mime]??null;
 $orig=strtolower(pathinfo($f['name'],PATHINFO_EXTENSION));
 if(!$ext||!in_array($orig,['pdf','jpg','jpeg','png'],true))throw new Exception('المرفق يجب أن يكون PDF أو JPG أو PNG.');
 $n=bin2hex(random_bytes(16)).'.'.$ext;
 if(!move_uploaded_file($f['tmp_name'],__DIR__.'/../uploads/leaves/'.$n))throw new Exception('تعذر حفظ المرفق.');return $n;}

function holiday_dates(string $a,string $b):array{global $pdo;$h=$pdo->prepare('SELECT holiday_date,COALESCE(end_date,holiday_date) e FROM holidays WHERE is_active=1 AND holiday_date<=? AND COALESCE(end_date,holiday_date)>=?');$h->execute([$b,$a]);$o=[];
foreach($h as $r)for($t=strtotime($r['holiday_date']);$t<=strtotime($r['e']);$t+=86400)$o[]=date('Y-m-d',$t);return $o;}
