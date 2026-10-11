<?php require_once __DIR__.'/../includes/layout.php';require_login(['admin']);post_guard();
if($_SERVER['REQUEST_METHOD']==='POST'){
 $a=$_POST['do']??'';$n=trim($_POST['name']??'');$id=(int)($_POST['id']??0);$d=(int)($_POST['department_id']??0)?:null;
 try{
  if($a==='save'){if($n==='')throw new Exception('اسم الوظيفة مطلوب.');
   if($id)$pdo->prepare('UPDATE positions SET name=?,department_id=? WHERE id=?')->execute([$n,$d,$id]);else $pdo->prepare('INSERT INTO positions(name,department_id) VALUES(?,?)')->execute([$n,$d]);
   audit('position_save',$n);flash('تم حفظ الوظيفة بنجاح.');}
  if($a==='del'){$pdo->prepare('UPDATE employees SET position_id=NULL WHERE position_id=?')->execute([$id]);$pdo->prepare('DELETE FROM positions WHERE id=?')->execute([$id]);audit('position_delete',"#$id");flash('تم حذف الوظيفة، وأصبح الموظفون المرتبطون بها بدون وظيفة.');}
 }catch(Exception $x){flash($x->getMessage(),'error');}
 header('Location: index.php');exit;}
$edit=null;if(isset($_GET['edit'])){$s=$pdo->prepare('SELECT * FROM positions WHERE id=?');$s->execute([(int)$_GET['edit']]);$edit=$s->fetch();}
$deps=$pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();
$rows=$pdo->query('SELECT p.*,d.name dname,(SELECT COUNT(*) FROM employees WHERE position_id=p.id) cnt FROM positions p LEFT JOIN departments d ON d.id=p.department_id ORDER BY p.name')->fetchAll();
page_start('الوظائف');ph('الوظائف','إدارة المسميات الوظيفية وربطها بالأقسام','<button type="button" class="btn" data-modal="psm" data-fill=\'{"id":0,"name":"","department_id":""}\' data-title="إضافة وظيفة" data-submit="إضافة وظيفة">'.ic('plus').'إضافة وظيفة</button>');
echo kpis([[count($rows),'الوظائف','','','briefcase'],[array_sum(array_column($rows,'cnt')),'موظفون بوظائف','','','users'],[count(array_filter($rows,fn($r)=>!$r['dname'])),'وظائف بلا قسم','warn','','alert']]);
modal_open('psm',$edit?'تعديل الوظيفة':'إضافة وظيفة',['form'=>'method="post"','desc'=>'اربط المسمى الوظيفي بقسم عند الحاجة.']);?>
<input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="do" value="save"><input type="hidden" name="id" value="<?=$edit['id']??0?>">
<div class="stack" style="gap:14px"><label class="fld">اسم الوظيفة<input name="name" placeholder="مثال: محاسب" value="<?=e($edit['name']??'')?>" required></label>
<label class="fld">القسم<select name="department_id"><option value="">بدون قسم</option><?php foreach($deps as $d):?><option value="<?=$d['id']?>" <?=($edit['department_id']??0)==$d['id']?'selected':''?>><?=e($d['name'])?></option><?php endforeach;?></select></label></div>
<?php modal_close('<button class="btn">'.($edit?'تحديث':'إضافة وظيفة').'</button><button type="button" class="btn ghost" data-close>إلغاء</button>',true);
if($edit)echo '<script>window.addEventListener("load",()=>openModal("psm"))</script>';?>
<div class="card tbl"><table><tr><th>الوظيفة</th><th>القسم</th><th>الموظفون</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td class="cmain"><b><?=e($r['name'])?></b></td><td><?=e($r['dname']??'—')?></td><td><span class="badge plain"><?=$r['cnt']?> موظف</span></td><td class="act"><a class="btn ghost sm" href="?edit=<?=$r['id']?>" data-modal="psm" data-title="تعديل الوظيفة" data-submit="تحديث" data-fill="<?=e(json_encode(['id'=>(int)$r['id'],'name'=>$r['name'],'department_id'=>$r['department_id']??''],JSON_UNESCAPED_UNICODE))?>"><?=ic('pencil')?>تعديل</a>
<form method="post" data-confirm="سيصبح الموظفون المرتبطون بهذه الوظيفة بدون وظيفة." data-confirm-title="حذف وظيفة «<?=e($r['name'])?>»؟" data-confirm-ok="حذف" data-confirm-tone="danger"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="do" value="del"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn danger sm"><?=ic('trash')?>حذف</button></form></td></tr>
<?php endforeach;if(!$rows)echo empty_row(4,'لا توجد وظائف بعد','أضف المسميات الوظيفية لتعيينها للموظفين.','briefcase');?></table></div>
<?php page_end();
