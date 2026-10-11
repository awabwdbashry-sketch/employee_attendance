<?php
require_once __DIR__.'/functions.php';
const BASE='/employee_attendance';
function flash($m=null,$t='success'){if($m!==null){$_SESSION['flash']=[$m,$t];return;}$f=$_SESSION['flash']??null;unset($_SESSION['flash']);return $f;}
function post_guard(){if($_SERVER['REQUEST_METHOD']==='POST'&&!hash_equals($_SESSION['csrf']??'',$_POST['csrf']??'')){error_page(419,'انتهت صلاحية الجلسة','انتهت صلاحية هذا النموذج. أعد تحميل الصفحة ثم حاول مرة أخرى.','العودة إلى الرئيسية');}}
function upload_photo(array $f):?string{
 if(($f['error']??4)===4)return null;
 if($f['error']!==0||$f['size']>2*1024*1024)throw new Exception('حجم الصورة يجب ألا يتجاوز 2 ميغابايت.');
 $mime=(new finfo(FILEINFO_MIME_TYPE))->file($f['tmp_name']);
 $ext=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'][$mime]??null;
 if(!$ext||!getimagesize($f['tmp_name']))throw new Exception('نوع الصورة غير مسموح (JPG/PNG/WEBP فقط).');
 $n=bin2hex(random_bytes(16)).'.'.$ext;
 if(!move_uploaded_file($f['tmp_name'],__DIR__.'/../uploads/photos/'.$n))throw new Exception('تعذر حفظ الصورة.');
 return $n;
}
function menu():array{
 $r=$_SESSION['role'];$A='admin';$M='manager';$E='employee';
 $m=[['الرئيسية',[['لوحة التحكم','/index.php',[$A,$M,$E]]]],
 ['الموظفون',[['الموظفون','/employees/index.php',[$A]],['فريقي','/employees/index.php',[$M]],['ملفي','/employees/view.php',[$E]],['رمز الحضور','/employees/view.php#qr-card',[$E]],['الأقسام','/departments/index.php',[$A]],['الوظائف','/positions/index.php',[$A]]]],
 ['الحضور',[['ماسح QR','/attendance/scan.php',[$A,$M,$E]],['سجل الحضور','/attendance/history.php',[$A,$M,$E]],['تقويم الحضور','/attendance/calendar.php',[$A,$M,$E]]]],
 ['الإجازات',[['طلب إجازة','/employee/leaves.php?new=1',[$A,$M,$E]],['إجازاتي','/employee/leaves.php',[$A,$M,$E]],['طلبات الإجازات','/leaves/approvals.php',[$A,$M]],['تقويم الإجازات','/leaves/calendar.php',[$A,$M,$E]],['أنواع الإجازات','/leaves/types.php',[$A]],['أرصدة الإجازات','/leaves/balances.php',[$A]]]],
 ['التقارير',[['تقرير الحضور','/reports/attendance.php',[$A,$M]],['تقرير الإجازات','/reports/leaves.php',[$A,$M]],['التقرير الشهري','/reports/monthly.php',[$A,$M]]]],
 ['النظام',[['الإشعارات','/notifications/index.php',[$A,$M,$E]],['الإعدادات','/settings/index.php',[$A]],['العطلات الرسمية','/settings/holidays.php',[$A]],['سجل التدقيق','/settings/audit.php',[$A]]]]];
 $out=[];foreach($m as [$g,$items]){$it=array_filter($items,fn($i)=>in_array($r,$i[2],true));if($it)$out[]=[$g,$it];}return $out;
}
function unread():int{global $pdo;$s=$pdo->prepare('SELECT COUNT(*) FROM notifications WHERE user_id=? AND is_read=0');$s->execute([$_SESSION['uid']]);return (int)$s->fetchColumn();}

/* ================= الواجهة: الأيقونات والمكوّنات المشتركة (عرض فقط) ================= */
function icons():array{static $I=null;if($I)return $I;return $I=[
 'home'=>'<path d="M3 11l9-8 9 8"/><path d="M5 10v10h5v-6h4v6h5V10"/>',
 'users'=>'<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6"/><circle cx="17.5" cy="9" r="2.5"/><path d="M17 14c2.7.2 4.5 2 4.5 5"/>',
 'user'=>'<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-7 8-7s8 2.6 8 7"/>',
 'building'=>'<rect x="5" y="3" width="14" height="18" rx="1"/><path d="M9 7h2M13 7h2M9 11h2M13 11h2M9 15h2M13 15h2M10 21v-3h4v3"/>',
 'briefcase'=>'<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a1 1 0 011-1h4a1 1 0 011 1v2M3 13h18"/>',
 'qr'=>'<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><path d="M14 14h3v3h-3zM20 14v3M14 20h3M20 20h1"/>',
 'scan'=>'<path d="M4 8V5a1 1 0 011-1h3M16 4h3a1 1 0 011 1v3M20 16v3a1 1 0 01-1 1h-3M8 20H5a1 1 0 01-1-1v-3M4 12h16"/>',
 'clock'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
 'calendar'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4"/>',
 'calendar-check'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M3 10h18M8 3v4M16 3v4M9 15l2 2 4-4"/>',
 'sun'=>'<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
 'inbox'=>'<path d="M3 13l3-8h12l3 8v6a1 1 0 01-1 1H4a1 1 0 01-1-1zM3 13h5l1 2h6l1-2h5"/>',
 'check'=>'<path d="M5 12.5l4.5 4.5L19 7"/>',
 'check-circle'=>'<circle cx="12" cy="12" r="9"/><path d="M8 12.5l3 3 5-6"/>',
 'x'=>'<path d="M6 6l12 12M18 6L6 18"/>',
 'x-circle'=>'<circle cx="12" cy="12" r="9"/><path d="M9 9l6 6M15 9l-6 6"/>',
 'bell'=>'<path d="M6 17v-6a6 6 0 0112 0v6l2 2H4zM10 21h4"/>',
 'sliders'=>'<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
 'flag'=>'<path d="M5 21V4M5 4h12l-2 4 2 4H5"/>',
 'shield'=>'<path d="M12 3l8 3v6c0 4.5-3.2 8-8 9-4.8-1-8-4.5-8-9V6z"/><path d="M9 12l2 2 4-4"/>',
 'file'=>'<path d="M7 3h7l5 5v13H7z"/><path d="M14 3v5h5M10 13h6M10 17h6"/>',
 'chart'=>'<path d="M4 20h16M7 20v-8M12 20V6M17 20v-5"/>',
 'download'=>'<path d="M12 4v11M7 11l5 5 5-5M5 20h14"/>',
 'printer'=>'<path d="M7 9V3h10v6M7 17H5a1 1 0 01-1-1v-5a2 2 0 012-2h12a2 2 0 012 2v5a1 1 0 01-1 1h-2M7 14h10v7H7z"/>',
 'plus'=>'<path d="M12 5v14M5 12h14"/>',
 'pencil'=>'<path d="M4 20h4L19 9l-4-4L4 16zM13.5 6.5l4 4"/>',
 'trash'=>'<path d="M4 7h16M10 11v6M14 11v6M6 7l1 13h10l1-13M9 7V4h6v3"/>',
 'filter'=>'<path d="M3 5h18l-7 8v6l-4 2v-8z"/>',
 'search'=>'<circle cx="11" cy="11" r="6.5"/><path d="M16 16l4.5 4.5"/>',
 'menu'=>'<path d="M4 7h16M4 12h16M4 17h16"/>',
 'logout'=>'<path d="M14 4h4a2 2 0 012 2v12a2 2 0 01-2 2h-4M10 16l-4-4 4-4M6 12h10"/>',
 'chev-down'=>'<path d="M6 9l6 6 6-6"/>',
 'chev-left'=>'<path d="M15 6l-6 6 6 6"/>',
 'chev-right'=>'<path d="M9 6l6 6-6 6"/>',
 'alert'=>'<path d="M12 3l10 18H2z"/><path d="M12 10v5M12 18h.01"/>',
 'info'=>'<circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7.5h.01"/>',
 'mail'=>'<rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 7l9 6 9-6"/>',
 'phone'=>'<path d="M5 4h4l2 5-2.5 1.5a11 11 0 005 5L15 13l5 2v4a2 2 0 01-2 2A16 16 0 013 6a2 2 0 012-2z"/>',
 'lock'=>'<rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 018 0v3"/>',
 'paperclip'=>'<path d="M20 11l-8.5 8.5a5 5 0 01-7-7L13 4a3.5 3.5 0 015 5l-8.5 8.5a2 2 0 01-3-3L14 7"/>',
 'scale'=>'<path d="M12 3v18M5 21h14M5 7h14M5 7l-3 7a3.5 3.5 0 006 0zM19 7l-3 7a3.5 3.5 0 006 0z"/>',
 'tag'=>'<path d="M3 12V4h8l10 10-8 8z"/><circle cx="7.5" cy="8.5" r="1.2"/>',
 'copy'=>'<rect x="8" y="8" width="12" height="12" rx="2"/><path d="M16 8V5a1 1 0 00-1-1H5a1 1 0 00-1 1v10a1 1 0 001 1h3"/>',
 'refresh'=>'<path d="M20 11a8 8 0 00-14-4L4 9M4 4v5h5M4 13a8 8 0 0014 4l2-2M20 20v-5h-5"/>',
 'camera'=>'<path d="M4 8h3l2-3h6l2 3h3a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V9a1 1 0 011-1z"/><circle cx="12" cy="13.5" r="3.5"/>',
 'list'=>'<path d="M4 6h16M4 12h16M4 18h10"/>',
 'arrow-right'=>'<path d="M5 12h14M13 6l6 6-6 6"/>',
 'arrow-left'=>'<path d="M19 12H5M11 6l-6 6 6 6"/>',
 'eye'=>'<path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/>',
 'login'=>'<path d="M10 4H6a2 2 0 00-2 2v12a2 2 0 002 2h4M14 8l4 4-4 4M18 12H8"/>',
 'dots'=>'<circle cx="5" cy="12" r="1.3"/><circle cx="12" cy="12" r="1.3"/><circle cx="19" cy="12" r="1.3"/>',
 'ban'=>'<circle cx="12" cy="12" r="9"/><path d="M5.6 5.6l12.8 12.8"/>',
 'undo'=>'<path d="M9 14L4 9l5-5"/><path d="M4 9h10a6 6 0 010 12h-3"/>',
];}
function icon_sprite():string{$s='<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false"><defs>';foreach(icons() as $k=>$p)$s.='<symbol id="i-'.$k.'" viewBox="0 0 24 24">'.$p.'</symbol>';return $s.'</defs></svg>';}
function ic(string $n,string $c=''):string{return '<svg class="i'.($c?' '.$c:'').'" aria-hidden="true"><use href="#i-'.e($n).'"/></svg>';}
/* شارة حالة موحّدة: $k = مفتاح الحالة (present/late/pending/approved/...) */
function sb(string $k,string $label):string{return '<span class="badge '.e($k).'">'.e($label).'</span>';}
/* شريط مؤشرات: [[قيمة, تسمية, نغمة(ok|warn|err|info|''), وصف, أيقونة, رابط]] */
function kpis(array $items):string{$o='<div class="kpis">';foreach($items as $k){[$v,$l]=$k;$t=$k[2]??'';$s=$k[3]??'';$i=$k[4]??'';$h=$k[5]??'';$tag=$h?'a':'div';
 $o.='<'.$tag.' class="kpi '.e($t).'"'.($h?' href="'.e($h).'"':'').'><span class="l">'.($i?ic($i):'').e($l).'</span><b>'.e($v).'</b>'.($s!==''?'<span class="s">'.e($s).'</span>':'').'</'.$tag.'>';}
 return $o.'</div>';}
/* حالة فارغة */
function empty_state(string $title,string $text='',string $action='',string $icon='inbox'):string{return '<div class="empty"><span class="ei">'.ic($icon,'lg').'</span><b>'.e($title).'</b>'.($text!==''?'<p>'.e($text).'</p>':'').$action.'</div>';}
function empty_row(int $cols,string $title,string $text='',string $icon='inbox'):string{return '<tr><td colspan="'.$cols.'" class="empty">'.empty_state($title,$text,'',$icon).'</td></tr>';}
/* نافذة منبثقة: modal_open(id,عنوان,[wide=>bool,desc=>نص,form=>سمات نموذج]) ... modal_close(أزرار التذييل) */
function modal_open(string $id,string $title,array $o=[]):void{$w=!empty($o['wide'])?' wide':'';
 echo '<div class="modal" id="'.e($id).'" hidden role="dialog" aria-modal="true" aria-labelledby="'.e($id).'-t"><div class="mbd"></div><div class="mbox'.$w.'">';
 if(isset($o['form']))echo '<form '.$o['form'].'>';
 echo '<div class="mh"><div><h3 id="'.e($id).'-t">'.e($title).'</h3>'.(!empty($o['desc'])?'<p>'.e($o['desc']).'</p>':'').'</div><button type="button" class="mx" data-close aria-label="إغلاق">'.ic('x').'</button></div><div class="mb'.(!empty($o['grid'])?' form':'').'">';}
function modal_close(string $footer='',bool $form=false):void{echo '</div>'.($footer!==''?'<div class="mf">'.$footer.'</div>':'').($form?'</form>':'').'</div></div>';}
/* عنوان قسم المحتوى داخل البطاقة */
function card_h(string $t,string $d='',string $a=''):string{return '<div class="card-h"><div><h3>'.e($t).'</h3>'.($d!==''?'<p>'.e($d).'</p>':'').'</div>'.($a?'<div class="row">'.$a.'</div>':'').'</div>';}
/* صفحة خطأ مستقلة (403/404/419/...) */
function error_page(int $code,string $title,string $msg,string $btn='العودة إلى الرئيسية',string $href=''):void{
 if(!headers_sent())http_response_code($code);$href=$href?:BASE.'/index.php';
 echo '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).'</title><link rel="icon" href="'.BASE.'/assets/favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="'.BASE.'/assets/css/app.css?v='.@filemtime(__DIR__.'/../assets/css/app.css').'"></head><body><div class="center-page"><main class="errp"><div class="code">'.$code.'</div><h1>'.e($title).'</h1><p>'.e($msg).'</p><a class="btn" href="'.e($href).'">'.e($btn).'</a></main></div></body></html>';exit;}
function flash_icon(string $t):string{return ic($t==='success'?'check':'alert');}

function menu_icons():array{return ['/index.php'=>'home','/employees/index.php'=>'users','/employees/view.php'=>'user','/employees/view.php#qr-card'=>'qr','/departments/index.php'=>'building','/positions/index.php'=>'briefcase','/attendance/scan.php'=>'scan','/attendance/history.php'=>'clock','/attendance/calendar.php'=>'calendar-check','/employee/leaves.php?new=1'=>'plus','/employee/leaves.php'=>'sun','/leaves/approvals.php'=>'inbox','/leaves/calendar.php'=>'calendar','/leaves/types.php'=>'tag','/leaves/balances.php'=>'scale','/reports/attendance.php'=>'chart','/reports/leaves.php'=>'file','/reports/monthly.php'=>'calendar','/notifications/index.php'=>'bell','/settings/index.php'=>'sliders','/settings/holidays.php'=>'flag','/settings/audit.php'=>'shield'];}
function nav_active(string $path):bool{$cur=$_SERVER['SCRIPT_NAME'];$p=parse_url($path,PHP_URL_PATH);$q=parse_url($path,PHP_URL_QUERY);$f=parse_url($path,PHP_URL_FRAGMENT);
 if($cur!==BASE.$p)return false;if($f)return false;if($q){parse_str($q,$a);foreach($a as $k=>$v)if(($_GET[$k]??null)!==$v)return false;return true;}
 if(in_array($p,['/employee/leaves.php'],true)&&isset($_GET['new']))return false;return true;}
function page_start($title){
 $mq=$GLOBALS['pdo']->prepare('SELECT full_name,photo FROM employees WHERE id=?');$mq->execute([$_SESSION['eid']]);$me=$mq->fetch()?:['full_name'=>'','photo'=>null];
 $rl=['admin'=>'الموارد البشرية','manager'=>'مدير','employee'=>'موظف'][$_SESSION['role']]??'';$f=flash();$co=(string)setting('company_name');$nc=unread();$MI=menu_icons();$grp='';
 foreach(menu() as [$g,$items])foreach($items as $i)if(nav_active($i[1])&&!$grp)$grp=$g;
 echo '<!DOCTYPE html><html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="theme-color" content="#f8f5ee"><title>'.e($title).' | '.e($co).'</title><link rel="icon" href="'.BASE.'/assets/favicon.svg" type="image/svg+xml"><link rel="stylesheet" href="'.BASE.'/assets/css/app.css?v='.@filemtime(__DIR__.'/../assets/css/app.css').'"></head><body>'.icon_sprite();
 echo '<a class="sr" href="#main">تخطي إلى المحتوى</a><div class="app"><aside class="side" id="side" aria-label="القائمة الرئيسية"><a class="brand" href="'.BASE.'/index.php"><span class="mark" aria-hidden="true">'.e(mb_substr($co,0,1)?:'ح').'</span><span class="brand-t"><b>'.e($co).'</b><small>نظام الحضور والإجازات</small></span></a><nav>';
 foreach(menu() as [$g,$items]){echo '<h6>'.e($g).'</h6>';foreach($items as $i){echo '<a class="'.(nav_active($i[1])?'on':'').'" href="'.BASE.$i[1].'"'.(nav_active($i[1])?' aria-current="page"':'').'>'.ic($MI[$i[1]]??'list').'<span>'.e($i[0]).'</span>'.(($i[1]==='/notifications/index.php'&&$nc)?'<span class="cnt" data-unread>'.$nc.'</span>':'').'</a>';}}
 echo '</nav><div class="side-f">'.e(date('Y')).' · '.e($co).'</div></aside><div class="main"><header class="top"><button type="button" class="ibtn burger" id="burger" aria-label="فتح القائمة" aria-controls="side" aria-expanded="false">'.ic('menu').'</button>';
 echo '<div class="crumb">'.($grp&&$grp!==$title?'<span class="grp">'.e($grp).'</span><span class="sep">'.ic('chev-left').'</span>':'').'<b>'.e($title).'</b></div><span class="sp"></span>';
 echo '<div class="dd"><button type="button" class="ibtn bell" data-dd="bd" aria-haspopup="true" aria-expanded="false" aria-label="الإشعارات">'.ic('bell').($nc?'<i class="n">'.$nc.'</i>':'').'</button><div class="dd-menu notif" id="bd" hidden><div class="notif-h"><b>الإشعارات</b><a href="#" data-read-all>تعليم الكل كمقروء</a></div><div class="notif-b" id="bd-b"><div class="empty sm">جارٍ التحميل…</div></div><div class="notif-f"><a href="'.BASE.'/notifications/index.php">عرض كل الإشعارات</a></div></div></div>';
 echo '<div class="dd"><button type="button" class="chip" data-dd="um" aria-haspopup="true" aria-expanded="false"><img src="'.($me['photo']?BASE.'/uploads/photos/'.e($me['photo']):BASE.'/assets/css/avatar.svg').'" alt=""><span>'.e($me['full_name']).'<small>'.$rl.'</small></span>'.ic('chev-down').'</button>';
 echo '<div class="dd-menu" id="um" hidden><div class="dd-head"><b>'.e($me['full_name']).'</b><small>'.$rl.'</small></div><hr><a href="'.BASE.'/employees/view.php">'.ic('user').'ملفي الشخصي</a><a href="'.BASE.'/notifications/index.php">'.ic('bell').'الإشعارات</a><hr><form method="post" action="'.BASE.'/logout.php"><input type="hidden" name="csrf" value="'.csrf().'"><button class="mi danger-i" type="submit" data-noload>'.ic('logout').'تسجيل الخروج</button></form></div></div></header>';
 echo '<div class="toasts" id="toasts" aria-live="polite">'.($f?'<div class="toast '.e($f[1]).'" role="'.($f[1]==='error'?'alert':'status').'"><span class="t-ic">'.flash_icon($f[1]).'</span><span class="t-m">'.e($f[0]).'</span><button class="t-x" type="button" aria-label="إغلاق">'.ic('x').'</button></div>':'').'</div><main class="content" id="main">';
}
function page_end(){echo '</main><footer class="foot"><span>'.e(setting('company_name')).'</span><span>نظام إدارة الحضور والإجازات</span></footer></div></div><script>window.APP={csrf:'.json_encode(csrf()).',base:'.json_encode(BASE).'};const CS=window.APP.csrf,B=window.APP.base;</script><script src="'.BASE.'/assets/js/app.js?v='.@filemtime(__DIR__.'/../assets/js/app.js').'"></script></body></html>';}
function ph($t,$d='',$a=''){echo '<div class="ph"><div><h1>'.e($t).'</h1>'.($d!==''?'<p>'.e($d).'</p>':'').'</div>'.($a!==''?'<div class="row noprint">'.$a.'</div>':'').'</div>';}
function pager($pages,$page){if($pages<2)return;$q=$_GET;$u=function($i)use($q){$q['p']=$i;return '?'.e(http_build_query($q));};
 $show=array_unique(array_filter([1,$page-1,$page,$page+1,$pages],fn($i)=>$i>=1&&$i<=$pages));sort($show);
 echo '<nav class="pager" aria-label="التنقل بين الصفحات">';if($page>1)echo '<a href="'.$u($page-1).'" aria-label="السابق">'.ic('chev-right').'</a>';
 $prev=0;foreach($show as $i){if($prev&&$i-$prev>1)echo '<span class="gap">…</span>';echo '<a class="'.($i==$page?'on':'').'" href="'.$u($i).'"'.($i==$page?' aria-current="page"':'').'>'.$i.'</a>';$prev=$i;}
 if($page<$pages)echo '<a href="'.$u($page+1).'" aria-label="التالي">'.ic('chev-left').'</a>';echo '</nav>';}
const ST=['active'=>'نشط','inactive'=>'غير نشط','on_leave'=>'في إجازة'];
/* وصف عربي لرموز سجل التدقيق (عرض فقط؛ القيمة الخام تبقى في قاعدة البيانات) */
function audit_label(string $a):string{static $M=['login'=>'تسجيل دخول','logout'=>'تسجيل خروج','check_in'=>'تسجيل حضور','check_out'=>'تسجيل انصراف','employee_create'=>'إضافة موظف','employee_update'=>'تعديل موظف','employee_enable'=>'تفعيل موظف','employee_disable'=>'تعطيل موظف','leave_request'=>'طلب إجازة','leave_cancel'=>'إلغاء إجازة','leave_approve'=>'موافقة على إجازة','leave_reject'=>'رفض إجازة','leave_auto_approve'=>'اعتماد تلقائي','dept_save'=>'حفظ قسم','dept_delete'=>'حذف قسم','position_save'=>'حفظ وظيفة','position_delete'=>'حذف وظيفة','leave_type_save'=>'حفظ نوع إجازة','leave_type_toggle'=>'تغيير حالة نوع إجازة','leave_type_delete'=>'حذف نوع إجازة','balance_adjust'=>'تعديل رصيد','settings_update'=>'تحديث الإعدادات','holiday_save'=>'حفظ عطلة','holiday_toggle'=>'تغيير حالة عطلة','holiday_delete'=>'حذف عطلة','qr_generate'=>'إنشاء رمز QR','qr_regenerate'=>'إعادة إنشاء رمز QR'];return $M[$a]??$a;}
function audit_tone(string $a):string{return preg_match('/delete|reject|disable/',$a)?'rejected':(preg_match('/login|logout/',$a)?'on_leave':(preg_match('/settings|adjust|regenerate/',$a)?'pending':'present'));}
/* مخطط أعمدة SVG (صفوف حقيقية فقط). الأقدم إلى اليمين لاتجاه RTL */
function cols(array $rows,string $l,string $v,string $unit='حاضر'):string{if(!$rows)return empty_state('لا توجد بيانات بعد','سيظهر المخطط عند تسجيل أول حضور.','','chart');
 $n=count($rows);$W=max(600,$n*84);$H=190;$pt=24;$pb=26;$m=max(1,...array_map(fn($r)=>(int)$r[$v],$rows));$slot=$W/$n;$bw=min(34,$slot*.56);
 $s='<div class="chart"><svg viewBox="0 0 '.$W.' '.$H.'" role="img" aria-label="مخطط أعمدة: '.e($unit).'">';
 foreach([0,.5,1] as $g){$y=$pt+($H-$pt-$pb)*(1-$g);$s.='<line class="grid-l" x1="0" x2="'.$W.'" y1="'.$y.'" y2="'.$y.'"/>';}
 foreach(array_values($rows) as $i=>$r){$val=(int)$r[$v];$h=max(3,($H-$pt-$pb)*$val/$m);$x=$W-($i+1)*$slot+($slot-$bw)/2;$y=$H-$pb-$h;$cx=$x+$bw/2;
  $s.='<path class="col'.($r[$l]===date('m-d')?' today':'').'" tabindex="0" data-tip="'.e($r[$l].' · '.$val.' '.$unit).'" d="M'.$x.' '.($H-$pb).'V'.($y+4).'q0-4 4-4h'.($bw-8).'q4 0 4 4V'.($H-$pb).'z"/>';
  $s.='<text class="val" x="'.$cx.'" y="'.($y-6).'" text-anchor="middle">'.$val.'</text><text x="'.$cx.'" y="'.($H-8).'" text-anchor="middle">'.e($r[$l]).'</text>';}
 return $s.'</svg></div>';}
/* أشرطة أفقية رفيعة */
function hbars(array $rows,string $l,string $v,string $unit=''):string{if(!$rows)return empty_state('لا توجد بيانات','','','chart');$m=max(1,...array_map(fn($r)=>(int)$r[$v],$rows));$o='<div class="hbar">';
 foreach($rows as $r)$o.='<div class="hbar-r" tabindex="0" data-tip="'.e($r[$l].' · '.(int)$r[$v].($unit?' '.$unit:'')).'"><span class="n">'.e($r[$l]).'</span><span class="tr"><i style="width:'.max(2,round($r[$v]/$m*100)).'%"></i></span><b>'.(int)$r[$v].'</b></div>';
 return $o.'</div>';}
/* شريط حالة اليوم: [[لون, تسمية, عدد], ...] */
function day_split(array $parts):string{$t=array_sum(array_column($parts,2));if($t<=0)return '';$o='<div class="stack-bar" role="img" aria-label="توزيع الحالات اليوم">';
 foreach($parts as [$c,$lb,$n])if($n>0)$o.='<i class="'.$c.'" style="flex:'.$n.'" data-tip="'.e($lb.' · '.$n).'"></i>';$o.='</div><div class="legend-l">';
 foreach($parts as [$c,$lb,$n])$o.='<span class="it"><i class="'.$c.'"></i>'.e($lb).' <b>'.$n.'</b></span>';return $o.'</div>';}
const SA=['present'=>'حاضر','late'=>'متأخر','absent'=>'غائب','half_day'=>'نصف يوم','on_leave'=>'في إجازة','holiday'=>'عطلة رسمية','weekend'=>'عطلة أسبوعية'];
function hm($m):string{$m=(int)$m;return floor($m/60).':'.str_pad((string)($m%60),2,'0',STR_PAD_LEFT);}
function av(?string $p):string{return $p?BASE.'/uploads/photos/'.e($p):BASE.'/assets/css/avatar.svg';}
function paginate($total,$per,$page){return ['pages'=>max(1,(int)ceil($total/$per)),'offset'=>($page-1)*$per];}
function ar_month(string $ym):string{static $M=['','يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];return $M[(int)substr($ym,5,2)].' '.substr($ym,0,4);}
function date_label(string $name,string $inputHtml):string{return '<label>'.e($name).$inputHtml.'</label>';}
