<?php
require_once __DIR__.'/../config/db.php';
session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax']);
session_start();
header('X-Frame-Options: SAMEORIGIN');header('X-Content-Type-Options: nosniff');header('Referrer-Policy: same-origin');
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function setting($k){global $pdo;$s=$pdo->prepare('SELECT `value` FROM settings WHERE `key`=?');$s->execute([$k]);return $s->fetchColumn();}
function csrf(){return $_SESSION['csrf']??($_SESSION['csrf']=bin2hex(random_bytes(32)));}
function csrf_check($t){if(!hash_equals($_SESSION['csrf']??'',(string)$t)){http_response_code(419);exit(json_encode(['ok'=>false,'level'=>'error','msg'=>'انتهت صلاحية الجلسة، أعد تحميل الصفحة.']));}}
function require_login(array $roles=[]){
 if(empty($_SESSION['uid'])||time()-($_SESSION['last']??0)>(int)setting('session_timeout')){session_destroy();header('Location: /employee_attendance/login.php');exit;}
 $_SESSION['last']=time();
 $c=$GLOBALS['pdo']->prepare("SELECT u.is_active,e.status,u.role FROM users u JOIN employees e ON e.id=u.employee_id WHERE u.id=?");$c->execute([$_SESSION['uid']]);$c=$c->fetch();
 if(!$c||!$c['is_active']||$c['status']==='inactive'){session_destroy();header('Location: /employee_attendance/login.php');exit;}
 $_SESSION['role']=$c['role'];
 if($roles&&!in_array($_SESSION['role'],$roles,true)){http_response_code(403);exit('<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>غير مصرح بالوصول</title><link rel="icon" href="/employee_attendance/assets/favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="/employee_attendance/assets/css/app.css?v='.@filemtime(__DIR__.'/../assets/css/app.css').'"></head><body><div class="center-page"><main class="errp"><div class="code">403</div><h1>غير مصرح لك بالوصول</h1><p>ليست لديك صلاحية لعرض هذه الصفحة. إن كنت تعتقد أن هذا خطأ، تواصل مع مسؤول النظام.</p><a class="btn" href="/employee_attendance/index.php">العودة إلى الرئيسية</a></main></div></body></html>');}
}
function audit($action,$details=''){global $pdo;$pdo->prepare('INSERT INTO audit_logs(user_id,action,details,ip) VALUES(?,?,?,?)')->execute([$_SESSION['uid']??null,$action,$details,$_SERVER['REMOTE_ADDR']??null]);}
function calc_status(string $in,?string $out=null):array{
 $start=strtotime(setting('work_start'));$end=strtotime(setting('work_end'));$grace=(int)setting('grace_minutes')*60;
 $i=strtotime($in);$status=($i-$start)>$grace?'late':'present';$lateMin=$status==='late'?(int)(($i-$start)/60):0;
 $work=0;$early=0;$ot=0;
 if($out){$o=strtotime($out);$work=max(0,(int)(($o-$i)/60));$early=max(0,(int)(($end-$o)/60));$ot=max(0,(int)(($o-$end)/60));
  if($work<(float)setting('half_day_hours')*60)$status='half_day';}
 return compact('status','lateMin','work','early','ot');
}
