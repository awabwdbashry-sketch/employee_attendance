<?php require_once __DIR__.'/../includes/layout.php';require_login();
page_start('ماسح الحضور');ph('ماسح الحضور','وجّه كاميرا الجهاز نحو رمز QR الخاص بالموظف لتسجيل الحضور أو الانصراف.');?>
<div class="scanner">
<div class="scan-frame"><div id="reader"></div><div class="scan-ph" id="scanph"><div><?=ic('camera')?><p>جارٍ تشغيل الكاميرا…</p></div></div></div>
<div id="result" class="result" role="status" aria-live="polite"><?=ic('info')?><span id="rtext">وجّه الكاميرا نحو رمز QR الخاص بالموظف</span></div>
<details class="manual"><summary><?=ic('lock')?>الكاميرا لا تعمل؟ أدخل الرمز يدويًا</summary><div class="in"><input id="manual" placeholder="الصق رمز QR" dir="ltr"><button type="button" class="btn" onclick="send(document.getElementById('manual').value)">إرسال</button></div></details>
<p style="margin-top:16px"><a class="btn ghost" href="../index.php"><?=ic('arrow-right')?>الرئيسية</a></p></div>
<script src="<?=BASE?>/assets/vendor/html5-qrcode.min.js"></script>
<script>
let busy=false;const CSRF=<?=json_encode(csrf())?>;
const IC={success:'check-circle',warn:'alert',error:'x-circle'};
function show(level,text){const el=document.getElementById('result');el.className='result '+level;el.innerHTML='<svg class="i" aria-hidden="true"><use href="#i-'+(IC[level]||'info')+'"/></svg><span></span>';el.lastChild.textContent=text;}
async function send(t){if(busy)return;busy=true;
 try{const r=await fetch('api_scan.php',{method:'POST',body:new URLSearchParams({token:t,csrf:CSRF})});
 const d=await r.json();show(d.level||'error',(d.name?d.name+' — ':'')+d.msg+(d.time?' ('+d.time+')':''));}catch(e){show('error','تعذر الاتصال بالخادم. حاول مرة أخرى.');}
 setTimeout(()=>busy=false,3000);}
new Html5Qrcode('reader').start({facingMode:'environment'},{fps:10,qrbox:250},send).then(()=>document.getElementById('scanph').remove()).catch(()=>{const p=document.getElementById('scanph');if(p)p.innerHTML='<div><svg class="i" aria-hidden="true"><use href="#i-camera"/></svg><p>تعذر فتح الكاميرا، استخدم الإدخال اليدوي.</p></div>';show('warn','تعذر فتح الكاميرا، استخدم الإدخال اليدوي.');});
</script>
<?php page_end();
