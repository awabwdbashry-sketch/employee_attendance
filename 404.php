<?php require_once __DIR__.'/includes/layout.php';
/* صفحة 404 اختيارية: لتفعيلها في Apache أضف ErrorDocument 404 /employee_attendance/404.php (في httpd.conf أو الـ vhost) */
error_page(404,'الصفحة غير موجودة','الرابط الذي فتحته غير صحيح أو تم نقل الصفحة.','العودة إلى الرئيسية');
