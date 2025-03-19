@extends(getTemplate() . '.layouts.app')

@section('content')
    <style>
        .form-control {
            font-size: 1rem;
            font-weight: 700;
            line-height: 1.5;
        }
    </style>
    <section class="container mt-10 mt-md-40">
        <div class="row">

            <div class="col-12 col-xl-7">
                <div class="post-show mt-30">


                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}<br>

                            <a href="{{ session('zoom_meeting_url') }}">Zoom Meeting Link</a>
                        </div>
                    @else
                        <form action="{{ route('subscribe_post') }}" method="POST" style="margin-top:20px;">
                            @csrf
                            <div class="form-group">
                                <input type="text" name="name" class="form-control" placeholder="الاسم">
                            </div>

                            <div class="form-group">
                                <input type="text" name="email" class="form-control" placeholder="ايميل">
                            </div>

                            <div class="form-group">
                                <input type="text" name="bussiness_name" class="form-control" placeholder="اسم الشركة">
                            </div>
                            <div class="form-group">
                                <select class="form-control" name="meeting_time" id="">
                                    <option value="" disabled selected>ميعاد الاجتماع</option>
                                    @foreach ($meeting_times as $meeting)
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
                                        @endphp
                                        <option value="{{ $meeting->id }}">
                                            ({{ $day }})
                                            &nbsp;<span>{{ $meeting->start_time }}</span>
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">

                                <select name="timezone" class="form-control select2">
                                    <option value="" disabled @if (empty($itemValue) or empty($itemValue['default_time_zone'])) selected @endif>
                                        المنطقة الزمنية</option>
                                    @foreach (getListOfTimezones() as $timezone)
                                        <option value="{{ $timezone }}"
                                            @if (!empty($itemValue) and !empty($itemValue['default_time_zone']) and $itemValue['default_time_zone'] == $timezone) selected @endif>{{ $timezone }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="text" name="phone" class="form-control" placeholder="رقم الهاتف">
                            </div>
                            <div class="form-group">
                                <input type="text" name="address" class="form-control" placeholder="عنوان مقر الشركة">
                            </div>



                            <button class="btn btn-primary btn-lg btn-block cs-btn"> تاكيد طلب التسجيل</button>

                        </form>
                    @endif

                </div>
                <div style="height:300px;"></div>
            </div>

        </div>
        </div>
        </div>
    </section>
@endsection

@push('scripts_bottom')
@endpush
