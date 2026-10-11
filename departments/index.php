<?php require_once __DIR__.'/../includes/layout.php';require_login(['admin']);post_guard();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $a=$_POST['do']??'';$n=trim($_POST['name']??'');$id=(int)($_POST['id']??0);
 try{
  if($a==='save'){if($n==='')throw new Exception('اسم القسم مطلوب.');
   if($id)$pdo->prepare('UPDATE departments SET name=? WHERE id=?')->execute([$n,$id]);else $pdo->prepare('INSERT INTO departments(name) VALUES(?)')->execute([$n]);
   audit('dept_save',$n);flash('تم حفظ القسم بنجاح.');}
  if($a==='del'){$c=$pdo->prepare('SELECT COUNT(*) FROM employees WHERE department_id=?');$c->execute([$id]);
   if($c->fetchColumn())throw new Exception('لا يمكن حذف قسم يضم موظفين. انقل الموظفين أولًا.');
   $pdo->prepare('UPDATE positions SET department_id=NULL WHERE department_id=?')->execute([$id]);$pdo->prepare('DELETE FROM departments WHERE id=?')->execute([$id]);audit('dept_delete',"#$id");flash('تم حذف القسم.');}
 }catch(PDOException $x){flash('اسم القسم مستخدم مسبقًا.','error');}catch(Exception $x){flash($x->getMessage(),'error');}
 header('Location: index.php');exit;}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM departments WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$rows=$pdo->query('SELECT d.*,(SELECT COUNT(*) FROM employees WHERE department_id=d.id) cnt FROM departments d ORDER BY name')->fetchAll();
page_start('الأقسام');ph('الأقسام','إدارة أقسام المؤسسة وعدد الموظفين في كل قسم','<button type="button" class="btn" data-modal="dpm" data-fill=\'{"id":0,"name":""}\' data-title="إضافة قسم" data-submit="إضافة قسم">'.ic('plus').'إضافة قسم</button>');
echo kpis([[count($rows),'الأقسام','','','building'],[array_sum(array_column($rows,'cnt')),'موظفون ضمن أقسام','','','users'],[count(array_filter($rows,fn($r)=>!$r['cnt'])),'أقسام بلا موظفين','warn','','alert']]);
modal_open('dpm',$edit?'تعديل القسم':'إضافة قسم',['form'=>'method="post"','desc'=>'اسم القسم يجب أن يكون فريدًا.']);?>
<input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="do" value="save"><input type="hidden" name="id" value="<?=$edit['id']??0?>">
<label class="fld">اسم القسم<input name="name" placeholder="مثال: الموارد البشرية" value="<?=e($edit['name']??'')?>" required maxlength="100"></label>
<?php modal_close('<button class="btn">'.($edit?'تحديث':'إضافة قسم').'</button><button type="button" class="btn ghost" data-close>إلغاء</button>',true);
if($edit)echo '<script>window.addEventListener("load",()=>openModal("dpm"))</script>';?>
<div class="card tbl"><table><tr><th>القسم</th><th>عدد الموظفين</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td class="cmain"><b><?=e($r['name'])?></b></td><td><span class="badge plain"><?=$r['cnt']?> موظف</span></td><td class="act"><a class="btn ghost sm" href="?edit=<?=$r['id']?>" data-modal="dpm" data-title="تعديل القسم" data-submit="تحديث" data-fill="<?=e(json_encode(['id'=>(int)$r['id'],'name'=>$r['name']],JSON_UNESCAPED_UNICODE))?>"><?=ic('pencil')?>تعديل</a>
<form method="post" data-confirm="لا يمكن حذف قسم يضم موظفين. هذا الإجراء لا يمكن التراجع عنه." data-confirm-title="حذف قسم «<?=e($r['name'])?>»؟" data-confirm-ok="حذف" data-confirm-tone="danger"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="do" value="del"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn danger sm"><?=ic('trash')?>حذف</button></form></td></tr>
<?php endforeach;if(!$rows)echo empty_row(3,'لا توجد أقسام بعد','أنشئ أول قسم لتنظيم الموظفين.','building');?></table></div>
<?php page_end();
