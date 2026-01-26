# متطلبات النظام - System Requirements

## 🖥️ متطلبات الأجهزة والبيئة

### متطلبات الخادم (Server)
```
- الذاكرة (RAM): 512 MB على الأقل (موصى به 2 GB)
- مساحة التخزين: 200 MB على الأقل
- معالج: أي معالج حديث
- الاتصال: إنترنت (لتحميل المكتبات)
```

### البرامج المطلوبة

#### 1. PHP
```
الإصدار الموصى به: PHP 8.2+
الإصدار المدعوم: PHP 8.1+

المقترنات (Extensions) المطلوبة:
✅ BCMath
✅ Ctype
✅ cURL
✅ DOM
✅ Fileinfo
✅ Filter
✅ Hash
✅ Mbstring
✅ OpenSSL
✅ PCRE
✅ PDO
✅ Session
✅ Tokenizer
✅ XML

قاعدة البيانات:
✅ MySQL 5.7+ أو
✅ PostgreSQL 10+ أو
✅ SQLite 3+
```

#### 2. Composer
```
الإصدار: 2.0 أو أعلى
الوصف: مدير المكتبات PHP
التحميل من: https://getcomposer.org
```

#### 3. Node.js و npm
```
Node.js: الإصدار 14+
npm: الإصدار 6+
التحميل من: https://nodejs.org
```

#### 4. Git (اختياري)
```
الإصدار: أي إصدار حديث
الاستخدام: للتحكم بالإصدارات
التحميل من: https://git-scm.com
```

---

## 📦 المكتبات والحزم الرئيسية

### Laravel Framework
```php
laravel/framework: ^12.0
// إطار العمل الأساسي
```

### Authentication & Authorization
```php
laravel/tinker: ^2.9
// أداة تفاعلية للتطوير
```

### Database
```php
doctrine/inflector: ^2.0
doctrine/lexer: ^3.0
// معالجة قاعدة البيانات
```

### View & Front-end
```javascript
@vitejs/plugin-vue: ^4.0.0
laravel-vite-plugin: ^0.8.0
axios: ^1.0
// معالجة الـ Front-end assets
```

### Development Dependencies
```php
laravel/pint: ^1.11
phpunit/phpunit: ^11.0.0
// أدوات الـ testing و linting
```

---

## 🌐 نسخ المتصفح المدعومة

### متصفحات مدعومة
```
✅ Google Chrome 90+
✅ Mozilla Firefox 88+
✅ Safari 14+
✅ Microsoft Edge 90+
✅ Opera 76+
```

### المميزات المطلوبة
```
✅ JavaScript enabled
✅ Cookies enabled
✅ LocalStorage support
✅ Session support
```

---

## 🔐 متطلبات الأمان

### بروتوكولات الاتصال
```
✅ HTTPS/TLS 1.2+ (للإنتاج)
✅ HTTP (للتطوير المحلي)
```

### معايير الأمان
```
✅ CSRF Protection
✅ XSS Protection
✅ SQL Injection Prevention
✅ Password Hashing (Bcrypt)
✅ Secure Session Management
✅ Rate Limiting (اختياري)
```

---

## 💾 متطلبات قاعدة البيانات

### تعريف التطبيق
```sql
Database Charset: utf8mb4
Database Collation: utf8mb4_unicode_ci

// هذا يدعم النصوص العربية بشكل كامل
```

### الجداول المطلوبة
```sql
users                  -- المستخدمين
hospitals              -- المستشفيات
departments            -- الأقسام الطبية
claim_entities         -- الجهات
claims                 -- المطالبات
returned_invoices      -- الفواتير العائدة
payment_orders         -- أوامر الدفع
cache                  -- للـ caching
jobs                   -- لـ queue jobs
```

---

## 📡 متطلبات الشبكة والخادم

### للتطوير المحلي
```
المنفذ (Port) الافتراضي: 8000
العنوان: http://localhost:8000
```

### للإنتاج
```
✅ Dedicated IP address
✅ SSL/TLS Certificate
✅ Domain name
✅ Email for notifications
✅ Backup solution
✅ Monitoring tools
```

---

## 🛠️ أدوات التطوير الاختيارية

### بيئات التطوير الموصى بها
```
Visual Studio Code
├── PHP Intelephense
├── Laravel Extension Pack
├── Database Client
├── Git Graph
└── REST Client

PhpStorm (مدفوع)
├── Built-in Laravel support
├── Excellent code inspection
├── Integrated debugging
└── Database tools
```

### أدوات إضافية مفيدة
```
✅ Postman - لاختبار API
✅ HeidiSQL - لإدارة MySQL
✅ DBeaver - لإدارة قاعدة البيانات
✅ Insomnia - بديل Postman
✅ Git Bash - لـ version control
```

---

## 📋 قائمة التحقق قبل التشغيل

### التحقق من التثبيت

```bash
# تحقق من PHP
php -v
# يجب أن تحصل على الإصدار 8.1+

# تحقق من Composer
composer --version
# يجب أن تحصل على 2.0+

# تحقق من Node.js
node -v
npm -v
# يجب أن تحصل على إصدارات حديثة

# تحقق من MySQL (إذا كنت تستخدمه)
mysql --version
# أو mysql -u root -p
```

### التحقق من المقترنات (Extensions)

```bash
# يمكنك فحص جميع المقترنات
php -m

# يجب أن تري القائمة التالية على الأقل:
# bcmath
# ctype
# curl
# dom
# fileinfo
# filter
# hash
# mbstring
# openssl
# pcre
# pdo
# pdo_mysql (أو pdo_pgsql, pdo_sqlite)
# session
# tokenizer
# xml
```

---

## 🚀 خطوات التحقق قبل البدء

```bash
# 1. استنساخ المشروع (إذا لزم الأمر)
git clone <repository-url> claims-system
cd claims-system

# 2. تثبيت مقترنات PHP
composer install

# 3. تثبيت مقترنات npm
npm install

# 4. إنشاء ملف البيئة
cp .env.example .env

# 5. إنشاء مفتاح التطبيق
php artisan key:generate

# 6. إنشاء مفتاح Passport (إذا استخدمت API authentication)
# php artisan passport:install (اختياري)

# 7. تشغيل الـ migrations
php artisan migrate

# 8. تحميل البيانات الأولية
php artisan db:seed

# 9. تجميع الأصول
npm run dev  # أو npm run build

# 10. تشغيل خادم التطوير
php artisan serve
```

---

## 🔧 ملف البيئة (.env)

### المتغيرات الأساسية المطلوبة

```env
# اسم التطبيق
APP_NAME="Claims System"

# بيئة التشغيل
APP_ENV=local  # local, staging, production

# وضع التصحيح
APP_DEBUG=true  # false في الإنتاج

# مفتاح التطبيق (يتم إنشاؤه تلقائياً)
APP_KEY=base64:...

# عنوان الموقع
APP_URL=http://localhost:8000

# قاعدة البيانات
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=claims_system
DB_USERNAME=root
DB_PASSWORD=

# البريد الإلكتروني
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS=noreply@claims-system.test

# اللغة الافتراضية
APP_LOCALE=ar  # ar أو en
```

---

## 📊 متطلبات الأداء

### للتطوير
```
استجابة الصفحة: < 500ms
حمل CPU: < 50%
استخدام الذاكرة: < 256MB
```

### للإنتاج
```
استجابة الصفحة: < 200ms
حمل CPU: < 30%
استخدام الذاكرة: < 512MB
عدد المستخدمين المتزامنين: 100+
```

---

## 🎯 النقاط المهمة

### قبل الإطلاق
- ✅ تم اختبار جميع المتطلبات
- ✅ تم التحقق من الإصدارات
- ✅ تم التأكد من المقترنات
- ✅ تم إعداد قاعدة البيانات
- ✅ تم إنشاء ملف البيئة

### أثناء التشغيل
- ✅ مراقبة استهلاك الموارد
- ✅ فحص سجلات الأخطاء
- ✅ اختبار جميع الوظائف
- ✅ التحقق من الأداء

### بعد الإطلاق
- ✅ إعداد النسخ الاحتياطية
- ✅ تفعيل المراقبة
- ✅ إعداد التنبيهات
- ✅ وثائق الصيانة

---

## 📞 استكشاف الأخطاء

### المشكلة: "Command not found"
```bash
# تأكد من أن المسار في PATH
php --ini
composer -v
npm -v
```

### المشكلة: "Missing extension"
```bash
# تثبيت المقترن المفقود
# في Windows: تعديل php.ini
# في Linux: apt-get install php-extension-name
```

### المشكلة: "Permission denied"
```bash
# تصحيح الصلاحيات
chmod -R 775 storage bootstrap/cache
```

---

**آخر تحديث**: 2026-01-25
**الإصدار**: 1.0.0

