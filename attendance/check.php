<?php require_once __DIR__.'/../includes/attendance.php';
header('Cache-Control: no-store');
function ipf(){return sys_get_temp_dir().'/ea_chk_'.sha1($_SERVER['REMOTE_ADDR']??'');}
if(isset($_GET['t'])||isset($_GET['token'])){ // يُنفَّذ ثم يُعاد التوجيه حتى لا تتكرر العملية عند التحديث
 $f=ipf();$d=@json_decode(@file_get_contents($f),true);$bad=($d&&time()-$d['t']<600)?$d['n']:0;
 $fail=function($m)use($f,$bad){@file_put_contents($f,json_encode(['n'=>$bad+1,'t'=>time()]));$_SESSION['chk']=['ok'=>false,'kind'=>'invalid','level'=>'error','msg'=>$m];header('Location: check.php');exit;};
 if($bad>=10)$fail('محاولات غير صالحة كثيرة. حاول لاحقًا.');
 $emp=null;
 if(isset($_GET['t']))$emp=resolve_signed((string)$_GET['t']);
 elseif(setting('allow_static_qr')==='1'&&preg_match('/^[a-f0-9]{64}$/',(string)$_GET['token'])){
  $s=$pdo->prepare("SELECT e.* FROM employee_qr_tokens q JOIN employees e ON e.id=q.employee_id JOIN users u ON u.employee_id=e.id AND u.is_active=1 WHERE q.token=? AND q.is_active=1 AND e.status<>'inactive'");$s->execute([$_GET['token']]);$emp=$s->fetch()?:null;}
 if(!$emp)$fail('انتهت صلاحية الرمز أو استُخدم من قبل. اطلب من الشاشة رمزًا جديدًا (يتجدد كل 45 ثانية).');
 $_SESSION['chk']=record_attendance($emp);header('Location: check.php');exit;}
$r=$_SESSION['chk']??null;$k=$r['kind']??'none';$lv=$r['level']??'error';
$t=fn($m)=>floor($m/60).' ساعات و'.($m%60).' دقيقة';
?><!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>نتيجة الحضور</title><link rel="icon" href="<?=BASE?>/assets/favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="../assets/css/app.css?v=<?=@filemtime(__DIR__.'/../assets/css/app.css')?>"></head><body><?=icon_sprite()?><div class="center-page">
<main class="res <?=e($lv)?>"><div class="ic"><?=ic($lv==='success'?'check':($lv==='warn'?'alert':'x'))?></div>
<?php if($r&&!empty($r['name'])):?><img class="ph-img" src="<?=$r['photo']?BASE.'/uploads/photos/'.e($r['photo']):BASE.'/assets/css/avatar.svg'?>" alt=""><h1><?=e($r['name'])?></h1><small>الرقم الوظيفي: <?=e($r['code'])?></small><?php endif;?>
<?php if($k==='in'):?><h1>تم تسجيل الحضور بنجاح</h1><?php elseif($k==='out'):?><h1>تم تسجيل الانصراف بنجاح</h1><?php else:?><h1><?=e($r['msg']??'افتح رمز الحضور من شاشة الكمبيوتر وامسحه بكاميرا هاتفك.')?></h1><?php endif;?>
<?php if($k==='in'||$k==='out'):?><div class="tm"><?=e(ar_time($r['ts']))?></div><small><?=e(ar_date($r['ts']))?></small><br>
<span class="pill"><?=$k==='in'?(($r['late']>0)?'متأخر '.(int)$r['late'].' دقيقة':'حاضر'):'مدة العمل: '.$t((int)$r['work'])?></span>
<?php if($k==='out'&&($r['early']>0||$r['ot']>0))echo '<br><small>'.($r['early']>0?'خروج مبكر '.(int)$r['early'].' د':'عمل إضافي '.(int)$r['ot'].' د').'</small>';?>
<?php elseif($k==='blocked'):?><p><?=e($r['msg'])?></p><?php endif;?><div class="brandline"><?=e(setting('company_name'))?></div></main></div></body></html>
