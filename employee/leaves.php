<?php require_once __DIR__.'/../includes/leave.php';require_login();post_guard();
$eid=(int)$_SESSION['eid'];$uid=(int)$_SESSION['uid'];$year=(int)date('Y');
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  if(($_POST['do']??'')==='cancel'){$id=(int)$_POST['id'];$pdo->beginTransaction();
   $s=$pdo->prepare('SELECT r.*,t.deducts_balance FROM leave_requests r JOIN leave_types t ON t.id=r.leave_type_id WHERE r.id=? AND r.employee_id=? FOR UPDATE');$s->execute([$id,$eid]);$r=$s->fetch();
   if(!$r||!in_array($r['status'],['pending','approved']))throw new Exception('لا يمكن إلغاء هذا الطلب.');
   if($r['status']==='approved'&&$r['start_date']<=date('Y-m-d'))throw new Exception('لا يمكن إلغاء إجازة بدأت بالفعل.');
   if($r['status']==='approved'&&$r['deducts_balance']&&setting('refund_on_cancel'))$pdo->prepare('UPDATE leave_balances SET used_days=GREATEST(0,used_days-?) WHERE employee_id=? AND leave_type_id=? AND year=?')->execute([$r['days'],$eid,$r['leave_type_id'],substr($r['start_date'],0,4)]);
   $pdo->prepare("UPDATE leave_requests SET status='cancelled' WHERE id=?")->execute([$id]);
   $pdo->prepare("INSERT INTO leave_approvals(request_id,approver_user_id,approver_role,action,note) VALUES(?,?,?,'cancelled','ألغاها الموظف')")->execute([$id,$uid,$_SESSION['role']]);
   foreach(reviewer_user_ids($eid) as $u)notify($u,'ألغى الموظف طلب الإجازة رقم '.$id);notify($uid,'تم إلغاء طلب الإجازة.');audit('leave_cancel',"#$id");$pdo->commit();flash('تم إلغاء الطلب.');}
  else{
   $tid=(int)$_POST['leave_type_id'];$a=$_POST['start_date']??'';$b=$_POST['end_date']??'';
   $ok=fn($d)=>preg_match('/^\d{4}-\d{2}-\d{2}$/',$d)&&strtotime($d);
   if(!$ok($a)||!$ok($b))throw new Exception('التواريخ غير صحيحة.');
   if($a>$b)throw new Exception('تاريخ البداية يجب ألا يتجاوز تاريخ النهاية.');
   if(substr($a,0,4)!==substr($b,0,4))throw new Exception('قدّم طلبًا منفصلًا لكل سنة.');
   if($a<date('Y-m-d')&&!setting('allow_past_leave'))throw new Exception('لا يمكن طلب إجازة بتاريخ سابق.');
   $st=$pdo->prepare('SELECT status FROM employees WHERE id=?');$st->execute([$eid]);if($st->fetchColumn()==='inactive')throw new Exception('حسابك غير نشط.');
   $t=$pdo->prepare('SELECT * FROM leave_types WHERE id=? AND is_active=1');$t->execute([$tid]);$t=$t->fetch();if(!$t)throw new Exception('نوع الإجازة غير متاح.');
   $days=working_days($a,$b);if($days<=0)throw new Exception('الفترة المختارة لا تحتوي أيام عمل.');
   $o=$pdo->prepare("SELECT COUNT(*) FROM leave_requests WHERE employee_id=? AND status IN('pending','approved') AND start_date<=? AND end_date>=?");$o->execute([$eid,$b,$a]);
   if($o->fetchColumn())throw new Exception('يوجد طلب إجازة قيد المراجعة أو معتمد يتداخل مع هذه الفترة.');
   if($t['deducts_balance']&&!$t['allow_negative']){ensure_balance($eid,$tid,(int)substr($a,0,4));
    $bq=$pdo->prepare('SELECT total_days-used_days FROM leave_balances WHERE employee_id=? AND leave_type_id=? AND year=?');$bq->execute([$eid,$tid,substr($a,0,4)]);
    $pend=$pdo->prepare("SELECT COALESCE(SUM(days),0) FROM leave_requests WHERE employee_id=? AND leave_type_id=? AND status='pending' AND YEAR(start_date)=?");$pend->execute([$eid,$tid,substr($a,0,4)]);
    if($days>$bq->fetchColumn()-$pend->fetchColumn())throw new Exception('الرصيد المتبقي غير كافٍ لهذا الطلب.');}
   $att=leave_attachment($_FILES['attachment']??['error'=>4]);
   $pdo->beginTransaction();
   $pdo->prepare('INSERT INTO leave_requests(employee_id,leave_type_id,start_date,end_date,days,reason,notes,attachment) VALUES(?,?,?,?,?,?,?,?)')->execute([$eid,$tid,$a,$b,$days,trim($_POST['reason']),trim($_POST['notes']),$att]);$rid=$pdo->lastInsertId();
   $nm=$pdo->prepare('SELECT full_name FROM employees WHERE id=?');$nm->execute([$eid]);$who=$nm->fetchColumn();
   if((int)$t['requires_approval']===1){
    foreach(reviewer_user_ids($eid) as $u)notify($u,'يوجد طلب إجازة جديد للمراجعة من '.$who);
    notify($uid,'تم تقديم طلب الإجازة وهو قيد المراجعة.');audit('leave_request',"#$rid $days يوم");}
   else{ // لا يحتاج موافقة: اعتماد تلقائي بنفس منطق الخصم، مرة واحدة داخل نفس الـTransaction
    if($t['deducts_balance']){ensure_balance($eid,$tid,(int)substr($a,0,4));$pdo->prepare('UPDATE leave_balances SET used_days=used_days+? WHERE employee_id=? AND leave_type_id=? AND year=?')->execute([$days,$eid,$tid,substr($a,0,4)]);}
    $pdo->prepare("UPDATE leave_requests SET status='approved' WHERE id=? AND status='pending'")->execute([$rid]);
    $pdo->prepare("INSERT INTO leave_approvals(request_id,approver_user_id,approver_role,action,note) VALUES(?,?,'system','approved','اعتماد تلقائي (النوع لا يحتاج موافقة)')")->execute([$rid,$uid]);
    notify($uid,'تم اعتماد إجازتك تلقائيًا.');audit('leave_auto_approve',"#$rid $days يوم");}
   $pdo->commit();flash((int)$t['requires_approval']===1?"تم تقديم الطلب ($days يوم عمل).":"تم اعتماد الإجازة تلقائيًا ($days يوم عمل).");}
 }catch(Exception $x){if($pdo->inTransaction())$pdo->rollBack();flash($x->getMessage(),'error');}
 header('Location: leaves.php');exit;}
$types=$pdo->query('SELECT * FROM leave_types WHERE is_active=1')->fetchAll();
$pdo->prepare('INSERT IGNORE INTO leave_balances(employee_id,leave_type_id,year,total_days) SELECT ?,id,?,default_days FROM leave_types WHERE is_active=1')->execute([$eid,$year]);
$bal=$pdo->prepare('SELECT b.*,t.name FROM leave_balances b JOIN leave_types t ON t.id=b.leave_type_id WHERE b.employee_id=? AND b.year=? AND t.is_active=1');$bal->execute([$eid,$year]);
$reqs=$pdo->prepare('SELECT r.*,t.name tn,(SELECT note FROM leave_approvals WHERE request_id=r.id ORDER BY id DESC LIMIT 1) note FROM leave_requests r JOIN leave_types t ON t.id=r.leave_type_id WHERE r.employee_id=? ORDER BY r.id DESC LIMIT 50');$reqs->execute([$eid]);
$sm=$pdo->prepare("SELECT status,COUNT(*) FROM leave_requests WHERE employee_id=? GROUP BY status");$sm->execute([$eid]);$sm=$sm->fetchAll(PDO::FETCH_KEY_PAIR);
$rem=$pdo->prepare('SELECT leave_type_id,total_days-used_days FROM leave_balances WHERE employee_id=? AND year=?');$rem->execute([$eid,$year]);$rem=$rem->fetchAll(PDO::FETCH_KEY_PAIR);
$hd=holiday_dates(date('Y-m-d'),date('Y-m-d',strtotime('+400 days')));$wkd=array_values(array_filter(array_map('intval',explode(',',(string)setting('weekend_days'))),fn($x)=>true));
page_start('طلب إجازة وإجازاتي');ph('إجازاتي','قدّم طلب إجازة وتابع حالة طلباتك وأرصدتك','<button type="button" class="btn" onclick="openModal(\'lvm\')">'.ic('plus').'طلب إجازة جديد</button>');
echo kpis([[(int)($sm['pending']??0),'قيد المراجعة','warn','','clock'],[(int)($sm['approved']??0),'معتمدة','ok','','check-circle'],[(int)($sm['rejected']??0),'مرفوضة','err','','x-circle']]);?>
<div class="bal-grid"><?php foreach($bal as $b):$rm=$b['total_days']-$b['used_days'];?><div class="bal"><div class="t"><?=e($b['name'])?></div><div class="v"><b><?=$rm+0?></b><span>يوم متبقٍ</span></div><div class="prog gold"><i style="width:<?=$b['total_days']>0?min(100,round($b['used_days']/$b['total_days']*100)):0?>%"></i></div><div class="f"><span>مستخدم <?=$b['used_days']+0?></span><span>من <?=$b['total_days']+0?></span></div></div><?php endforeach;?></div>
<?php modal_open('lvm','طلب إجازة جديد',['form'=>'method="post" enctype="multipart/form-data"','wide'=>true,'grid'=>true,'desc'=>'اختر النوع والتواريخ وسيظهر الرصيد المتبقي وعدد أيام العمل.']);?><input type="hidden" name="csrf" value="<?=csrf()?>">
<label>نوع الإجازة<select name="leave_type_id" required><?php foreach($types as $t)echo '<option value="'.$t['id'].'" data-ap="'.(int)$t['requires_approval'].'">'.e($t['name']).'</option>';?></select></label>
<label>من تاريخ<input type="date" name="start_date" required></label><label>إلى تاريخ<input type="date" name="end_date" required></label>
<label>السبب<input name="reason" required></label><label>ملاحظات<input name="notes"></label><label>مرفق (PDF/JPG/PNG)<input type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png"></label>
<div class="full preview" id="lvinfo"><?=ic('info')?>اختر النوع والتواريخ لعرض الرصيد وعدد الأيام.</div>
<?php modal_close('<button class="btn">'.ic('check').'تقديم الطلب</button><button type="button" class="btn ghost" data-close>إلغاء</button>',true);?>
<div class="card tbl"><?=card_h('طلباتي','آخر 50 طلبًا')?><table><tr><th>النوع</th><th>من</th><th>إلى</th><th>الأيام</th><th>الحالة</th><th>ملاحظة المراجع</th><th></th></tr>
<?php foreach($reqs as $r):?><tr><td class="cmain"><b><?=e($r['tn'])?></b></td><td><?=e($r['start_date'])?></td><td><?=e($r['end_date'])?></td><td><?=$r['days']+0?></td><td><?=sb($r['status'],LS[$r['status']])?></td><td class="wrap"><?=e($r['note']??'')?></td>
<td class="act"><?php if(in_array($r['status'],['pending','approved'])):?><form method="post" data-confirm="<?=$r['status']==='approved'?'سيُلغى الطلب المعتمد وقد يُعاد الرصيد حسب الإعدادات.':'سيتم إلغاء الطلب قبل مراجعته.'?>" data-confirm-title="إلغاء طلب الإجازة؟" data-confirm-ok="إلغاء الطلب" data-confirm-tone="danger"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="do" value="cancel"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn danger sm"><?=ic('x')?>إلغاء</button></form><?php endif;?></td></tr>
<?php endforeach;if(!$reqs->rowCount())echo empty_row(7,'لا توجد طلبات بعد','قدّم أول طلب إجازة وسيظهر هنا مع حالته.','sun');?></table></div>
<script>(function(){const R=<?=json_encode($rem)?>,H=<?=json_encode($hd)?>,W=<?=json_encode($wkd)?>,f=document.querySelector("#lvm form"),i=document.getElementById("lvinfo");
if(location.search.indexOf("new=1")>-1)window.addEventListener("load",()=>openModal("lvm"));
function up(){const t=f.leave_type_id,a=f.start_date.value,b=f.end_date.value;let n=0;if(a&&b&&a<=b){for(let d=new Date(a+"T00:00");d<=new Date(b+"T00:00");d.setDate(d.getDate()+1)){const s=d.toISOString().slice(0,10);if(!W.includes(d.getDay())&&!H.includes(s))n++;}}
const ap=t.selectedOptions[0]&&t.selectedOptions[0].dataset.ap==="1";const r=R[t.value];i.textContent="الرصيد المتبقي: "+(r===undefined?"—":r)+" يوم · أيام العمل (تقديري): "+n+" · "+(ap?"يحتاج موافقة":"يُعتمد تلقائيًا")+(r!==undefined&&n>r?" · تنبيه: الطلب يتجاوز الرصيد":"");}
["leave_type_id","start_date","end_date"].forEach(k=>f[k].addEventListener("change",up));up();})();</script>
<?php page_end();
