@extends('admin.layouts.app')

@push('libraries_top')
@endpush

@php
    $segments = explode('/', request()->path());
    $lastSegment = end($segments);
@endphp

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ trans('admin/main.students') }} {{ trans('admin/main.list') }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a>{{ trans('admin/main.students') }}</a></div>
                <div class="breadcrumb-item"><a href="#">{{ trans('admin/main.users_list') }}</a></div>
            </div>
        </div>
    </section>

    <div class="section-body">
        <div class="row">
            <div class="col-12">
                <div class="card card-statistic-1">
                    <div class="card-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="card-wrap">
                        <div class="card-header">
                            <h4>{{ trans('admin/main.total_students') }}</h4>
                        </div>
                        <div class="card-body">
                            {{ $totalStudents }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <section class="card">
            <div class="card-body">
                <form method="get" class="mb-0">

                    <div class="row">
                        @if ($lastSegment === 'users')
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="input-label">كود الطالب</label>
                                    <input name="user_code" type="text" class="form-control"
                                        value="{{ request()->get('user_code') }}">
                                </div>
                            </div>
                        @endif

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">بريد الطالب</label>
                                <input name="email" type="text" class="form-control"
                                    value="{{ request()->get('email') }}">
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">اسم الطالب</label>
                                <input name={{ 'user_name' }} type="text" class="form-control"
                                    value="{{ request()->get('user_name') }}">
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">هاتف الطالب</label>
                                <input name="mobile" type="text" class="form-control"
                                    value="{{ request()->get('mobile') }}">
                            </div>
                        </div>



                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="input-label">{{ trans('admin/main.status') }}</label>
                                <select name="status" data-plugin-selectTwo class="form-control populate">
                                    <option value="">{{ trans('admin/main.all_status') }}</option>
                                    <option value="active_verified" @if (request()->get('status') == 'active_verified') selected @endif>
                                        {{ trans('admin/main.active_verified') }}</option>
                                    <option value="active_notVerified" @if (request()->get('status') == 'active_notVerified') selected @endif>
                                        {{ trans('admin/main.active_not_verified') }}</option>
                                    <option value="inactive" @if (request()->get('status') == 'inactive') selected @endif>
                                        {{ trans('admin/main.inactive') }}</option>
                                    <option value="ban" @if (request()->get('status') == 'ban') selected @endif>
                                        {{ trans('admin/main.banned') }}</option>
                                </select>
                            </div>
                        </div>


                        <div class="col-md-3">
                            <div class="form-group mt-1">
                                <label class="input-label mb-4"> </label>
                                <input type="submit" class="text-center btn btn-primary w-100"
                                    value="{{ trans('admin/main.show_results') }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </div>

    <div class="card">
        <div class="card-header">
            @can('admin_users_export_excel')
                @if ($lastSegment === 'users')
                    <a href="{{ getAdminPanelUrl() }}/students/excelStudent?{{ (!empty($class->id) ? 'class_id=' . $class->id . '&&' : '') . http_build_query(request()->all()) }}"
                        class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>

                    @include('admin.students.includes.importStudents', [
                        'url' => getAdminPanelUrl() . '/students/importStudent',
                        'btnClass' => 'btn btn-danger d-flex align-items-center btn-sm mt-1  mr-3',
                        'btnText' => '<span class="ml-2">رفع الطلاب من الاكسيل</span>',
                        'hideDefaultClass' => true,
                    ])

                    <a href="{{ asset('files/import_student_template.xlsx') }}" class="btn btn-success" download>تحميل قالب
                        النموذج</a>
                    <a href="{{ getAdminPanelUrl() }}/bundles/bundleCodeExcel" class="btn btn-info mr-3">تحميل اكواد الدبلومات
                    </a>
                @else
                    <a href="{{ getAdminPanelUrl() }}/students/excel?{{ (!empty($class->id) ? 'class_id=' . $class->id . '&&' : '') . http_build_query(request()->all()) }}"
                        class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                @endif
            @endcan
            <div class="h-10"></div>
        </div>
        @livewire('registered-student-actions')
        
    </div>


    <section class="card">
        <div class="card-body">
            <div class="section-title ml-0 mt-0 mb-3">
                <h5>{{ trans('admin/main.hints') }}</h5>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">
                            {{ trans('admin/main.students_hint_title_1') }}</div>
                        <div class=" text-small font-600-bold">{{ trans('admin/main.students_hint_description_1') }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">
                            {{ trans('admin/main.students_hint_title_2') }}</div>
                        <div class=" text-small font-600-bold">{{ trans('admin/main.students_hint_description_2') }}
                        </div>
                    </div>
                </div>


                <div class="col-md-4">
                    <div class="media-body">
                        <div class="text-primary mt-0 mb-1 font-weight-bold">
                            {{ trans('admin/main.students_hint_title_3') }}</div>
                        <div class="text-small font-600-bold">{{ trans('admin/main.students_hint_description_3') }}</div>
                    </div>
                </div>
                @if ($lastSegment === 'users')
                    <div class="col-6">
                        <div class="media-body">
                            <div class="text-primary mt-25 mb-1 font-weight-bold">
                                رفع الطلاب من اكسيل
                            </div>
                            <div class="text-small font-600-bold">
                                لإضافة طلاب من خلال ملف اكسيل
                                قم بتحميل قالب النموذج ثم قم بإدخال بيانات الطلاب كامله بيه ثم قم بالضغط علي زر رفع الطلاب
                                من اكسيل ثم قم بتحديد ملف الإكسيل الذي قمت بتحميله بعد اضافه بيانات الطلاب كاملة ثم اضغط
                                ارسال
                            </div>
                        </div>
                    </div>
                @endif



            </div>
        </div>
    </section>
@endsection

@php
    $bundlesByCategory = [];
    foreach ($category as $item) {
        $bundlesByCategory[$item->id] = $item->bundles;
    }
@endphp

@push('scripts_bottom')
    <script src="/assets/default/vendors/daterangepicker/daterangepicker.min.js"></script>

    <script>
        var undefinedActiveSessionLang = '{{ trans('webinars.undefined_active_session') }}';
        var saveSuccessLang = '{{ trans('webinars.success_store') }}';
        var selectChapterLang = '{{ trans('update.select_chapter') }}';
    </script>

    <script src="/assets/default/js/panel/make_next_session.min.js"></script>
    {{-- bundle toggle and education section toggle --}}
    <script>
        function toggleHiddenInput(event) {
            var bundles = @json($bundlesByCategory);

            let selectInput = event.target;
            let myForm = selectInput.closest('form');
            let hiddenInput = myForm.bundle_id;
            let certificateSection = myForm.certificate_section;

            if (selectInput.value && hiddenInput) {
                var categoryId = selectInput.value;
                var categoryBundles = bundles[categoryId];

                if (categoryBundles) {
                    var options = categoryBundles.map(function(bundle) {
                        var isSelected = bundle.id == "{{ old('toDiploma', $student->bundle_id ?? null) }}" ?
                            'selected' : '';
                        return `<option value="${bundle.id}" ${isSelected} has_certificate="${bundle.has_certificate}">${bundle.title}</option>`;
                    }).join('');

                    hiddenInput.outerHTML =
                        '<select id="bundle_id" name="toDiploma"  class="form-control" onchange="CertificateSectionToggle(event)" required>' +
                        '<option value="" class="placeholder" disabled="" selected="selected">اختر التخصص الذي تود دراسته في اكاديمية انس للفنون</option>' +
                        options +
                        '</select>';
                }
            }
        }
    </script>


    {{-- Certificate Section Toggle --}}
    <script>
        function CertificateSectionToggle(event) {

            let myForm = event.target.closest('form');

            let certificateSection = myForm.querySelector("#certificate_section");
            let bundleSelect = myForm.querySelector("#bundle_id");
            // Get the selected option
            var selectedOption = bundleSelect.options[bundleSelect.selectedIndex];
            if (selectedOption.getAttribute('has_certificate') == 1) {
                certificateSection.classList.remove("d-none");
            } else {
                certificateSection.classList.add("d-none");

            }
        }

        function showCertificateMessage(event) {
            let myForm = event.target.closest('form');
            let messageSection = myForm.querySelector("#certificate_message");
            let certificateOption = myForm.querySelector("input[name='certificate']:checked");
            if (certificateOption.value === "1") {
                messageSection.innerHTML = "سوف يحصل على خصم 23%"
            } else if (certificateOption.value === "0") {
                messageSection.innerHTML = "بيفوته الحصول علي خصم 23%"

            } else {
                messageSection.innerHTML = ""

            }
        }
    </script>
@endpush
