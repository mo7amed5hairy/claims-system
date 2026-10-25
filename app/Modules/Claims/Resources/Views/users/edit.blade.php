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

                <div id="viewAllSection" style="{{ old('role', $user->role) == 'admin' ? 'display: none;' : '' }}">
                    <div class="alert alert-info">
                        <label class="checkbox-container"
                            style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                            <input type="checkbox" name="can_view_all_data" value="1" {{ old('can_view_all_data', $user->can_view_all_data) ? 'checked' : '' }}
                                style="width: 20px; height: 20px;">
                            <span>عرض جميع بيانات المستخدمين (المطالبات + أوامر الدفع + الفواتير المخصمة + سندات القبض)</span>
                        </label>
                        <small class="text-muted">إذا لم يُفعّل، سيظهر للمستخدم بياناته فقط</small>
                    </div>
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

                @php
                    $boundFlow = is_array($user->bound_flow) ? $user->bound_flow : null;
                @endphp

                <div id="boundFlowSection">
                    <hr>
                    <h3><i class="fa-solid fa-link"></i> ربط المستخدم بجهة معينة (اختياري)</h3>
                    <p class="text-muted">لو ربطت المستخدم بجهة، هيدخل على طول لاختيار المستشفى والقسم من غير ما يختار الجهة من الرئيسية. ولو سايبها من غير تحديد هيكمل عادي زي ما هو.</p>

                    <div class="form-group">
                        <label>الجهة الأساسية</label>
                        <select name="bound_entity_type" id="boundEntityType" class="form-control">
                            <option value="">من غير ربط (الدخول العادي)</option>
                            <option value="contracts" {{ old('bound_entity_type', $boundFlow['entity_type'] ?? '') == 'contracts' ? 'selected' : '' }}>التعاقدات</option>
                            <option value="insurance" {{ old('bound_entity_type', $boundFlow['entity_type'] ?? '') == 'insurance' ? 'selected' : '' }}>التأمين الصحي</option>
                            <option value="ministry" {{ old('bound_entity_type', $boundFlow['entity_type'] ?? '') == 'ministry' ? 'selected' : '' }}>مديرية الشئون الصحية</option>
                            <option value="comprehensive" {{ old('bound_entity_type', $boundFlow['entity_type'] ?? '') == 'comprehensive' ? 'selected' : '' }}>التأمين الصحي الشامل</option>
                            <option value="waiting_lists" {{ old('bound_entity_type', $boundFlow['entity_type'] ?? '') == 'waiting_lists' ? 'selected' : '' }}>قوائم الانتظار</option>
                        </select>
                    </div>

                    <div class="form-group" id="boundSubGroup" style="display:none;">
                        <label>الجهة الفرعية</label>
                        <select name="bound_entity_id" id="boundSubEntity" class="form-control">
                            <option value="">اختر الجهة الفرعية...</option>
                        </select>
                        <small class="text-muted" id="boundSubNote" style="display:none;"></small>
                    </div>

                    <div class="form-group" id="boundLocationGroup" style="display:none;">
                        <label>المحافظة</label>
                        <select name="bound_location" id="boundLocation" class="form-control">
                            <option value="">اختر المحافظة...</option>
                        </select>
                    </div>

                    <div class="form-group" id="boundCascadeLawGroup" style="display:none;">
                        <label>المستفيد</label>
                        <select name="bound_law" id="boundCascadeLaw" class="form-control">
                            <option value="">اختر المستفيد...</option>
                        </select>
                    </div>

                    <div id="boundWaitingSection" style="display:none;">
                        <div class="form-group">
                            <label>نوع قائمة الانتظار</label>
                            <select name="bound_waiting_list_type" id="boundWaitingType" class="form-control">
                                <option value="insurance">قوائم انتظار التأمين الصحي</option>
                                <option value="ministry">قوائم انتظار مديرية الشئون الصحية</option>
                            </select>
                        </div>

                        <div class="form-group" id="boundHospitalGroup" style="display:none;">
                            <label>المستشفى</label>
                            <select name="bound_hospital" id="boundHospital" class="form-control">
                                <option value="">اختر المستشفى...</option>
                                <option value="children">مستشفى الأطفال</option>
                                <option value="women">مستشفى النساء والتوليد</option>
                                <option value="ain_shams">مستشفى عين شمس الباطنة</option>
                                <option value="others">باقى المستشفيات</option>
                            </select>
                        </div>

                        <div class="form-group" id="boundDepartmentGroup" style="display:none;">
                            <label>القسم / المستشفى</label>
                            <select name="bound_department" id="boundDepartment" class="form-control">
                                <option value="">اختر...</option>
                            </select>
                        </div>

                        <div class="form-group" id="boundLawGroup" style="display:none;">
                            <label>المستفيد</label>
                            <select name="bound_law" id="boundLaw" class="form-control">
                                <option value="">اختر المستفيد...</option>
                                <option value="طلبة">طلبة</option>
                                <option value="مواليد">مواليد</option>
                                <option value="منتفعين">منتفعين</option>
                                <option value="امرأة معيلة">امرأة معيلة</option>
                            </select>
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
                    $('#viewAllSection').slideUp(300);
                } else {
                    $('#permissionsSection').slideDown(300);
                    $('#userTypeSection').slideDown(300);
                    $('#viewAllSection').slideDown(300);
                }
            });

            // ===== ربط المستخدم بجهة (Bound Flow) =====
            const entityGroups = @json($entityGroups);
            const departmentsData = {
                'children': ['قسم الأطفال العام', 'قسم حديثي الولادة', 'قسم العناية المركزة للأطفال'],
                'women': ['قسم النساء العام', 'قسم الولادة', 'قسم العناية المركزة للنساء'],
                'ain_shams': ['قسم القلب والرعاية المركزة', 'قسم السكتة الدماغية'],
                'others': ['مستشفى الدمرداش الجراحى', 'مستشفى العبور', 'مستشفى أمراض وجراحات القلب']
            };
            const boundFlow = @json($boundFlow);

            function resetBoundFlow() {
                $('#boundSubGroup').hide();
                $('#boundWaitingSection').hide();
                $('#boundHospitalGroup, #boundDepartmentGroup, #boundLawGroup, #boundLocationGroup, #boundCascadeLawGroup').hide();
                $('#boundSubNote').hide();
                $('#boundSubEntity').val('');
                $('#boundHospital, #boundLaw, #boundLocation, #boundCascadeLaw').val('');
                $('#boundDepartment').val('').empty();
            }

            $('#boundEntityType').on('change', function () {
                resetBoundFlow();
                const type = $(this).val();
                if (!type) return;

                if (type === 'ministry') {
                    $('#boundSubNote').text('سيتم الربط مباشرة بجهة (مديريه الشؤون الصحيه)');
                    $('#boundSubNote').show();
                    $('#boundSubEntity').hide();
                    $('#boundSubGroup').show();
                } else if (type === 'waiting_lists') {
                    $('#boundWaitingSection').show();
                    $('#boundWaitingType').trigger('change');
                } else {
                    const list = entityGroups[type] || [];
                    $('#boundSubEntity').empty().append('<option value="">اختر الجهة الفرعية...</option>');
                    list.forEach(function (e) {
                        $('#boundSubEntity').append('<option value="' + e.id + '">' + e.name + '</option>');
                    });
                    $('#boundSubEntity').show();
                    $('#boundSubGroup').show();
                }
            });

            // Sub-entity change -> populate governorates (المحافظات) from metadata
            $('#boundSubEntity').on('change', function () {
                $('#boundLocationGroup, #boundCascadeLawGroup').hide();
                $('#boundLocation, #boundCascadeLaw').val('');
                const type = $('#boundEntityType').val();
                const entity_id = $(this).val();
                if (!entity_id) return;

                const entity = (entityGroups[type] || []).find(e => String(e.id) === String(entity_id));
                if (!entity || !entity.metadata) return;
                const items = entity.metadata.locations || entity.metadata.governorates || [];
                if (items.length) {
                    $('#boundLocation').empty().append('<option value="">اختر المحافظة...</option>');
                    items.forEach(function (l) {
                        $('#boundLocation').append(new Option(l, l));
                    });
                    $('#boundLocationGroup').show();
                }
            });

            // Governorate change -> populate beneficiaries (المستفيدين) for insurance
            $('#boundLocation').on('change', function () {
                $('#boundCascadeLawGroup').hide();
                $('#boundCascadeLaw').val('');
                const type = $('#boundEntityType').val();
                if (type !== 'insurance' || !$(this).val()) return;

                const entity = (entityGroups['insurance'] || []).find(e => String(e.id) === String($('#boundSubEntity').val()));
                if (entity && entity.metadata && entity.metadata.laws) {
                    $('#boundCascadeLaw').empty().append('<option value="">اختر المستفيد...</option>');
                    entity.metadata.laws.forEach(function (l) {
                        $('#boundCascadeLaw').append(new Option(l, l));
                    });
                    $('#boundCascadeLawGroup').show();
                }
            });

            $('#boundWaitingType').on('change', function () {
                $('#boundHospitalGroup, #boundDepartmentGroup, #boundLawGroup').hide();
                $('#boundHospital, #boundLaw').val('');
                $('#boundDepartment').val('').empty();
                $('#boundHospitalGroup').show();
            });

            $('#boundHospital').on('change', function () {
                const hospital = $(this).val();
                $('#boundDepartmentGroup, #boundLawGroup').hide();
                $('#boundDepartment').val('').empty();
                $('#boundLaw').val('');
                if (!hospital) return;

                const departments = departmentsData[hospital] || [];
                if (departments.length) {
                    $('#boundDepartment').empty().append('<option value="">' + (hospital === 'others' ? 'اختر المستشفى...' : 'اختر القسم...') + '</option>');
                    departments.forEach(function (d) {
                        $('#boundDepartment').append('<option value="' + d + '">' + d + '</option>');
                    });
                    $('#boundDepartmentGroup').show();
                }
            });

            $('#boundDepartment').on('change', function () {
                $('#boundLawGroup').hide();
                $('#boundLaw').val('');
                if ($(this).val() && $('#boundWaitingType').val() === 'insurance') {
                    $('#boundLawGroup').show();
                }
            });

            // ===== Initialize bound flow (old() on validation error, else saved values) =====
            const oldBoundType = '{{ old('bound_entity_type') }}';
            const oldBoundSub = '{{ old('bound_entity_id') }}';
            const oldBoundLoc = '{{ old('bound_location') }}';
            const oldBoundCascadeLaw = '{{ old('bound_law') }}';
            const oldBoundWlt = '{{ old('bound_waiting_list_type') }}';
            const oldBoundHosp = '{{ old('bound_hospital') }}';
            const oldBoundDept = '{{ old('bound_department') }}';
            const hasOld = oldBoundType !== '';

            const effType = hasOld ? oldBoundType : (boundFlow ? (boundFlow.entity_type || '') : '');
            if (effType) {
                $('#boundEntityType').val(effType).trigger('change');

                if (effType === 'ministry') {
                    // no sub-entity; fixed to 84
                } else if (effType === 'waiting_lists') {
                    const vWlt = hasOld ? oldBoundWlt : (boundFlow.waiting_list_type || 'insurance');
                    const vHosp = hasOld ? oldBoundHosp : (boundFlow.hospital || '');
                    const vDept = hasOld ? oldBoundDept : (boundFlow.department || '');
                    const vLaw = hasOld ? oldBoundCascadeLaw : (boundFlow.law || '');
                    $('#boundWaitingType').val(vWlt || 'insurance').trigger('change');
                    if (vHosp) {
                        $('#boundHospital').val(vHosp).trigger('change');
                    }
                    if (vDept) {
                        $('#boundDepartment').val(vDept).trigger('change');
                    }
                    if (vLaw && $('#boundWaitingType').val() === 'insurance') {
                        $('#boundLaw').val(vLaw);
                        $('#boundLawGroup').show();
                    }
                } else {
                    const vSub = hasOld ? oldBoundSub : String(boundFlow.entity_id || '');
                    const vLoc = hasOld ? oldBoundLoc : (boundFlow.location || '');
                    const vLaw = hasOld ? oldBoundCascadeLaw : (boundFlow.law || '');
                    if (vSub) {
                        $('#boundSubEntity').val(vSub).trigger('change');
                    }
                    if (vLoc) {
                        $('#boundLocation').val(vLoc).trigger('change');
                    }
                    if (vLaw) {
                        $('#boundCascadeLaw').val(vLaw);
                        $('#boundCascadeLawGroup').show();
                    }
                }
            }
        });
    </script>
@endsection