<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;
use App\Models\Wagroupnumber;
use Illuminate\Http\Request;
use App\Models\TelegramMessage;
use App\Models\ZoomAccessToken;
use Illuminate\Routing\Controller;
use App\Http\Controllers\LogController;
use App\Models\TelegramMessagesAccount;
use App\Http\Controllers\ZoomController;

class EventController extends Controller
{
    protected $zoomController;
    protected $logController;

    public function __construct(ZoomController $zoomController, LogController $logController)
    {
        $this->zoomController = $zoomController;
        $this->logController = $logController;
    }

    public function index()
    {
        $user = Auth::user();
        $waphonenumber = $user->waphonenumber;
        $alert = $waphonenumber === null ? "yes" : "no"; // Gunakan $alert daripada $allert untuk konsistensi dan ejaan yang benar

        $events = Event::orderBy('start', 'desc')->get();
        // $ruangrapat = Category::getlistCategoryMemberByName('ruangrapat'); 
        // Daftar ruang rapat
        $ruangrapat = [
            'All',
            'Penggunaan Account Zoom Meeting',
            'Ruang Rapat 3.1',
            'Ruang Rapat 3.2',
            'Ruang Rapat 3.3',
            'Ruang Rapat 3.4',
            'Co-Working Space',
            'Ruang Rapat Basic Design I'
        ];

        $today = now()->toDateString(); // Ambil tanggal hari ini



        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $tomorrow = now()->addDay()->endOfDay();

        // Mengambil event yang aktif pada hari ini, kemarin, dan besok
        $eventshariinikemarinbesok = Event::where(function ($query) use ($today, $yesterday, $tomorrow) {
            $query->whereDate('start', '<=', $today)
                ->whereDate('end', '>=', $today);
        })
            ->orWhere(function ($query) use ($yesterday, $today) {
                $query->whereDate('start', '<=', $yesterday)
                    ->whereDate('end', '>=', $yesterday)
                    ->whereDate('end', '<', $today); // Hanya yang berakhir kemarin
            })
            ->orWhere(function ($query) use ($today, $tomorrow) {
                $query->whereDate('start', '<=', $tomorrow)
                    ->whereDate('end', '>=', $tomorrow);
            })
            ->get();

        // Prepare the revisiall array
        $revisiall = ['All' => ['events' => $eventshariinikemarinbesok]];

        foreach ($ruangrapat as $room) {
            $filteredEvents = $events->where('room', $room)->values();
            $key = str_replace(['.', ' '], ['-', '_'], $room);
            $revisiall[$key]['events'] = $filteredEvents;
        }
        $room = "Penggunaan Account Zoom Meeting";
        $filteredEvents = $events->where('room', $room)->values();
        $key = str_replace(['.', ' '], ['-', '_'], $room);
        $revisiall[$key]['events'] = $filteredEvents;

        $eventsdaypilot = $eventshariinikemarinbesok->map(function ($event) {
            return [
                'id' => $event->id,
                'text' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'pic' => $event->pic,
                'resource' => str_replace(['.', ' '], ['-', '_'], $event->room)
            ];
        });

        return view('rapat.index', compact('eventshariinikemarinbesok', 'revisiall', 'ruangrapat', 'eventsdaypilot', 'alert'));





    }

    public function algoritmcheck($room, $start, $end)
    {
        $existingEvents = Event::where('room', $room)
            ->where(function ($query) use ($start, $end) {
                $query->where(function ($query) use ($start, $end) {
                    $query->whereBetween('start', [$start, $end])
                        ->orWhereBetween('end', [$start, $end]);
                })
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('start', '<', $start)
                            ->where('end', '>', $end);
                    });
            });
        return $existingEvents;
    }

    public function room($room)
    {// Ambil semua event yang terkait dengan ruangan yang diberikan
        $events = Event::where('room', $room)->get();
        return view('rapat.room', compact('events', 'room'));
    }

    public function create()
    {
        $availableUsers = User::all();
        $excludedAccounts = ['EngineeringOffice Notif', 'DataBackup', 'Backup Data Inka'];
        $telegramaccounts = TelegramMessagesAccount::whereNotIn('account', $excludedAccounts)->get();
        $ruangrapat = Category::getlistCategoryMemberByName('ruangrapat');
        return view('rapat.inputevent', compact('ruangrapat', 'telegramaccounts', 'availableUsers'));
    }

    public function store(Request $request)
    {
        // Convert start and end times to timestamps
        $startTimestamp = strtotime($request->start);
        $endTimestamp = strtotime($request->end);

        // Check if start time is earlier than end time
        if ($startTimestamp >= $endTimestamp) {
            return response()->json(['error' => 'The start time must be earlier than the end time.'], 400);
        }
        $value = $this->availabilitycheck($request);
        if ($value == "truetrue" || $value == "zerotrue" || $value == "truezero") {
            $kondisi = "";
            $parent_id = null;
            $startFormatted = date('d/m/Y H:i', strtotime($request->start));
            $endFormatted = date('d/m/Y H:i', strtotime($request->end));

            if ($request->room != "Tidak Menggunakan Ruangan") {
                $event = $this->roompick($request);
                if ($event instanceof \Illuminate\Http\JsonResponse) {
                    return $event;
                }
                $parent_id = $event->id;

                if ($request->input('use_zoom') === 'yes') {
                    $zoom = $this->zoompick($request, $parent_id);
                    if ($zoom instanceof \Illuminate\Http\JsonResponse) {
                        return $zoom;
                    }
                }

                if ($request->input('use_zoom') === 'linkeksternal') {
                    $kondisi = "linkeksternalaktif";
                }

            } else {
                $kondisi = "Zoom Saja";
                $zoom = $this->zoompick($request, $parent_id);
                if ($zoom instanceof \Illuminate\Http\JsonResponse) {
                    return $zoom;
                }
            }

            $agendaUnits = $request->input('agenda_unit', []);

            if (!empty($agendaUnits)) {
                $messagesToCreate = [];

                foreach ($agendaUnits as $unitname) {
                    $unit = TelegramMessagesAccount::where('account', $unitname)->first();
                    if ($unit) {
                        $pesan = $this->generateMessage($unitname, $event ?? null, $zoom ?? null, $startFormatted, $endFormatted, $request->input('use_zoom'), $kondisi);

                        // $messagesToCreate[] = [
                        //     'message_kind' => "text",
                        //     'message' => $pesan,
                        //     'telegram_messages_accounts_id' => $unit->id,
                        //     'created_at' => now(),
                        //     'updated_at' => now(),
                        // ];
                        Wagroupnumber::ujisendunit($unitname, $pesan);
                    }
                }

                // if (!empty($messagesToCreate)) {
                //     TelegramMessage::insert($messagesToCreate);
                // }
            }

            $personal_users_id = $request->input('personal_users_id', []);

            if (!empty($personal_users_id)) {
                $listnohp = [];
                $listuser = User::whereIn('id', $personal_users_id)->get();

                foreach ($listuser as $user) {
                    $listnohp[] = $user->waphonenumber;
                }

                $pesan = $this->generateMessage("anda", $event ?? null, $zoom ?? null, $startFormatted, $endFormatted, $request->input('use_zoom'), $kondisi);
                Wagroupnumber::sendWaMessage($listnohp, $pesan);
            }






            return response()->json(['message' => $kondisi === "Zoom Saja" ? $zoom->id : $event->id], 200);

        } else {
            return response()->json(['error' => 'The selected room is already booked for another event within the specified time period.'], 400);
        }

    }



    private function generateMessage($unit, $event, $zoom, $startFormatted, $endFormatted, $useZoom, $kondisi)
    {
        if ($kondisi === "Zoom Saja") {
            return "📧 Hai $unit, undangan sudah dikirim! 📅\nAgenda: **$zoom->title**\n💻 Rapatnya akan via Zoom, jangan lupa cek link-nya di sini: [🔗 $zoom->join_url](#)\n🕒 Waktu: $startFormatted - $endFormatted.\nSampai jumpa di Zoom! 😉";
        }

        if ($kondisi === "linkeksternalaktif") {
            return "📧 Hai $unit, undangan hybrid sudah dikirim! 📅\nAgenda: **$event->title**\n🏢 Lokasi rapat: **$event->room** & Zoom link: [🔗 $event->join_url](#)\n🕒 Waktu: $startFormatted - $endFormatted.\nKita tunggu kehadiranmu ya!";
        }

        if ($useZoom === 'yes') {
            return "📧 Hai $unit, undangan hybrid sudah siap! 📅\nAgenda: **$event->title**\n🏢 Tempat rapat: **$event->room** & Zoom link: [🔗 $zoom->join_url](#)\n🕒 Waktu: $startFormatted - $endFormatted.\nSampai ketemu di sana! 😊";
        }

        return "📧 Hai $unit, undangan sudah dikirim! 📅\nAgenda: **$event->title**\n🏢 Lokasi rapat: **$event->room**\n🕒 Waktu: $startFormatted - $endFormatted.\nKami tunggu ya! 👍";
    }


    // private function generateMessage($unit, $event, $zoom, $startFormatted, $endFormatted, $useZoom, $kondisi)
    // {
    //     if ($kondisi === "Zoom Saja") {
    //         return "Undangan untuk $unit telah dikirim. Dengan agenda: $zoom->title. Rapat dilakukan dengan zoom dengan link: $zoom->join_url. Dimulai pada tanggal dan jam: $startFormatted hingga $endFormatted.";
    //     }

    //     if($kondisi==="linkeksternalaktif"){
    //         return "Undangan untuk $unit telah dikirim. Dengan agenda: $event->title. Rapat dilakukan hybrid dengan ruangan: $event->room dan link zoom: $event->join_url. Dimulai pada tanggal dan jam: $startFormatted hingga $endFormatted.";
    //     }

    //     if ($useZoom === 'yes') {
    //         return "Undangan untuk $unit telah dikirim. Dengan agenda: $event->title. Rapat dilakukan hybrid dengan ruangan: $event->room dan link zoom: $zoom->join_url. Dimulai pada tanggal dan jam: $startFormatted hingga $endFormatted.";
    //     }

    //     return "Undangan untuk $unit telah dikirim. Dengan agenda: $event->title. Rapat berada di ruang: $event->room. Dimulai pada tanggal dan jam: $startFormatted hingga $endFormatted.";
    // }


    public function roompick(Request $request)
    {
        // Process start and end datetime
        $start = $request->start;
        $end = $request->end;



        if ($request->filled('allDay')) {
            $start = date('Y-m-d 00:00:00', strtotime($request->start));
            $end = date('Y-m-d 23:59:59', strtotime($request->end));
        }

        $existingEvents = $this->algoritmcheck($request->room, $start, $end);
        $existingEvent = $existingEvents->first();

        if ($existingEvent) {
            return response()->json(['error' => 'The selected room is already booked for another event within the specified time period.'], 400);
        }

        $agendaUnits = $request->input('agenda_unit', []);
        $customUnits = $request->input('custom_units', []);
        $allUnits = array_merge($agendaUnits, $customUnits);

        $event = new Event;

        if ($request->input('use_zoom') === 'linkeksternal') {
            $event->join_url = $request->zoom_link;
        }

        $event->title = $request->title;
        $event->pic = $request->pic;
        $event->agenda_desc = $request->agenda_desc;
        $event->agenda_unit = json_encode($allUnits);
        $event->start = $start;
        $event->end = $end;
        $event->room = $request->room;
        $event->backgroundColor = $request->backgroundColor ?? '#0073b7';
        $event->borderColor = $request->backgroundColor ?? '#0073b7';
        $event->allDay = $request->filled('allDay');
        $event->save();
        return $event;
    }

    public function zoompick(Request $request, $parent_id)
    {

        $zoomroom = "Penggunaan Account Zoom Meeting";
        $start = $request->start;
        $end = $request->end;

        if ($request->filled('allDay')) {
            $start = date('Y-m-d 00:00:00', strtotime($request->start));
            $end = date('Y-m-d 23:59:59', strtotime($request->end));
        }

        $existingEvents = $this->algoritmcheck($zoomroom, $start, $end);
        $existingEvent = $existingEvents->get();
        // Check if there are more than 2 existing events
        if ($existingEvent->count() >= 2) {
            return response()->json(['error' => 'Zoom is already booked for more than 2 events within the specified time period.'], 400);
        }

        $agendaUnits = $request->input('agenda_unit', []);
        $customUnits = $request->input('custom_units', []);
        $allUnits = array_merge($agendaUnits, $customUnits);

        $event = new Event;
        if ($parent_id != null) {
            $event->parent_id = $parent_id;
        }
        $event->title = $request->title;
        $event->pic = $request->pic;
        $event->agenda_desc = $request->agenda_desc;
        $event->agenda_unit = json_encode($allUnits);
        $event->start = $start;
        $event->end = $end;
        $event->room = $zoomroom;
        $event->backgroundColor = $request->backgroundColor ?? '#0073b7';
        $event->borderColor = $request->backgroundColor ?? '#0073b7';
        $event->allDay = $request->filled('allDay');
        $durationInMinutes = (new \DateTime($end))->diff(new \DateTime($start))->i;
        $meetingData = $this->zoomController->createMeeting($request->title, (new \DateTime($start))->format(DATE_ISO8601), $durationInMinutes, "123456789", "eimadmin");
        $event->password = "123456789";
        $event->join_url = $meetingData['join_url'];
        $event->idrapat = $meetingData['idrapat'];
        $event->save();
        return $event;
    }

    public function availabilitycheck(Request $request)
    {
        // truetrue => ruangan tersedia, zoom tersedia
        // truefalse => ruangan tersedia, zoom tidak tersedia
        // falsetrue => ruangan tidak tersedia, zoom tersedia
        // falsefalse => ruangan tidak tersedia, zoom tidak tersedia
        // zerotrue => tidak menggunakan ruangan, zoom tersedia
        // zerofalse => tidak menggunakan ruangan, zoom tidak tersedia
        // truezero => ruangan tersedia, tidak menggunakan zoom
        // falsezero => ruangan tidak tersedia, tidak menggunakan zoom
        $value = '';

        $start = $request->input('start');
        $end = $request->input('end');
        // Convert start and end times to timestamps
        $startTimestamp = strtotime($start);
        $endTimestamp = strtotime($end);

        // Check if start time is earlier than end time
        if ($startTimestamp >= $endTimestamp) {
            $status = "endearlierstart";
            return $status;
        }
        $room = $request->input('room');
        $useZoom = $request->input('use_zoom');

        if ($room != "Tidak Menggunakan Ruangan") {
            // Check availability for physical room
            $existingEvents = $this->algoritmcheck($room, $start, $end);
            $existingEvent = $existingEvents->first();
            if ($existingEvent) {
                $value .= "false";
            } else {
                $value .= "true";
            }
        } else {
            $value .= "zero";
        }

        if ($useZoom == "yes") {
            // Check availability for Zoom
            $zoomRoom = "Penggunaan Account Zoom Meeting";
            $existingEvents = $this->algoritmcheck($zoomRoom, $start, $end);
            $existingEvent = $existingEvents->get();
            if ($existingEvent->count() >= 2) {
                $value .= "false";
            } else {
                $value .= "true";
            }
        } else {
            $value .= "zero";
        }
        return $value;
    }

    public function checkRoomAvailability(Request $request)
    {
        // truetrue => ruangan tersedia, zoom tersedia
        // truefalse => ruangan tersedia, zoom tidak tersedia
        // falsetrue => ruangan tidak tersedia, zoom tersedia
        // falsefalse => ruangan tidak tersedia, zoom tidak tersedia
        // zerotrue => tidak menggunakan ruangan, zoom tersedia
        // zerofalse => tidak menggunakan ruangan, zoom tidak tersedia
        // truezero => ruangan tersedia, tidak menggunakan zoom
        // falsezero => ruangan tidak tersedia, tidak menggunakan zoom
        $value = $this->availabilitycheck($request);
        return response()->json(['roomavailable' => $value]);
    }

    public function show($id)
    {
        $event = Event::with('children')->findOrFail($id);
        if ($event->room == "Penggunaan Account Zoom Meeting") {
            $zoomaccount = ZoomAccessToken::where('account_name', 'eimadmin')->first();
            return view('rapat.zoom', compact('event', 'zoomaccount'));
        }
        return view('rapat.show', compact('event'));
    }

    public function edit($id)
    {
        $excludedAccounts = ['EngineeringOffice Notif', 'DataBackup', 'Backup Data Inka'];
        $telegramaccounts = TelegramMessagesAccount::whereNotIn('account', $excludedAccounts)->get();
        $ruangrapat = Category::getlistCategoryMemberByName('ruangrapat');
        $event = Event::with('children')->findOrFail($id);
        if ($event->room == "Penggunaan Account Zoom Meeting") {
            $zoomaccount = ZoomAccessToken::where('account_name', 'eimadmin')->first();
            return view('rapat.zoom', compact('event', 'zoomaccount', 'ruangrapat', 'telegramaccounts'));
        }
        return view('rapat.edit', compact('event', 'ruangrapat', 'telegramaccounts'));
    }

    public function destroy($id)
    {
        $event = Event::with('children')->findOrFail($id);
        if ($event->room == "Penggunaan Account Zoom Meeting") {
            $meetingId = $event->idrapat;
            $account_name = "eimadmin";
            $destroyRapat = $this->zoomController->deleteMeeting($account_name, $meetingId);
        }
        if (isset($event->children)) {
            foreach ($event->children as $child) {
                $meetingId = $child->idrapat;
                $account_name = "eimadmin";
                $destroyRapat = $this->zoomController->deleteMeeting($account_name, $meetingId);
            }
        }

        $event->delete();
        return redirect()->route('events.all')->with('success', 'Event deleted successfully');
    }

    public function listMeetingParticipants($id)
    {
        $event = Event::with('children')->findOrFail($id);

        $participants = [];
        if ($event->room == "Penggunaan Account Zoom Meeting") {
            $meetingId = $event->idrapat;
            $account_name = "eimadmin";
            $response = $this->zoomController->listMeetingParticipants($account_name, $meetingId);

            $participants = $response['data']['participants'] ?? [];
        }
        if (isset($event->children)) {
            foreach ($event->children as $child) {
                $meetingId = $child->idrapat;
                $account_name = "eimadmin";
                $response = $this->zoomController->listMeetingParticipants($account_name, $meetingId);
                $participants = $response['data']['participants'] ?? [];
            }
        }


        return response()->json([
            'success' => true,
            'participants' => $participants
        ]);
    }


    public function update(Request $request, $id)
    {
        $event = Event::with('children')->findOrFail($id);
        // Convert start and end times to timestamps
        $startTimestamp = strtotime($request->start);
        $endTimestamp = strtotime($request->end);

        // Check if start time is earlier than end time
        if ($startTimestamp >= $endTimestamp) {
            return response()->json(['error' => 'The start time must be earlier than the end time.'], 400);
        }

        $event->title = $request->title;
        $event->pic = $request->pic;
        $event->agenda_desc = $request->agenda_desc;
        $event->start = null;
        $event->end = null;
        $event->save();
        $value = $this->availabilitycheck($request);
        if ($value == "truetrue" || $value == "zerotrue" || $value == "truezero") {
            $kondisi = "";
            $parent_id = null;
            $startFormatted = date('d/m/Y H:i', strtotime($request->start));
            $endFormatted = date('d/m/Y H:i', strtotime($request->end));




            if ($request->room != "Tidak Menggunakan Ruangan") {
                $event->room = $request->room;

                $parent_id = $event->id;

                if ($request->input('use_zoom') === 'yes') {

                    if ($event->start != $request->start || $event->end != $request->end) {
                        $meetingId = $event->idrapat;
                        $account_name = "eimadmin";
                        $destroyRapat = $this->zoomController->deleteMeeting($account_name, $meetingId);
                        $zoom = $this->zoompick($request, $parent_id);
                        if ($zoom instanceof \Illuminate\Http\JsonResponse) {
                            return $zoom;
                        }
                    }




                }

                if ($request->input('use_zoom') === 'linkeksternal') {
                    $kondisi = "linkeksternalaktif";
                    $event->join_url = $request->zoom_link;
                }

            } else {
                if ($event->start != $request->start || $event->end != $request->end) {
                    $zoomlink = $event->children;
                    foreach ($zoomlink as $zoom) {
                        $zoommeetingId = $zoom->idrapat;
                        $account_name = "eimadmin";
                        $destroyRapat = $this->zoomController->deleteMeeting($account_name, $zoommeetingId);
                        $kondisi = "Zoom Saja";
                        $zoom = $this->zoompick($request, $parent_id);
                        if ($zoom instanceof \Illuminate\Http\JsonResponse) {
                            return $zoom;
                        }
                    }

                }
            }





            $agendaUnits = $request->input('agenda_unit', []);
            $messagesToCreate = [];

            foreach ($agendaUnits as $unitname) {
                $unit = TelegramMessagesAccount::where('account', $unitname)->first();
                if ($unit) {
                    $pesan = $this->generateMessage($unitname, $event ?? null, $zoom ?? null, $startFormatted, $endFormatted, $request->input('use_zoom'), $kondisi);

                    $messagesToCreate[] = [
                        'message_kind' => "text",
                        'message' => $pesan . " (PERUBAHAN JADWAL)",
                        'telegram_messages_accounts_id' => $unit->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if (!empty($messagesToCreate)) {
                TelegramMessage::insert($messagesToCreate);
            }

            return response()->json(['message' => $kondisi === "Zoom Saja" ? $zoom->id : $event->id], 200);

        } else {
            return response()->json(['error' => 'The selected room is already booked for another event within the specified time period.'], 400);
        }




    }

    public function getschedule(Request $request)
    {
        $excludedAccounts = ['EngineeringOffice Notif', 'DataBackup', 'Backup Data Inka'];
        $telegramaccounts = TelegramMessagesAccount::whereNotIn('account', $excludedAccounts)->get();
        $ruangrapat = Category::getlistCategoryMemberByName('ruangrapat');

        $user = Auth::user();
        $waphonenumber = '0812345';
        $alert = $waphonenumber === null ? "yes" : "no";

        // Ambil tanggal dari request dengan format dd-mm-yyyy, atau gunakan tanggal hari ini jika tidak ada
        $thisday = $request->input('date', now()->format('d-m-Y')); // Default format hari ini dd-mm-yyyy

        // Ubah format tanggal dari dd-mm-yyyy menjadi yyyy-mm-dd agar bisa digunakan di query
        try {
            $thisday = \Carbon\Carbon::createFromFormat('d-m-Y', $thisday)->toDateString();
        } catch (\Exception $e) {
            // Jika format tanggal salah, gunakan tanggal hari ini sebagai fallback
            $thisday = now()->toDateString();
        }

        $yesterday = now()->subDay()->startOfDay();
        $tomorrow = now()->addDay()->endOfDay();

        $events = Event::orderBy('start', 'desc')->get();

        // Daftar ruang rapat
        $ruangrapat = [
            'All',
            'Penggunaan Account Zoom Meeting',
            'Ruang Rapat 3.1',
            'Ruang Rapat 3.2',
            'Ruang Rapat 3.3',
            'Ruang Rapat 3.4',
            'Co-Working Space',
            'Ruang Rapat Basic Design I'
        ];

        // Mengambil event yang aktif pada hari ini, kemarin, dan besok
        $eventshariinikemarinbesok = Event::where(function ($query) use ($thisday) {
            $query->whereDate('start', '<=', $thisday)
                ->whereDate('end', '>=', $thisday);
        })
            ->orWhere(function ($query) use ($yesterday, $thisday) {
                $query->whereDate('start', '<=', $yesterday)
                    ->whereDate('end', '>=', $yesterday)
                    ->whereDate('end', '<', $thisday); // Hanya yang berakhir kemarin
            })
            ->orWhere(function ($query) use ($tomorrow) {
                $query->whereDate('start', '<=', $tomorrow)
                    ->whereDate('end', '>=', $tomorrow);
            })
            ->get();

        // Prepare the revisiall array
        $revisiall = ['All' => ['events' => $eventshariinikemarinbesok]];

        foreach ($ruangrapat as $room) {
            $filteredEvents = $events->where('room', $room)->values();
            $key = str_replace(['.', ' '], ['-', '_'], $room);
            $revisiall[$key]['events'] = $filteredEvents;
        }

        // Handle specific room for "Penggunaan Account Zoom Meeting"
        $room = "Penggunaan Account Zoom Meeting";
        $filteredEvents = $events->where('room', $room)->values();
        $key = str_replace(['.', ' '], ['-', '_'], $room);
        $revisiall[$key]['events'] = $filteredEvents;


        // Format untuk eventsdaypilot
        $eventsdaypilot = $eventshariinikemarinbesok->map(function ($event) {

            return [
                'id' => $event->id,
                'text' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'pic' => $event->pic,
                'resource' => str_replace(['.', ' '], ['-', '_'], $event->room)
            ];
        });
        $availableUsers = User::all();

        return view('rapat.indexdownload', compact('eventshariinikemarinbesok', 'revisiall', 'ruangrapat', 'eventsdaypilot', 'alert', 'thisday', 'ruangrapat', 'telegramaccounts', 'availableUsers'));
    }



}
