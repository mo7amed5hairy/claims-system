# النسخ الاحتياطي والاستعادة (Project + Database Backup)

## الإعداد

في `.env` (ولا تُرفع للقیت):

```env
PROJECT_BACKUP_ENABLED=false
PROJECT_BACKUP_PATH=C:/Backups/claims-system
PROJECT_BACKUP_RETENTION_DAYS=14
PROJECT_BACKUP_DAILY_AT=02:30
PROJECT_BACKUP_MYSQLDUMP=mysqldump
```

> بعد أي تعديل في `.env` على سيرفر يستخدم `config:cache` نفّذ:
> `php artisan config:cache`

## ما الذي يتم نسخه؟

- ملف ZIP باسم `backup-YYYY-MM-DD_HHmmss.zip`
- داخله:
  - `database/*.sql` (نتيجة mysqldump إن توفرت)
  - `project/*` ملفات المشروع باستثناء: `vendor`, `node_modules`, `.git`, `storage/framework/cache`, `storage/framework/sessions`, `storage/framework/views`, `storage/logs`, `public/build`, وملفات `.env*`.

## التشغيل اليدوي

- من واجهة الأدمن: `/admin/backups` زر "نسخ احتياطي الآن".
- من CLI:

```powershell
php artisan backup:project
```

## الجدولة

تمت إضافة جدولة يومية في `routes/console.php` عند وقت `PROJECT_BACKUP_DAILY_AT`، ولا تعمل إلا إذا `PROJECT_BACKUP_ENABLED=true`. تحتاج مهمة نظام لتشغيل:

```powershell
php artisan schedule:run
```

## الاحتفاظ (Retention)

`PROJECT_BACKUP_RETENTION_DAYS` (افتراضي 14). عند كل نسخ ناجح يتم حذف ملفات `backup-*.zip` الأقدم من المدة المحددة من مجلد النسخ فقط.

## الاستعادة

1. **قاعدة البيانات**: استخرج ملف SQL من ZIP ثم استورده:
   ```powershell
   mysql -u USER -p DATABASE_NAME < database\xxxx.sql
   ```
2. **ملفات المشروع**: استخرج `project/` إلى مجلد نظيف، ثم:
   - انسخ `.env` الصحيح للبيئة (غير موجود في ZIP).
   - `composer install --no-dev --optimize-autoloader` (إذا لزم).
   - تأكد من صلاحيات `storage/` و`bootstrap/cache/`.
   - `php artisan config:cache` و`php artisan route:cache` حسب البيئة.
3. تحقق من تسجيل الدخول، الجلسات، والمرفقات.

## التحقق

1. فعّل مؤقتًا على بيئة اختبار: `PROJECT_BACKUP_ENABLED=true` ثم `php artisan config:cache`.
2. نفذ `php artisan backup:project` وتأكد من إنشاء ZIP.
3. افتح ZIP وتأكد من وجود SQL وملفات المشروع.
4. تأكد أن `.env` غير موجود داخل ZIP.
5. تحقق من حذف النسخ الأقدم بعد تجاوز فترة الاحتفاظ (اختبار بتقليل المدة مؤقتًا على بيئة اختبار).
6. أعد التعطيل على الإنتاج إن لم يكن مطلوبًا: `PROJECT_BACKUP_ENABLED=false` ثم `php artisan config:cache`.