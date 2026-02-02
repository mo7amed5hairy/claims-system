@extends('claims::layouts.app')

@section('title', 'تسجيل أمر دفع')


<style>
    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr !important;
        gap: 24px;
        margin-bottom: 24px;
    }
</style>

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <a href="{{ route('flow.operations') }}" class="breadcrumb-item">العمليات</a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">تسجيل أمر دفع</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-credit-card"></i> تسجيل أمر دفع</h1>
        <p class="page-subtitle">
            <span><i class="fa-solid fa-hospital"></i> {{ $hospital->name }}</span>
            <span style="margin: 0 8px;">•</span>
            <span><i class="fa-solid fa-stethoscope"></i> {{ $department->name }}</span>
        </p>
    </div>

    <div class="form-container">
        <div class="form-card">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-list-check"></i> نوع الحساب</label>
                        <select name="account_type" class="form-control select2" required>
                            <option value="">اختر النوع</option>
                            <option value="بنكى" {{ old('account_type') == 'بنكى' ? 'selected' : '' }}>بنكى</option>
                            <option value="أمر دفع برقم مؤسسى" {{ old('account_type') == 'أمر دفع برقم مؤسسى' ? 'selected' : '' }}>أمر دفع برقم مؤسسى</option>
                            <option value="شيك نقدى" {{ old('account_type') == 'شيك نقدى' ? 'selected' : '' }}>شيك نقدى
                            </option>
                        </select>
                        @error('account_type') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-hashtag"></i> رقم الـ GP / الشيك</label>
                        <input type="text" name="gp_number" class="form-control" value="{{ old('gp_number') }}" required>
                        @error('gp_number') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-money-bill-wave"></i> المبلغ (ج.م)</label>
                        <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount') }}"
                            required>
                        @error('amount') <span class="error-message">{!! $message !!}</span> @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-calendar-check"></i> تاريخ الاستحقاق</label>
                        <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}" required>
                        @error('due_date') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-building"></i> الجهة المسددة</label>
                        <select name="payer_entity_id" id="entitySelect" class="form-control select2" required>
                            <option value="">اختر الجهة</option>
                            @foreach($entities as $entity)
                                <option value="{{ $entity->id }}" data-metadata='@json($entity->metadata)' {{ old('payer_entity_id') == $entity->id ? 'selected' : '' }}>{{ $entity->name }}</option>
                            @endforeach
                        </select>
                        @error('payer_entity_id') <span class="error-message">{{ $message }}</span> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label"><i class="fa-solid fa-barcode"></i> رقم الفاتورة الإلكترونية</label>
                        <input type="text" name="electronic_invoice_no" class="form-control"
                            value="{{ old('electronic_invoice_no') }}">
                        @error('electronic_invoice_no') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Dynamic Fields for Entity -->
                <div class="form-row d-flex" style="gap: 8px; margin: 0; margin-bottom: 15px;">

                    <div class="form-group" id="branchContainer" style="display: none; flex: 1; margin: 0;">
                        <label class="form-label">
                            <i class="fa-solid fa-layer-group"></i> الفروع
                        </label>
                        <select name="branch" id="branchSelect" class="form-control select2">
                            <option value="">اختر الفرع</option>
                        </select>
                    </div>

                    <div class="form-group" id="subContainer" style="display: none; flex: 1; margin: 0;">
                        <label class="form-label" id="subLabel">
                            <i class="fa-solid fa-map-marker-alt"></i> المحافظات / المواقع
                        </label>
                        <select name="location" id="subSelect" class="form-control select2">
                            <option value="">اختر المحافظة / الموقع</option>
                        </select>
                    </div>

                    <div class="form-group" id="lawsContainer" style="display: none; flex: 1; margin: 0;">
                        <label class="form-label">
                            <i class="fa-solid fa-file-lines"></i> المستفيدين / القوانين
                        </label>
                        <select name="beneficiary" id="lawsSelect" class="form-control select2">
                            <option value="">اختر المستفيد</option>
                        </select>
                    </div>

                </div>


                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-note-sticky"></i> ملاحظات</label>
                    <textarea name="notes" class="form-control" rows="3"
                        placeholder="أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                    @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                </div>

                <div class="form-actions" style="margin-top: 32px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check"></i> تأكيد أمر الدفع
                    </button>
                    <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-right"></i> العودة
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection



@section('scripts')
    <link rel="stylesheet" href="{{ asset('css/select2.min.css') }}">
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/select2.min.js') }}"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2({ dir: "rtl", width: '100%' });

            const entitySelect = $('#entitySelect');
            const branchContainer = $('#branchContainer');
            const branchSelect = $('#branchSelect');
            const subContainer = $('#subContainer');
            const subSelect = $('#subSelect');
            const subLabel = $('#subLabel');
            const lawsContainer = $('#lawsContainer');
            const lawsSelect = $('#lawsSelect');

            function resetSub() {
                subSelect.empty().append('<option value="">اختر الاختيار</option>');
                subContainer.hide();

                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                lawsContainer.hide();
            }

            function fillBranchOptions(metadata, selectedBranch = '') {
                branchSelect.empty().append('<option value="">اختر الفرع</option>');
                if (metadata?.branches?.length) {
                    metadata.branches.forEach(branch => {
                        branchSelect.append(`<option value="${branch}">${branch}</option>`);
                    });
                    branchContainer.show();
                    if (selectedBranch) branchSelect.val(selectedBranch).trigger('change');
                } else {
                    branchContainer.hide();
                }
            }

            function fillSubOptions(metadata, branchVal, selectedSub = '') {
                subSelect.empty().append('<option value="">اختر الاختيار</option>');
                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                lawsContainer.hide();

                if (!branchVal) return;

                let subItems = [];
                let labelText = 'المحافظات / المواقع';

                if (metadata.laws) {
                    subItems = metadata.governorates || [];
                    labelText = 'المحافظات';
                } else if (metadata.governorates) {
                    subItems = metadata.governorates;
                    labelText = 'المحافظات';
                } else if (metadata.locations) {
                    subItems = metadata.locations;
                    labelText = 'المواقع';
                }

                if (subItems.length) {
                    subItems.forEach(item => subSelect.append(`<option value="${item}">${item}</option>`));
                    subLabel.text(labelText);
                    subContainer.show();
                    if (selectedSub) subSelect.val(selectedSub).trigger('change');
                }
            }

            function fillLawsOptions(metadata, subVal, selectedLaw = '') {
                lawsSelect.empty().append('<option value="">اختر المستفيد</option>');
                if (!metadata.laws || !subVal) return;

                metadata.laws.forEach(item => lawsSelect.append(`<option value="${item}">${item}</option>`));
                lawsContainer.show();
                if (selectedLaw) lawsSelect.val(selectedLaw).trigger('change');
            }

            entitySelect.on('change', function () {
                const selectedOption = $(this).find('option:selected');
                const metadata = selectedOption.data('metadata');
                fillBranchOptions(metadata);
                resetSub();
            });

            branchSelect.on('change', function () {
                const branchVal = $(this).val() || '';
                const metadata = entitySelect.find('option:selected').data('metadata');
                fillSubOptions(metadata, branchVal);
            });

            subSelect.on('change', function () {
                const subVal = $(this).val() || '';
                const metadata = entitySelect.find('option:selected').data('metadata');
                fillLawsOptions(metadata, subVal);
            });

            // Initialize if old values exist
            const initialEntity = '{{ old("payer_entity_id") }}';
            const initialBranch = '{{ old("branch") }}';
            const initialSub = '{{ old("location") }}';
            const initialLaw = '{{ old("beneficiary") }}';

            if (initialEntity) {
                // Since select2 is initialized, we can just trigger change logic if we had metadata access easily
                // But we need to make sure the element has the data
                // The HTML matches old(), so the option is selected. We just need to trigger the logic.
                const selectedOption = entitySelect.find('option:selected');
                if (selectedOption.length) {
                    const metadata = selectedOption.data('metadata');
                    fillBranchOptions(metadata, initialBranch);
                    if (initialBranch) fillSubOptions(metadata, initialBranch, initialSub);
                    if (initialSub) fillLawsOptions(metadata, initialSub, initialLaw);
                }
            }
        });
    </script>
@endsection