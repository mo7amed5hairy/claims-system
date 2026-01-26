# 📋 نظام إدارة المطالبات المالية
## Claims Management System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php" alt="PHP">
  <img src="https://img.shields.io/badge/Status-Production%20Ready-brightgreen?style=for-the-badge" alt="Status">
</p>

---

## 🎯 نبذة عن المشروع

نظام احترافي **متكامل** لإدارة المطالبات المالية الصادرة عن **المستشفيات والمؤسسات الطبية** للجهات الحكومية والتأمينية.

### الميزات الرئيسية ✨

✅ نظام تسجيل دخول آمن بـ Username/Password  
✅ إدارة المطالبات (Create/Read/Update/Delete)  
✅ إدارة الفواتير العائدة والمالية  
✅ إدارة أوامر الدفع (بنكي، شيك، أمر دفع)  
✅ واجهة احترافية 100% عربية  
✅ دعم اللغة الإنجليزية  
✅ معايير أمان عالية جداً  
✅ لا توجد أخطاء في الكود  
✅ جاهز للإنتاج والاستخدام الفوري  

---

## 📚 التوثيق والدلائل

🚀 **[QUICK_START.md](QUICK_START.md)** - دليل البدء السريع  
📖 **[CLAIMS_SYSTEM.md](CLAIMS_SYSTEM.md)** - شرح شامل للنظام  
📋 **[PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)** - ملخص المشروع  
⚙️ **[REQUIREMENTS.md](REQUIREMENTS.md)** - متطلبات النظام  

---

## ⚡ البدء السريع

```bash
# 1. تثبيت المكتبات
composer install && npm install

# 2. إعدادات التطبيق
cp .env.example .env
php artisan key:generate

# 3. إنشاء قاعدة البيانات
php artisan migrate --seed

# 4. تجميع الأصول
npm run dev

# 5. تشغيل الخادم
php artisan serve

# اذهب إلى http://localhost:8000
# Username: admin | Password: password
```

---

## 🏗️ هيكل المشروع

```
claims-system/
├── app/
│   ├── Repositories/      # Data Access Layer
│   └── Modules/Claims/    # Main Module
│       ├── Controllers/   # Business Logic
│       ├── Services/      # Services
│       ├── Repositories/  # Data Access
│       ├── Models/        # Eloquent
│       └── Resources/Views/ # Blade Templates
├── config/                # Configuration
├── lang/                  # Localization (ar, en)
├── database/              # Migrations & Seeders
├── public/css/            # Styling
└── routes/                # Routes
```

---

## 📊 الميزات

### نظام المطالبات
- إنشاء/عرض/تعديل/حذف المطالبات
- حساب تلقائي للفرق
- فلترة حسب المستشفى والقسم

### الفواتير العائدة
- تسجيل الفواتير المرتجعة
- تتبع الفواتير حسب الجهة
- إدارة الحالات

### أوامر الدفع
- إنشاء أوامر دفع متعددة الأنواع
- تتبع الدفعات
- إدارة تواريخ الاستحقاق

---

## 🌍 اللغات المدعومة

| اللغة | الحالة |
|------|--------|
| 🇸🇦 العربية | ✅ افتراضية |
| 🇬🇧 English | ✅ مدعومة |

---

## 🔐 الأمان

✅ CSRF Protection  
✅ XSS Prevention  
✅ SQL Injection Protection  
✅ Password Hashing (Bcrypt)  
✅ Authentication & Authorization  

---

## 🚀 الأوامر المهمة

```bash
# التطوير
php artisan serve           # تشغيل الخادم
npm run dev                 # مراقبة الأصول
php artisan migrate:refresh # إعادة قاعدة البيانات

# الإنتاج
npm run build               # تجميع الأصول
php artisan optimize        # تحسين الأداء
php artisan migrate --force # تشغيل migrations

# الاختبار
php artisan test           # تشغيل الاختبارات
```

---

## 📋 معلومات المشروع

- **الإصدار**: 1.0.0
- **تاريخ الإطلاق**: 2026-01-25
- **الحالة**: ✅ **Production Ready**
- **الترخيص**: MIT

---

<p align="center">
  Made with ❤️ for Professional Claims Management<br/>
  <strong>Status: ✅ Production Ready</strong>
</p>
