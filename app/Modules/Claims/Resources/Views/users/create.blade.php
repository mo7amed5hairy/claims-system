@extends('claims::layouts.app')

@section('content')
    <div class="card d-flex justify-content-center" style="gap: 2rem !important;">
        <div class="card-header">
            <h2><i class="fa-solid fa-user-plus"></i> إضافة مستخدم جديد</h2>
        </div>

        <div class="card-body">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="form-row-3">
                    <div class="col-md-6 form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>اسم المستخدم (ID)</label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                        @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-row-3">
                    <div class="col-md-6 form-group">
                        <label>الدور</label>
                        <select name="role" id="roleSelect" class="form-control" required>
                            <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>مستخدم (صلاحيات محدودة)
                            </option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>مدير نظام (صلاحيات كاملة)
                            </option>
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label>كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required>
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-container"
                        style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="active" value="1" {{ old('active', true) ? 'checked' : '' }}
                            style="width: 20px; height: 20px;">
                        <span>تفعيل الحساب (Active)</span>
                    </label>
                </div>

                <hr>

                <div id="permissionsSection" style="{{ old('role') == 'admin' ? 'display: none;' : '' }}">
                    <h3><i class="fa-solid fa-shield-halved"></i> صلاحيات الموديولات</h3>
                    <p class="text-muted">اختر الصلاحيات المحددة لهذا المستخدم:</p>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead class="bg-light">
                                <tr>
                                    <th>الموديول</th>
                                    <th>عرض</th>
                                    <th>إضافة</th>
                                    <th>تعديل</th>
                                    <th>حذف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $modules = [
                                        'claims' => 'المطالبات',
                                        'returns' => 'المرتجعات',
                                        'payments' => 'أوامر الدفع',
                                        'hospitals' => 'المستشفيات',
                                        'entities' => 'الجهات'
                                    ];
                                    $actions = ['view', 'add', 'edit', 'delete'];
                                @endphp
                                @foreach($modules as $key => $label)
                                    <tr>
                                        <td class="text-right"><strong>{{ $label }}</strong></td>
                                        @foreach($actions as $action)
                                            <td>
                                                <input type="checkbox" name="permissions[{{ $key }}][]" value="{{ $action }}"
                                                    class="perm-checkbox" {{ (is_array(old("permissions.$key")) && in_array($action, old("permissions.$key"))) ? 'checked' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card-footer" style="padding: 20px 0;">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-save"></i> حفظ المستخدم
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary btn-lg">إلغاء</a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .form-group {
            margin-bottom: 20px;
        }

        .perm-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .bg-light {
            background-color: #f8f9fa;
        }

        .text-muted {
            font-size: 0.9em;
            color: #6c757d;
            margin-bottom: 15px;
        }
    </style>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#roleSelect').on('change', function () {
                if ($(this).val() === 'admin') {
                    $('#permissionsSection').slideUp(300);
                } else {
                    $('#permissionsSection').slideDown(300);
                }
            });
        });
    </script>
@endsection