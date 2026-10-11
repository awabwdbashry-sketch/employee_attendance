<?php require_once __DIR__.'/../includes/leave.php';require_login(['admin']);post_guard();
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{$new=['company_name'=>trim($_POST['company_name']),'work_start'=>$_POST['work_start'],'work_end'=>$_POST['work_end'],'grace_minutes'=>(string)max(0,(int)$_POST['grace_minutes']),'half_day_hours'=>(string)max(0,(float)$_POST['half_day_hours']),'session_timeout'=>(string)max(300,(int)$_POST['session_timeout']),'weekend_days'=>implode(',',array_values(array_filter(array_map('intval',$_POST['weekend']??[]),fn($d)=>$d>=0&&$d<=6))),'allow_past_leave'=>isset($_POST['allow_past_leave'])?'1':'0','refund_on_cancel'=>isset($_POST['refund_on_cancel'])?'1':'0','public_url'=>rtrim(trim($_POST['public_url']??''),'/')];
  foreach(['work_start','work_end'] as $k)if(!preg_match('/^\d{2}:\d{2}$/',$new[$k]))throw new Exception('صيغة الوقت غير صحيحة.');
  if($new['work_start']>=$new['work_end'])throw new Exception('بداية الدوام يجب أن تسبق نهايته.');if($new['public_url']!==''&&!preg_match('#^https?://[^\s]+$#',$new['public_url']))throw new Exception('الرابط العام غير صحيح.');
  if($new['company_name']==='')throw new Exception('اسم الشركة مطلوب.');
  $ch=[];$up=$pdo->prepare('INSERT INTO settings(`key`,`value`) VALUES(?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
  foreach($new as $k=>$v){$o=setting($k);if((string)$o!==$v){$ch[]="$k: $o → $v";$up->execute([$k,$v]);}}
  audit('settings_update',implode(' | ',$ch)?:'بدون تغيير');flash('تم حفظ الإعدادات.');}catch(Exception $x){flash($x->getMessage(),'error');}
 header('Location: index.php');exit;}
$wk=array_map('intval',array_filter(explode(',',(string)setting('weekend_days')),'strlen'));$S=fn($k)=>e(setting($k));
page_start('الإعدادات');ph('الإعدادات','إعدادات الدوام والإجازات والرابط العام للنظام');?>
<form method="post" class="card"><input type="hidden" name="csrf" value="<?=csrf()?>">
<div class="set-sec" data-tab="general"><label>اسم الشركة<input name="company_name" value="<?=$S('company_name')?>" required></label></div>
<div class="set-sec" data-tab="hours"><label>بداية الدوام<input type="time" name="work_start" value="<?=$S('work_start')?>"></label><label>نهاية الدوام<input type="time" name="work_end" value="<?=$S('work_end')?>"></label>
<label>فترة السماح (دقائق)<input type="number" min="0" name="grace_minutes" value="<?=$S('grace_minutes')?>"><span class="hint">التأخير لا يُحتسب خلال هذه الفترة.</span></label><label>ساعات نصف اليوم (أقل من)<input type="number" step="0.5" name="half_day_hours" value="<?=$S('half_day_hours')?>"><span class="hint">أقل من هذا العدد من الساعات يُسجَّل نصف يوم.</span></label></div>
<div class="set-sec" data-tab="days"><div class="full"><b>أيام العطلة الأسبوعية</b><div class="days-pick" style="margin-top:10px"><?php foreach(['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'] as $i=>$d)echo '<label class="check"><input type="checkbox" name="weekend[]" value="'.$i.'" '.(in_array($i,$wk,true)?'checked':'').'> '.$d.'</label>';?></div></div></div>
<div class="set-sec" data-tab="leave"><label class="check full"><input type="checkbox" name="allow_past_leave" <?=setting('allow_past_leave')?'checked':''?>> السماح بطلب إجازة بتاريخ ماضٍ</label><label class="check full"><input type="checkbox" name="refund_on_cancel" <?=setting('refund_on_cancel')?'checked':''?>> إرجاع الرصيد عند إلغاء إجازة معتمدة</label></div>
<div class="set-sec" data-tab="system" data-tab-label="الرابط والأمان"><label>مدة الجلسة (ثوانٍ)<input type="number" min="300" name="session_timeout" value="<?=$S('session_timeout')?>"><span class="hint">تنتهي الجلسة بعد هذه المدة من عدم النشاط (300 كحد أدنى).</span></label>
<label class="full">الرابط العام للنظام (لرمز QR)<input name="public_url" dir="ltr" style="text-align:right" placeholder="http://192.168.1.10/employee_attendance" value="<?=$S('public_url')?>"><span class="hint">يُستخدم لبناء رابط الحضور الذي يفتحه الهاتف عند مسح الرمز.</span></label></div>
<div class="savebar noprint"><span class="hint">تُطبَّق الإعدادات على جميع الأقسام بعد الحفظ.</span><button class="btn"><?=ic('check')?>حفظ الإعدادات</button></div></form><?php page_end();
