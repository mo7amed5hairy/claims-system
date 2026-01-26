# نظام المطالبات المالية - Claims System

## الوصف
نظام محترف لإدارة المطالبات المالية الصادرة عن المستشفيات والمؤسسات الطبية للجهات الحكومية والتأمينية.

## الميزات
✅ نظام تسجيل دخول آمن باستخدام اسم المستخدم وكلمة المرور
✅ واجهة عربية احترافية بنسبة 100%
✅ إدارة المطالبات المالية (CRUD)
✅ إدارة الفواتير العائدة
✅ إدارة أوامر الدفع
✅ إدارة الجهات والمستشفيات والأقسام
✅ نظام Modular الاحترافي مع Repository Pattern
✅ Service Layer للمنطق التجاري
✅ Localization (عربي وإنجليزي)

## هيكل المشروع

```
app/
├── Interfaces/                    # قواعد العقود (Interfaces)
│   └── RepositoryInterface.php   # واجهة Repository الأساسية
├── Repositories/
│   └── BaseRepository.php         # قاعدة Repository الأساسية
└── Modules/
    └── Claims/                    # موديول المطالبات الرئيسي
        ├── Http/
        │   ├── Controllers/       # Controllers CRUD
        │   └── Requests/          # Form Requests
        ├── Models/                # Eloquent Models
        ├── Repositories/          # Data Layer
        ├── Services/              # Business Logic Layer
        ├── Interfaces/            # Repository Interfaces
        ├── Resources/Views/       # Blade Views
        ├── Routes/
        │   └── web.php           # Module Routes
        └── ClaimsModuleServiceProvider.php
```

## الهيكل المعماري

### 1. **Authentication Layer**
- تسجيل دخول باستخدام `username` و `password`
- Role-based access (Admin/User)
- Sessions management

### 2. **Module Structure**
- **Repository Pattern**: BaseRepository + ClaimRepository + EntityRepository
- **Service Layer**: ClaimService لمعالجة المنطق التجاري
- **Controllers**: CRUD بسيط وأنيق مع validation

### 3. **Database Models**
- `User`: المستخدمين
- `Hospital`: المستشفيات
- `Department`: الأقسام الطبية
- `Claim`: المطالبات
- `ClaimEntity`: الجهات (التأمينات والوزارات)
- `ReturnedInvoice`: الفواتير العائدة
- `PaymentOrder`: أوامر الدفع

## المسارات (Routes)

```
GET  /                                   # صفحة تسجيل الدخول
POST /login                              # معالجة تسجيل الدخول
POST /logout                             # تسجيل الخروج

// Protected Routes (Authenticated)
GET  /dashboard                          # لوحة التحكم الرئيسية
GET  /dashboard/flow/options             # اختيار نوع الجهة
GET  /dashboard/flow/hospital            # اختيار المستشفى والقسم
GET  /dashboard/flow/operations          # عمليات النظام

// Claims Management
GET  /dashboard/claims                   # قائمة المطالبات
GET  /dashboard/claims/create            # نموذج إضافة مطالبة
POST /dashboard/claims                   # حفظ مطالبة جديدة
GET  /dashboard/claims/{id}/edit         # تعديل مطالبة
PUT  /dashboard/claims/{id}              # تحديث مطالبة
DELETE /dashboard/claims/{id}            # حذف مطالبة

// Returned Invoices
GET  /dashboard/returns                  # قائمة الفواتير العائدة
GET  /dashboard/returns/create           # نموذج فاتورة عائدة
POST /dashboard/returns                  # حفظ فاتورة عائدة
GET  /dashboard/returns/{id}/edit        # تعديل فاتورة عائدة
PUT  /dashboard/returns/{id}             # تحديث فاتورة عائدة
DELETE /dashboard/returns/{id}           # حذف فاتورة عائدة

// Payment Orders
GET  /dashboard/payments                 # قائمة أوامر الدفع
GET  /dashboard/payments/create          # نموذج أمر دفع
POST /dashboard/payments                 # حفظ أمر دفع
GET  /dashboard/payments/{id}/edit       # تعديل أمر دفع
PUT  /dashboard/payments/{id}            # تحديث أمر دفع
DELETE /dashboard/payments/{id}          # حذف أمر دفع

// Entities Management
GET  /dashboard/entities                 # قائمة الجهات
GET  /dashboard/entities/create          # نموذج جهة جديدة
POST /dashboard/entities                 # حفظ جهة جديدة
GET  /dashboard/entities/{id}/edit       # تعديل جهة
PUT  /dashboard/entities/{id}            # تحديث جهة
DELETE /dashboard/entities/{id}          # حذف جهة

// Hospitals Management
GET  /dashboard/hospitals                # قائمة المستشفيات
```

## كيفية الاستخدام

### 1. تثبيت المشروع
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

### 2. تسجيل الدخول
- استخدم بيانات المستخدم من الـ seeders
- Username: أي مستخدم تم إنشاؤه في الـ database
- Password: كلمة المرور المشفرة

### 3. استخدام النظام
1. اختر نوع الجهة (تعاقدات، تأمين صحي، وزارة صحة، إلخ)
2. اختر المستشفى والقسم
3. قم بإدارة المطالبات والفواتير وأوامر الدفع

## التعريب (Localization)

### اللغات المدعومة
- العربية (ar) - الافتراضية
- الإنجليزية (en)

### ملفات الترجمة
```
lang/
├── ar/
│   ├── auth.php          # رسائل المصادقة
│   └── messages.php      # الرسائل العامة
└── en/
    ├── auth.php
    └── messages.php
```

## الأمان

### معايير الأمان المطبقة
✅ CSRF Protection على جميع الـ forms
✅ Request Validation على جميع المدخلات
✅ Password Hashing مع Bcrypt
✅ Role-based Authorization
✅ SQL Injection Protection (Eloquent ORM)
✅ XSS Protection (Blade templating)

## الميزات المضافة

### 1. Responsive Design
- تصميم متجاوب يعمل على جميع الأجهزة
- CSS variables للألوان والمتغيرات
- Flexbox و Grid layouts

### 2. Form Validation
- Server-side validation على جميع النماذج
- رسائل خطأ واضحة بالعربية
- Old values عند الخطأ

### 3. Data Persistence
- Pagination على جميع القوائم
- Eloquent relationships صحيحة
- Soft deletes (اختياري)

## الملفات المهمة للتخصيص

1. **النصوص والرسائل**: `lang/ar/messages.php`
2. **التصاميم**: `public/css/style.css`
3. **الألوان**: CSS variables في `:root`
4. **البيانات الأولية**: `database/seeders/`

## تطوير إضافي

### للإضافة:
1. Models جديدة في `app/Modules/Claims/Models/`
2. Repositories في `app/Modules/Claims/Repositories/`
3. Services في `app/Modules/Claims/Services/`
4. Controllers في `app/Modules/Claims/Http/Controllers/`
5. Routes في `app/Modules/Claims/Routes/web.php`

### للتعديل:
- تعديل Repositories للبحث والفلترة المتقدمة
- إضافة Export to Excel/PDF
- إضافة Dashboard مع إحصائيات

## الدعم والمساعدة

للمساعدة والاستفسارات، يرجى مراجعة:
- التعليقات في الـ Code
- Documentation في المشروع
- Laravel Documentation (https://laravel.com)

---

**تم الإنشاء**: 2026-01-25
**الإصدار**: 1.0.0
**الحالة**: Production Ready ✅
