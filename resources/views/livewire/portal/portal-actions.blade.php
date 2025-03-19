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

                        <div class="card-body">

                            <ul class="nav nav-pills pb-5" id="myTab3" role="tablist">

                                <li class="nav-item">
                                    <a class="nav-link @if (empty($social)) active @endif" id="basic-tab"
                                        data-toggle="tab" href="#basic" role="tab" aria-controls="basic"
                                        aria-selected="true">{{ trans('admin/main.basic') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link @if (!empty($social)) active @endif" id="socials-tab"
                                        data-toggle="tab" href="#socials" role="tab" aria-controls="socials"
                                        aria-selected="true">{{ trans('admin/main.socials') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="features-tab" data-toggle="tab" href="#features"
                                        role="tab" aria-controls="features"
                                        aria-selected="true">{{ trans('update.features') }}</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="interface-tab" data-toggle="tab" href="#interface"
                                        role="tab" aria-controls="security" aria-selected="true">الواجهة</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="security-tab" data-toggle="tab" href="#security"
                                        role="tab" aria-controls="security" aria-selected="true">كلمة المرور</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link" id="settings-tab" data-toggle="tab" href="#settings"
                                        role="tab" aria-controls="settings" aria-selected="true">الاعدادت</a>
                                </li>


                            </ul>

                            <div class="tab-content" id="myTabContent2">
                                @if (session('success'))
                                    <div class="alert alert-success">
                                        {{ session('success') }}
                                    </div>
                                @endif
                                <div class="tab-pane mt-3 fade @if (empty($social)) show active @endif"
                                    id="basic" role="tabpanel" aria-labelledby="basic-tab">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <form
                                                action="
                                    {{ route('portal.basic.update', $data->id) }}
                                    "
                                                method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <label for="">الاسم </label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="{{ $portal->name }}">
                                                    @error('name')
                                                        {{ $message('name') }}
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="">الايميل</label>
                                                    <input type="text" name="email" class="form-control"
                                                        value="{{ $portal->email }}">
                                                    @error('email')
                                                        {{ $message('email') }}
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="">الهاتف</label>
                                                    <input type="text" name="phone" class="form-control"
                                                        value="{{ $portal->phone }}">
                                                    @error('phone')
                                                        {{ $message('phone') }}
                                                    @enderror
                                                </div>
                                                <div class="form-group">
                                                    <label for="">العنوان</label>
                                                    <input type="text" name="address" class="form-control"
                                                        value="{{ $portal->address }}">
                                                    @error('address')
                                                        {{ $message('address') }}
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="">عدد المستخدمين</label>
                                                    <input type="number" name="max_users" class="form-control"
                                                        value="{{ $data->max_users ?? '' }}">
                                                    @error('number')
                                                        {{ $message('number') }}
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="">Zoom Meeting </label>
                                                    <a
                                                        href="https://www.zoom.us/meeting/{{ $data->zoom_meeting_id }}">{{ $data->zoom_meeting_id }}</a>

                                                </div>
                                                <button class="btn btn-success mt-20" type="submit">حفظ</button>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane mt-3 fade @if (!empty($interface)) show active @endif"
                                    id="interface" role="tabpanel" aria-labelledby="interface-tab">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <form method="POST"
                                                action="{{ route('portal.interface.update', $data->id) }}"
                                                enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <label for="">اسم المؤسسة</label>
                                                    <input type="text" name="bussiness_name" class="form-control"
                                                        value="{{ $portal->bussiness_name ?? '' }}">
                                                    @error('bussiness_name')
                                                        {{ $message('bussiness_name') }}
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="">الاسم فى الرابط</label>
                                                    <input type="text" name="url_name" class="form-control"
                                                        value="{{ $data->url_name ?? '' }}">
                                                    @error('url_name')
                                                        {{ $message('url_name') }}
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="">Description Tag (SEO)</label>
                                                    <input type="text" name="seo_description" class="form-control"
                                                        value="{{ $data->seo_description ?? '' }}">
                                                    @error('description')
                                                        {{ $message('description') }}
                                                    @enderror
                                                </div>


                                                <div class="form-group">
                                                    <label for="">الشعار</label>
                                                    @if (!empty($data->logo))
                                                        <img src="{{ asset('images/' . $data->logo . ' ') }}"
                                                            style="width:50%;height:100px">
                                                    @endif
                                                    <input type="file" name="logo" class="form-control mt-10">
                                                    @error('logo')
                                                        {{ $message('logo') }}
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="">الوصف فى صفحة الدخول</label>
                                                    <textarea name="description" class="form-control" rows="10">{{ $data->url_name ?? '' }}</textarea>
                                                    @error('description')
                                                        {{ $message('description') }}
                                                    @enderror
                                                </div>

                                                <button class="btn btn-success mt-20" type="submit">حفظ</button>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane mt-3 fade @if (!empty($social)) show active @endif"
                                    id="socials" role="tabpanel" aria-labelledby="socials-tab">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <form method="POST"
                                                action="{{ route('portal.socail.update', $data->id) }}">
                                                @method('PUT')
                                                @csrf
                                                <div class="form-group">
                                                    <label for="">Facebook</label>
                                                    <input type="text" name="facebook" class="form-control"
                                                        value="{{ $data->facebook }}">
                                                    @error('facebook')
                                                        {{ $message('facebook') }}
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="">Linkedin</label>
                                                    <input type="text" name="linkedin" class="form-control"
                                                        value="{{ $data->linkedin }}">
                                                    @error('linkedin')
                                                        {{ $message('linkedin') }}
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="">Twitter</label>
                                                    <input type="text" name="twitter" class="form-control"
                                                        value="{{ $data->twitter }}">
                                                    @error('twitter')
                                                        {{ $message('twitter') }}
                                                    @enderror
                                                </div>

                                                <div class="form-group">
                                                    <label for="">Other Link</label>
                                                    <input type="text" name="other_link" class="form-control"
                                                        value="{{ $data->other_link }}">
                                                    @error('other_link')
                                                        {{ $message('other_link') }}
                                                    @enderror
                                                </div>
                                                <button class="btn btn-success mt-20" type="submit">حفظ</button>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane mt-3 fade" id="features" role="tabpanel"
                                    aria-labelledby="features-tab">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <div class="form-check form-check-inline mt-2">
                                                <form action="{{ route('portal.features.update', $data->id) }}"
                                                    method="POST">
                                                    @method('PUT')
                                                    @csrf
                                                    <input class="form-check-input" name="independent_copyright"
                                                        type="checkbox" id="inlineCheckbox1" 
                                                        value="1"
                                                        @checked($data->independent_copyright)
                                                        >
                                                    <label class="form-check-label mr-1" for="inlineCheckbox1">
                                                        حقوق ملكية مستقلة
                                                    </label>

                                                    <div style="height:20px;"></div>
                                                    <button class="btn btn-success mt-20" type="submit">حفظ</button>

                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane mt-3 fade" id="security" role="tabpanel"
                                    aria-labelledby="security-tab">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            <form action="{{ route('portal.password.update', $data->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="form-group">
                                                    <label for="">كلمة المرور</label>
                                                    <input name="password" type="password" class="form-control">
                                                </div>
                                                @error('password')
                                                    {{ $message }}
                                                @enderror
                                                <div class="form-group">
                                                    <label for="">تاكيد كلمة المرور</label>
                                                    <input type="password" name="password_confirmation"
                                                        class="form-control">
                                                </div>
                                                <button class="btn btn-success mt-20" type="submit">حفظ</button>

                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="tab-pane mt-3 fade" id="settings" role="tabpanel"
                                    aria-labelledby="settings-tab">
                                    <div class="row">
                                        <div class="col-12 col-md-6">
                                            
                                        </div>
                                    </div>
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
