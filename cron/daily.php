<?php
// تشغيل: php C:\wamp64\www\employee_attendance\cron\daily.php [YYYY-MM-DD]  — آمن للتكرار (Idempotent)
if(PHP_SAPI!=='cli'){http_response_code(403);exit('CLI only');}
require_once __DIR__.'/../includes/leave.php';
$dates=isset($argv[1])?[$argv[1]]:[date('Y-m-d',strtotime('-1 day')),date('Y-m-d')];
$wk=array_map('intval',array_filter(explode(',',(string)setting('weekend_days')),'strlen'));
$ins=$pdo->prepare('INSERT IGNORE INTO attendance(employee_id,work_date,status) VALUES(?,?,?)');$n=0;
foreach($dates as $d){
 $isToday=$d===date('Y-m-d');$hol=(bool)holiday_dates($d,$d);$weekend=in_array((int)date('w',strtotime($d)),$wk,true);
 $emps=$pdo->prepare("SELECT id FROM employees WHERE status<>'inactive' AND hire_date<=?");$emps->execute([$d]);
 $lv=$pdo->prepare("SELECT 1 FROM leave_requests WHERE employee_id=? AND status='approved' AND ? BETWEEN start_date AND end_date");
 foreach($emps->fetchAll(PDO::FETCH_COLUMN) as $id){
  if($hol)$st='holiday';elseif($weekend)$st='weekend';else{$lv->execute([$id,$d]);$st=$lv->fetch()?'on_leave':(($isToday&&date('H:i')<=setting('work_end'))?null:'absent');}
  if($st){$ins->execute([$id,$d,$st]);$n+=$ins->rowCount();}}
 // إجازة معتمدة بعد وجود سجل غياب بدون دخول → تحويله إلى إجازة
 $pdo->prepare("UPDATE attendance a SET status='on_leave' WHERE a.work_date=? AND a.status='absent' AND a.check_in IS NULL AND EXISTS(SELECT 1 FROM leave_requests r WHERE r.employee_id=a.employee_id AND r.status='approved' AND a.work_date BETWEEN r.start_date AND r.end_date)")->execute([$d]);
 $pdo->prepare("UPDATE employees e SET status=IF(EXISTS(SELECT 1 FROM leave_requests r WHERE r.employee_id=e.id AND r.status='approved' AND CURDATE() BETWEEN r.start_date AND r.end_date),'on_leave','active') WHERE e.status IN('active','on_leave')")->execute();}
$pdo->prepare('INSERT INTO audit_logs(action,details) VALUES(?,?)')->execute(['cron_daily',"سجلات جديدة: $n"]);echo "تمت المعالجة. سجلات جديدة: $n\n";
