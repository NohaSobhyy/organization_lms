@extends(getTemplate() . '.auth.auth_layout')
@push('styles_top')
    <link rel="stylesheet" href="/assets/default/vendors/select2/select2.min.css">
@endpush

@section('content')
    <style>
        .cs-btn {
            background-color: #ED1088 !important;
        }

        .cs-btn:hover {
            background-color: #5F2B80 !important;
        }

        .custom-control-label::after,
        .custom-control-label::before {
            left: initial !important;
            right: -1.5rem !important;
        }

        .iti__country-list {
            position: absolute;
            z-index: 2;
            list-style: none;
            text-align: left;
            padding: 0;
            margin: 0 0 0 -1px;
            box-shadow: 1px 1px 4px rgba(0, 0, 0, .2);
            background-color: #fff;
            border: 1px solid #ccc;
            white-space: nowrap;
            max-height: 200px;
            overflow-y: scroll;
            -webkit-overflow-scrolling: touch;
            left: 0 !important;
            direction: ltr !important;
        }
    </style>
    @php
        $siteGeneralSettings = getGeneralSettings();
    @endphp
    @php
        $registerMethod = getGeneralSettings('register_method') ?? 'mobile';
        $showOtherRegisterMethod = getFeaturesSettings('show_other_register_method') ?? false;
        $showCertificateAdditionalInRegister = getFeaturesSettings('show_certificate_additional_in_register') ?? false;
        $selectRolesDuringRegistration = getFeaturesSettings('select_the_role_during_registration') ?? null;
    @endphp
    <div class="p-md-4 m-md-3">
        <div class="col-6 col-md-6 p-0 mb-5 mt-3 mt-md-auto">
            <img src="{{ $siteGeneralSettings['logo'] ?? '' }}" alt="logo" width="100%" class="">
        </div>

        <h1 class="font-20 font-weight-bold mb-3">
            <svg width="34" height="29" viewBox="0 0 34 29" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M22 27C22 23.3181 17.5228 20.3333 12 20.3333C6.47715 20.3333 2 23.3181 2 27M32 12L25.3333 18.6667L22 15.3333M12 15.3333C8.3181 15.3333 5.33333 12.3486 5.33333 8.66667C5.33333 4.98477 8.3181 2 12 2C15.6819 2 18.6667 4.98477 18.6667 8.66667C18.6667 12.3486 15.6819 15.3333 12 15.3333Z"
                    stroke="#5E0A83" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            {{ trans('auth.signup') }}
        </h1>

        {{-- show messages --}}
        @if (!empty(session()->has('msg')))
            <div class="alert alert-info alert-dismissible fade show mt-30" role="alert">
                {{ session()->get('msg') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form method="post" action="/register" class="mt-35" id="registerForm">

            <input type="hidden" name="_token" value="{{ csrf_token() }}">

            @if (!empty($selectRolesDuringRegistration) and count($selectRolesDuringRegistration))
                <div class="form-group">
                </div>
            @endif
            <div class="form-group">
                <label class="input-label" for="full_name">الأسم الثلاثي باللغة العربية *</label>

                <input name="full_name" type="text" value="{{ old('full_name') }}" required
                    class="form-control @error('full_name') is-invalid @enderror" placeholder="أدخل الأسم ">
                @error('full_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label class="input-label" for="en_name">الأسم الثلاثي باللغة الإنجليزية *</label>

                <input name="en_name" type="text" value="{{ old('en_name') }}" required
                    class="form-control @error('en_name') is-invalid @enderror" placeholder="أدخل الأسم ">
                @error('en_name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            @if ($registerMethod == 'mobile')
                @include('web.default.auth.register_includes.mobile_field')

                @if ($showOtherRegisterMethod)
                    @include('web.default.auth.register_includes.email_field', ['optional' => false])
                @endif
            @else
                @include('web.default.auth.register_includes.email_field')

                <div class="form-group">
                    <label class="input-label" for="email">اعد كتابة الإيميل
                        {{ !empty($optional) ? '(' . trans('public.optional') . ')' : '' }}*</label>
                    <input name="email_confirmation" type="text" required
                        class="form-control @error('email_confirmation') is-invalid @enderror"
                        value="{{ old('email_confirmation') }}" id="email" aria-describedby="emailHelp">

                    @error('email_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                @if ($showOtherRegisterMethod)
                    @include('web.default.auth.register_includes.mobile_field', ['optional' => false])
                @endif
            @endif




            <div class="password-section">

                <div class="form-group  p-0">
                    <label class="input-label" for="password">{{ trans('auth.password') }}:</label>
                    <input name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                        id="password" aria-describedby="passwordHelp" required>
                    @error('password')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="input-label" for="confirm_password">{{ trans('auth.retype_password') }}:</label>
                    <input name="password_confirmation" type="password" required
                        class="form-control @error('password_confirmation') is-invalid @enderror" id="confirm_password"
                        aria-describedby="confirmPasswordHelp">
                    @error('password_confirmation')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

            </div>


            {{-- @if (getFeaturesSettings('timezone_in_register'))
                @php
                    $selectedTimezone = getGeneralSettings('default_time_zone');
                @endphp

                <div class="form-group">
                    <label class="input-label">{{ trans('update.timezone') }}</label>
                    <select name="timezone" class="form-control select2" data-allow-clear="false">
                        <option value="" {{ empty($user->timezone) ? 'selected' : '' }} disabled>
                            {{ trans('public.select') }}</option>
                        @foreach (getListOfTimezones() as $timezone)
                            <option value="{{ $timezone }}" @if ($selectedTimezone == $timezone) selected @endif>
                                {{ $timezone }}</option>
                        @endforeach
                    </select>
                    @error('timezone')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif --}}

            @if (!empty($referralSettings) and $referralSettings['status'])
                <div class="form-group ">
                    <label class="input-label" for="referral_code">{{ trans('financial.referral_code') }}:</label>
                    <input name="referral_code" type="text"
                        class="form-control @error('referral_code') is-invalid @enderror" id="referral_code"
                        value="{{ !empty($referralCode) ? $referralCode : old('referral_code') }}"
                        aria-describedby="confirmPasswordHelp">
                    @error('referral_code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>
            @endif

            @if (!empty(getGeneralSecuritySettings('captcha_for_register')))
                @include('web.default.includes.captcha_input')
            @endif
            <!--start-->

            {{-- <div class="custom-control custom-checkbox">
                <input type="checkbox" name="term" value="1"
                    {{ (!empty(old('term')) and old('term') == '1') ? 'checked' : '' }}
                    class="custom-control-input @error('term') is-invalid @enderror" id="term">
                <label class="custom-control-label font-14 mr-20" for="term">
                    <p class="term">
                        {{ trans('auth.i_agree_with') }}

                        <a href="pages/terms" target="_blank"
                            class="text-secondary font-weight-bold font-14">{{ trans('auth.terms_and_rules') }}</a>

                    </p>
                </label>

                @error('term')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            @error('term')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror --}}
            <!--end-->
            {{-- application section --}}

            {{-- application type ( main categories) --}}
            <div class="form-group">
                <label class="form-label">حدد نوع البرنامج الدراسي<span class="text-danger">*</span></label>
                <select id="typeSelect" name="main_category_id" required
                    class="form-control @error('main_category_id') is-invalid @enderror" onchange="handleApplicationForm()">
                    <option selected hidden value="">اختر نوع التقديم التي تريد دراسته في
                        اكاديمية انس للفنون </option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @if (old('main_category_id', !empty($main_category_id) ? $main_category_id : null) == $category->id) selected @endif>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>

                @error('main_category_id')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- @livewire('register-bundles') --}}


            {{-- sub categories --}}

            <div class="form-group  d-none">
                <label class="form-label">حدد التخصص الذي تريده<span class="text-danger">*</span></label>
                <select id="subCategiresSelect" name="sub_category_id"
                    class="form-control @error('sub_category_id') is-invalid @enderror"
                    onchange="handleSubCategoryChange(event)">

                    <option selected hidden value="">اختر التخصص الذي تريد دراسته في
                        اكاديمية انس للفنون </option>

                </select>

                @error('sub_category_id')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>


            {{-- programs --}}

            <div class="form-group  d-none">
                <label class="form-label">حدد البرنامج الذي تريده<span class="text-danger">*</span></label>
                <select id="bundleSelect" name="bundle_id" class="form-control @error('bundle_id') is-invalid @enderror"
                    onchange="handleBundleChange(event);CertificateSectionToggle(event)">
                    <option selected hidden value="">اختر البرنامج الذي تريد دراسته في
                        اكاديمية انس للفنون </option>

                </select>

                @error('bundle_id')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- addition_section --}}
            <div class="form-group  d-none" id="addition_section">
                <label>إضافة رغبة في دراسة تخصص مزدوج ؟<span class="text-danger">*</span></label>

                <div class="row mr-5 mt-5">

                    <div class="col-sm-4 col">
                        <label for="">
                            <input type="radio" id="want_addition" name="want_addition" value="1"
                                onchange="bundleAddtionSelectToggle()"
                                class=" @error('want_addition') is-invalid @enderror"
                                {{ old('want_addition') === '1' ? 'checked' : '' }}>
                            نعم
                        </label>
                    </div>


                    <div class="col">
                        <label for="">
                            <input type="radio" id="doesn't_want_addition" name="want_addition"
                                onchange="bundleAddtionSelectToggle()" value="0"
                                class="@error('want_addition') is-invalid @enderror"
                                {{ old('want_addition') === '0' ? 'checked' : '' }}>
                            لا
                        </label>
                    </div>
                </div>
            </div>

            {{-- additions bundles --}}
            <div class="form-group d-none">
                <label class="form-label">حدد التخصص المزدوج الذي تريده<span
                        class="text-danger">*</span></label>
                <select id="additionBundleSelect" name="addition_bundle_id"
                    class="form-control @error('addition_bundle_id') is-invalid @enderror" onchange="">
                </select>

                @error('addition_bundle_id')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>

            {{-- courses --}}
            <div class="form-group  d-none">
                <label class="form-label">حدد الدورة التي تريدها<span class="text-danger">*</span></label>
                <select id="webinarSelect" name="webinar_id"
                    class="form-control @error('webinar_id') is-invalid @enderror" onchange="coursesToggle(event)">
                    <option selected hidden value="">اختر الدورة التي تريد دراستها في
                        اكاديمية انس للفنون </option>

                </select>

                @error('webinar_id')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                @enderror
            </div>


            <button type="submit" class="btn btn-primary btn-block font-16 mt-20 py-10 cs-btn">
                الخطوة التالية <i class="fas fa-arrow-left"></i>
            </button>
        </form>

        <div class="ft-text text-center mt-20 mb-35">
            <span class="text-secondary">
                لديك حساب بالفعل ؟

                <br>
                <a href="/login?{{ request()->getQueryString() }}" class="text-secondary font-weight-bold">تسجيل دخول</a>
            </span>
        </div>



    </div>
@endsection
@push('scripts_bottom')
    <script src="/assets/default/vendors/select2/select2.min.js"></script>
@endpush
<script>
    const categories = @json($categories);
    console.log(categories);
    // Pass all old values to a global JavaScript variable
    const oldValues = @json(session()->getOldInput());
    if(!(oldValues.main_category_id)){
        oldValues.main_category_id = @json($main_category_id ?? null)
    }
    if(!(oldValues.sub_category_id)){
        oldValues.sub_category_id = @json($sub_category_id ?? null)
    }
    if(!(oldValues.webinar_id)){
        oldValues.webinar_id = @json($webinar_id ?? null)
    }
    if(!(oldValues.bundle_id)){
        oldValues.bundle_id = @json($bundle_id ?? null)
    }
</script>
<script src="/assets/default/js/applicationForm.js"></script>

<script>
    window.onload = function() {
        handleApplicationForm();
        let form = document.getElementById('registerForm');

        form.onsubmit = function(event) {
            event.preventDefault();
            let code = document.getElementsByClassName('iti__selected-dial-code')[0].innerHTM
            document.getElementById('code').value = code;

            form.submit();

        }

    }
</script>
