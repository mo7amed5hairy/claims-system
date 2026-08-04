# المرحلة السادسة: التقارير والمطالبات مسبقة الدفع
**الفرع:** `ninth-update` | **التاريخ:** ~9 أسابيع مضت

## الملخص
إضافة نظام التقارير المتكامل للمطالبات وأوامر الدفع، وإنشاء نظام المطالبات مسبقة الدفع (Prepaid Claims/Payments) بالكامل.

## الإنجازات

### 1. نظام التقارير (Reports)
- **Controllers:**
  - `ReportController` - تقارير المطالبات العادية
  - `PrepaidReportController` - تقارير المطالبات مسبقة الدفع
- **Views:**
  - `reports/index.blade.php` - صفحة التقارير مع:
    - فلترة حسب السنة والشهر والمستشفى
    - جدول بعرض: المستشفى، الجهة، القسم، رقم المطالبة، الشهر، عدد الحالات، مبلغ العملية، حالات/مبلغ المراجعة، حالات/مبلغ التحصيل، الفرق، رقم أمر الدفع، تاريخ الاستحقاق
    - قسم ملخص الإجماليات (Total summary)
    - أزرار تصدير Excel مع تنسيق RTL
    - فلاتر متعددة الأعمدة (Column filters)
    - فلاتر فرعية للجهات (Entity sub-filters: المحافظات للتأمين الصحي، أنواع لقوائم الانتظار)
  - `prepaid-reports/index.blade.php` - نفس هيكل التقارير لكن للمطالبات مسبقة الدفع

### 2. نظام المطالبات مسبقة الدفع (Prepaid)
- **Controllers:**
  - `PrepaidClaimController` - CRUD للمطالبات مسبقة الدفع
  - `PrepaidPaymentOrderController` - CRUD لأوامر الدفع المسبقة
  - `PrepaidDiscountedInvoiceController` - الفواتير المخصمة للمدفوعات المسبقة
- **Models:**
  - `PrepaidClaim` - يرث من `Claim` مع سكوب `is_prepaid = 1`
  - `PrepaidPaymentOrder` - يرث من `PaymentOrder` مع سكوب `is_prepaid = 1`
  - `PrepaidDiscountedInvoice` - يرث من `DiscountedInvoice` مع سكوب `is_prepaid = 1`
- **Views:**
  - `prepaid-claims/create.blade.php`, `edit.blade.php`, `index.blade.php`
  - `prepaid-payments/create.blade.php`, `edit.blade.php`, `index.blade.php`
  - `prepaid-discounted-invoices/index.blade.php`
  - `flow/prepaid_operations.blade.php` - لوحة عمليات الدفع المسبق

### 3. نظام التدفق للدفع المسبق
- إضافة مسار `flow/prepaid_operations` في `FlowController`
- دعم قوائم الانتظار (Waiting Lists) للدفع المسبق
- دعم أنواع متعددة: insurance, ministry, health directorate

### 4. تحسينات في الفلاتر
- فلاتر فرعية للجهات في التقارير:
  - الهيئة العامة للتأمين الصحي → محافظات (القاهرة، الجيزة، رئاسة الهيئة، ...)
  - الهيئة العامة للتأمين الصحي الشامل → محافظات (السويس، الإسماعيلية، الأقصر، ...)
  - قوائم الانتظار → أنواع (قوائم انتظار التأمين الصحي، قوائم انتظار مديرية الشئون الصحية)

### 5. التحميلات والمرفقات
- دعم رفع المرفقات في أوامر الدفع (attachments)
- دعم إزالة المرفقات عند التعديل
