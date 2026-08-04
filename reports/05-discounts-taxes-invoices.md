# المرحلة الخامسة: الخصومات والضرائب والفواتير المخصمة
**الفرع:** `eightth-update` | **التاريخ:** 4 أشهر إلى 10 أسابيع مضت

## الملخص
إضافة نظام الخصومات والضرائب لأوامر الدفع، وإنشاء نظام الفواتير المخصمة (Discounted Invoices) مع التجميع التلقائي.

## الإنجازات

### 1. إضافة الخصم والضرائب لأوامر الدفع
- إضافة أعمدة `deduction` و `taxes` لجدول `payment_orders`
- الهجرة: `2026_05_12_143434_add_deduction_and_taxes_to_payment_orders_table.php`
- الخصم والضرائب كنسبة مئوية (وليست قيمة ثابتة)
- الحساب التلقائي: `amount_after_review = amount - (amount × deduction%) - (amount × taxes%)`

### 2. إنشاء الفواتير المخصمة (Discounted Invoices)
- **Controller:** `DiscountedInvoiceController`
- **Model:** `DiscountedInvoice` مع جدول `discounted_invoices`
- **View:** `discounted-invoices/index.blade.php`
- الفواتير تُنشأ تلقائياً عند إضافة أوامر الدفع
- تعرض: عدد الفواتير الأصلي، عدد الفواتير المخصمة، المبلغ الأصلي، المبلغ بعد الخصم

### 3. تعديل أوامر الدفع
- إضافة خانات `deduction` و `taxes` و `amount_after_review` في `payments/create.blade.php`
- تعديل `payments/edit.blade.php` لدعم التعديل
- إضافة `invoice_count_after_review` و `amount_after_review` لجدول `payment_orders`

### 4. خدمات التحديث (Updates)
- تحديث `PaymentOrderController` لدعم الحقول الجديدة
- تحديث `PaymentOrder` model مع `$casts` و `$fillable`

### 5. التحسينات الأخرى
- `make some modifications in system` - تعديلات عامة (`commit 776d0306`)
- `modify claims,payments and add discount-invoices` (`commit 5a741890`)
- تحديثات متفرقة (`commits: d8f84550, 27293378`)

## الملفات المتأثرة
- `app/Modules/Claims/Http/Controllers/DiscountedInvoiceController.php` **(جديد)**
- `app/Modules/Claims/Http/Controllers/PaymentOrderController.php`
- `app/Modules/Claims/Models/DiscountedInvoice.php` **(جديد)**
- `app/Modules/Claims/Models/PaymentOrder.php`
- `app/Modules/Claims/Resources/Views/discounted-invoices/index.blade.php` **(جديد)**
- `app/Modules/Claims/Resources/Views/payments/create.blade.php`
- `app/Modules/Claims/Resources/Views/payments/edit.blade.php`
- `app/Modules/Claims/Resources/Views/payments/index.blade.php`
- `database/migrations/2026_05_12_143434_add_deduction_and_taxes_to_payment_orders_table.php` **(جديد)**
