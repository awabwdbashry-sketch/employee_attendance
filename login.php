<?php require_once __DIR__.'/includes/layout.php';$err='';
function lk(){return sys_get_temp_dir().'/ea_login_'.sha1(($_SERVER['REMOTE_ADDR']??'').'|'.strtolower(trim($_POST['email']??'')));}
function login_blocked($k){$d=@json_decode(@file_get_contents($k),true);return $d&&$d['n']>=5&&time()-$d['t']<600;}
function login_fail($k){$d=@json_decode(@file_get_contents($k),true);$n=($d&&time()-$d['t']<600)?$d['n']+1:1;@file_put_contents($k,json_encode(['n'=>$n,'t'=>time()]));}
function login_clear($k){@unlink($k);}
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??''))$err='انتهت صلاحية الجلسة.';
 elseif(login_blocked($k=lk()))$err='محاولات كثيرة. حاول مجددًا بعد 10 دقائق.';
 else{$s=$pdo->prepare('SELECT * FROM users WHERE email=? AND is_active=1');$s->execute([trim($_POST['email']??'')]);$u=$s->fetch();
  if($u&&password_verify($_POST['password']??'',$u['password_hash'])){session_regenerate_id(true);$_SESSION['uid']=$u['id'];$_SESSION['eid']=$u['employee_id'];$_SESSION['role']=$u['role'];$_SESSION['last']=time();login_clear($k);audit('login',$u['email']);header('Location: index.php');exit;}
  login_fail($k);$err='البريد الإلكتروني أو كلمة المرور غير صحيحة.';}}
?><!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#f8f5ee"><title>تسجيل الدخول | <?=e(setting('company_name'))?></title><link rel="icon" href="assets/favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="assets/css/app.css?v=<?=@filemtime(__DIR__.'/assets/css/app.css')?>"></head>
<body class="auth"><?=icon_sprite()?><main class="auth-wrap">
<section class="auth-brand"><span class="mark" aria-hidden="true"><?=e(mb_substr((string)setting('company_name'),0,1)?:'ح')?></span><h1><?=e(setting('company_name'))?></h1><span class="rule"></span><p>نظام إدارة الحضور والإجازات</p></section>
<section class="auth-card"><h2>تسجيل الدخول</h2><p class="muted">أدخل بيانات حسابك للمتابعة.</p>
<?php if($err):?><div class="auth-err" role="alert"><?=ic('alert')?><span><?=e($err)?></span></div><?php endif;?>
<form method="post" id="lf"><input type="hidden" name="csrf" value="<?=csrf()?>">
<label class="fld"><span>البريد الإلكتروني</span><div><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M4 6h16v12H4zM4 7l8 6 8-6"/></svg><input type="email" name="email" autocomplete="username" value="<?=e($_POST['email']??'')?>" required autofocus></div></label>
<label class="fld"><span>كلمة المرور</span><div><svg viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M6 11h12v9H6zM8.5 11V8a3.5 3.5 0 017 0v3"/></svg><input type="password" name="password" id="pw" autocomplete="current-password" required><button type="button" class="eye" data-pw="pw" aria-label="إظهار كلمة المرور">إظهار</button></div></label>
<button class="btn auth-btn" id="sb">دخول</button></form></section></main>
<script>window.APP={csrf:<?=json_encode(csrf())?>,base:<?=json_encode(BASE)?>};</script><script src="assets/js/app.js?v=<?=@filemtime(__DIR__.'/assets/js/app.js')?>"></script></body></html>
