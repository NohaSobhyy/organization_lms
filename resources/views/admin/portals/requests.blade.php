@extends('admin.layouts.app')
@php
    use App\Models\PortalMeetingsSlots;
@endphp
@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush
@push('libraries_top')
@endpush

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>{{ $pageTitle }}</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item active"><a href="{{ getAdminPanelUrl() }}">{{ trans('admin/main.dashboard') }}</a>
                </div>
                <div class="breadcrumb-item">{{ $pageTitle }}</div>
            </div>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            {{-- @can('admin_pages_create')
                        <a href="{{ getAdminPanelUrl() }}/pages/create" class="btn btn-primary">{{
                            trans('admin/main.add_new') }}</a>
                        @endcan --}}
                        </div>

                        <div class="card-body">
                            @if (session('success'))
                                <div class="alert alert-success">
                                    {{ session('success') }}
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table class="table table-striped font-14">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>الاسم</th>
                                            <th>اسم الشركة</th>
                                            <th>الايميل</th>
                                            <th>الهاتف</th>
                                            <th>عنوان الشركة</th>
                                            <th>ميعاد الاجتماع</th>
                                            <th>الاجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($portals as $portal)
                                            @php
                                                $meeting_time = PortalMeetingsSlots::find( $portal->meeting_time);
                                            @endphp
                                            <tr>
                                                <td>{{ $portal->id }}</td>
                                                <td>{{ $portal->name }}</td>
                                                <td>{{ $portal->bussiness_name }}</td>
                                                <td>{{ $portal->email }}</td>
                                                <td>{{ $portal->phone }}</td>
                                                <td>{{ $portal->address }}</td>
                                                <td>
                                                    <b>
                                                    يوم {{ $meeting_time->day }} 
                                                    </b>
                                                    <br>
                                                    من
                                                    {{ $meeting_time->start_time }} 
                                                    <br>
                                                    الى
                                                    {{ $meeting_time->end_time }}
                                                </td>
                                                <td>
                                                    <form class="d-inline"
                                                        action="{{ route('portal.accept', $portal->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-sm btn-success"
                                                            onclick="return confirm('هل انت متاكد ؟  ')">قبول</button>
                                                    </form>

                                                    <a href="{{ route('portal.create', $portal->id) }}"
                                                        class="btn btn-sm btn-warning">تخصيص</a>
                                                    <form class="d-inline"
                                                        action="{{ route('subscribe.destroy', $portal->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="btn btn-sm btn-danger"
                                                            onclick="return confirm('هل انت متاكد ؟  ')">X</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="card-footer text-center">
                            {{ $portals->appends(request()->input())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
