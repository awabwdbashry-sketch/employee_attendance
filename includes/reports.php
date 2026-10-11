<?php require_once __DIR__.'/leave.php';
function report_guard(){require_login(['admin','manager']);}
function scope(string $al='e'):array{return $_SESSION['role']==='admin'?['1=1',[]]:["$al.manager_id=?",[$_SESSION['eid']]];}
function dt($k,$d){return preg_match('/^\d{4}-\d{2}-\d{2}$/',$_GET[$k]??'')?$_GET[$k]:$d;}
function csv_out(string $name,array $h,array $rows){header('Content-Type: text/csv; charset=utf-8');header('Content-Disposition: attachment; filename="'.$name.'-'.date('Ymd').'.csv"');
 echo "\xEF\xBB\xBF";$o=fopen('php://output','w');fputcsv($o,$h);foreach($rows as $r)fputcsv($o,$r);fclose($o);exit;}
function filter_opts(){global $pdo;[$w,$p]=scope();$s=$pdo->prepare("SELECT e.id,e.full_name FROM employees e WHERE $w ORDER BY full_name");$s->execute($p);return [$s->fetchAll(),$pdo->query('SELECT * FROM departments ORDER BY name')->fetchAll()];}
function report_view(string $title,array $h,array $rows,string $extra='',array $sum=[]){
 page_start($title);$exp='?'.e(http_build_query($_GET+['x'=>1])).'&export=1';
 ph($title,'تقرير قابل للتصدير والطباعة حسب الفلاتر المختارة','<button type="button" class="btn ghost" onclick="window.print()">'.ic('printer').'طباعة</button><a class="btn gold" href="'.$exp.'">'.ic('download').'تصدير CSV</a>');
 $tone=function($k){return preg_match('/غياب|غائب/u',$k)?'err':(preg_match('/تأخير|متأخر|قيد|معلق/u',$k)?'warn':(preg_match('/حضور|حاضر|معتمد|موافق|ساعات/u',$k)?'ok':(preg_match('/إجاز/u',$k)?'info':'')));};
 $k=[[count($rows),'عدد السجلات','','','list']];foreach($sum as $l=>$v)$k[]=[$v,$l,$tone($l)];echo kpis($k);
 [$emps,$deps]=filter_opts();$g=fn($k)=>$_GET[$k]??'';
 echo '<form class="card filters" method="get">'.$extra.'<label>الموظف<select name="emp"><option value="">كل الموظفين</option>';foreach($emps as $x)echo '<option value="'.$x['id'].'" '.($g('emp')==$x['id']?'selected':'').'>'.e($x['full_name']).'</option>';
 echo '</select></label><label>القسم<select name="dep"><option value="">كل الأقسام</option>';foreach($deps as $x)echo '<option value="'.$x['id'].'" '.($g('dep')==$x['id']?'selected':'').'>'.e($x['name']).'</option>';
 echo '</select></label><button class="btn">'.ic('filter').'عرض</button></form><div class="card tbl"><table><tr>';
 foreach($h as $c)echo '<th>'.e($c).'</th>';echo '</tr>';foreach($rows as $r){echo '<tr>';foreach($r as $n=>$c)echo '<td'.($n===0?' class="cmain"':'').'>'.e($c).'</td>';echo '</tr>';}
 if(!$rows)echo empty_row(count($h),'لا توجد بيانات ضمن الفلاتر','غيّر الفترة أو الفلاتر لعرض نتائج أخرى.','file');echo '</table></div>';page_end();}
function common_filters(array &$w,array &$p){if(!empty($_GET['emp'])){$w[]='e.id=?';$p[]=(int)$_GET['emp'];}if(!empty($_GET['dep'])){$w[]='e.department_id=?';$p[]=(int)$_GET['dep'];}}
