@extends('claims::layouts.app')

@section('title', 'مسوداتي')

@section('content')
<style>
    .drafts-card { background:#fff; border-radius:12px; box-shadow:0 2px 10px rgba(0,0,0,.06); padding:20px; }
    .drafts-table { width:100%; border-collapse:collapse; }
    .drafts-table th,.drafts-table td { padding:10px 12px; border-bottom:1px solid #eee; text-align:right; vertical-align:middle; }
    .drafts-table th { background:#f8fafc; font-weight:700; }
    .badge-type { display:inline-block; padding:3px 10px; border-radius:999px; background:#eef2ff; color:#4338ca; font-size:12px; font-weight:600; }
    .draft-actions { display:flex; gap:6px; flex-wrap:wrap; }
    .btn-sm { padding:5px 10px; font-size:12px; border-radius:6px; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; gap:4px; }
    .btn-continue { background:#2563eb; color:#fff; }
    .btn-discard { background:#fee2e2; color:#dc2626; }
    .btn-finalize { background:#dcfce7; color:#16a34a; }
    .drafts-empty { padding:30px; text-align:center; color:#64748b; }
</style>

<div class="drafts-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;gap:10px;flex-wrap:wrap;">
        <h2 style="margin:0;font-size:20px;"><i class="fa-solid fa-file-pen"></i> مسوداتي</h2>
        <div style="color:#64748b;font-size:13px;">المسودات يتم حفظها تلقائياً أثناء الكتابة، ويمكنك استكمالها لاحقاً.</div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($drafts->count() === 0)
        <div class="drafts-empty">
            <i class="fa-solid fa-folder-open" style="font-size:40px;color:#cbd5e1;display:block;margin-bottom:10px;"></i>
            لا توجد مسودات محفوظة حالياً.
        </div>
    @else
        <div style="overflow-x:auto;">
        <table class="drafts-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>النوع</th>
                    <th>العنوان / الملخص</th>
                    <th>آخر تعديل</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
            @foreach($drafts as $draft)
                <tr id="draft-row-{{ $draft->id }}">
                    <td>{{ $draft->id }}</td>
                    <td><span class="badge-type">{{ $draft->typeLabel() }}</span></td>
                    <td>{{ $draft->title ?: 'مسودة بدون عنوان' }}</td>
                    <td style="white-space:nowrap;font-size:12px;color:#475569;">{{ $draft->updated_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <div class="draft-actions">
                            <a class="btn-sm btn-continue" href="{{ route('drafts.continue', $draft) }}"><i class="fa-solid fa-pen"></i> استكمال</a>
                            @if(in_array($draft->type, ['claim_create','claim_edit']))
                            <form method="POST" action="{{ route('drafts.finalize', $draft) }}" style="display:inline" onsubmit="return confirm('سيتم حفظ المسودة كسجل فعلي. هل تريد المتابعة؟');">
                                @csrf
                                <button type="submit" class="btn-sm btn-finalize"><i class="fa-solid fa-check"></i> حفظ نهائي</button>
                            </form>
                            @endif
                            <form method="POST" action="{{ route('drafts.discard', $draft) }}" style="display:inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المسودة؟');">
                                @csrf
                                <button type="submit" class="btn-sm btn-discard"><i class="fa-solid fa-trash"></i> حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        </div>
        <div style="margin-top:12px;">{{ $drafts->links() }}</div>
    @endif
</div>
@endsection
