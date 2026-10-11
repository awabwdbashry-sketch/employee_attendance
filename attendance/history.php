<?php require_once __DIR__.'/../includes/leave.php';require_login();
$SA=['present'=>'حاضر','late'=>'متأخر','absent'=>'غائب','half_day'=>'نصف يوم','on_leave'=>'في إجازة','holiday'=>'عطلة رسمية','weekend'=>'عطلة أسبوعية'];
$w=['1=1'];$p=[];$r=$_SESSION['role'];
if($r==='employee'){$w[]='a.employee_id=?';$p[]=$_SESSION['eid'];}
elseif($r==='manager'){$w[]='(e.manager_id=? OR e.id=?)';array_push($p,$_SESSION['eid'],$_SESSION['eid']);}
if($r!=='employee'&&!empty($_GET['emp'])){$w[]='a.employee_id=?';$p[]=(int)$_GET['emp'];}
if($r!=='employee'&&!empty($_GET['dep'])){$w[]='e.department_id=?';$p[]=(int)$_GET['dep'];}
if(!empty($_GET['st'])&&isset($SA[$_GET['st']])){$w[]='a.status=?';$p[]=$_GET['st'];}
$from=preg_match('/^\d{4}-\d{2}-\d{2}$/',$_GET['from']??'')?$_GET['from']:date('Y-m-01');$to=preg_match('/^\d{4}-\d{2}-\d{2}$/',$_GET['to']??'')?$_GET['to']:date('Y-m-d');
$w[]='a.work_date BETWEEN ? AND ?';array_push($p,$from,$to);$W=implode(' AND ',$w);
$sm=$pdo->prepare("SELECT a.status,COUNT(*) FROM attendance a JOIN employees e ON e.id=a.employee_id WHERE $W GROUP BY a.status");$sm->execute($p);$sm=$sm->fetchAll(PDO::FETCH_KEY_PAIR);
$page=max(1,(int)($_GET['p']??1));$per=15;$c=$pdo->prepare("SELECT COUNT(*) FROM attendance a JOIN employees e ON e.id=a.employee_id WHERE $W");$c->execute($p);$pg=paginate((int)$c->fetchColumn(),$per,$page);
$s=$pdo->prepare("SELECT a.*,e.full_name FROM attendance a JOIN employees e ON e.id=a.employee_id WHERE $W ORDER BY a.work_date DESC,e.full_name LIMIT $per OFFSET {$pg['offset']}");$s->execute($p);$rows=$s->fetchAll();
$emps=$r==='employee'?[]:$pdo->query($r==='admin'?'SELECT id,full_name FROM employees ORDER BY full_name':'SELECT id,full_name FROM employees WHERE manager_id='.(int)$_SESSION['eid'].' ORDER BY full_name')->fetchAll();
$deps=$pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll();$hm=fn($m)=>floor($m/60).':'.str_pad($m%60,2,'0',STR_PAD_LEFT);
page_start('سجل الحضور');ph('سجل الحضور',$r==='employee'?'سجل حضورك وانصرافك خلال الفترة المحددة':'سجل الحضور والانصراف لموظفيك خلال الفترة المحددة');
echo kpis([[(int)(($sm['present']??0)+($sm['late']??0)+($sm['half_day']??0)),'حضور','ok','','check-circle'],[(int)($sm['late']??0),'متأخر','warn','','clock'],[(int)($sm['absent']??0),'غائب','err','','x-circle'],[(int)($sm['on_leave']??0),'إجازة','info','','sun']]);?>
<form class="card filters" method="get"><?php if($r!=='employee'):?><label>الموظف<select name="emp"><option value="">كل الموظفين</option><?php foreach($emps as $x)echo '<option value="'.$x['id'].'" '.(($_GET['emp']??'')==$x['id']?'selected':'').'>'.e($x['full_name']).'</option>';?></select></label>
<label>القسم<select name="dep"><option value="">كل الأقسام</option><?php foreach($deps as $x)echo '<option value="'.$x['id'].'" '.(($_GET['dep']??'')==$x['id']?'selected':'').'>'.e($x['name']).'</option>';?></select></label><?php endif;?>
<label>الحالة<select name="st"><option value="">كل الحالات</option><?php foreach($SA as $k=>$v)echo '<option value="'.$k.'" '.(($_GET['st']??'')===$k?'selected':'').'>'.$v.'</option>';?></select></label>
<label>من<input type="date" name="from" value="<?=e($from)?>"></label><label>إلى<input type="date" name="to" value="<?=e($to)?>"></label><button class="btn"><?=ic('filter')?>تصفية</button></form>
<div class="card tbl"><table><tr><th>الموظف</th><th>التاريخ</th><th>الدخول</th><th>الانصراف</th><th>ساعات العمل</th><th>التأخير (د)</th><th>الخروج المبكر (د)</th><th>إضافي (د)</th><th>الحالة</th></tr>
<?php foreach($rows as $a):?><tr><td class="cmain"><b><?=e($a['full_name'])?></b></td><td><?=e($a['work_date'])?></td><td><?=e($a['check_in']?:'—')?></td><td><?=e($a['check_out']?:'—')?></td><td><?=$hm($a['work_minutes'])?></td><td><?=$a['late_minutes']>0?'<span class="badge late plain">'.(int)$a['late_minutes'].'</span>':'<span class="muted">0</span>'?></td><td><?=$a['early_leave_minutes']>0?'<span class="badge half_day plain">'.(int)$a['early_leave_minutes'].'</span>':'<span class="muted">0</span>'?></td><td><?=$a['overtime_minutes']>0?'<span class="badge present plain">'.(int)$a['overtime_minutes'].'</span>':'<span class="muted">0</span>'?></td><td><?=sb($a['status'],$SA[$a['status']])?></td></tr>
<?php endforeach;if(!$rows)echo empty_row(9,'لا توجد سجلات ضمن الفلاتر المختارة','غيّر الفترة أو الحالة لعرض سجلات أخرى.','clock');?></table></div>
<?php pager($pg['pages'],$page);page_end();
