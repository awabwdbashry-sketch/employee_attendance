<?php require_once __DIR__.'/includes/layout.php';require_login();require_once __DIR__.'/includes/dash.php';if($_SESSION['role']==='employee'){emp_dash();exit;}if($_SESSION['role']==='manager'){mgr_dash();exit;}
function q($sql,$p=[]){global $pdo;$s=$pdo->prepare($sql);$s->execute($p);return (int)$s->fetchColumn();}
$d=date('Y-m-d');
$total=q("SELECT COUNT(*) FROM employees WHERE status<>'inactive'");
$present=q("SELECT COUNT(*) FROM attendance WHERE work_date=? AND status IN('present','late','half_day')",[$d]);
$late=q("SELECT COUNT(*) FROM attendance WHERE work_date=? AND status='late'",[$d]);
$leave=q("SELECT COUNT(*) FROM leave_requests WHERE status='approved' AND ? BETWEEN start_date AND end_date",[$d]);
$pending=q("SELECT COUNT(*) FROM leave_requests WHERE status='pending'");
page_start('لوحة التحكم');
$hn=$pdo->prepare('SELECT full_name FROM employees WHERE id=?');$hn->execute([$_SESSION['eid']]);require_once __DIR__.'/includes/attendance.php';
$ro=$_SESSION['role'];$sc=$ro==='admin'?'':($ro==='manager'?' AND e.manager_id='.(int)$_SESSION['eid']:' AND e.id='.(int)$_SESSION['eid']);
$lq=fn($st)=>q("SELECT COUNT(*) FROM leave_requests r JOIN employees e ON e.id=r.employee_id WHERE r.status='$st'$sc");
$nowLeave=q("SELECT COUNT(*) FROM leave_requests r JOIN employees e ON e.id=r.employee_id WHERE r.status='approved' AND CURDATE() BETWEEN r.start_date AND r.end_date$sc");
$pa=$pdo->query("SELECT r.id,e.full_name,e.photo,t.name tn,r.start_date,r.end_date,r.days FROM leave_requests r JOIN employees e ON e.id=r.employee_id JOIN leave_types t ON t.id=r.leave_type_id WHERE r.status='pending' ORDER BY r.id DESC LIMIT 5")->fetchAll();
$ra=$pdo->query("SELECT l.action,l.details,l.created_at,u.email FROM audit_logs l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.id DESC LIMIT 8")->fetchAll();
$d7=$pdo->query("SELECT DATE_FORMAT(work_date,'%m-%d') d,SUM(status IN('present','late','half_day')) c FROM attendance WHERE work_date>=CURDATE()-INTERVAL 6 DAY GROUP BY work_date ORDER BY work_date")->fetchAll();
$lt=$pdo->query("SELECT t.name n,COALESCE(SUM(r.days),0) c FROM leave_types t LEFT JOIN leave_requests r ON r.leave_type_id=t.id AND r.status='approved' GROUP BY t.id ORDER BY c DESC")->fetchAll();
$dp=$pdo->query("SELECT d.name n,COUNT(a.id) c FROM departments d LEFT JOIN employees e ON e.department_id=d.id LEFT JOIN attendance a ON a.employee_id=e.id AND a.work_date=CURDATE() AND a.status IN('present','late','half_day') GROUP BY d.id")->fetchAll();
$absent=max(0,$total-$present-$leave);?>
<div class="hello"><div><h1>مرحبًا، <?=e($hn->fetchColumn())?></h1><span><?=e(ar_date(time()))?></span></div><div class="row noprint"><a class="btn ghost" href="attendance/scan.php"><?=ic('scan')?>تسجيل الحضور</a><a class="btn ghost" href="employee/leaves.php?new=1"><?=ic('plus')?>طلب إجازة</a><?php if($ro==='admin'):?><a class="btn" href="employees/form.php"><?=ic('plus')?>إضافة موظف</a><?php endif;?></div></div>
<?=kpis([[$total,'إجمالي الموظفين','','','users','employees/index.php'],[$present,'الحاضرون اليوم','ok','','check-circle'],[$late,'المتأخرون','warn','','clock'],[$absent,'الغائبون','err','','x-circle'],[$leave,'في إجازة','info','','sun'],[$pending,'طلبات معلقة','','','inbox','leaves/approvals.php']])?>
<div class="split"><div class="card"><?=card_h('حالة اليوم','توزيع الموظفين النشطين')?><?=$total?day_split([['ok','حاضر في الوقت',max(0,$present-$late)],['warn','متأخر',$late],['info','في إجازة',$leave],['err','غائب / لم يسجل',$absent]]):empty_state('لا يوجد موظفون نشطون','','','users')?></div>
<div class="card"><?=card_h('طلبات الإجازة','حسب الحالة')?><div class="kv"><div><span>قيد المراجعة</span><b><a href="leaves/approvals.php?st=pending"><?=$lq('pending')?></a></b></div><div><span>موافق عليها</span><b><a href="leaves/approvals.php?st=approved"><?=$lq('approved')?></a></b></div><div><span>مرفوضة</span><b><a href="leaves/approvals.php?st=rejected"><?=$lq('rejected')?></a></b></div><div><span>في إجازة الآن</span><b><?=$nowLeave?></b></div></div></div></div>
<div class="split"><div class="card tbl"><?=card_h('طلبات بانتظار الإجراء','أحدث خمسة طلبات معلقة','<a class="btn sm ghost" href="leaves/approvals.php">كل الطلبات</a>')?><table><tr><th>الموظف</th><th>النوع</th><th>الفترة</th><th>الأيام</th><th></th></tr><?php foreach($pa as $x):?><tr><td class="cwho"><div class="who"><img src="<?=av($x['photo'])?>" alt=""><b><?=e($x['full_name'])?></b></div></td><td><?=e($x['tn'])?></td><td><?=e($x['start_date'])?> ← <?=e($x['end_date'])?></td><td><?=$x['days']+0?></td><td class="act"><a class="btn sm" href="leaves/approvals.php">مراجعة الطلب</a></td></tr><?php endforeach;if(!$pa)echo empty_row(5,'لا توجد طلبات معلقة','كل الطلبات تمت معالجتها.','check-circle');?></table></div>
<div class="card"><?=card_h('آخر العمليات','','<a class="btn sm ghost" href="settings/audit.php">السجل الكامل</a>')?><ul class="tl"><?php foreach($ra as $x)echo '<li><span class="tl-t">'.e(audit_label((string)$x['action'])).'</span>'.($x['details']!==''&&$x['details']!==null?' — '.e(mb_substr((string)$x['details'],0,60)):'').'<small>'.e($x['email']??'النظام').' · '.e($x['created_at']).'</small></li>';if(!$ra)echo '<li class="empty">لا توجد عمليات.</li>';?></ul></div></div>
<div class="card"><?=card_h('الحضور في آخر 7 أيام','عدد الحاضرين يوميًا')?><?=cols($d7,'d','c','حاضر')?></div>
<div class="two"><div class="card"><?=card_h('الإجازات المعتمدة حسب النوع','بالأيام')?><?=hbars($lt,'n','c','يوم')?></div><div class="card"><?=card_h('حضور الأقسام اليوم')?><?=hbars($dp,'n','c','حاضر')?></div></div>
<?php page_end();
