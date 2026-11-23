@extends('claims::layouts.app')

@section('title', 'النسخ الاحتياطي')

@section('content')
<div style="background:#fff;border-radius:12px;box-shadow:0 2px 10px rgba(0,0,0,.06);padding:20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;">
        <h2 style="margin:0;font-size:20px;"><i class="fa-solid fa-database"></i> النسخ الاحتياطي للمشروع وقاعدة البيانات</h2>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <form method="POST" action="{{ route('admin.backups.toggle') }}" onsubmit="return confirm('{{ $enabled ? 'هل تريد تعطيل النسخ الاحتياطي؟' : 'هل تريد تفعيل النسخ الاحتياطي؟ سيتم تعديل .env وتنفيذ config:cache.' }}');">
                @csrf
                <button type="submit" style="background:{{ $enabled ? '#dc2626' : '#16a34a' }};color:#fff;border:none;padding:8px 14px;border-radius:8px;cursor:pointer;">
                    <i class="fa-solid fa-power-off"></i> {{ $enabled ? 'تعطيل النسخ الاحتياطي' : 'تفعيل النسخ الاحتياطي' }}
                </button>
            </form>
            <form method="POST" action="{{ route('admin.backups.run') }}" onsubmit="return confirm('سيتم إنشاء نسخة احتياطية الآن. قد تستغرق بعض الوقت. هل تريد المتابعة؟');">
                @csrf
                <button type="submit" style="background:#2563eb;color:#fff;border:none;padding:8px 14px;border-radius:8px;cursor:pointer;" {{ !$enabled ? 'disabled title="فعّل النسخ الاحتياطي أولاً"' : '' }}>
                    <i class="fa-solid fa-play"></i> نسخ احتياطي الآن
                </button>
            </form>
        </div>
    </div>

    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:12px;margin-bottom:16px;">
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;">
            <div style="color:#64748b;font-size:12px;">الحالة</div>
            <div style="font-weight:700;color:{{ $enabled ? '#16a34a' : '#dc2626' }};">{{ $enabled ? 'مفعل' : 'غير مفعل' }}</div>
        </div>
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;">
            <div style="color:#64748b;font-size:12px;">مسار الحفظ</div>
            <div style="font-weight:600;direction:ltr;text-align:right;font-size:12px;word-break:break-all;">{{ $path }}</div>
        </div>
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;">
            <div style="color:#64748b;font-size:12px;">سياسة الاحتفاظ</div>
            <div style="font-weight:700;">{{ $retention }} يوم</div>
        </div>
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:12px;">
            <div style="color:#64748b;font-size:12px;">آخر نسخة</div>
            <div style="font-weight:600;font-size:13px;">{{ $latest['date'] ?? 'لا توجد' }}</div>
        </div>
    </div>

    @if(!$enabled)
        <div style="background:#fff7ed;border:1px solid #fdba74;color:#9a3412;padding:12px;border-radius:8px;margin-bottom:16px;">
            النسخ الاحتياطي غير مفعل. لتفعيله: اضبط <code>PROJECT_BACKUP_ENABLED=true</code> و(اختياريًا) <code>PROJECT_BACKUP_PATH</code> و<code>PROJECT_BACKUP_MYSQLDUMP</code> في <code>.env</code> ثم نفذ <code>php artisan config:cache</code>.
        </div>
    @endif

    <div style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:right;">الملف</th>
                    <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:right;">التاريخ</th>
                    <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:right;">الحجم</th>
                    <th style="padding:10px;border-bottom:1px solid #e2e8f0;text-align:right;">تنزيل</th>
                </tr>
            </thead>
            <tbody>
            @forelse($backups as $b)
                <tr>
                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;direction:ltr;text-align:right;">{{ $b['name'] }}</td>
                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ $b['date'] }}</td>
                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">{{ number_format($b['size'] / 1024 / 1024, 2) }} MB</td>
                    <td style="padding:10px;border-bottom:1px solid #f1f5f9;">
                        <a href="{{ route('admin.backups.download', $b['name']) }}" style="background:#dcfce7;color:#166534;padding:5px 10px;border-radius:6px;text-decoration:none;font-size:12px;">
                            <i class="fa-solid fa-download"></i> تنزيل
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" style="padding:20px;text-align:center;color:#64748b;">لا توجد نسخ احتياطية بعد.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection