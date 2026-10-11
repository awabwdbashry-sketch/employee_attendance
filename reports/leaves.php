<?php require_once __DIR__.'/../includes/reports.php';report_guard();global $pdo;
[$w0,$p]=scope();$w=[$w0,'r.start_date<=?','r.end_date>=?'];$p[]=$t=dt('to',date('Y-m-t'));$p[]=$f=dt('from',date('Y-m-01'));common_filters($w,$p);
if(!empty($_GET['type'])){$w[]='r.leave_type_id=?';$p[]=(int)$_GET['type'];}if(!empty($_GET['st'])&&isset(LS[$_GET['st']])){$w[]='r.status=?';$p[]=$_GET['st'];}
$s=$pdo->prepare('SELECT e.full_name,t.name tn,r.start_date,r.end_date,r.days,r.status FROM leave_requests r JOIN employees e ON e.id=r.employee_id JOIN leave_types t ON t.id=r.leave_type_id WHERE '.implode(' AND ',$w).' ORDER BY r.start_date DESC LIMIT 2000');$s->execute($p);
$rows=array_map(fn($r)=>[$r['full_name'],$r['tn'],$r['start_date'],$r['end_date'],$r['days']+0,LS[$r['status']]],$s->fetchAll());$h=['الموظف','النوع','البداية','النهاية','الأيام','الحالة'];
if(isset($_GET['export']))csv_out('leaves',$h,$rows);
$cn=array_count_values(array_column($rows,5));$sum=[LS['pending']=>$cn[LS['pending']]??0,LS['approved']=>$cn[LS['approved']]??0,LS['rejected']=>$cn[LS['rejected']]??0,'أيام الإجازات المعتمدة'=>array_sum(array_map(fn($r)=>$r[5]===LS['approved']?$r[4]:0,$rows))];
$ex='<label>من<input type="date" name="from" value="'.e($f).'"></label><label>إلى<input type="date" name="to" value="'.e($t).'"></label><label>النوع<select name="type"><option value="">كل الأنواع</option>';foreach($pdo->query('SELECT id,name FROM leave_types') as $x)$ex.='<option value="'.$x['id'].'" '.(($_GET['type']??'')==$x['id']?'selected':'').'>'.e($x['name']).'</option>';
$ex.='</select></label><label>الحالة<select name="st"><option value="">كل الحالات</option>';foreach(LS as $k=>$v)$ex.='<option value="'.$k.'" '.(($_GET['st']??'')===$k?'selected':'').'>'.$v.'</option>';
report_view('تقرير الإجازات',$h,$rows,$ex.'</select></label>',$sum);
