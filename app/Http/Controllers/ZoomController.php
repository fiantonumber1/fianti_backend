<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\ZoomAccessToken; // Assuming you have a ZoomAccessToken model to handle database operations

class ZoomController extends Controller
{
    private function urlEncode($url)
    {
        return urlencode($url);
    }

    public function redirectToZoom($account_name)
    {
        $zoomaccount = ZoomAccessToken::where('account_name', $account_name)->first();
        $zoom_clientid = $zoomaccount->zoom_clientid;
        $zoom_redirecturl = $zoomaccount->zoom_redirecturl;
        $encoded_redirect_url = $this->urlEncode($zoom_redirecturl);
        $url = "https://zoom.us/oauth/authorize?response_type=code&client_id={$zoom_clientid}&redirect_uri={$encoded_redirect_url}";
        return redirect($url);
    }

    public function create()
    {
        return view('zoom.create');
    }

    public function store(Request $request)
    {
        // Create a new Zoom access token
        ZoomAccessToken::create([
            'account_name' => $request->account_name,
            'zoom_clientid' => $request->zoom_clientid,
            'zoom_clientsecret' => $request->zoom_clientsecret,
            'zoom_redirecturl' => $request->zoom_redirecturl,
            'zoom_hotkey' => $request->zoom_hotkey,
            'jenis' => $request->jenis,
            'account_expired' => $request->account_expired
        ]);

        // Redirect to the index view with a success message
        return redirect()->route('zoom.index')->with('success', 'Zoom access token created successfully.');
    }

    public function index()
    {
        // Fetch all Zoom access tokens to display in the index view
        $allzoomaccess = ZoomAccessToken::all();
        return view('zoom.index', compact('allzoomaccess'));
    }
    public function getAllAccountNames()
    {
        $accountNames = ZoomAccessToken::getAllAccountNames();
        return $accountNames;
    }

    public function show($id)
    {
        // Fetch a specific Zoom access token by its ID
        $zoomaccess = ZoomAccessToken::findOrFail($id);
        return view('zoom.show', compact('zoomaccess'));
    }

    public function update(Request $request, $id)
    {
        $zoomaccess = ZoomAccessToken::findOrFail($id);
        $zoomaccess->update([
            'account_name' => $request->account_name,
            'zoom_clientid' => $request->zoom_clientid,
            'zoom_clientsecret' => $request->zoom_clientsecret,
            'zoom_redirecturl' => $request->zoom_redirecturl,
            'zoom_hotkey' => $request->zoom_hotkey,
            'jenis' => $request->jenis,
            'account_expired' => $request->account_expired
        ]);

        // Redirect back to the show view with a success message
        return redirect()->route('zoom.show', $id)->with('success', 'Zoom access token updated successfully.');
    }

    public function deleteMultiple(Request $request)
    {
        $documentIds = $request->input('document_ids', []);
        if (!empty($documentIds)) {
            ZoomAccessToken::whereIn('id', $documentIds)->delete();
            return response()->json(['message' => 'Documents deleted successfully'], 200);
        }
        return response()->json(['message' => 'No documents selected'], 400);
    }
    public function destroy($id)
    {
        // Find the Zoom access token by ID and delete it
        $zoomaccess = ZoomAccessToken::findOrFail($id);
        $zoomaccess->delete();

        // Redirect back to the index view with a success message
        return redirect()->route('zoom.index')->with('success', 'Zoom access token deleted successfully.');
    }




    // https://marketplace.zoom.us/develop/apps/k28Tfob9T8KmBtxeMk2Udg/activation
    public function handleZoomCallback(Request $request, $account_name)
    {
        $zoomaccount = ZoomAccessToken::where('account_name', $account_name)->first();
        $code = $request->query('code');
        if (!$code) {
            return response()->json(['error' => 'Authorization code not provided'], 400);
        }

        $client = new Client(['base_uri' => 'https://zoom.us']);
        try {
            $response = $client->request('POST', '/oauth/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($zoomaccount->zoom_clientid . ':' . $zoomaccount->zoom_clientsecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'code' => $code,
                    'redirect_uri' => $zoomaccount->zoom_redirecturl,
                    'scope' => 'offline_access', // Ensure 'offline_access' scope is requested for refresh tokens
                ],
            ]);

            $token = json_decode($response->getBody()->getContents(), true);
            $zoomaccount->access_token = $token["access_token"];
            $zoomaccount->refresh_token = $token["refresh_token"];
            $zoomaccount->expires_at = Carbon::now()->addSeconds($token['expires_in']);
            $zoomaccount->save();

            return response()->json(['message' => 'Access token and refresh token inserted or updated successfully.'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Refresh the access token if necessary
    public function refreshToken($account_name)
    {

        $zoomaccount = ZoomAccessToken::where('account_name', $account_name)->first();

        if (!$zoomaccount || !$zoomaccount->refresh_token) {
            return response()->json(['error' => 'Refresh token not found'], 500);
        }

        $client = new Client(['base_uri' => 'https://zoom.us']);

        try {
            $response = $client->request('POST', '/oauth/token', [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($zoomaccount->zoom_clientid . ':' . $zoomaccount->zoom_clientsecret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $zoomaccount->refresh_token,
                ],
            ]);

            $token = json_decode($response->getBody()->getContents(), true);

            // Update the access token and refresh token in the database
            $zoomaccount->update([
                'access_token' => $token['access_token'],
                'refresh_token' => $token['refresh_token'],
                'expires_at' => Carbon::now()->addSeconds($token['expires_in'])->format('Y-m-d H:i:s')
            ]);

            return response()->json(['message' => 'Access token refreshed successfully'], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to refresh token', 'message' => $e->getMessage()], 500);
        }
    }

    // Check if the access token is expired and refresh it if necessary
    private function checkAndRefreshToken($account_name)
    {
        $zoomaccount = ZoomAccessToken::where('account_name', $account_name)->first();

        if (!$zoomaccount || !$zoomaccount->expires_at || Carbon::now()->greaterThan($zoomaccount->expires_at)) {
            $this->refreshToken($account_name);
        }
    }

    // Example of how to use the refresh token function before making an API call
    public function createMeeting($topic, $start, $duration, $password, $account_name)
    {
        $this->checkAndRefreshToken($account_name);

        $client = new Client(['base_uri' => 'https://api.zoom.us']);

        try {
            $zoomaccount = ZoomAccessToken::where('account_name', $account_name)->first();

            if (!$zoomaccount) {
                return response()->json(['error' => 'Access token not found'], 500);
            }
            $accessToken = $zoomaccount->access_token;

            // Tambahkan 7 jam ke waktu start sebelum dikonversi ke UTC
            $start = Carbon::parse($start)->addHours(7)->setTimezone('UTC')->toIso8601String();

            $response = $client->request('POST', '/v2/users/me/meetings', [
                "headers" => [
                    "Authorization" => "Bearer $accessToken",
                    "Content-Type" => "application/json",
                ],
                'json' => [
                    "topic" => $topic,
                    "type" => 2,
                    "start_time" => $start,
                    "duration" => $duration,
                    "password" => $password,
                    "settings" => [
                        "join_before_host" => true,  // Allow participants to join before the host
                        "waiting_room" => false,
                        "host_video" => true,
                        "participant_video" => true,
                        "mute_upon_entry" => true,
                        "auto_recording" => "cloud",

                    ]

                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $meetingData = [
                'topic' => $data['topic'],
                'start_time' => $data['start_time'],
                'join_url' => $data['join_url'],
                'idrapat' => $data['id'],
                'password' => $data['password'],
            ];

            return $meetingData;

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function deleteMeeting($account_name, $meetingId, )
    {
        $this->checkAndRefreshToken($account_name);
        $client = new Client(['base_uri' => 'https://api.zoom.us']);

        try {
            $zoomaccount = ZoomAccessToken::where('account_name', $account_name)->first();
            if (!$zoomaccount) {
                return ['message' => 'Access token not found', 'status_code' => 500];
            }
            $accessToken = $zoomaccount->access_token;

            $response = $client->request('DELETE', "/v2/meetings/$meetingId", [
                "headers" => [
                    "Authorization" => "Bearer $accessToken",
                    "Content-Type" => "application/json",
                ],
            ]);

            $statusCode = $response->getStatusCode();

            return ['message' => 'Meeting deleted successfully', 'status_code' => $statusCode];

        } catch (\Exception $e) {
            return ['message' => $e->getMessage(), 'status_code' => 500];
        }
    }


    public function listMeetingParticipants($account_name, $meetingId, $pageSize = 30, $nextPageToken = null)
    {
        // Validasi input
        if (empty($account_name) || empty($meetingId)) {
            return [
                'error' => true,
                'message' => 'Invalid input parameters. Account name and meeting ID are required.',
            ];
        }

        // Zoom API base URL
        $zoomApiBaseUrl = 'https://api.zoom.us/v2';

        // Ambil token akses Zoom
        $zoomaccount = ZoomAccessToken::where('account_name', $account_name)->first();
        if (!$zoomaccount) {
            return [
                'error' => true,
                'message' => 'Zoom account not found for the provided account name.',
            ];
        }

        // Pastikan token sudah diperbarui
        $this->checkAndRefreshToken($account_name);
        $accessToken = $zoomaccount->access_token;

        // Endpoint dan parameter query
        $endpoint = "/past_meetings/{$meetingId}/participants";
        $queryParams = [
            'page_size' => $pageSize,
        ];
        if (!empty($nextPageToken)) {
            $queryParams['next_page_token'] = $nextPageToken;
        }

        // Buat klien HTTP menggunakan Guzzle
        $client = new Client();

        // Kirim permintaan ke Zoom API
        $response = $client->request('GET', $zoomApiBaseUrl . $endpoint, [
            'headers' => [
                'Authorization' => 'Bearer ' . $accessToken,
                'Accept' => 'application/json',
            ],
            'query' => $queryParams,
        ]);

        // Decode respons JSON
        $participantsData = json_decode($response->getBody(), true);

        // Kembalikan data peserta
        return [
            'error' => false,
            'data' => $participantsData,
        ];
    }


}
