<?php require_once __DIR__.'/../includes/layout.php';require_login(['admin','manager']);post_guard();
$admin=$_SESSION['role']==='admin';
if($_SERVER['REQUEST_METHOD']==='POST'&&$admin){$id=(int)$_POST['id'];if($id===(int)$_SESSION['eid']&&$_POST['do']==='disable'){flash('لا يمكنك تعطيل حسابك.','error');header('Location: index.php');exit;}$st=$_POST['do']==='disable'?'inactive':'active';
 $pdo->prepare('UPDATE employees SET status=? WHERE id=?')->execute([$st,$id]);$pdo->prepare('UPDATE users SET is_active=? WHERE employee_id=?')->execute([$st==='active'?1:0,$id]);
 audit($st==='active'?'employee_enable':'employee_disable',"#$id");flash($st==='active'?'تمت إعادة تفعيل الموظف.':'تم تعطيل الموظف.');header('Location: index.php');exit;}
$w=['1=1'];$p=[];
if(!$admin){$w[]='e.manager_id=?';$p[]=$_SESSION['eid'];}
if(($q=trim($_GET['q']??''))!==''){$w[]='(e.full_name LIKE ? OR e.employee_code LIKE ? OR e.email LIKE ?)';array_push($p,"%$q%","%$q%","%$q%");}
if(!empty($_GET['dep'])){$w[]='e.department_id=?';$p[]=(int)$_GET['dep'];}
if(!empty($_GET['st'])&&isset(ST[$_GET['st']])){$w[]='e.status=?';$p[]=$_GET['st'];}
$W=implode(' AND ',$w);$page=max(1,(int)($_GET['p']??1));$per=10;
$c=$pdo->prepare("SELECT COUNT(*) FROM employees e WHERE $W");$c->execute($p);$pg=paginate((int)$c->fetchColumn(),$per,$page);
$s=$pdo->prepare("SELECT e.*,d.name dn,po.name pn FROM employees e LEFT JOIN departments d ON d.id=e.department_id LEFT JOIN positions po ON po.id=e.position_id WHERE $W ORDER BY e.id DESC LIMIT $per OFFSET {$pg['offset']}");$s->execute($p);$rows=$s->fetchAll();
$deps=$pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();
page_start('الموظفون');$ks=$pdo->prepare('SELECT status,COUNT(*) FROM employees e WHERE '.($admin?'1=1':'e.manager_id=?').' GROUP BY status');$ks->execute($admin?[]:[$_SESSION['eid']]);$ks=$ks->fetchAll(PDO::FETCH_KEY_PAIR);
ph($admin?'الموظفون':'فريقي','إدارة بيانات الموظفين وحساباتهم',$admin?'<a class="btn" href="form.php">'.ic('plus').'إضافة موظف</a>':'');
echo kpis([[array_sum($ks),'إجمالي الموظفين','','','users'],[(int)($ks['active']??0),'نشطون','ok','','check-circle'],[(int)($ks['on_leave']??0),'في إجازة','info','','sun'],[(int)($ks['inactive']??0),'غير نشطين','','','ban']]);
$hasF=($_GET['q']??'')!==''||!empty($_GET['dep'])||!empty($_GET['st']);?>
<form class="card filters" method="get" role="search"><div class="search"><?=ic('search')?><input name="q" placeholder="بحث بالاسم أو الرقم أو البريد" value="<?=e($_GET['q']??'')?>" aria-label="بحث"></div>
<select name="dep" aria-label="القسم"><option value="">كل الأقسام</option><?php foreach($deps as $d):?><option value="<?=$d['id']?>" <?=($_GET['dep']??'')==$d['id']?'selected':''?>><?=e($d['name'])?></option><?php endforeach;?></select>
<select name="st" aria-label="الحالة"><option value="">كل الحالات</option><?php foreach(ST as $k=>$v):?><option value="<?=$k?>" <?=($_GET['st']??'')===$k?'selected':''?>><?=$v?></option><?php endforeach;?></select>
<button class="btn"><?=ic('search')?>بحث</button><?php if($hasF):?><a class="btn ghost" href="index.php">مسح التصفية</a><?php endif;?></form>
<div class="card tbl"><table><tr><th>الموظف</th><th>الرقم</th><th>القسم</th><th>الوظيفة</th><th>الحالة</th><th></th></tr>
<?php foreach($rows as $r):?><tr><td class="cwho"><div class="who"><img src="<?=av($r['photo'])?>" alt=""><div><b><?=e($r['full_name'])?></b><small><?=e($r['email'])?></small></div></div></td><td><?=e($r['employee_code'])?></td><td><?=e($r['dn']??'—')?></td><td><?=e($r['pn']??'—')?></td>
<td><?=sb($r['status'],ST[$r['status']])?></td><td class="act"><a class="btn ghost sm" href="view.php?id=<?=$r['id']?>">عرض</a>
<?php if($admin):?><a class="btn ghost sm" href="form.php?id=<?=$r['id']?>"><?=ic('pencil')?>تعديل</a><a class="btn ghost sm" href="view.php?id=<?=$r['id']?>#qr-card"><?=ic('qr')?>QR</a><form method="post" data-confirm="<?=$r['status']==='inactive'?'سيتمكّن الموظف من تسجيل الدخول مجددًا.':'لن يتمكّن الموظف من تسجيل الدخول أو تسجيل الحضور بعد التعطيل.'?>" data-confirm-title="<?=$r['status']==='inactive'?'تفعيل حساب '.e($r['full_name']).'؟':'تعطيل حساب '.e($r['full_name']).'؟'?>" data-confirm-ok="<?=$r['status']==='inactive'?'تفعيل':'تعطيل'?>" <?=$r['status']==='inactive'?'':'data-confirm-tone="danger"'?>><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$r['id']?>"><input type="hidden" name="do" value="<?=$r['status']==='inactive'?'enable':'disable'?>"><button class="btn sm <?=$r['status']==='inactive'?'ghost':'danger'?>"><?=$r['status']==='inactive'?'تفعيل':'تعطيل'?></button></form><?php endif;?></td></tr>
<?php endforeach;if(!$rows)echo empty_row(6,$hasF?'لا يوجد موظفون مطابقون':'لا يوجد موظفون بعد',$hasF?'جرّب تغيير كلمات البحث أو مسح التصفية.':($admin?'ابدأ بإضافة أول موظف.':'لا يوجد موظفون مرتبطون بك.'),'users');?></table></div>
<?php pager($pg['pages'],$page);page_end();
