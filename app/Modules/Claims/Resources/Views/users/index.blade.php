@extends('claims::layouts.app')

@section('content')
    <div class="card">
        <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
            <h2><i class="fa-solid fa-users-cog"></i> إدارة المستخدمين</h2>
            <a href="{{ route('users.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-user-plus"></i> إضافة مستخدم جديد
            </a>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table" id="usersTable">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>اسم المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الدور</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email ?? '---' }}</td>
                                <td>
                                    @if($user->role === 'admin')
                                        <span class="badge badge-admin">مدير نظام</span>
                                    @else
                                        <span class="badge badge-user">مستخدم</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->active)
                                        <span class="text-success"><i class="fa-solid fa-check-circle"></i> نشط</span>
                                    @else
                                        <span class="text-danger"><i class="fa-solid fa-times-circle"></i> معطل</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-info"
                                            title="تعديل">
                                            <i class="fa-solid fa-edit"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                style="display:inline;"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-admin {
            background-color: #e3f2fd;
            color: #1976d2;
        }

        .badge-user {
            background-color: #f5f5f5;
            color: #616161;
        }

        .text-success {
            color: #2e7d32;
        }

        .text-danger {
            color: #d32f2f;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#usersTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/ar.json'
                }
            });
        });
    </script>
@endsection