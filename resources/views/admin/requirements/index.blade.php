@extends('admin.layouts.app')
@livewireStyles
@section('content')
    <section class="section">
        <div class="section-header">
            <h1>قائمة بمتطلبات القبول</h1>
        </div>

        @if (Session::has('success'))
            <div class="container d-flex justify-content-center mt-80">
                <p class="alert alert-success w-75 text-center"> {{ Session::get('success') }} </p>
            </div>
        @endif

        @if (Session::has('error'))
            <div class="container d-flex justify-content-center mt-80">
                <p class="alert alert-success w-75 text-center"> {{ Session::get('error') }} </p>
            </div>
        @endif
        @error('message')
            <div class="container d-flex justify-content-center mt-80">
                <p class="alert alert-danger w-75 text-center fs-3"> {{ 'يرجي تسجيل سبب الرفض ' }} </p>
            </div>
        @enderror
        <div class="section-body">
            <div class="row">
                <div class="col-12 col-md-12">
                    <section class="card">
                        <div class="card-body">
                            <form method="get" class="mb-0">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="input-label">كود الطالب</label>
                                            <input name="user_code" type="text" class="form-control"
                                                value="{{ request()->get('user_code') }}">
                                        </div>
                                    </div>

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
                                            <input name='ar_name' type="text" class="form-control"
                                                value="{{ request()->get('ar_name') }}">
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
                    <div class="card">
                        <div class="card-header">
                            @can('admin_requirements_export_excel')
                                <a href="{{ getAdminPanelUrl() }}/requirements/excel?{{ http_build_query(request()->all()) }}"
                                    class="btn btn-primary">{{ trans('admin/main.export_xls') }}</a>
                            @endcan
                            <div class="h-10"></div>
                        </div>
                            @livewire('requirment-actions')
                    </div>
                </div>
            </div>
        </div>

    </section>
@endsection
@push('scripts_bottom')
    <script>
        function submitForm(e) {
            e.preventDefault();
            let form = e.target;
            let confirmBtn = form.querySelector('#confirmAction');
            confirmBtn.disabled = true;
            confirmBtn.classList.add('loadingbar', 'danger');
            form.submit();
        }
    </script>
@endpush
@livewireScripts
@push('libraries_top')
    <link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.carousel.min.css">
    <link rel="stylesheet" href="/assets/admin/vendor/owl.carousel/owl.theme.min.css">
@endpush
