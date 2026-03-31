@extends('claims::layouts.app')

@section('content')
    <div class="card">
        <div class="alert alert-info">
            <h2><i class="fa-solid fa-user-edit"></i> تعديل بيانات المستخدم: {{ $user->name }}</h2>
        </div>

        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-row-3">
                    <div class="col-md-6 form-group">
                        <label>الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>اسم المستخدم (ID)</label>
                        <input type="text" name="username" class="form-control"
                            value="{{ old('username', $user->username) }}" required>
                        @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-md-6 form-group">
                        <label>البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}">
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="form-row-3">
                    <div class="col-md-6 form-group">
                        <label>الدور</label>
                        <select name="role" id="roleSelect" class="form-control" required>
                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>مستخدم (صلاحيات
                                محدودة)</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>مدير نظام
                                (صلاحيات كاملة)</option>
                        </select>
                    </div>

                    <div class="col-md-6 form-group">
                        <label>كلمة المرور (اتركها فارغة للتعديل بدون تغيير)</label>
                        <input type="password" name="password" class="form-control">
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                    <div class="col-md-6 form-group">
                        <label>تأكيد كلمة المرور</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>
                </div>

                <div class="alert alert-info">
                    <label class="checkbox-container"
                        style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                        <input type="checkbox" name="active" value="1" {{ old('active', $user->active) ? 'checked' : '' }}
                            style="width: 20px; height: 20px;">
                        <span>تفعيل الحساب (Active)</span>
                    </label>
                </div>

                <hr>

                <div id="permissionsSection" style="{{ old('role', $user->role) == 'admin' ? 'display: none;' : '' }}">
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
                                    $userPerms = is_array($user->permissions) ? $user->permissions : [];
                                @endphp
                                @foreach($modules as $key => $label)
                                    <tr>
                                        <td class="text-right"><strong>{{ $label }}</strong></td>
                                        @foreach($actions as $action)
                                            <td>
                                                <input type="checkbox" name="permissions[{{ $key }}][]" value="{{ $action }}"
                                                    class="perm-checkbox" {{ (is_array(old("permissions.$key", $userPerms[$key] ?? [])) && in_array($action, old("permissions.$key", $userPerms[$key] ?? []))) ? 'checked' : '' }}>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="userTypeSection" style="{{ old('role', $user->role) == 'admin' ? 'display: none;' : '' }}">
                    <hr>
                    <h3><i class="fa-solid fa-user-tag"></i> نوع المستخدم</h3>
                    <p class="text-muted">اختر نوع المستخدم (يمكن اختيار أكثر من نوع):</p>
                    <div class="form-group">
                        <div class="d-flex gap-4" style="gap: 30px; display: flex; flex-wrap: wrap;">
                            @php
                                $userTypes = is_array($user->user_type) ? $user->user_type : [];
                            @endphp
                            <label class="checkbox-container" style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" name="user_type[]" value="مراجع" {{ (is_array(old('user_type', $userTypes)) && in_array('مراجع', old('user_type', $userTypes))) ? 'checked' : '' }}
                                    style="width: 20px; height: 20px;">
                                <span>مراجع</span>
                            </label>
                            <label class="checkbox-container" style="display: inline-flex; align-items: center; gap: 10px; cursor: pointer;">
                                <input type="checkbox" name="user_type[]" value="معاملات مالية" {{ (is_array(old('user_type', $userTypes)) && in_array('معاملات مالية', old('user_type', $userTypes))) ? 'checked' : '' }}
                                    style="width: 20px; height: 20px;">
                                <span>معاملات مالية</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card-footer" style="padding: 20px 0;">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-save"></i> تحديث المستخدم
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
                    $('#userTypeSection').slideUp(300);
                } else {
                    $('#permissionsSection').slideDown(300);
                    $('#userTypeSection').slideDown(300);
                }
            });
        });
    </script>
@endsection