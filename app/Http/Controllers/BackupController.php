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
}