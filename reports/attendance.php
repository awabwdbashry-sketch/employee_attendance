<?php require_once __DIR__.'/../includes/reports.php';report_guard();global $pdo;
$SA=['present'=>'حاضر','late'=>'متأخر','absent'=>'غائب','half_day'=>'نصف يوم','on_leave'=>'في إجازة','holiday'=>'عطلة رسمية','weekend'=>'عطلة أسبوعية'];
[$w0,$p]=scope();$w=[$w0,'a.work_date BETWEEN ? AND ?'];$p[]=$f=dt('from',date('Y-m-01'));$p[]=$t=dt('to',date('Y-m-d'));common_filters($w,$p);
if(!empty($_GET['st'])&&isset($SA[$_GET['st']])){$w[]='a.status=?';$p[]=$_GET['st'];}
$s=$pdo->prepare('SELECT e.full_name,a.work_date,a.status,a.check_in,a.check_out,a.work_minutes,a.late_minutes,a.early_leave_minutes,a.overtime_minutes FROM attendance a JOIN employees e ON e.id=a.employee_id WHERE '.implode(' AND ',$w).' ORDER BY a.work_date DESC,e.full_name LIMIT 2000');$s->execute($p);
$rows=array_map(fn($r)=>[$r['full_name'],$r['work_date'],$SA[$r['status']],$r['check_in']?:'',$r['check_out']?:'',round($r['work_minutes']/60,2),$r['late_minutes'],$r['early_leave_minutes'],$r['overtime_minutes']],$s->fetchAll());
$h=['الموظف','التاريخ','الحالة','الدخول','الانصراف','ساعات العمل','التأخير (د)','الخروج المبكر (د)','إضافي (د)'];
if(isset($_GET['export']))csv_out('attendance',$h,$rows);
$cn=array_count_values(array_column($rows,2));$sum=['حاضر'=>$cn['حاضر']??0,'متأخر'=>$cn['متأخر']??0,'غائب'=>$cn['غائب']??0,'في إجازة'=>$cn['في إجازة']??0,'إجمالي التأخير (د)'=>array_sum(array_column($rows,6)),'إجمالي الإضافي (د)'=>array_sum(array_column($rows,8))];
$st='<label>الحالة<select name="st"><option value="">كل الحالات</option>';foreach($SA as $k=>$v)$st.='<option value="'.$k.'" '.(($_GET['st']??'')===$k?'selected':'').'>'.$v.'</option>';
report_view('تقرير الحضور',$h,$rows,'<label>من<input type="date" name="from" value="'.e($f).'"></label><label>إلى<input type="date" name="to" value="'.e($t).'"></label>'.$st.'</select></label>',$sum);
