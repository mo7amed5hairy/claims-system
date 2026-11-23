<?php

namespace App\Http\Controllers;

use App\Services\ProjectBackupService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BackupController extends Controller
{
    public function __construct(private ProjectBackupService $service)
    {
    }

    private function ensureAdmin(): void
    {
        $u = Auth::user();
        if (!$u || $u->role !== 'admin') abort(403);
    }

    public function index()
    {
        $this->ensureAdmin();
        $backups = $this->service->listBackups();
        $enabled = $this->service->enabled();
        $path = $this->service->backupPath();
        $retention = (int) config('project_backup.retention_days', 14);
        $latest = $backups[0] ?? null;
        return view('admin.backups.index', compact('backups', 'enabled', 'path', 'retention', 'latest'));
    }

    public function run(Request $request)
    {
        $this->ensureAdmin();
        if (!$this->service->enabled()) {
            return back()->with('error', 'النسخ الاحتياطي غير مفعل. اضبط PROJECT_BACKUP_ENABLED=true في .env ثم نفذ config:cache.');
        }
        $result = $this->service->create();
        if ($result['ok']) {
            return back()->with('success', 'تم إنشاء النسخة الاحتياطية بنجاح: ' . basename($result['zip']));
        }
        return back()->with('error', 'فشل النسخ الاحتياطي: ' . ($result['error'] ?? 'خطأ غير معروف'));
    }

    public function download(string $filename)
    {
        $this->ensureAdmin();
        if (!preg_match('/^backup-[\w\-]+\.zip$/', $filename)) abort(404);
        $full = $this->service->backupPath() . DIRECTORY_SEPARATOR . $filename;
        if (!is_file($full)) abort(404);
        return response()->download($full, $filename);
    }

    public function toggle(Request $request)
    {
        $this->ensureAdmin();
        $envPath = base_path('.env');
        if (!is_file($envPath) || !is_readable($envPath) || !is_writable($envPath)) {
            return back()->with('error', 'تعذر تعديل ملف .env. تحقق من الصلاحيات.');
        }

        $content = file_get_contents($envPath);
        $current = filter_var(env('PROJECT_BACKUP_ENABLED', false), FILTER_VALIDATE_BOOL);
        $new = $current ? 'false' : 'true';

        if (preg_match('/^PROJECT_BACKUP_ENABLED=.*/m', $content)) {
            $content = preg_replace('/^PROJECT_BACKUP_ENABLED=.*/m', 'PROJECT_BACKUP_ENABLED=' . $new, $content);
        } else {
            $content .= "\nPROJECT_BACKUP_ENABLED=" . $new . "\n";
        }

        $tmp = $envPath . '.tmp';
        if (file_put_contents($tmp, $content) === false) {
            return back()->with('error', 'تعذر كتابة ملف .env المؤقت.');
        }
        @chmod($tmp, fileperms($envPath) & 0777);
        if (!@rename($tmp, $envPath)) {
            @unlink($tmp);
            return back()->with('error', 'تعذر تحديث ملف .env.');
        }

        // Rebuild config cache so the change takes effect
        try {
            \Illuminate\Support\Facades\Artisan::call('config:clear');
            \Illuminate\Support\Facades\Artisan::call('config:cache');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'تم تعديل .env لكن فشل تحديث config cache: ' . $e->getMessage());
        }

        $msg = $new === 'true' ? 'تم تفعيل النسخ الاحتياطي.' : 'تم تعطيل النسخ الاحتياطي.';
        return back()->with('success', $msg);
    }

    public function delete(Request $request, string $filename)
    {
        $this->ensureAdmin();
        if (!$this->service->deleteBackup($filename)) {
            return back()->with('error', 'تعذر حذف ملف النسخة الاحتياطية.');
        }
        return back()->with('success', 'تم حذف النسخة الاحتياطية: ' . $filename);
    }
}
