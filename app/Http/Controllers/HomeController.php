<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Models\NewMemo;
use App\Models\Category;
use App\Models\ProjectType;
use App\Models\Event;
use App\Models\Newreport;
use Illuminate\Routing\Controller;
use App\Http\Controllers\FileController;
use App\Models\NewProgressReportDocumentKind;

class HomeController extends Controller
{
    protected $fileController;

    protected $progressreportController;
    protected $bottelegramController;

    public function __construct(FileController $fileController, ProgressreportController $progressreportController, BotTelegramController $bottelegramController)
    {
        $this->fileController = $fileController;

        $this->progressreportController = $progressreportController;
        $this->bottelegramController = $bottelegramController;
    }




    public function homeinduk()
    {

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

        $ruangrapat = Category::getlistCategoryMemberByName('ruangrapat');
        $ruangrapat[] = 'Penggunaan Account Zoom Meeting';

        $today = now()->toDateString(); // Ambil tanggal hari ini

        $eventshariini = Event::whereDate('start', '<=', $today)
            ->whereDate('end', '>=', $today)
            ->get();
        $data = [
            'ruangrapat' => $ruangrapat,
            'events' => $eventsdaypilot
        ];
        // Return the view with compacted variables
        return $data;
    }
    public function showHome()
    {
        $user = Auth::user();
        $waphonenumber = $user->waphonenumber;
        $alert = $waphonenumber === null ? "yes" : "no"; // Gunakan $alert daripada $allert untuk konsistensi dan ejaan yang benar

        $data = $this->homeinduk();

        $ruangrapat = $data['ruangrapat'];
        $events = $data['events'];


        $availabledocumentname = NewProgressReportDocumentKind::pluck('name', 'id');
        $projectsData = [];
        // Cache selama 3 jam (180 menit)
        $proyek_types = Cache::remember('proyek_types', 180, function () {
            return ProjectType::all();
        });
        foreach ($proyek_types as $proyek_type) {
            $projectsData[$proyek_type->title] = [];
        }



        $project = $request->projectName ?? "";
        $download = $request->download ?? 'false';




        return view('auth.home', compact('projectsData', 'project', 'download', 'ruangrapat', 'events', 'alert'));
    }

    public function showHomeslider()
    {
        $data = $this->homeinduk();
        $revisiall = $data['revisiall'];
        $allunitunderpe = $data['allunitunderpe'];
        $unitsingkatan = $data['unitsingkatan'];
        $ruangrapat = $data['ruangrapat'];
        $events = $data['events'];

        return view('auth.homeslider', compact('revisiall', 'allunitunderpe', 'unitsingkatan', 'ruangrapat', 'events'));
    }





}
