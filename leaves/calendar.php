<?php require_once __DIR__.'/../includes/leave.php';require_login();
$m=preg_match('/^\d{4}-\d{2}$/',$_GET['m']??'')?$_GET['m']:date('Y-m');$first=strtotime("$m-01");$last=date('Y-m-t',$first);
$w=['r.status IN(\'pending\',\'approved\')','r.start_date<=?','r.end_date>=?'];$p=[$last,"$m-01"];
if($_SESSION['role']==='employee'){$w[]='r.employee_id=?';$p[]=$_SESSION['eid'];}
elseif($_SESSION['role']==='manager'){$w[]='(e.manager_id=? OR r.employee_id=?)';array_push($p,$_SESSION['eid'],$_SESSION['eid']);}
$s=$pdo->prepare('SELECT r.*,e.full_name,t.name tn FROM leave_requests r JOIN employees e ON e.id=r.employee_id JOIN leave_types t ON t.id=r.leave_type_id WHERE '.implode(' AND ',$w));$s->execute($p);$L=$s->fetchAll();
$h=$pdo->prepare('SELECT * FROM holidays WHERE holiday_date BETWEEN ? AND ?');$h->execute(["$m-01",$last]);$H=array_column($h->fetchAll(),'name','holiday_date');
page_start('تقويم الإجازات');$pad=(int)date('w',$first);ph('تقويم الإجازات','الإجازات المعتمدة والمعلّقة والعطلات الرسمية خلال الشهر');?>
<div class="card"><div class="cal-head"><h3><?=e(ar_month($m))?></h3><div class="row noprint"><a class="btn ghost sm" href="?m=<?=date('Y-m',strtotime('-1 month',$first))?>"><?=ic('chev-right')?>السابق</a><a class="btn ghost sm" href="?m=<?=date('Y-m')?>">هذا الشهر</a><a class="btn ghost sm" href="?m=<?=date('Y-m',strtotime('+1 month',$first))?>">التالي<?=ic('chev-left')?></a></div></div>
<div class="cal"><?php foreach(['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'] as $d)echo '<div class="h">'.$d.'</div>';
for($i=0;$i<$pad;$i++)echo '<div class="pad"></div>';
for($d=1;$d<=(int)date('t',$first);$d++){$k=sprintf('%s-%02d',$m,$d);echo '<div class="'.(isset($H[$k])?'hol':'').($k===date('Y-m-d')?' is-today':'').'"><b>'.$d.'</b>'.(isset($H[$k])?'<small>'.e($H[$k]).'</small>':'');
 foreach($L as $r)if($r['start_date']<=$k&&$r['end_date']>=$k)echo '<small class="'.($r['status']==='pending'?'p':'').'" title="'.e($r['tn'].($r['status']==='pending'?' (قيد المراجعة)':'')).'">'.e($r['full_name']).'</small>';echo '</div>';}?></div>
<div class="legend"><span class="lg-i"><i class="appr"></i>إجازة معتمدة</span><span class="lg-i"><i class="pend"></i>قيد المراجعة</span><span class="lg-i"><i class="hol"></i>عطلة رسمية</span></div></div>
<?php page_end();
