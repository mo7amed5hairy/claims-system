@extends('claims::layouts.app')
@section('title', 'الخيارات الفرعية')

@section('content')
    <!-- Breadcrumb Navigation -->
    <div class="breadcrumb-nav">
        <a href="{{ route('dashboard') }}" class="breadcrumb-item">
            <i class="fa-solid fa-home"></i> الرئيسية
        </a>
        <span class="breadcrumb-separator"><i class="fa-solid fa-chevron-left"></i></span>
        <span class="breadcrumb-item active">الخيارات الفرعية</span>
    </div>

    <div class="page-header">
        <h1 class="page-title"><i class="fa-solid fa-sliders"></i> استكمال البيانات</h1>
        <p class="page-subtitle">{{ $type === 'contracts' ? 'اختيار خيارات التعاقدات' : 'اختيار الخيارات الفرعية' }}</p>
    </div>

    <div class="form-container">
        <div class="form-card">
            <form method="POST" action="{{ route('flow.store-options') }}">
                @csrf

                <!-- Entity Selection -->
                <div class="form-group">
                    <label class="form-label">
                        <i class="fa-solid fa-building"></i> الجهة
                    </label>
                    <select name="entity_id" id="entity_select" class="form-control select2" required>
                        <option value="">اختر الجهة...</option>
                        @foreach($entities as $entity)
                            <option value="{{ $entity->id }}">{{ $entity->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Dynamic Containers -->
                <div id="branches_container" class="form-group" style="display:none;">
                    <label class="form-label">
                        <i class="fa-solid fa-code-branch"></i> الفروع / قوائم الانتظار
                    </label>
                    <select name="branch" id="branch_select" class="form-control select2">
                        <option value="">اختر...</option>
                    </select>
                </div>

                <div id="locations_container" class="form-group" style="display:none;">
                    <label class="form-label">
                        <i class="fa-solid fa-map-marker-alt"></i> المحافظة / الموقع
                    </label>
                    <select name="location" id="location_select" class="form-control select2">
                        <option value="">اختر...</option>
                    </select>
                </div>

                <div id="laws_container" class="form-group" style="display:none;">
                    <label class="form-label">
                        <i class="fa-solid fa-scale-balanced"></i> القانون / المنتفعين
                    </label>
                    <select name="law" id="law_select" class="form-control select2">
                        <option value="">اختر...</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" id="next_btn" class="btn btn-primary" disabled>
                        <i class="fa-solid fa-arrow-left"></i> التالي
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
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
            $('.select2').select2({
                dir: "rtl",
                width: '100%',
                closeOnSelect: true
            }).on('select2:select', function (e) {
                $(this).select2('close');
            });

            const entities = @json($entities);

            const nextBtn = $('#next_btn');

            function validateFlow() {
                const entityId = $('#entity_select').val();
                if (!entityId) {
                    disableNext();
                    return;
                }

                const entity = entities.find(e => e.id == entityId);
                const entityName = $('#entity_select').find('option:selected').text().trim();
                const metadata = entity ? entity.metadata : null;

                if (!metadata || (Object.keys(metadata).length === 0)) {
                    enableNext();
                    return;
                }

                let isValid = true;

                // Standard (HI/MoH) -> Require Entity + Branch + Location
                if (!metadata.laws) {
                    if (metadata.branches && metadata.branches.length > 0) {
                        if (!$('#branch_select').val()) isValid = false;
                    }
                    let locations = metadata.locations || metadata.governorates;
                    if (locations && locations.length > 0) {
                        if (!$('#location_select').val()) isValid = false;
                    }
                }
                // UHI -> Require Entity + Location + Law (Ignore Branch)
                else {
                    let locations = metadata.locations || metadata.governorates;
                    if (locations && locations.length > 0) {
                        if (!$('#location_select').val()) isValid = false;
                    }
                    if (metadata.laws && metadata.laws.length > 0) {
                        if (!$('#law_select').val()) isValid = false;
                    }
                }

                if (isValid) {
                    enableNext();
                } else {
                    disableNext();
                }
            }

            function enableNext() {
                nextBtn.prop('disabled', false).css('opacity', '1').css('cursor', 'pointer');
            }

            function disableNext() {
                nextBtn.prop('disabled', true).css('opacity', '0.6').css('cursor', 'not-allowed');
            }

            // Monitor changes on all selects for validation
            $('select').on('change', function () {
                setTimeout(validateFlow, 100);
            });

            $('#entity_select').on('change', function () {
                const entityId = $(this).val();

                // 1. Hide following steps smoothly
                $('#branches_container, #locations_container, #laws_container').slideUp(400);

                // 2. Clear values
                $('#branch_select, #location_select, #law_select').val('').trigger('change.select2');

                if (!entityId) return;

                const entity = entities.find(e => e.id == entityId);
                const entityName = $(this).find('option:selected').text().trim();

                if (entity && entity.metadata) {
                    const data = entity.metadata;

                    // Special Case: UHI -> Skip Branch, Show Governorate
                    if (data.laws) {
                        $('#branch_select').empty().append('<option value="">اختر...</option>');

                        let locations = data.locations || data.governorates;
                        if (locations && locations.length > 0) {
                            $('#location_select').empty().append('<option value="">اختر المحافظة...</option>');
                            locations.forEach(l => {
                                $('#location_select').append(new Option(l, l));
                            });
                            $('#locations_container').stop(true, true).delay(400).slideDown(400);
                        }
                    }
                    // Normal Case: Show Branches
                    else if (data.branches && data.branches.length > 0) {
                        $('#branch_select').empty().append('<option value="">اختر الفرع...</option>');
                        data.branches.forEach(b => {
                            $('#branch_select').append(new Option(b, b));
                        });
                        $('#branches_container').stop(true, true).delay(400).slideDown(400);
                    }
                }
            });

            $('#branch_select').on('change', function () {
                const branchVal = $(this).val();

                // 1. Hide following steps
                $('#locations_container, #laws_container').slideUp(400);
                $('#location_select, #law_select').val('').trigger('change.select2');

                if (!branchVal) return;

                const entityId = $('#entity_select').val();
                const entity = entities.find(e => e.id == entityId);

                if (entity && entity.metadata) {
                    const data = entity.metadata;
                    let locations = data.locations || data.governorates;
                    if (locations && locations.length > 0) {
                        $('#location_select').empty().append('<option value="">اختر المحافظة...</option>');
                        locations.forEach(l => {
                            $('#location_select').append(new Option(l, l));
                        });
                        $('#locations_container').stop(true, true).delay(400).slideDown(400);
                    }
                }
            });

            $('#location_select').on('change', function () {
                const locVal = $(this).val();

                // 1. Hide following steps
                $('#laws_container').slideUp(400);
                $('#law_select').val('').trigger('change.select2');

                if (!locVal) return;

                const entityId = $('#entity_select').val();
                const entity = entities.find(e => e.id == entityId);

                if (entity && entity.metadata && entity.metadata.laws && entity.metadata.laws.length > 0) {
                    $('#law_select').empty().append('<option value="">اختر المستفيد...</option>');
                    entity.metadata.laws.forEach(l => {
                        $('#law_select').append(new Option(l, l));
                    });
                    $('#laws_container').stop(true, true).delay(400).slideDown(400);
                }
            });

            // Run initial check
            validateFlow();
        });
    </script>
@endsection