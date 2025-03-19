<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PortalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use Firebase\JWT\JWT;

class ZoomController extends Controller
{

    public function __construct()
    {

    }
    //++++++++++++++++++++++++++++++++++++++++++++++++
    //++++++++++++++++++++++++++++++++++++++++++++++++
    // public function zoom_index(Request $request)
    // {
        
    //     if (!$request->code) {
    //         $this->get_oauth_step_1();
    //     } else {
    //         $getToken         = $this->get_oauth_step_2($request->code);
    //         $get_zoom_details = $this->create_a_zoom_meeting([
    //             'topic'      => 'Test meeting',
    //             'start_time' => date('Y-m-d h:i:00'),
    //             'agenda'     => "Test meeting",
    //             'jwtToken'   => $getToken['access_token'],
    //         ]);
    //         //dd($get_zoom_details);
    //         return view('zoom')->with('respond', json_encode($get_zoom_details));
    //     }
    // }
    
    
    // private function get_oauth_step_1()
    // {
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     $redirectURL  = 'http://localhost:8000/zoom/zoom-meeting-create';
    //     $authorizeURL = 'https://zoom.us/oauth/authorize';
    //     //++++++++++++++++++++++++++++++++++++++++++++++++++
    //     $clientID     = env("ZOOM_CLIENT_ID");
    //     $clientSecret = env("ZOOM_CLIENT_SECRECT");
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     $authURL = $authorizeURL . '?client_id=' . $clientID . '&redirect_uri=' . $redirectURL . '&response_type=code&scope=&state=xyz';
    //     header('Location: ' . $authURL);
    //     exit;
    // }
   
    // private function get_oauth_step_2($code)
    // {
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     $tokenURL    = 'https://zoom.us/oauth/token';
    //     $redirectURL = 'http://localhost:8000/zoom/zoom-meeting-create';
    //     //++++++++++++++++++++++++++++++++++++++++++++++++++
    //     $clientID     = env("ZOOM_CLIENT_ID");
    //     $clientSecret = env("ZOOM_CLIENT_SECRECT");
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     $curl   = curl_init();
    //     $params = array(CURLOPT_URL => $tokenURL . "?"
    //         . "code=" . $code
    //         . "&grant_type=authorization_code"
    //         . "&client_id=" . $clientID
    //         . "&client_secret=" . $clientSecret
    //         . "&redirect_uri=" . $redirectURL,
    //         CURLOPT_RETURNTRANSFER      => true,
    //         CURLOPT_MAXREDIRS           => 10,
    //         CURLOPT_TIMEOUT             => 30,
    //         CURLOPT_HTTP_VERSION        => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST       => "POST",
    //         CURLOPT_NOBODY              => false,
    //         CURLOPT_HTTPHEADER          => array(
    //             "cache-control: no-cache",
    //             "content-type: application/x-www-form-urlencoded",
    //             "accept: *",
    //         ),
    //     );
    //     curl_setopt_array($curl, $params);
    //     $response = curl_exec($curl);
    //     //++++++++++++++++++++++++++++++++++++++++++++++++++
    //     $err = curl_error($curl);
    //     curl_close($curl);
    //     //++++++++++++++++++++++++++++++++++++++++++++++++++
    //     $response = json_decode($response, true);
    //     return $response;
    // }
    
    // private function create_a_zoom_meeting($meetingConfig = [])
    // {
        
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     $requestBody = [
    //         'topic'      => $meetingConfig['topic'] ?? 'Anas Academy Subscription',
    //         'type'       => $meetingConfig['type'] ?? 2,
    //         'start_time' => $meetingConfig['start_time'] ?? date('Y-m-d h:i:00'),
    //         'duration'   => $meetingConfig['duration'] ?? 30,
    //         'password'   => $meetingConfig['password'] ?? mt_rand(),
    //         'timezone'   => 'Asia/Riyadh',
    //         'agenda'     => $meetingConfig['agenda'] ?? 'Testing Meeting',
    //         'settings'   => [
    //             'host_video'        => true,
    //             'participant_video' => true,
    //             'cn_meeting'        => false,
    //             'in_meeting'        => false,
    //             'join_before_host'  => true,
    //             'mute_upon_entry'   => true,
    //             'watermark'         => false,
    //             'use_pmi'           => false,
    //             'approval_type'     => 0,
    //             'registration_type' => 0,
    //             'audio'             => 'voip',
    //             'auto_recording'    => 'none',
    //             'waiting_room'      => false,
    //         ],
    //     ];
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     $curl = curl_init();
    //     curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // Skip SSL Verification
    //     curl_setopt_array($curl, array(
    //         CURLOPT_URL            => "https://api.zoom.us/v2/users/me/meetings",
    //         CURLOPT_RETURNTRANSFER => true,
    //         CURLOPT_ENCODING       => "",
    //         CURLOPT_MAXREDIRS      => 10,
    //         CURLOPT_SSL_VERIFYHOST => 0,
    //         CURLOPT_SSL_VERIFYPEER => 0,
    //         CURLOPT_TIMEOUT        => 30,
    //         CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
    //         CURLOPT_CUSTOMREQUEST  => "POST",
    //         CURLOPT_POSTFIELDS     => json_encode($requestBody),
    //         CURLOPT_HTTPHEADER     => array(
    //             "Authorization: Bearer " . $meetingConfig['jwtToken'],
    //             "Content-Type: application/json",
    //             "cache-control: no-cache",
    //         ),
    //     ));
    //     $response = curl_exec($curl);
    //     $err      = curl_error($curl);
    //     curl_close($curl);
    //     //++++++++++++++++++++++++++++++++++++++++++++++++
    //     if ($err) {
    //         return [
    //             'success'  => false,
    //             'msg'      => 'cURL Error #:' . $err,
    //             'response' => null,
    //         ];
    //     } else {
    //         return [
    //             'success'  => true,
    //             'msg'      => 'success',
    //             'response' => json_decode($response, true),
    //         ];
    //     }
    // }
    public function createMeeting(Request $request)
    {
        // Validate the input
        $request->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);

        // Combine date and time into a single ISO8601 format
        $meetingTime = $request->date . 'T' . $request->time . ':00Z';

        // Your Zoom API credentials
        $apiKey = env('ZOOM_CLIENT_KEY');
        $apiSecret = env('ZOOM_CLIENT_SECRET');

        // JWT Token generation
        $token = base64_encode($apiKey . ':' . $apiSecret);

        // Create the meeting
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post('https://api.zoom.us/v2/users/me/meetings', [
            'topic' => 'Laravel Zoom Meeting',
            'type' => 2, // Scheduled meeting
            'start_time' => $meetingTime, // ISO8601 format
            'duration' => 30, // Duration in minutes
            'timezone' => 'UTC',
            'settings' => [
                'host_video' => true,
                'participant_video' => true,
                'join_before_host' => false,
                'mute_upon_entry' => true,
            ],
        ]);

        if ($response->successful()) {
            $meeting = $response->json();
            return redirect()->back()->with('success', 'Meeting created successfully! Join URL: ' . $meeting['join_url']);
        }

        return redirect()->back()->with('error', 'Failed to create meeting. Error: ' . $response->body());
    }
}