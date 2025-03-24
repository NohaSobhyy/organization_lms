<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bundle;
use App\Models\Countries;
use App\Models\Department;
use App\Models\Portal;
use App\Models\PortalMeetingsSlots;
use App\Models\PortalRequest;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Client;

class PortalController extends Controller
{


    public function subscribe_post(Request $request)
    {
        $meeting_time = PortalMeetingsSlots::findorFail($request->meeting_time);



        $msg = 'تم تسجيل طلب الانضمام , سيتم التواصل معك لتحديد البرامج الدراسية و بيانات الدخول الخاصة بك';

        Session::put('zoom_meeting_name', '[ Anas Academy - ' . $request->name . ' ]');

        $meeting_time = PortalMeetingsSlots::find($request->meeting_time);
        $day = $meeting_time['day']; // 'Tuesday'
        $time = $meeting_time['start_time']; // '10:05 AM'

        $time24HourWithSeconds = Carbon::createFromFormat('h:i A', $time)->format('H:i:00');
        $currentDate = Carbon::now();
        $nextDay = $currentDate->isToday() && $currentDate->format('l') === $day
            ? $currentDate
            : $currentDate->next($day);

        $localDateTime = $nextDay->format('Y-m-d') . ' ' . $time24HourWithSeconds;

        // Convert the local datetime to UTC
        $utcDateTime = Carbon::createFromFormat('Y-m-d H:i:s', $localDateTime, $request->timezone)
            ->setTimezone('UTC')
            ->format('Y-m-d\TH:i:s\Z');

        Session::put('zoom_meeting_time', $utcDateTime);


        $client = new Client();

        $client_id = env('ZOOM_CLIENT_KEY');
        $client_secret = env('ZOOM_CLIENT_SECRET');
        $account_id = env('ZOOM_ACCOUNT_ID');

        $response = $client->post('https://zoom.us/oauth/token', [
            'headers' => [
                'Authorization' => 'Basic ' . base64_encode("$client_id:$client_secret"),
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'grant_type' => 'account_credentials',
                'account_id' => $account_id,
            ],
        ]);

        $tokenData = json_decode($response->getBody(), true);

        $response = $client->post('https://api.zoom.us/v2/users/me/meetings', [
            'headers' => [
                'Authorization' => 'Bearer ' . $tokenData['access_token'],
                'Content-Type'  => 'application/json',
            ],
            'json' => [
                'topic'      => Session::get('zoom_meeting_name'),        // Meeting topic
                'type'       => 2,                    // Scheduled meeting
                'start_time' => Session::get('zoom_meeting_time'), // ISO 8601 format
                'duration'   => 60,                   // Meeting duration in minutes
                'timezone'   => $request->timezone,         // Timezone
                // 'password'   => '12345678',           // Optional: Meeting password
                'settings'   => [
                    'join_before_host'  => false,     // Join before host
                    'host_video'        => true,      // Enable host video
                    'participant_video' => true,      // Enable participant video
                    'mute_upon_entry'   => false,     // Mute participants on entry
                    'waiting_room'      => false,     // Enable waiting room
                    'approval_type'     => 0,         // Automatically approve participants
                ],
            ],
        ]);

        $meeting = json_decode($response->getBody(), true);
        // dd($meeting);
        // echo 'Meeting Created! Meeting ID: ' . $meeting['id'];
        // echo 'Join URL: ' . $meeting['join_url'];
        $msg = 'تم تسجيل طلب الانضمام , سيتم التواصل معك لتحديد البرامج الدراسية و بيانات الدخول الخاصة بك';

        $zoom_meeting_url = $meeting['start_url'];
        $zoom_meeting_id = $meeting['id'];

        Portal::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'bussiness_name' => $request->bussiness_name,
            'meeting_time' => $request->meeting_time,
            'zoom_meeting_id' => $meeting['id']

        ]);

        return redirect()->route('subscribe')->with(
            [
                'success' => $msg,
                'zoom_meeting_url' => $zoom_meeting_url
            ]
        );
    }

    public function portal_auth(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log the user in
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            // Redirect based on user role or other logic
            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif (Auth::user()->role === 'user') {
                return redirect()->route('user.dashboard');
            }
        }

        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function portal_login($url_name)
    {
        $portal = Portal::where('url_name', $url_name)->first();

        if ($portal) {
            return view('web.default.organization.portal.login', compact('portal'));
        } else {
            return redirect()->route('homepage');
        }
    }

    public function index()
    {
        $pageTitle = 'المنافذ';
        $portals = Portal::latest()
            ->whereNot('accepted', 0)
            ->paginate(20);
        return view('admin.portals.index', compact('pageTitle', 'portals'));
    }

    public function portal()
    {
        $pageTitle = 'المنافذ';
        return view('admin.portals.portal', compact('pageTitle'));
    }

    public function requests()
    {
        $pageTitle = 'المنافذ';
        $portals = Portal::orderBy('id', 'DESC')->where('accepted', 0)->paginate(20);
        return view('admin.portals.requests', compact('pageTitle', 'portals'));
    }

    public function subscribe()
    {
        $countries = Countries::all();
        $siteGeneralSettings = getGeneralSettings();
        $pageTitle = 'تسجيل طلب اشتراك';
        $meeting_times = PortalMeetingsSlots::all();
        // switch ($meeting_times['day']) {
        //     case 'saturday':
        //         $day = 'السبت';
        //         break;
        //     case 'sunday':
        //         $day = 'الاحد';
        //         break;
        //     case 'monday':
        //         $day = 'الاثنين';
        //         break;
        //     case 'tuesday':
        //         $day = 'الثلاثاء';
        //         break;

        //     case 'wednesday':
        //         $day = 'الاربعاء';
        //         break;
        //     case 'thursday':
        //         $day = 'الخميس';
        //         break;
        //     case 'friday':
        //         $day = 'الجمعة';
        //         break;
        // }
        return view(getTemplate() . '.organization.subscription', compact('pageTitle', 'meeting_times', 'siteGeneralSettings', 'countries'));
    }

    public function portal_create($id)
    {

        $pageTitle = 'انشاء منفذ';
        $portal = Portal::findorFail($id);
        // dd($portal->accepted);
        $data = $portal;
        return view('admin.portals.portal', compact('pageTitle', 'data', 'portal'));
    }

    public function portal_basic_update(Portal $portal, Request $request)
    {
        $portal->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'max_users' => $request->max_users,

        ]);
        $msg = 'تم اضافة التعديلات بنجاح';
        return back()->with('success', $msg);
    }

    public function portal_social_update(Portal $portal, Request $request)
    {
        $portal->update([
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'linkedin' => $request->linkedin,
            'other_link' => $request->other_link,
        ]);
        $msg = 'تم اضافة التعديلات بنجاح';
        return back()->with('success', $msg);
    }

    public function portal_interface_update(Portal $portal, Request $request)
    {
        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_' . $logo->getClientOriginalName();
            $logo->move(public_path('images'), $logoName);
            if ($portal->logo && file_exists(public_path('images/' . $portal->logo))) {
                unlink(public_path('images/' . $portal->logo));
            }
            $portal->update([
                'bussiness_name' => $request->bussiness_name,
                'url_name' => $request->url_name,
                'description' => $request->description,
                'seo_description' => $request->seo_description,
                'logo' => $logoName // Save only the logo filename in the database
            ]);
        } else {
        }
        $portal->update([
            'bussiness_name' => $request->bussiness_name,
            'url_name' => $request->url_name,
            'description' => $request->description,
            'seo_description' => $request->seo_description
        ]);

        $msg = 'تم اضافة التعديلات بنجاح';
        return back()->with('success', $msg);
    }


    public function portal_features_update(Portal $portal, Request $request)
    {
        $independentCopyright = $request->has('independent_copyright') ? 1 : 0;

        $portal->update([
            'independent_copyright' => $request->independent_copyright,
        ]);

        // If independent copyright is enabled, remove the logo
        if ($independentCopyright && $portal->logo) {
            if (file_exists(public_path('images/' . $portal->logo))) {
                unlink(public_path('images/' . $portal->logo)); // Delete logo file
            }
            $portal->update([
                'logo' => null, // Remove logo from database
            ]);
        }
        $msg = 'تم اضافة التعديلات بنجاح';
        return back()->with('success', $msg);
    }

    public function portal_password_update(Portal $portal, Request $request)
    {
        $request->validate([
            'password' => 'confirmed|required'

        ]);
        $portal->update([
            'password' => Hash::make($request->password)
        ]);

        $msg = 'تم اضافة التعديلات بنجاح';
        return back()->with('success', $msg);
    }

    public function portal_accept(Portal $portal, Request $request)
    {
        $portal->update([
            'accepted' => 1
        ]);
        $msg = 'تم قبول الحساب بنجاح';
        return redirect()->route('portal.index')->with('success', $msg);
    }
    public function active_toggle($id)
    {
        $portal = Portal::find($id);
        $portal->activated = !$portal->activated;
        $portal->save();
        $msg = 'تم تغيير حالة الحساب بنجاح';
        return back()->with('success', $msg);
    }

    public function subscribe_delete($id)
    {
        Portal::where('id', $id)->first()->delete();
        $msg = 'تم حذف الطلب بنجاح';
        return redirect()->route('portal.requests')->with('success', $msg);
    }

    public function meetings()
    {
        $pageTitle = 'ادارة توقيتات الاجتماعات';
        $meetings = PortalMeetingsSlots::where('deleted', 0)->get();
        return view('admin.portals.meetings', compact('pageTitle', 'meetings'));
    }

    public function meetings_active_toggle($id)
    {
        $meeting = PortalMeetingsSlots::findorFail($id);
        if ($meeting->active == 1) {
            $value = 0;
        } else {
            $value =  1;
        }
        $meeting->update([
            'active' => $value
        ]);
        return back()->with('success', 'تم تغيير حالة الاجتماع');
    }

    public function meetings_delete($id)
    {
        $meeting = PortalMeetingsSlots::findorFail($id);
        $meeting->update([
            'deleted' => 1
        ]);
        return back()->with('success', 'تم حذف الاجتماع');
    }

    public function meetings_create()
    {
        $pageTitle = 'اضافة ميعاد اجتماع';
        return view('admin.portals.meetings_create', compact('pageTitle'));
    }

    public function meetings_store(Request $request)
    {

        $start_time = Carbon::createFromFormat('H:i', $request->start_time)->format('h:i A');
        PortalMeetingsSlots::create([
            'day' => $request->day,
            'start_time' => $start_time,
        ]);
        return to_route('meetings.index');
    }

    public function departments()
    {
        $departments = Department::orderby('id', 'desc')
            ->whereNot('deleted', true)
            ->paginate(20);
        $pageTitle = 'الاقسام';
        return view('admin.portals.departments', [
            'pageTitle' => $pageTitle,
            'departments' => $departments
        ]);
    }

    public function departments_create()
    {
        $pageTitle = 'إنشاء قسم';
        return view('admin.portals.departments_create', [
            'pageTitle' => $pageTitle,

        ]);
    }

    public function departments_store(Request $request)
    {
        Department::create([
            'name_ar' => $request->name,
            'name' => $request->name_en
        ]);
        $msg = 'تم انشاء القسم';
        return redirect()->route('departments.index')->with('success', $msg);
    }
}
