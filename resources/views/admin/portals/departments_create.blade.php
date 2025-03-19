@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush
@push('libraries_top')
@endpush

@section('content')
    <div>
        <section class="section">
            <div class="section-header">
                <h1>{{ $pageTitle }}</h1>
                <div class="section-header-breadcrumb">
                    <div class="breadcrumb-item active"><a
                            href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                    </div>
                    <div class="breadcrumb-item">{{ $pageTitle }}</div>
                </div>
            </div>

            <div class="section-body">

                <div class="row">
                    <div class="col-12 col-md-12">
                        <div class="card">
                            <div class="card-header">

                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <div class="col-xl-6 col-lg-6 col-12">
                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif
                                        <form action="{{route('departments.store')}}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="">الاسم</label>
                                                <input type="text" name="name" class="form-control">
                                                @error('name')
                                                    {{ $message }}
                                                @enderror
                                            </div>

                                            <div class="form-group">
                                                <label for="">الاسم بالانجليزية</label>
                                                <input type="text" name="name_en" class="form-control">
                                                @error('name_en')
                                                    {{ $message }}
                                                @enderror
                                            </div>

                                            <button class="btn btn-success btn-sm mt-3">اضافة</button>

                                        </form>
                                    </div>
                                </div>

                            </div>

                            <div class="card-footer text-center">

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
