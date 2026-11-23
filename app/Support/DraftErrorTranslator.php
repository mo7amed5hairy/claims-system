<?php

namespace App\Support;

use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Throwable;

class DraftErrorTranslator
{
    public static function translate(Throwable $e): string
    {
        if ($e instanceof ValidationException) {
            $msgs = [];
            foreach ($e->errors() as $field => $errors) {
                foreach ($errors as $msg) $msgs[] = $msg;
            }
            return implode(' | ', array_slice($msgs, 0, 4)) ?: 'بيانات غير صالحة';
        }

        if ($e instanceof QueryException) {
            $msg = $e->getMessage();
            if (str_contains($msg, 'Duplicate entry')) {
                return 'يوجد سجل بنفس البيانات (مكرر).';
            }
            if (str_contains($msg, 'foreign key constraint')) {
                return 'بيانات مرتبطة بسجل آخر غير موجود أو لا يمكن تعديلها.';
            }
            return 'خطأ في قاعدة البيانات أثناء حفظ المسودة.';
        }

        return 'حدث خطأ أثناء حفظ المسودة. حاول مرة أخرى.';
    }
}
