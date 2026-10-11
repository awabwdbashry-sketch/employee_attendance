<?php require_once __DIR__.'/leave.php';
// المنطق المشترك لتسجيل الحضور/الانصراف (يستخدمه الماسح القديم ورابط QR الذكي)
function public_base():string{$u=trim((string)setting('public_url'));if($u!=='')return rtrim($u,'/');
 $https=(!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off');return ($https?'https':'http').'://'.($_SERVER['HTTP_HOST']??'localhost').BASE;}
function ar_time(int $ts):string{return date('h:i',$ts).' '.(date('A',$ts)==='AM'?'ص':'م');}
function ar_date(int $ts):string{$d=['الأحد','الإثنين','الثلاثاء','الأربعاء','الخميس','الجمعة','السبت'];$m=['','يناير','فبراير','مارس','أبريل','مايو','يونيو','يوليو','أغسطس','سبتمبر','أكتوبر','نوفمبر','ديسمبر'];
 return $d[(int)date('w',$ts)].'، '.(int)date('j',$ts).' '.$m[(int)date('n',$ts)].' '.date('Y',$ts);}
function record_attendance(array $emp):array{global $pdo;
 $b=['name'=>$emp['full_name'],'photo'=>$emp['photo'],'code'=>$emp['employee_code'],'time'=>date('H:i:s'),'ts'=>time()];$blk=fn($m,$l='warn')=>$b+['ok'=>false,'kind'=>'blocked','level'=>$l,'msg'=>$m];
 $today=date('Y-m-d');$now=date('H:i:s');
 if(holiday_dates($today,$today))return $blk('اليوم عطلة رسمية، لا يتم تسجيل حضور.');
 $wk=array_map('intval',array_filter(explode(',',(string)setting('weekend_days')),'strlen'));
 if(in_array((int)date('w'),$wk,true))return $blk('اليوم عطلة أسبوعية، لا يتم تسجيل حضور.');
 $l=$pdo->prepare("SELECT 1 FROM leave_requests WHERE employee_id=? AND status='approved' AND ? BETWEEN start_date AND end_date");$l->execute([$emp['id'],$today]);
 if($l->fetch())return $blk('أنت في إجازة معتمدة اليوم.');
 try{$pdo->beginTransaction();
  $a=$pdo->prepare('SELECT * FROM attendance WHERE employee_id=? AND work_date=? FOR UPDATE');$a->execute([$emp['id'],$today]);$row=$a->fetch();
  if(!$row||!$row['check_in']){$c=calc_status($now);
   if($row)$pdo->prepare('UPDATE attendance SET check_in=?,status=?,late_minutes=? WHERE id=?')->execute([$now,$c['status'],$c['lateMin'],$row['id']]);
   else $pdo->prepare('INSERT INTO attendance(employee_id,work_date,check_in,status,late_minutes) VALUES(?,?,?,?,?)')->execute([$emp['id'],$today,$now,$c['status'],$c['lateMin']]);
   audit('check_in',$emp['full_name']);$pdo->commit();$late=$c['status']==='late';
   return $b+['ok'=>true,'kind'=>'in','level'=>$late?'warn':'success','msg'=>$late?'🟡 تم تسجيل الحضور مع التأخير':'✅ تم تسجيل الحضور','status'=>$late?'متأخر':'حاضر','late'=>$c['lateMin']];}
  if($row['check_out']){$pdo->rollBack();return $blk('تم تسجيل الانصراف لهذا الموظف بالفعل.');}
  if(strtotime($now)-strtotime($row['check_in'])<60){$pdo->rollBack();return $blk('تم تسجيل الحضور قبل لحظات. انتظر دقيقة قبل تسجيل الانصراف.');}
  $c=calc_status($row['check_in'],$now);$st=$c['status']==='half_day'?'half_day':$row['status'];
  $pdo->prepare('UPDATE attendance SET check_out=?,status=?,work_minutes=?,early_leave_minutes=?,overtime_minutes=? WHERE id=?')->execute([$now,$st,$c['work'],$c['early'],$c['ot'],$row['id']]);
  audit('check_out',$emp['full_name']);$pdo->commit();
  return $b+['ok'=>true,'kind'=>'out','level'=>'success','msg'=>'✅ تم تسجيل الانصراف','status'=>'انصراف','work'=>$c['work'],'early'=>$c['early'],'ot'=>$c['ot']];
 }catch(PDOException $x){if($pdo->inTransaction())$pdo->rollBack();return $blk('تعذر تسجيل العملية، حاول مرة أخرى.','error');}}

// ===== QR متجدد موقّع (بدون تغيير قاعدة البيانات): الرابط يتغير كل 45 ثانية ويُستهلك مرة واحدة =====
const QR_WIN=45;
function qr_secret():string{global $pdo;$s=setting('qr_secret');if(!$s){$s=bin2hex(random_bytes(32));$pdo->prepare("INSERT IGNORE INTO settings(`key`,`value`) VALUES('qr_secret',?)")->execute([$s]);$s=setting('qr_secret');}return (string)$s;}
function qr_sig(string $secret,string $token,int $win):string{return substr(hash_hmac('sha256',$token.'|'.$win,$secret),0,32);}
function make_qr_url(string $token):string{$w=intdiv(time(),QR_WIN);return public_base().'/attendance/check.php?t='.substr(hash('sha256',$token),0,20).'.'.$w.'.'.qr_sig(qr_secret(),$token,$w);}
function resolve_signed(string $t):?array{global $pdo;
 if(!preg_match('/^([a-f0-9]{20})\.(\d{1,10})\.([a-f0-9]{32})$/',$t,$m))return null;$w=(int)$m[2];$now=intdiv(time(),QR_WIN);if($w>$now||$now-$w>1)return null;
 $s=$pdo->prepare("SELECT q.token,e.* FROM employee_qr_tokens q JOIN employees e ON e.id=q.employee_id JOIN users u ON u.employee_id=e.id AND u.is_active=1 WHERE q.is_active=1 AND e.status<>'inactive' AND SHA2(q.token,256) LIKE CONCAT(?,'%')");$s->execute([$m[1]]);
 foreach($s->fetchAll() as $r){if(hash_equals(qr_sig(qr_secret(),$r['token'],$w),$m[3])){
  $f=@fopen(sys_get_temp_dir().'/ea_used_'.sha1($m[3]),'x');if(!$f)return null; // استُهلك مسبقًا
  fclose($f);unset($r['token']);return $r;}}return null;}
