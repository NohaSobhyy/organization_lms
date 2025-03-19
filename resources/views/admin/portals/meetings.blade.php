@extends('admin.layouts.app')

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

                            <a href="{{ getAdminPanelUrl() }}/portals/meetings/create"
                                class="btn btn-primary">{{ trans('admin/main.add_new') }}</a>

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
                                            <th>اليوم</th>
                                            <th>توقيت البداية</th>
                                            {{-- <th>توقيت الانتهاء</th> --}}
                                            <th>الاجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($meetings as $meeting)
                                            <tr>
                                                <td>{{ $meeting->id }}</td>
                                                <td>
                                                    @php
                                                        switch ($meeting->day) {
                                                            case 'saturday':
                                                                $day = 'السبت';
                                                                break;
                                                            case 'sunday':
                                                                $day = 'الاحد';
                                                                break;
                                                            case 'monday':
                                                                $day = 'الاثنين';
                                                                break;
                                                            case 'tuesday':
                                                                $day = 'الثلاثاء';
                                                                break;

                                                            case 'wednesday':
                                                                $day = 'الاربعاء';
                                                                break;
                                                            case 'thursday':
                                                                $day = 'الخميس';
                                                                break;
                                                            case 'friday':
                                                                $day = 'الجمعة';
                                                                break;
                                                        }
                                                        echo $day;
                                                    @endphp
                                                </td>
                                                <td>{{ $meeting->start_time }}</td>
                                                {{-- <td>{{ $meeting->end_time }}</td> --}}
                                                <td>
                                                    <form action="{{ route('meetings.active.toggle', $meeting->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        @if ($meeting->active)
                                                            <button class="btn btn-sm btn-warning">تعطيل</button>
                                                        @else
                                                            <button class="btn btn-sm btn-success">تفعيل</button>
                                                        @endif
                                                    </form>

                                                    <form action="{{ route('meetings.delete', $meeting->id) }}"
                                                        method="POST" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-danger">حذف</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="card-footer text-center">
                            {{-- {{ $portals->appends(request()->input())->links() }} --}}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
