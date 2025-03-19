@extends('admin.layouts.app')

@push('styles_top')
    <link rel="stylesheet" href="/assets/vendors/summernote/summernote-bs4.min.css">
@endpush
@push('libraries_top')
@endpush

@section('content')
@livewire('portal.portal-actions',[
    'pageTitle'=>$pageTitle,
    'portal'=>$portal,
    'data'=>$data,
])
@endsection
