<?php require_once __DIR__.'/../includes/layout.php';require_login();post_guard();$u=(int)$_SESSION['uid'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(($_POST['do']??'')==='all')$pdo->prepare('UPDATE notifications SET is_read=1 WHERE user_id=?')->execute([$u]);
 else $pdo->prepare('UPDATE notifications SET is_read=1 WHERE id=? AND user_id=?')->execute([(int)$_POST['id'],$u]);
 header('Location: index.php');exit;}
$rows=$pdo->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY id DESC LIMIT 100');$rows->execute([$u]);$rows=$rows->fetchAll();
$cat=function($m){return preg_match('/إجاز/u',$m)?['leave','sun','الإجازات']:(preg_match('/حضور|انصراف/u',$m)?['att','clock','الحضور']:['sys','sliders','النظام']);};
$link=fn($m)=>preg_match('/للمراجعة/u',$m)?BASE.'/leaves/approvals.php':(preg_match('/إجاز/u',$m)?BASE.'/employee/leaves.php':'');
page_start('الإشعارات');ph('مركز الإشعارات','آخر 100 إشعار لحسابك','<form method="post"><input type="hidden" name="csrf" value="'.csrf().'"><input type="hidden" name="do" value="all"><button class="btn ghost">'.ic('check').'تعليم الكل كمقروء</button></form>');?>
<div class="tabs" id="nf"><button class="on" data-f="">الكل</button><button data-f="un">غير مقروء</button><button data-f="leave">الإجازات</button><button data-f="att">الحضور</button><button data-f="sys">النظام</button></div>
<div class="card" style="padding:0;overflow:hidden"><?php foreach($rows as $r):[$c,$ic,$cl]=$cat($r['message']);$lk=$link($r['message']);?>
<div class="nrow <?=$r['is_read']?'':'un'?>" data-c="<?=$c?>" data-u="<?=$r['is_read']?0:1?>"><span class="nic"><?=ic($ic)?></span><div><b><?=e($cl)?></b><p><?=e($r['message'])?></p><small><?=e($r['created_at'])?></small></div>
<div class="act"><?php if($lk):?><a class="btn ghost sm" href="<?=e($lk)?>">فتح</a><?php endif;if(!$r['is_read']):?><form method="post"><input type="hidden" name="csrf" value="<?=csrf()?>"><input type="hidden" name="id" value="<?=$r['id']?>"><button class="btn ghost sm">مقروء</button></form><?php endif;?></div></div>
<?php endforeach;?><div id="ne" <?=$rows?'hidden':''?>><?=empty_state('لا توجد إشعارات حاليًا','ستظهر هنا تنبيهات الإجازات والحضور.','','bell')?></div></div>
<script>document.querySelectorAll("#nf button").forEach(b=>b.onclick=()=>{document.querySelectorAll("#nf button").forEach(x=>x.classList.toggle("on",x===b));const f=b.dataset.f;let n=0;document.querySelectorAll(".nrow").forEach(r=>{const ok=!f||(f==="un"?r.dataset.u==="1":r.dataset.c===f);r.hidden=!ok;if(ok)n++;});document.getElementById("ne").hidden=n>0;});</script>
<?php page_end();
