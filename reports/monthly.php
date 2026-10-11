<?php require_once __DIR__.'/../includes/reports.php';report_guard();global $pdo;
$m=preg_match('/^\d{4}-\d{2}$/',$_GET['month']??'')?$_GET['month']:date('Y-m');$a="$m-01";$b=date('Y-m-t',strtotime($a));$wd=working_days($a,$b);
[$w0,$p]=scope();$w=[$w0,"e.status<>'inactive'"];common_filters($w,$p);array_unshift($p,$a,$b);
$s=$pdo->prepare("SELECT e.full_name,SUM(a.status IN('present','late','half_day')) pr,SUM(a.status='late') lt,SUM(a.status='absent') ab,SUM(a.status='on_leave') lv,COALESCE(SUM(a.work_minutes),0) wm,COALESCE(SUM(a.overtime_minutes),0) ot FROM employees e LEFT JOIN attendance a ON a.employee_id=e.id AND a.work_date BETWEEN ? AND ? WHERE ".implode(' AND ',$w).' GROUP BY e.id ORDER BY e.full_name');$s->execute($p);
$rows=array_map(fn($r)=>[$r['full_name'],$wd,(int)$r['pr'],(int)$r['ab'],(int)$r['lt'],(int)$r['lv'],round($r['wm']/60,2),(int)$r['ot']],$s->fetchAll());
$h=['الموظف','أيام العمل','الحضور','الغياب','التأخير','الإجازات','ساعات العمل','إضافي (د)'];if(isset($_GET['export']))csv_out('monthly-'.$m,$h,$rows);
$sum=['إجمالي الحضور'=>array_sum(array_column($rows,2)),'إجمالي الغياب'=>array_sum(array_column($rows,3)),'إجمالي التأخير'=>array_sum(array_column($rows,4)),'إجمالي الإجازات'=>array_sum(array_column($rows,5)),'ساعات العمل'=>round(array_sum(array_column($rows,6)),2),'الإضافي (د)'=>array_sum(array_column($rows,7))];
report_view('التقرير الشهري',$h,$rows,'<label>الشهر<input type="month" name="month" value="'.e($m).'"></label>',$sum);
