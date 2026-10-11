# 🕒 Employee Attendance & Leave Management System

### نظام إدارة حضور وانصراف الموظفين والإجازات

<p align="center">
  <a href="https://awabdev.byethost12.com/?i=1#top">
    <strong>Awab Bashary | AwabBuilds</strong>
  </a>
</p>

<p align="center">
  💻 Software Developer\
  <br>
  🌐 Web & Flutter Developer\
  <br>
  🤖 AI Developer\
  <br>
  📱 Building Modern Web, Mobile & AI Applications
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white" alt="MySQL">
  <img src="https://img.shields.io/badge/JavaScript-Vanilla-F7DF1E?logo=javascript&logoColor=black" alt="JavaScript">
  <img src="https://img.shields.io/badge/HTML5-Markup-E34F26?logo=html5&logoColor=white" alt="HTML5">
  <img src="https://img.shields.io/badge/CSS3-Styling-1572B6?logo=css3&logoColor=white" alt="CSS3">
  <img src="https://img.shields.io/badge/RTL-Arabic%20Support-198754" alt="RTL">
  <img src="https://img.shields.io/badge/QR-Code%20Attendance-6F42C1" alt="QR Attendance">
</p>

---

# 🇸🇩 العربية

## 📌 عن المشروع

**Employee Attendance & Leave Management System** هو نظام ويب لإدارة حضور وانصراف الموظفين وطلبات الإجازات، تم تطويره باستخدام **PHP وMySQL وHTML وCSS وJavaScript**، دون الاعتماد على إطار عمل Backend أو خطوات بناء Frontend.

يوفر النظام واجهة موحدة لإدارة الموظفين والأقسام والمسميات الوظيفية، وتسجيل الحضور والانصراف، وحساب التأخير وساعات العمل، ومتابعة الإجازات والأرصدة، وإدارة الموافقات والتقارير.

يستخدم النظام آلية **QR Code دوّارة بتوقيع تشفيري** لتسجيل الحضور من خلال الهاتف، مع صلاحية قصيرة للرمز ومنع إعادة استخدام رابط التسجيل.

🎯 الهدف هو توفير نظام موارد بشرية منظم، بواجهة عربية تدعم اتجاه الكتابة من اليمين إلى اليسار (RTL)، مع صلاحيات مختلفة للموارد البشرية والمديرين والموظفين.

---

## ✨ المميزات الرئيسية

### 👥 إدارة الموظفين

- ➕ إضافة موظفين جدد وتعديل بياناتهم.
- 🔍 البحث عن الموظفين وتصفية النتائج.
- 🏢 ربط الموظف بالقسم والمسمى الوظيفي.
- 👔 تحديد المدير المباشر.
- 🖼️ رفع صور الموظفين.
- 🔐 ربط الموظف بحساب دخول ودور محدد.
- 🟢 تفعيل حساب الموظف أو تعطيله.
- 📂 صفحة مستقلة لملف الموظف تشمل بياناته وحضوره وإجازاته ورصيده ورمز QR ونشاطه.

### 🏢 إدارة الأقسام والمسميات الوظيفية

- إنشاء الأقسام وتعديلها وحذفها.
- إدارة المسميات الوظيفية وربطها بالأقسام.
- منع حذف القسم الذي لا يزال مرتبطاً بموظفين.
- تنظيم الهيكل الوظيفي للمؤسسة.

### ⏰ تسجيل الحضور والانصراف

- 📱 تسجيل الحضور باستخدام QR Code.
- 🔄 رموز تسجيل موقعة تتجدد كل 45 ثانية.
- 🔐 التحقق من توقيع الرمز على الخادم.
- ♻️ قبول نافذة الرمز الحالية والسابقة وفق آلية التحقق.
- 🛡️ منع إعادة استخدام رابط QR بعد استهلاكه.
- 🚪 أول تسجيل في اليوم يُستخدم للدخول، والثاني للانصراف وفق شروط النظام.
- ⏱️ حساب دقائق التأخير.
- 🕓 حساب الانصراف المبكر.
- 🧮 حساب ساعات العمل الفعلية.
- ➕ حساب ساعات العمل الإضافية.
- 📅 سجل يومي وتقويم شهري للحضور.
- 🚫 رفض التسجيل في العطلات الرسمية والأسبوعية أو أثناء الإجازات المعتمدة.
- 🛑 تقييد المحاولات غير الصالحة مؤقتاً.

> ⚠️ تسجيل الحضور الفعلي يعتمد على استيفاء شروط النظام، بما في ذلك نشاط حساب الموظف وصلاحية الرمز وعدم وجود مانع مثل الإجازة المعتمدة.

### 🏖️ إدارة الإجازات

- 📝 تقديم طلبات الإجازات.
- 📎 إرفاق المستندات الداعمة.
- 📆 حساب أيام الإجازة مع استثناء أيام العطلات المحددة.
- 💳 متابعة رصيد الإجازات.
- 🔍 منع تداخل الطلبات.
- 🚧 التحقق من حدود التواريخ والأرصدة.
- 👨‍💼 موافقة المدير أو الموارد البشرية على الطلبات.
- ✅ دعم أنواع الإجازات التي لا تحتاج إلى موافقة، وفق الإعدادات.
- ❌ رفض الطلبات مع إمكانية تسجيل ملاحظة.
- ↩️ إلغاء الطلب وفق صلاحيات النظام وإعداداته.
- 🧮 تعديل الأرصدة يدوياً مع تسجيل عملية التعديل.
- 🗓️ تقويم للإجازات.
- 📊 متابعة الطلبات المعلقة والمعتمدة والمرفوضة والملغاة.

### 📊 التقارير والإحصائيات

- 📅 تقرير الحضور.
- 🏖️ تقرير الإجازات.
- 📈 التقرير الشهري.
- 🔍 فلاتر لتحديد نطاق البيانات.
- 🖨️ طباعة التقارير.
- 📥 تصدير البيانات إلى CSV.
- 🌐 دعم النصوص العربية عند التصدير باستخدام UTF-8 BOM.

### 🔔 الإشعارات

- عرض الإشعارات داخل النظام.
- صفحة مستقلة للإشعارات.
- إشعارات مرتبطة بعمليات النظام وفق الوظائف المنفذة.

### 🗓️ العطلات والمواعيد

- تحديد أيام العطلة الأسبوعية.
- إدارة العطلات الرسمية.
- دعم العطلات التي تستمر لأكثر من يوم.
- تحديد بداية ونهاية الدوام.
- ضبط فترة السماح للتأخير.
- تحديد الحد الأدنى لساعات العمل لاحتساب نصف يوم.

### 📝 سجل العمليات والتدقيق — Audit Log

يسجل النظام مجموعة من العمليات المهمة، مثل:

- 🔐 تسجيل الدخول والخروج.
- 👥 تعديلات بيانات الموظفين.
- 🏢 عمليات الأقسام والمسميات الوظيفية.
- ⏰ عمليات الحضور.
- 🏖️ طلبات الإجازات وموافقاتها.
- 💳 تعديلات أرصدة الإجازات.
- ⚙️ تغييرات إعدادات النظام.
- 🔄 عمليات QR ذات الصلة.

يساعد سجل التدقيق على تتبع العمليات الإدارية ومراجعة الأنشطة المهمة.

### 🔐 تسجيل الدخول والصلاحيات

يعتمد النظام على الأدوار والصلاحيات التي يتم التحقق منها على الخادم.

| الدور | الصلاحيات الرئيسية |
|---|---|
| 👑 Admin | إدارة الموظفين والأقسام والإجازات والأرصدة والإعدادات والتقارير وسجل التدقيق |
| 👔 Manager | متابعة الفريق ومراجعة الطلبات والتقارير ضمن نطاق صلاحياته |
| 👤 Employee | الوصول إلى بياناته وحضوره وإجازاته ورصيده ورمز QR الخاص به |

- 🔑 تخزين كلمات المرور باستخدام `password_hash`.
- 🛡️ التحقق من كلمات المرور باستخدام `password_verify`.
- 🔄 تجديد معرّف الجلسة عند تسجيل الدخول.
- 🧾 حماية نماذج POST باستخدام CSRF tokens.
- 🚦 الحد من محاولات تسجيل الدخول.
- ⏳ مهلة قابلة للضبط للجلسات.
- 🔎 إعادة التحقق من حالة المستخدم ودوره على الخادم.

---

## 🛠️ التقنيات المستخدمة

| التقنية | الاستخدام |
|---|---|
| 🐘 PHP 8.1+ | منطق النظام وBackend |
| 🗄️ MySQL / MariaDB | قاعدة البيانات |
| 🔌 PDO | الاتصال بقاعدة البيانات والاستعلامات المحضّرة |
| 🎨 HTML5 | هيكلة الواجهات |
| 🖌️ CSS3 | التصميم المتجاوب |
| ⚡ Vanilla JavaScript | التفاعلات داخل الواجهة |
| 📱 HTML5 QR Code | مسح رموز QR بالكاميرا |
| 🔳 QRCode Library | إنشاء رموز QR |
| 🔤 Cairo Font | الخط العربي المحلي |
| 🛡️ PHP Sessions | إدارة الجلسات |
| 🔐 HMAC-SHA256 | توقيع روابط QR |
| 📊 SVG | الرسوم البيانية والأيقونات |
| 🧰 Apache / WAMP | بيئة التشغيل المحلية |

### 💡 ملاحظات تقنية

- لا يعتمد المشروع على Laravel أو إطار Backend مشابه.
- لا يحتاج إلى Composer أو npm وفق البنية الحالية.
- مكتبات QR موجودة محلياً داخل المشروع.
- لا يعتمد على CDN أو تحميل الخطوط من مواقع خارجية.
- يستخدم `utf8mb4` لدعم النصوص العربية.
- يعتمد على ملفات PHP إجرائية ومنطق مشترك داخل `includes/`.

---

## 📁 هيكل المشروع

```text
employee_attendance/
│
├── index.php                  # 📊 لوحة التحكم
├── login.php                  # 🔐 تسجيل الدخول
├── logout.php                 # 🚪 تسجيل الخروج
├── 404.php                    # 🚫 صفحة غير موجود
├── diagnose.php               # 🩺 أداة تشخيص
│
├── config/
│   └── db.php                 # 🗄️ اتصال قاعدة البيانات
│
├── includes/
│   ├── functions.php          # 🧠 وظائف مشتركة
│   ├── layout.php             # 🎨 مكونات الواجهة
│   ├── attendance.php         # ⏰ منطق الحضور
│   ├── leave.php              # 🏖️ منطق الإجازات
│   ├── dash.php               # 📊 بيانات لوحة التحكم
│   └── reports.php            # 📈 وظائف التقارير
│
├── employees/                 # 👥 إدارة الموظفين
├── departments/               # 🏢 إدارة الأقسام
├── positions/                 # 💼 إدارة الوظائف
│
├── attendance/
│   ├── scan.php               # 📱 واجهة مسح QR
│   ├── api_scan.php           # 🔌 واجهة معالجة المسح
│   ├── check.php              # 🔄 التحقق من رمز QR
│   ├── history.php            # 📅 سجل الحضور
│   └── calendar.php           # 🗓️ تقويم الحضور
│
├── employee/
│   └── leaves.php             # 🏖️ إجازات الموظف
│
├── leaves/                    # 📝 إدارة الإجازات
├── reports/                   # 📊 التقارير
├── settings/                  # ⚙️ إعدادات النظام
├── notifications/             # 🔔 الإشعارات
├── cron/
│   └── daily.php              # 🗓️ المهمة اليومية
│
├── database/
│   ├── database.sql
│   ├── migration_leaves.sql
│   ├── migration_002_settings_holidays.sql
│   ├── seed_staff_data.sql
│   └── seed_staff_data_remove.sql
│
├── uploads/
│   ├── photos/                # 🖼️ صور الموظفين
│   └── leaves/                # 📎 مرفقات الإجازات
│
├── assets/
│   ├── css/
│   │   └── app.css
│   ├── js/
│   │   └── app.js
│   ├── fonts/                 # 🔤 ملفات Cairo
│   └── vendor/                # 🧰 مكتبات QR المحلية
│
├── .htaccess
└── README.md
```

> 📌 هذا الهيكل ملخص لأهم ملفات ومجلدات المشروع. قد تختلف بعض الملفات الفرعية حسب النسخة الحالية.

---

## 🗄️ قاعدة البيانات

اسم قاعدة البيانات الافتراضي:

```text
employee_attendance
```

### 📚 الجداول الرئيسية

| الجدول | الوظيفة |
|---|---|
| `employees` | بيانات الموظفين |
| `users` | حسابات المستخدمين والأدوار |
| `departments` | الأقسام |
| `positions` | المسميات الوظيفية |
| `employee_qr_tokens` | بيانات رموز QR |
| `attendance` | سجلات الحضور والانصراف |
| `leave_types` | أنواع الإجازات |
| `leave_balances` | أرصدة الإجازات |
| `leave_requests` | طلبات الإجازات |
| `leave_approvals` | سجلات الموافقات |
| `leave_balance_adjustments` | تعديلات الأرصدة |
| `notifications` | الإشعارات |
| `holidays` | العطلات الرسمية |
| `settings` | إعدادات النظام |
| `audit_logs` | سجل العمليات |

### 🔗 العلاقات الأساسية

- يرتبط الموظف بقسم ومسمى وظيفي ومدير مباشر.
- يرتبط حساب المستخدم بموظف عند إنشاء حساب مرتبط به.
- يرتبط سجل الحضور بالموظف وتاريخ العمل.
- يرتبط طلب الإجازة بالموظف ونوع الإجازة.
- ترتبط الموافقات بطلبات الإجازات والمستخدمين.
- ترتبط أرصدة الإجازات بالموظفين وأنواع الإجازات والسنة.
- ترتبط الإشعارات بحسابات المستخدمين.

### 📦 ملفات SQL

| الملف | الاستخدام |
|---|---|
| `database/database.sql` | إنشاء قاعدة البيانات الأساسية وبنيتها وبيانات البداية |
| `database/migration_leaves.sql` | ترقية مخطط الإجازات لقاعدة موجودة |
| `database/migration_002_settings_holidays.sql` | ترقية إعدادات العطلات والأرصدة |
| `database/seed_staff_data.sql` | بيانات موظفين وسجلات إضافية لأغراض الاختبار |
| `database/seed_staff_data_remove.sql` | إزالة السجلات الإضافية المرتبطة بأكواد `SEED-` |

> ⚠️ لا تستورد ملفات الترقية إلى قاعدة جديدة دون الحاجة. لا تستورد ملفات البيانات الإضافية في نسخة الإنتاج. احتفظ بنسخة احتياطية قبل أي تغيير في قاعدة قائمة.

---

## 🚀 التثبيت والتشغيل محلياً

### 1️⃣ المتطلبات

- 🖥️ Windows أو بيئة متوافقة.
- 🐘 PHP 8.1 أو أحدث.
- 🗄️ MySQL 5.7+ أو MariaDB 10.4+.
- 🌐 Apache مع دعم `.htaccess`.
- 🔌 امتداد PHP `pdo_mysql`.
- 📁 امتداد `fileinfo`.
- 🔤 امتداد `mbstring`.
- 🌍 متصفح حديث.

### 2️⃣ وضع ملفات المشروع

ضع المجلد في المسار التالي داخل WAMP:

```text
C:\wamp64\www\employee_attendance
```

المسار الأساسي `/employee_attendance` مستخدم داخل الروابط الحالية، لذلك يجب مراعاته عند تغيير مكان المشروع.

### 3️⃣ إنشاء قاعدة البيانات

من phpMyAdmin أنشئ قاعدة البيانات:

```text
employee_attendance
```

ثم استورد الملف:

```text
database/database.sql
```

إذا كنت تجهز قاعدة قديمة تحتاج إلى ترقية، راجع الترتيب المناسب لملفات migration قبل تنفيذها.

### 4️⃣ إعداد الاتصال

افتح:

```text
config/db.php
```

إعدادات WAMP المحلية الافتراضية في النسخة الحالية:

```php
DB_HOST = 'localhost';
DB_NAME = 'employee_attendance';
DB_USER = 'root';
DB_PASS = '';
```

> 🔐 قد تختلف بيانات الاتصال على الاستضافة. لا ترفع بيانات اعتماد حقيقية أو ملف إعدادات خاص إلى مستودع عام.

### 5️⃣ تشغيل النظام

شغّل Apache وMySQL من WAMP، ثم افتح:

```text
http://localhost/employee_attendance/login.php
```

### 6️⃣ إعداد صلاحيات الملفات

تحتاج مجلدات الرفع إلى صلاحيات كتابة مناسبة لخادم الويب:

```text
uploads/photos/
uploads/leaves/
```

احتفظ بقواعد `.htaccess` التي تمنع تنفيذ ملفات غير آمنة داخل مجلدات الرفع.

### 7️⃣ صفحة الخطأ 404

يمكن إعداد صفحة الخطأ في Apache باستخدام:

```apache
ErrorDocument 404 /employee_attendance/404.php
```

يجب التحقق من إعداد المسار عند النشر في استضافة مختلفة.

---

## 📱 إعداد QR والوصول من الهاتف

### 🔄 كيف يعمل QR الدوّار؟

1. يفتح المسؤول أو الموظف المصرّح له شاشة QR داخل النظام.
2. يعرض النظام رمزاً مرتبطاً بحساب الموظف.
3. يحتوي رابط المسح على بيانات موقعة تشفيرياً.
4. يتجدد الرمز كل 45 ثانية.
5. يتحقق الخادم من توقيع الرابط وصلاحيته.
6. يُمنع استخدام الرابط مرة أخرى بعد استهلاكه.
7. يسجل النظام الدخول أو الانصراف إذا استوفت العملية الشروط.

### 🌐 الوصول من جهاز آخر

يجب أن يستطيع الهاتف الوصول إلى خادم الويب الذي يشغّل النظام. عند استخدام الشبكة المحلية:

- 📶 اربط الهاتف والكمبيوتر بالشبكة المناسبة.
- 🖥️ تأكد من تشغيل Apache.
- 🔥 راجع إعدادات Windows Firewall.
- 🌐 اضبط `public_url` على العنوان الذي يستطيع الهاتف الوصول إليه.
- 🔒 استخدم HTTPS عند النشر العام.

> 📷 تتطلب الكاميرا في المتصفحات الحديثة سياقاً آمناً، مثل `localhost` أو HTTPS. لا تفترض أن رابط HTTP محلي سيتيح الكاميرا في كل جهاز.

---

## 🗓️ المهمة اليومية

يتضمن المشروع:

```text
cron/daily.php
```

تُستخدم المهمة اليومية لمعالجة سجلات الغياب والإجازات والعطلات وفق منطق النظام.

يمكن إعداد تشغيلها باستخدام Windows Task Scheduler أو cron حسب بيئة الخادم.

مثال عام:

```bash
php cron/daily.php
```

يمكن تمرير تاريخ اختياري عند الحاجة:

```bash
php cron/daily.php 2026-10-11
```

> ⚠️ اختبر المهمة على قاعدة منفصلة قبل جدولة تشغيلها في الإنتاج، وتأكد من إعداد مسار PHP الصحيح في بيئتك.

---

## 🔐 الأمان والخصوصية

يراعي المشروع مجموعة من ممارسات الأمان:

- 🔑 استخدام Password Hashing.
- 🛡️ حماية نماذج POST باستخدام CSRF.
- 🔍 التحقق من الصلاحيات على الخادم.
- 🚦 تقييد محاولات تسجيل الدخول غير الناجحة.
- 🔄 إدارة الجلسات وتجديد معرّفها عند تسجيل الدخول.
- 🗄️ استخدام PDO والاستعلامات المحضّرة.
- 🔐 توقيع روابط QR باستخدام HMAC-SHA256.
- ♻️ منع إعادة استخدام رابط QR المستهلك.
- 📂 تقييد الوصول المباشر إلى المجلدات الحساسة.
- 🖼️ التحقق من أنواع الملفات المرفوعة.
- 📝 تسجيل العمليات الإدارية المهمة.

### ⚠️ قبل النشر العام

- راجع إعدادات HTTPS وخصائص ملفات تعريف الارتباط.
- استخدم بيانات اتصال خاصة بالاستضافة.
- تأكد من حماية ملفات الإعدادات والنسخ الاحتياطية.
- احذف أدوات التشخيص مثل `diagnose.php` من نسخة الإنتاج إذا لم تكن مطلوبة.
- لا تنشر بيانات الموظفين أو سجلات الحضور الحقيقية.
- راجع محرك جداول MySQL؛ يفضّل استخدام InnoDB لتطبيق قيود العلاقات المرجعية.
- اختبر جميع الصلاحيات وتدفقات الحضور والإجازات على بيئة منفصلة.
- لا تعتمد على نجاح الاختبارات المحلية وحده لإثبات أمان النشر العام.

---

## 🎨 واجهة المستخدم

تم بناء الواجهة مع التركيز على:

- 🧭 تنقل واضح بين أقسام النظام.
- 🌐 دعم اللغة العربية واتجاه RTL.
- 📱 تصميم متجاوب مع الشاشات الصغيرة.
- 🔤 خط Cairo محلي.
- 🧩 مكونات واجهة مشتركة.
- 📊 رسوم بيانية SVG.
- 🪟 نوافذ تأكيد داخل الواجهة.
- 🗂️ تحويل الجداول إلى بطاقات على الشاشات الصغيرة.

---

## 🧪 الاختبارات وحالة المشروع

**Project Status: Local Development & Testing 🚧**

تم فحص بنية المشروع ووظائفه ومراجعة منطق QR والصلاحيات، وفق تقرير الفحص الحالي.

لكن لم تُختبر جميع سيناريوهات التشغيل على جهاز WAMP الفعلي، ولم يُستكمل اختبار الكاميرا الفعلية أو جميع المتصفحات.

قبل نشر النسخة النهائية، يجب إجراء اختبار عملي يشمل:

- 🔐 تسجيل الدخول والخروج.
- 👥 إضافة موظف وتعديل بياناته.
- ⏰ تسجيل حضور وانصراف فعليين.
- 🔄 رفض إعادة استخدام رمز QR.
- 🏖️ إنشاء طلب إجازة ومراجعته.
- 📊 عرض التقارير وتصدير CSV.
- 📱 فتح النظام من الهاتف.
- 🔒 اختبار الصلاحيات ومنع الوصول غير المصرح به.

---

## 🌐 النشر والاستضافة

يمكن استخدام GitHub لحفظ الكود وعرضه ضمن معرض الأعمال.

لتشغيل النظام فعلياً عبر الإنترنت، تحتاج إلى استضافة تدعم:

- PHP.
- MySQL أو MariaDB.
- Apache أو خادم ويب متوافق.
- إعدادات آمنة لملفات المشروع ومجلدات الرفع.
- HTTPS للوصول الآمن إلى الكاميرا والاتصالات الحساسة.

> 🚫 GitHub Pages لا يشغّل PHP ولا يوفر قاعدة بيانات MySQL. يجب استضافة التطبيق على خادم يدعم هذه التقنيات.

---

## 👨‍💻 المطوّر

**[Awab Bashary | AwabBuilds](https://awabdev.byethost12.com/?i=1#top)**

💻 Software Developer  
🌐 Web & Flutter Developer  
🤖 AI Developer  
📱 Building Modern Web, Mobile & AI Applications

🌍 Portfolio: [awabdev.byethost12.com](https://awabdev.byethost12.com/?i=1#top)

---

## ❤️ رسالة المشروع

تم تطوير هذا المشروع بهدف تقديم تجربة عملية لإدارة حضور الموظفين والإجازات، باستخدام تقنيات ويب مباشرة، مع دعم العربية وتنظيم الصلاحيات وسجلات العمليات.

**Built with passion by Awab Bashary | AwabBuilds.**

🚀 Building modern digital experiences, one project at a time.

© 2026 AwabBuilds

---

# 🇬🇧 English

## 📌 About the Project

**Employee Attendance & Leave Management System** is a web-based application designed to manage employee attendance, check-in/check-out records, leave requests, leave balances, and essential HR operations.

Built with **PHP, MySQL, HTML5, CSS3, and Vanilla JavaScript**, the application uses a procedural PHP architecture without a backend framework or frontend build process.

The system provides a centralized interface for employee management, attendance tracking, leave approvals, reports, notifications, system settings, and audit logging.

It also includes a **rotating, cryptographically signed QR attendance workflow**, with short-lived links and replay protection.

---

## ✨ Key Features

### 👥 Employee Management

- Create and update employee records.
- Search and filter employees.
- Assign departments, positions, and managers.
- Upload employee profile photos.
- Associate employee records with user accounts.
- Assign access roles.
- Activate or deactivate employees.
- View employee profiles, attendance history, leave balances, QR details, and activity.

### 🏢 Departments & Positions

- Create, edit, and manage departments.
- Manage job positions and their department associations.
- Prevent deletion of departments that are still assigned to employees.
- Maintain an organized employee structure.

### ⏰ Attendance & Check-in/Check-out

- 📱 QR-based attendance recording.
- 🔄 Rotating signed QR links with a 45-second time window.
- 🔐 HMAC-SHA256 signature verification.
- ♻️ Replay protection for consumed QR links.
- 🚪 Check-in and check-out workflow.
- ⏱️ Late-arrival calculations.
- 🕓 Early-departure calculations.
- 🧮 Working-hours calculation.
- ➕ Overtime calculation.
- 📅 Attendance history and monthly calendar.
- 🚫 Restrictions for holidays, weekly days off, and approved leave.
- 🛑 Temporary rate limiting for invalid QR attempts.

### 🏖️ Leave Management

- Submit leave requests.
- Attach supporting documents.
- Calculate working days while accounting for configured holidays.
- Validate date ranges and overlapping requests.
- Check leave balances.
- Review pending requests.
- Approve or reject requests with notes.
- Support leave types that do not require approval when configured.
- Cancel requests according to permissions and settings.
- Adjust leave balances with an adjustment record.
- Display leave calendars and request statuses.

### 📊 Reporting

- Attendance reports.
- Leave reports.
- Monthly reports.
- Date and data filters.
- Print-friendly report views.
- CSV export with UTF-8 BOM for Arabic compatibility.

### 🔔 Notifications

- In-app notification display.
- A dedicated notifications page.
- Notifications associated with supported system events.

### 🗓️ Work Schedule & Holidays

- Configurable working hours.
- Grace period for late arrivals.
- Half-day thresholds.
- Weekly day-off settings.
- Public holiday management.
- Support for multi-day holidays.

### 📝 Audit Logging

Important operations can be recorded, including authentication, employee updates, attendance, leave approvals, balance adjustments, and system configuration changes.

### 🔐 Authentication & Authorization

- Password hashing and verification.
- Session ID regeneration on login.
- CSRF protection for POST operations.
- Login attempt limits.
- Configurable session timeout.
- Server-side permission checks.
- Role-based access for administrators, managers, and employees.

---

## 🛠️ Technology Stack

| Technology | Purpose |
|---|---|
| 🐘 PHP 8.1+ | Backend application |
| 🗄️ MySQL / MariaDB | Database |
| 🔌 PDO | Database connectivity and prepared statements |
| 🎨 HTML5 | Page structure |
| 🖌️ CSS3 | Responsive styling |
| ⚡ Vanilla JavaScript | Client-side interactions |
| 📱 HTML5 QR Code | QR scanning |
| 🔳 QRCode Library | QR generation |
| 🔤 Cairo Font | Local Arabic typography |
| 🛡️ PHP Sessions | Session management |
| 🔐 HMAC-SHA256 | Signed QR links |
| 📊 SVG | Icons and charts |
| 🧰 Apache / WAMP | Local server environment |

### Technical Notes

- No Laravel or similar backend framework.
- No Composer or npm build step in the current project.
- QR libraries are stored locally.
- No CDN or externally hosted fonts are required by the current frontend.
- The application uses UTF-8 / `utf8mb4` for Arabic text support.

---

## 📁 Project Structure

```text
employee_attendance/
│
├── index.php
├── login.php
├── logout.php
├── 404.php
├── diagnose.php
│
├── config/
│   └── db.php
│
├── includes/
│   ├── functions.php
│   ├── layout.php
│   ├── attendance.php
│   ├── leave.php
│   ├── dash.php
│   └── reports.php
│
├── employees/
├── departments/
├── positions/
│
├── attendance/
│   ├── scan.php
│   ├── api_scan.php
│   ├── check.php
│   ├── history.php
│   └── calendar.php
│
├── employee/
│   └── leaves.php
│
├── leaves/
├── reports/
├── settings/
├── notifications/
│
├── cron/
│   └── daily.php
│
├── database/
│   ├── database.sql
│   ├── migration_leaves.sql
│   ├── migration_002_settings_holidays.sql
│   ├── seed_staff_data.sql
│   └── seed_staff_data_remove.sql
│
├── uploads/
│   ├── photos/
│   └── leaves/
│
├── assets/
│   ├── css/
│   ├── js/
│   ├── fonts/
│   └── vendor/
│
├── .htaccess
└── README.md
```

This tree summarizes the main directories and files. Some subdirectories contain additional PHP pages and supporting assets.

---

## 🗄️ Database

Default database name:

```text
employee_attendance
```

### Main Tables

| Table | Purpose |
|---|---|
| `employees` | Employee records |
| `users` | User accounts and roles |
| `departments` | Departments |
| `positions` | Job positions |
| `employee_qr_tokens` | QR token data |
| `attendance` | Attendance records |
| `leave_types` | Leave categories |
| `leave_balances` | Employee leave balances |
| `leave_requests` | Leave requests |
| `leave_approvals` | Approval records |
| `leave_balance_adjustments` | Balance adjustments |
| `notifications` | User notifications |
| `holidays` | Public holidays |
| `settings` | Application settings |
| `audit_logs` | Audit trail |

### SQL Files

| File | Purpose |
|---|---|
| `database/database.sql` | Core schema and initial records |
| `database/migration_leaves.sql` | Leave-related schema migration |
| `database/migration_002_settings_holidays.sql` | Holiday and balance-adjustment migration |
| `database/seed_staff_data.sql` | Additional test data |
| `database/seed_staff_data_remove.sql` | Removes records associated with `SEED-` employee codes |

> ⚠️ Back up existing databases before applying migrations. Do not import optional test data into production.

---

## 🚀 Installation & Setup

### 1️⃣ Requirements

- PHP 8.1 or newer.
- MySQL 5.7+ or MariaDB 10.4+.
- Apache with `.htaccess` support.
- PHP `pdo_mysql`, `fileinfo`, and `mbstring` extensions.
- A modern web browser.

### 2️⃣ Place the Project

For the current WAMP configuration, use:

```text
C:\wamp64\www\employee_attendance
```

The `/employee_attendance` base path is hard-coded in the current application and must be considered when changing the deployment location.

### 3️⃣ Create the Database

Create a database named:

```text
employee_attendance
```

Import:

```text
database/database.sql
```

For an existing database, review the migration requirements before applying any migration file.

### 4️⃣ Configure Database Access

Open:

```text
config/db.php
```

The current local WAMP defaults are:

```php
DB_HOST = 'localhost';
DB_NAME = 'employee_attendance';
DB_USER = 'root';
DB_PASS = '';
```

These values are specific to the local development setup. Configure the correct credentials for your server.

### 5️⃣ Start the Application

Start Apache and MySQL in WAMP, then visit:

```text
http://localhost/employee_attendance/login.php
```

### 6️⃣ Upload Directories

The web server needs appropriate write permissions for:

```text
uploads/photos/
uploads/leaves/
```

Keep the upload-directory security rules that prevent execution of unsafe uploaded files.

### 7️⃣ Configure the 404 Page

Apache can be configured to use:

```apache
ErrorDocument 404 /employee_attendance/404.php
```

Adjust the path for other deployment environments.

---

## 📱 QR Attendance & Mobile Access

### 🔄 QR Workflow

1. An authorized user opens the QR screen.
2. The system displays a QR code associated with an employee.
3. The QR link contains cryptographically signed data.
4. The signing window rotates every 45 seconds.
5. The server validates the signature and time window.
6. A consumed QR link cannot be reused.
7. Attendance is recorded only if the application's attendance rules are satisfied.

### 🌐 Access from a Phone

- Connect the phone to a network that can reach the server.
- Ensure Apache is running.
- Configure firewall rules where required.
- Set `public_url` to an address reachable by the phone.
- Use HTTPS for public deployment.

Camera access in modern browsers generally requires a secure context, such as HTTPS or localhost.

---

## 🗓️ Daily Attendance Task

The project includes:

```text
cron/daily.php
```

This script handles daily attendance-related processing according to the application's rules, including absence, leave, and holiday records.

It can be scheduled using Windows Task Scheduler or cron.

Example:

```bash
php cron/daily.php
```

An optional date can be supplied:

```bash
php cron/daily.php 2026-10-11
```

> ⚠️ Test scheduled tasks against a separate database before enabling them in production. Confirm the PHP executable path and execution environment.

---

## 🔐 Security & Privacy

The application includes several security-related controls:

- 🔑 Password hashing.
- 🛡️ CSRF protection.
- 🔍 Server-side authorization checks.
- 🚦 Login attempt throttling.
- 🔄 Session ID regeneration.
- 🗄️ PDO prepared statements.
- 🔐 HMAC-SHA256 QR signatures.
- ♻️ QR replay protection.
- 📂 Restricted access to sensitive directories.
- 🖼️ Uploaded file type validation.
- 📝 Audit logging.

### Before Public Deployment

- Configure HTTPS and review secure cookie settings.
- Use production-specific database credentials.
- Keep private configuration and backups out of public repositories.
- Remove diagnostic utilities such as `diagnose.php` from production if they are not needed.
- Never publish real employee data or attendance records.
- Prefer InnoDB for enforcing database foreign-key constraints.
- Test attendance, leave workflows, and authorization rules in a separate environment.
- Do not treat local testing alone as proof of production readiness.

---

## 🎨 User Interface

The application focuses on:

- 🧭 Clear navigation.
- 🌐 Arabic and right-to-left support.
- 📱 Responsive layouts.
- 🔤 Locally hosted Cairo font files.
- 🧩 Shared interface components.
- 📊 SVG-based charts.
- 🪟 Custom confirmation dialogs.
- 🗂️ Mobile-friendly table layouts.

---

## 🧪 Testing & Project Status

**Project Status: Local Development & Testing 🚧**

The current codebase has been inspected, and QR validation and replay rejection have been tested in the reported environment.

However, the complete workflow has not been verified on the target WAMP machine, and actual camera scanning and all browser combinations remain to be tested.

Recommended final checks include:

- 🔐 Login and logout.
- 👥 Employee creation and editing.
- ⏰ Real check-in and check-out.
- 🔄 QR replay rejection.
- 🏖️ Leave submission and approval.
- 📊 Reports and CSV export.
- 📱 Mobile access.
- 🔒 Authorization and access-control checks.

---

## 🌐 Deployment

GitHub can store and showcase the source code. Running this application online requires a hosting environment that supports PHP, MySQL or MariaDB, and the required server configuration.

**GitHub Pages alone cannot execute PHP or provide a MySQL database.**

---

## 👨‍💻 Developer

**[Awab Bashary | AwabBuilds](https://awabdev.byethost12.com/?i=1#top)**

💻 Software Developer  
🌐 Web & Flutter Developer  
🤖 AI Developer  
📱 Building Modern Web, Mobile & AI Applications

🌍 Portfolio: [awabdev.byethost12.com](https://awabdev.byethost12.com/?i=1#top)

---

## ❤️ Project Philosophy

This project was built to provide a practical approach to employee attendance and leave management, combining Arabic-friendly interfaces, QR-based attendance, role-based access, and organized reporting.

**Built with passion by Awab Bashary | AwabBuilds.**

🚀 Building modern digital experiences, one project at a time.

© 2026 AwabBuilds
