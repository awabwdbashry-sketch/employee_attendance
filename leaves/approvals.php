<?php require_once __DIR__.'/../includes/leave.php';require_login(['admin','manager']);post_guard();
$SEL='SELECT r.*,t.name tn,t.deducts_balance,t.allow_negative,e.full_name,e.manager_id,e.status es FROM leave_requests r JOIN leave_types t ON t.id=r.leave_type_id JOIN employees e ON e.id=r.employee_id';
if($_SERVER['REQUEST_METHOD']==='POST'){$id=(int)$_POST['id'];$act=$_POST['do']??'';$note=trim($_POST['note']??'');
 try{
  if(!in_array($act,['approve','reject']))throw new Exception('إجراء غير صالح.');
  if($act==='reject'&&$note==='')throw new Exception('سبب الرفض مطلوب.');
  $pdo->beginTransaction();
  $s=$pdo->prepare("$SEL WHERE r.id=? FOR UPDATE");$s->execute([$id]);$r=$s->fetch();
  if(!$r||!can_review($r))throw new Exception('غير مصرح لك بمراجعة هذا الطلب.');
  if($r['status']!=='pending')throw new Exception('تمت معالجة هذا الطلب مسبقًا.');
  $y=(int)substr($r['start_date'],0,4);
  if($act==='approve'){
   if($r['deducts_balance']){ensure_balance((int)$r['employee_id'],(int)$r['leave_type_id'],$y);
    $b=$pdo->prepare('SELECT total_days-used_days FROM leave_balances WHERE employee_id=? AND leave_type_id=? AND year=? FOR UPDATE');$b->execute([$r['employee_id'],$r['leave_type_id'],$y]);
    if(!$r['allow_negative']&&$b->fetchColumn()<$r['days'])throw new Exception('الرصيد لم يعد كافيًا للموافقة.');
    $pdo->prepare('UPDATE leave_balances SET used_days=used_days+? WHERE employee_id=? AND leave_type_id=? AND year=?')->execute([$r['days'],$r['employee_id'],$r['leave_type_id'],$y]);}
   $pdo->prepare("UPDATE leave_requests SET status='approved' WHERE id=?")->execute([$id]);}
  else $pdo->prepare("UPDATE leave_requests SET status='rejected' WHERE id=?")->execute([$id]);
  $pdo->prepare('INSERT INTO leave_approvals(request_id,approver_user_id,approver_role,action,note) VALUES(?,?,?,?,?)')->execute([$id,$_SESSION['uid'],$_SESSION['role'],$act==='approve'?'approved':'rejected',$note?:'تمت الموافقة']);
  $eu=$pdo->prepare('SELECT id FROM users WHERE employee_id=?');$eu->execute([$r['employee_id']]);
  if($u=$eu->fetchColumn())notify((int)$u,$act==='approve'?'تمت الموافقة على طلب الإجازة.':'تم رفض طلب الإجازة. السبب: '.$note);
  audit($act==='approve'?'leave_approve':'leave_reject',"#$id");$pdo->commit();flash($act==='approve'?'تمت الموافقة وخصم الرصيد.':'تم رفض الطلب.');
 }catch(Exception $x){if($pdo->inTransaction())$pdo->rollBack();flash($x->getMessage(),'error');}
 header('Location: approvals.php');exit;}
$w=$_SESSION['role']==='admin'?'':'WHERE e.manager_id='.(int)$_SESSION['eid'];
$st=$_GET['st']??'pending';if($w===''&&$st!=='all')$w="WHERE r.status=".$pdo->quote($st);elseif($st!=='all')$w.=" AND r.status=".$pdo->quote($st);
$rows=$pdo->query("$SEL $w ORDER BY r.id DESC LIMIT 100")->fetchAll();
$hist=$pdo->prepare('SELECT a.*,u.email FROM leave_approvals a JOIN users u ON u.id=a.approver_user_id WHERE request_id=? ORDER BY a.id');
page_start('طلبات الإجازات');ph('طلبات الإجازات','مراجعة طلبات الموظفين والموافقة عليها أو رفضها');?>
<div class="row" style="margin-bottom:18px"><div class="seg" role="tablist"><?php foreach(['pending'=>'قيد المراجعة','approved'=>'الموافق عليها','rejected'=>'المرفوضة','all'=>'الكل'] as $k=>$v)echo '<a class="'.($st===$k?'on':'').'" href="?st='.$k.'"'.($st===$k?' aria-current="page"':'').'>'.$v.'</a>';?></div></div>
<div class="card tbl"><table><tr><th>الموظف</th><th>النوع</th><th>من</th><th>إلى</th><th>الأيام</th><th>السبب</th><th>المرفق</th><th>الحالة</th><th>الإجراء</th></tr>
<?php foreach($rows as $r):?><tr><td class="cmain"><b><?=e($r['full_name'])?></b></td><td><?=e($r['tn'])?></td><td><?=e($r['start_date'])?></td><td><?=e($r['end_date'])?></td><td><?=$r['days']+0?></td><td class="wrap"><?=e($r['reason'])?><?php if($r['notes']):?><br><small><?=e($r['notes'])?></small><?php endif;?></td>
<td><?=$r['attachment']?'<a class="btn ghost sm" href="attachment.php?id='.$r['id'].'">'.ic('paperclip').'عرض</a>':'<span class="muted">—</span>'?></td><td class="wrap"><?=sb($r['status'],LS[$r['status']])?><?php $hist->execute([$r['id']]);foreach($hist as $h)echo '<br><small>'.e($h['approver_role']).': '.e($h['note']).'</small>';?></td>
<td class="act"><?php if($r['status']==='pending'&&can_review($r)):?><form method="post" class="act"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn ok sm" name="do" value="approve" data-confirm="<?=$r['deducts_balance']?'سيُخصم '.($r['days']+0).' يوم من رصيد الموظف.':'هذا النوع لا يُخصم من الرصيد.'?>" data-confirm-title="الموافقة على طلب <?=e($r['full_name'])?>؟" data-confirm-ok="موافقة"><?=ic('check')?>موافقة</button><button type="button" class="btn danger sm" onclick="rj(<?=$r['id']?>)"><?=ic('x')?>رفض</button></form><?php endif;?></td></tr>
<?php endforeach;if(!$rows)echo empty_row(9,'لا توجد طلبات','لا توجد طلبات بهذه الحالة حاليًا.','inbox');?></table></div>
<?php modal_open('rjm','رفض طلب الإجازة',['form'=>'method="post"','desc'=>'سيصل سبب الرفض إلى الموظف في إشعار.']);?><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="id" id="rid"><input type="hidden" name="do" value="reject"><label class="fld">سبب الرفض (إلزامي)<textarea name="note" required rows="3"></textarea></label>
<?php modal_close('<button class="btn danger solid">تأكيد الرفض</button><button type="button" class="btn ghost" data-close>إلغاء</button>',true);?>
<script>function rj(i){document.getElementById("rid").value=i;openModal("rjm")}</script>
<?php page_end();
