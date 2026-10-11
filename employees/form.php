<?php require_once __DIR__.'/../includes/layout.php';require_login(['admin']);post_guard();
$id=(int)($_GET['id']??0);$emp=null;$user=null;
if($id){$s=$pdo->prepare('SELECT * FROM employees WHERE id=?');$s->execute([$id]);$emp=$s->fetch();if(!$emp)error_page(404,'الموظف غير موجود','لم نجد موظفًا بهذا المعرّف. ربما حُذف أو أن الرابط غير صحيح.','العودة إلى الموظفين',BASE.'/employees/index.php');$u=$pdo->prepare('SELECT * FROM users WHERE employee_id=?');$u->execute([$id]);$user=$u->fetch();}
if($_SERVER['REQUEST_METHOD']==='POST'){
 try{
  $f=['full_name'=>trim($_POST['full_name']),'email'=>trim($_POST['email']),'phone'=>trim($_POST['phone']),'department_id'=>(int)$_POST['department_id']?:null,'position_id'=>(int)$_POST['position_id']?:null,'manager_id'=>(int)$_POST['manager_id']?:null,'hire_date'=>$_POST['hire_date'],'status'=>$_POST['status']];
  if($f['full_name']===''||!filter_var($f['email'],FILTER_VALIDATE_EMAIL)||!$f['hire_date']||!isset(ST[$f['status']]))throw new Exception('يرجى إدخال الاسم والبريد الصحيح وتاريخ التوظيف.');
  if($id&&$f['manager_id']===$id)throw new Exception('لا يمكن أن يكون الموظف مديرًا لنفسه.');
  $role=in_array($_POST['role'],['admin','manager','employee'])?$_POST['role']:'employee';if($id&&$id===(int)($_SESSION['eid']??0)){$cur=$pdo->prepare('SELECT role FROM users WHERE employee_id=?');$cur->execute([$id]);$role=$cur->fetchColumn()?:$role;} // لا يغيّر المستخدم دور حسابه الحالي حتى لا يفقد صلاحياته$photo=upload_photo($_FILES['photo']??['error'=>4]);
  $pdo->beginTransaction();
  if($id){$set=implode(',',array_map(fn($k)=>"$k=?",array_keys($f)));$v=array_values($f);if($photo){$set.=',photo=?';$v[]=$photo;}$v[]=$id;$pdo->prepare("UPDATE employees SET $set WHERE id=?")->execute($v);
   $pdo->prepare('UPDATE users SET email=?,role=? WHERE employee_id=?')->execute([$f['email'],$role,$id]);
   if(!empty($_POST['password'])){if(strlen($_POST['password'])<8)throw new Exception('كلمة المرور 8 أحرف على الأقل.');$pdo->prepare('UPDATE users SET password_hash=? WHERE employee_id=?')->execute([password_hash($_POST['password'],PASSWORD_DEFAULT),$id]);}
   audit('employee_update',$f['full_name']);}
  else{if(strlen($_POST['password'])<8)throw new Exception('كلمة المرور 8 أحرف على الأقل.');
   $code='EMP-'.str_pad((string)(1+(int)$pdo->query('SELECT COALESCE(MAX(id),0) FROM employees')->fetchColumn()),4,'0',STR_PAD_LEFT);
   $pdo->prepare('INSERT INTO employees(employee_code,full_name,email,phone,department_id,position_id,manager_id,hire_date,status,photo) VALUES(?,?,?,?,?,?,?,?,?,?)')->execute([$code,$f['full_name'],$f['email'],$f['phone'],$f['department_id'],$f['position_id'],$f['manager_id'],$f['hire_date'],$f['status'],$photo]);
   $id=(int)$pdo->lastInsertId();
   $pdo->prepare('INSERT INTO users(employee_id,email,password_hash,role) VALUES(?,?,?,?)')->execute([$id,$f['email'],password_hash($_POST['password'],PASSWORD_DEFAULT),$role]);
   $pdo->prepare('INSERT INTO employee_qr_tokens(employee_id,token) VALUES(?,?)')->execute([$id,bin2hex(random_bytes(32))]);
   $pdo->prepare('INSERT INTO leave_balances(employee_id,leave_type_id,year,total_days) SELECT ?,id,YEAR(CURDATE()),default_days FROM leave_types WHERE is_active=1')->execute([$id]);
   audit('employee_create',$code.' '.$f['full_name']);audit('qr_generate',$code);}
  $pdo->commit();flash('تم حفظ بيانات الموظف بنجاح.');header('Location: view.php?id='.$id);exit;
 }catch(PDOException $x){if($pdo->inTransaction())$pdo->rollBack();flash($x->getCode()==23000?'البريد الإلكتروني مستخدم مسبقًا.':'خطأ في قاعدة البيانات.','error');}
 catch(Exception $x){if($pdo->inTransaction())$pdo->rollBack();flash($x->getMessage(),'error');}
 $emp=array_merge($emp??[],$_POST);}
$deps=$pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();$pos=$pdo->query('SELECT * FROM positions ORDER BY name')->fetchAll();
$mgr=$pdo->query("SELECT id,full_name FROM employees WHERE status<>'inactive' ORDER BY full_name")->fetchAll();
$v=fn($k)=>e($emp[$k]??'');
$opt=function($rows,$key,$sel,$lbl='name'){foreach($rows as $r)echo '<option value="'.$r['id'].'" '.(($sel??'')==$r['id']?'selected':'').'>'.e($r[$lbl]).'</option>';};
page_start($id?'تعديل موظف':'إضافة موظف');
ph($id?'تعديل بيانات موظف':'إضافة موظف جديد',$id?'حدّث البيانات الشخصية والوظيفية وبيانات الحساب.':'أدخل البيانات الأساسية لإنشاء الموظف وحسابه.','<a class="btn ghost" href="index.php">'.ic('arrow-right').'العودة للقائمة</a>');?>
<form method="post" enctype="multipart/form-data" class="card form"><input type="hidden" name="csrf" value="<?=csrf()?>">
<h3 class="full sec">البيانات الشخصية</h3><label>الاسم الكامل<input name="full_name" value="<?=$v('full_name')?>" required autocomplete="off"></label>
<label>البريد الإلكتروني<input type="email" name="email" value="<?=$v('email')?>" required autocomplete="off" dir="ltr" style="text-align:right"></label>
<label>الهاتف<input name="phone" value="<?=$v('phone')?>" inputmode="tel" dir="ltr" style="text-align:right"></label>
<label>الصورة الشخصية<span class="photo-pick"><img src="<?=av($emp['photo']??null)?>" alt="" id="pp"><input type="file" name="photo" accept="image/jpeg,image/png,image/webp" onchange="const f=this.files[0];if(f)document.getElementById('pp').src=URL.createObjectURL(f)"></span><span class="hint">JPG أو PNG أو WEBP، حتى 2 ميغابايت.</span></label>
<h3 class="full sec">البيانات الوظيفية</h3><label>تاريخ التوظيف<input type="date" name="hire_date" value="<?=$v('hire_date')?>" required></label>
<label>القسم<select name="department_id"><option value="">—</option><?php $opt($deps,'id',$emp['department_id']??null)?></select></label>
<label>الوظيفة<select name="position_id"><option value="">—</option><?php $opt($pos,'id',$emp['position_id']??null)?></select></label>
<label>المدير المباشر<select name="manager_id"><option value="">—</option><?php $opt($mgr,'id',$emp['manager_id']??null,'full_name')?></select></label>
<label>الحالة<select name="status"><?php foreach(ST as $k=>$t)echo '<option value="'.$k.'" '.(($emp['status']??'active')===$k?'selected':'').'>'.$t.'</option>';?></select></label>
<h3 class="full sec">الحساب والصلاحيات</h3><label>الدور<select name="role"><?php foreach(['employee'=>'موظف','manager'=>'مدير','admin'=>'مدير النظام / موارد بشرية'] as $k=>$t)echo '<option value="'.$k.'" '.(($user['role']??'employee')===$k?'selected':'').'>'.$t.'</option>';?></select></label>
<label>كلمة المرور <?=$id?'(اتركها فارغة بدون تغيير)':''?><span class="pw"><input type="password" name="password" id="pwf" minlength="8" autocomplete="new-password" <?=$id?'':'required'?>><button type="button" data-pw="pwf" aria-label="إظهار كلمة المرور">إظهار</button></span><span class="hint">8 أحرف على الأقل.</span></label>
<div class="full savebar noprint"><span class="hint"><?=$id?'التغييرات تُطبَّق فور الحفظ.':'سيُنشأ رمز QR وأرصدة الإجازات تلقائيًا.'?></span><span class="row"><a class="btn ghost" href="index.php">إلغاء</a><button class="btn"><?=ic('check')?>حفظ</button></span></div></form>
<?php page_end();
