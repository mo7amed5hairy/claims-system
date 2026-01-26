# دليل البدء السريع - Quick Start Guide

## 🚀 التثبيت والتشغيل

### 1. متطلبات النظام
```bash
- PHP 8.2 أو أعلى
- Composer
- Node.js و npm
- SQLite أو MySQL
```

### 2. خطوات الإعداد الأساسية

#### الخطوة 1: تثبيت المتطلبات
```bash
# تثبيت PHP dependencies
composer install

# تثبيت Node dependencies
npm install
```

#### الخطوة 2: إنشاء ملف البيئة
```bash
# نسخ ملف البيئة النموذجي
cp .env.example .env

# إنشاء مفتاح التطبيق
php artisan key:generate
```

#### الخطوة 3: إعداد قاعدة البيانات
```bash
# إنشاء قاعدة البيانات وتشغيل الـ migrations
php artisan migrate

# تشغيل الـ seeders (بيانات العينة)
php artisan db:seed
```

#### الخطوة 4: تشغيل التطبيق
```bash
# الطريقة الأولى: استخدام Artisan
php artisan serve

# الطريقة الثانية: استخدام Laragon (إذا كان مثبتاً)
# انقر على الزر "Start All" في Laragon
```

### 3. تجميع الأصول (Assets)
```bash
# تطوير
npm run dev

# الإنتاج
npm run build
```

---

## 🔐 تسجيل الدخول

### بيانات المستخدم الافتراضي

بعد تشغيل الـ seed، ستكون لديك:

**المسؤول (Admin)**
- Username: `admin`
- Password: `password`
- Role: مسؤول النظام

**المستخدم العادي (User)**
- Username: `user`
- Password: `password`
- Role: مستخدم عادي

### كيفية الدخول
1. افتح المتصفح واذهب إلى `http://localhost:8000`
2. أدخل بيانات تسجيل الدخول
3. اختر نوع الجهة (تأمين صحي، تعاقدات، إلخ)
4. اختر المستشفى والقسم
5. ابدأ بإضافة المطالبات والفواتير

---

## 📁 هيكل المشروع الأساسي

```
📦 claims-system/
├── 📄 artisan                    # Console application
├── 📄 composer.json              # PHP dependencies
├── 📄 package.json               # Node dependencies
├── 📄 vite.config.js             # Vite config
├── 📄 phpunit.xml                # Testing config
│
├── 📂 app/
│   ├── 📂 Repositories/          # Base Repository
│   ├── 📂 Interfaces/            # Repository Interfaces
│   ├── 📂 Modules/
│   │   └── 📂 Claims/            # Claims Module
│   │       ├── Http/Controllers/ # Controllers
│   │       ├── Models/           # Eloquent Models
│   │       ├── Repositories/     # Data Access Layer
│   │       ├── Services/         # Business Logic
│   │       ├── Resources/Views/  # Blade Views
│   │       └── Routes/web.php    # Module Routes
│   └── Models/                   # Main Models
│
├── 📂 routes/
│   ├── web.php                   # Main routes
│   └── console.php               # Console commands
│
├── 📂 database/
│   ├── migrations/               # Database migrations
│   ├── seeders/                  # Database seeders
│   └── factories/                # Model factories
│
├── 📂 resources/
│   ├── views/                    # Blade templates
│   ├── css/app.css              # CSS styles
│   └── js/app.js                # JavaScript
│
├── 📂 public/
│   ├── css/style.css             # Main stylesheet
│   └── index.php                 # Entry point
│
├── 📂 config/
│   ├── app.php                   # App config
│   ├── database.php              # Database config
│   └── auth.php                  # Auth config
│
└── 📂 lang/
    ├── ar/                       # Arabic translations
    │   ├── auth.php
    │   └── messages.php
    └── en/                       # English translations
        ├── auth.php
        └── messages.php
```

---

## 🔧 الأوامر المهمة

### تطوير
```bash
# تشغيل خادم التطوير
php artisan serve

# مراقبة تغييرات الـ assets
npm run dev

# إعادة تثبيت قاعدة البيانات
php artisan migrate:refresh --seed

# مسح الـ cache
php artisan cache:clear
```

### الاختبار
```bash
# تشغيل الاختبارات
php artisan test

# تشغيل اختبار معين
php artisan test --filter=TestName
```

### الإنتاج
```bash
# تجميع الأصول
npm run build

# تحسين الأداء
php artisan optimize

# تخزين الـ cache
php artisan config:cache
php artisan route:cache
```

---

## 🎨 التخصيص

### تغيير اللغة الافتراضية
الملف: `config/app.php`
```php
'locale' => 'ar',  // للعربية
'locale' => 'en',  // للإنجليزية
```

### تغيير الألوان والتصاميم
الملف: `public/css/style.css`
```css
:root {
    --primary-color: #3498db;
    --danger-color: #e74c3c;
    --success-color: #27ae60;
    /* ... */
}
```

### تعديل الرسائل
- اللغة العربية: `lang/ar/messages.php`
- اللغة الإنجليزية: `lang/en/messages.php`

---

## 🐛 استكشاف الأخطاء

### المشكلة: صفحة بيضاء
- افتح `storage/logs/laravel.log` للرسائل التفصيلية
- تأكد من تثبيت جميع المتطلبات

### المشكلة: خطأ في قاعدة البيانات
```bash
# أعد إنشاء قاعدة البيانات
php artisan migrate:refresh --seed
```

### المشكلة: الأصول (CSS/JS) لا تظهر
```bash
# أعد بناء الأصول
npm run build
```

### المشكلة: خطأ في الصلاحيات
```bash
# صحح الصلاحيات
chmod -R 775 storage bootstrap/cache
```

---

## 📊 حالة النظام

الحالة الحالية: ✅ **جاهز للاستخدام والإنتاج**

### ✅ المكتمل
- ✅ نظام المصادقة (Authentication)
- ✅ إدارة المطالبات (Claims CRUD)
- ✅ إدارة الفواتير العائدة (Returns CRUD)
- ✅ إدارة أوامر الدفع (Payments CRUD)
- ✅ نظام الجهات والمستشفيات (Entities & Hospitals)
- ✅ Repository Pattern مع Interface
- ✅ Service Layer للمنطق التجاري
- ✅ Localization (عربي وإنجليزي)
- ✅ تصميم احترافي مع RTL
- ✅ Form Validation
- ✅ CSRF Protection
- ✅ جميع الـ Routes مسجلة
- ✅ لا توجد أخطاء في النظام

### 🔄 قيد التطوير
- Dashboard مع إحصائيات
- Export to Excel/PDF
- Advanced Search & Filters
- User Management System
- Audit Logs

---

## 📚 المراجع والموارد

- [Laravel Documentation](https://laravel.com/docs)
- [Blade Templating](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)
- [Repository Pattern](https://designpatternsphp.readthedocs.io/en/latest/Creational/AbstractFactory/index.html)

---

## 💡 نصائح للتطوير

1. استخدم `php artisan tinker` للاختبار السريع
2. استخدم `php artisan route:list` لمشاهدة جميع الـ routes
3. استخدم `dd()` للـ debugging السريع
4. استخدم `@dd()` في الـ Blade templates

---

**آخر تحديث**: 2026-01-25
**الإصدار**: 1.0.0
**الحالة**: Production Ready ✅

---

## 📞 الدعم

للمساعدة والاستفسارات، يرجى التحقق من:
- التعليقات في الـ Code
- [documentation](./CLAIMS_SYSTEM.md)
- Laravel Documentation
