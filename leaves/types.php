<?php require_once __DIR__.'/../includes/leave.php';require_login(['admin']);post_guard();
if($_SERVER['REQUEST_METHOD']==='POST'){$a=$_POST['do']??'';$id=(int)($_POST['id']??0);
 try{
  if($a==='save'){$n=trim($_POST['name']);$d=max(0,(int)$_POST['default_days']);if($n==='')throw new Exception('اسم النوع مطلوب.');
   $v=[$n,$d,(int)isset($_POST['deducts']),(int)isset($_POST['approval']),(int)isset($_POST['neg'])];
   if($id){$pdo->prepare('UPDATE leave_types SET name=?,default_days=?,deducts_balance=?,requires_approval=?,allow_negative=? WHERE id=?')->execute([...$v,$id]);}
   else{$pdo->prepare('INSERT INTO leave_types(name,default_days,deducts_balance,requires_approval,allow_negative) VALUES(?,?,?,?,?)')->execute($v);$id=(int)$pdo->lastInsertId();
    $pdo->prepare("INSERT IGNORE INTO leave_balances(employee_id,leave_type_id,year,total_days) SELECT id,?,YEAR(CURDATE()),? FROM employees WHERE status<>'inactive'")->execute([$id,$d]);}
   audit('leave_type_save',$n);flash('تم حفظ نوع الإجازة.');}
  if($a==='toggle'){$pdo->prepare('UPDATE leave_types SET is_active=1-is_active WHERE id=?')->execute([$id]);audit('leave_type_toggle',"#$id");flash('تم تحديث حالة النوع.');}
  if($a==='del'){$c=$pdo->prepare('SELECT COUNT(*) FROM leave_requests WHERE leave_type_id=?');$c->execute([$id]);if($c->fetchColumn())throw new Exception('لا يمكن حذف نوع مرتبط بطلبات. عطّله بدلًا من ذلك.');
   $pdo->prepare('DELETE FROM leave_balances WHERE leave_type_id=?')->execute([$id]);$pdo->prepare('DELETE FROM leave_types WHERE id=?')->execute([$id]);audit('leave_type_delete',"#$id");flash('تم حذف النوع.');}
 }catch(Exception $x){flash($x->getMessage(),'error');}
 header('Location: types.php');exit;}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM leave_types WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$rows=$pdo->query('SELECT * FROM leave_types ORDER BY id')->fetchAll();
page_start('أنواع الإجازات');ph('أنواع الإجازات','تحديد قواعد الخصم والموافقة لكل نوع','<a class="btn" href="types.php?new=1" data-modal="tm" data-title="إضافة نوع إجازة" data-submit="إضافة نوع" data-fill=\'{"id":0,"name":"","default_days":0,"deducts":true,"approval":true,"neg":false}\'>'.ic('plus').'إضافة نوع</a>');$ck=fn($k,$d=1)=>($edit?($edit[$k]??0):$d)?'checked':'';
modal_open('tm',$edit?'تعديل نوع الإجازة':'إضافة نوع إجازة',['form'=>'method="post"','wide'=>true,'grid'=>true,'desc'=>'حدّد الرصيد السنوي وقواعد الخصم والموافقة.']);?><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="do" value="save"><input type="hidden" name="id" value="<?=$edit['id']??0?>">
<label>الاسم<input name="name" value="<?=e($edit['name']??'')?>" required></label><label>الأيام السنوية الافتراضية<input type="number" min="0" name="default_days" value="<?=e($edit['default_days']??0)?>"></label>
<label class="check full"><input type="checkbox" name="deducts" <?=$ck('deducts_balance')?>> تُخصم من الرصيد</label><label class="check full"><input type="checkbox" name="approval" <?=$ck('requires_approval')?>> تحتاج موافقة</label><label class="check full"><input type="checkbox" name="neg" <?=$ck('allow_negative',0)?>> يسمح بتجاوز الرصيد</label>
<?php modal_close('<button class="btn">'.($edit?'تحديث':'إضافة نوع').'</button><a class="btn ghost" href="types.php" data-close>إلغاء</a>',true);?>
<?php if($edit||isset($_GET['new']))echo '<script>window.addEventListener("load",()=>openModal("tm"))</script>';?>
<div class="card tbl"><table><tr><th>النوع</th><th>الأيام</th><th>خصم</th><th>موافقة</th><th>الحالة</th><th>آخر تعديل</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td class="cmain"><b><?=e($r['name'])?></b></td><td><?=$r['default_days']?></td><td><?=$r['deducts_balance']?'نعم':'لا'?></td><td><?=$r['requires_approval']?'نعم':'لا'?></td><td><?=sb($r['is_active']?'active':'inactive',$r['is_active']?'نشط':'غير نشط')?></td><td><?=e($r['updated_at'])?></td>
<td class="act"><a class="btn ghost sm" href="?edit=<?=$r['id']?>" data-modal="tm" data-title="تعديل نوع الإجازة" data-submit="تحديث" data-fill="<?=e(json_encode(['id'=>(int)$r['id'],'name'=>$r['name'],'default_days'=>(int)$r['default_days'],'deducts'=>(bool)$r['deducts_balance'],'approval'=>(bool)$r['requires_approval'],'neg'=>(bool)$r['allow_negative']],JSON_UNESCAPED_UNICODE))?>"><?=ic('pencil')?>تعديل</a>
<?php foreach([['toggle',$r['is_active']?'تعطيل':'تفعيل','ghost'],['del','حذف','danger']] as [$d,$t,$c]):?><form method="post" data-confirm="<?=$d==='del'?'لا يمكن حذف نوع مرتبط بطلبات؛ عطّله بدلًا من ذلك.':'يمكنك تغيير الحالة لاحقًا في أي وقت.'?>" data-confirm-title="<?=$t?> «<?=e($r['name'])?>»؟" data-confirm-ok="<?=$t?>" <?=$d==='del'?'data-confirm-tone="danger"':''?>><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="do" value="<?=$d?>"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn sm <?=$c?>"><?=$d==='del'?ic('trash'):''?><?=$t?></button></form><?php endforeach;?></td></tr><?php endforeach;if(!$rows)echo empty_row(7,'لا توجد أنواع إجازات','أضف نوع الإجازة الأول.','tag');?></table></div>
<?php page_end();
