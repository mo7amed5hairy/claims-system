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
                    <button type="submit" class="btn btn-primary">
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
                width: '100%'
            });

            const entities = @json($entities);

            $('#entity_select').on('change', function () {
                const entityId = $(this).val();
                const entity = entities.find(e => e.id == entityId);

                // Reset and hide all dynamic fields
                $('#branches_container, #locations_container, #laws_container').slideUp();
                $('#branch_select, #location_select, #law_select').empty().append('<option value="">اختر...</option>');

                if (entity && entity.metadata) {
                    const data = entity.metadata;

                    // Level 1: Branches
                    if (data.branches && data.branches.length > 0) {
                        $('#branches_container').slideDown();
                        data.branches.forEach(b => {
                            $('#branch_select').append(new Option(b, b));
                        });
                    }
                }
            });

            $('#branch_select').on('change', function () {
                const entityId = $('#entity_select').val();
                const entity = entities.find(e => e.id == entityId);

                // Reset downstream
                $('#locations_container, #laws_container').slideUp();
                $('#location_select, #law_select').empty().append('<option value="">اختر...</option>');

                if (!entity || !entity.metadata) return;
                const data = entity.metadata;

                let locations = data.locations || data.governorates;
                if (locations && locations.length > 0) {
                    $('#locations_container').slideDown();
                    locations.forEach(l => {
                        $('#location_select').append(new Option(l, l));
                    });
                }
            });

            $('#location_select').on('change', function () {
                const entityId = $('#entity_select').val();
                const entity = entities.find(e => e.id == entityId);

                $('#laws_container').slideUp();
                $('#law_select').empty().append('<option value="">اختر...</option>');

                if (!entity || !entity.metadata) return;
                const data = entity.metadata;

                // Level 3: Laws (for Comprehensive)
                if (data.laws && data.laws.length > 0) {
                    $('#laws_container').slideDown();
                    data.laws.forEach(l => {
                        $('#law_select').append(new Option(l, l));
                    });
                }
            });
        });
    </script>
@endsection