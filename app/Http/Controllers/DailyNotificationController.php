<?php

namespace App\Http\Controllers;

use App\Models\DailyNotification;
use App\Models\Wagroupnumber;
use App\Models\NewProgressReportDocumentKind;
use App\Models\NotificationDaily;
use App\Models\CollectFile;
use Carbon\Carbon; // Import Carbon class
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class DailyNotificationController extends Controller
{
    /**
     * Menampilkan daftar DailyNotification beserta relasi newProgressReportHistories.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $dailyNotifications = DailyNotification::with(['newProgressReportHistories', 'users'])->get();

        return view('daily_notifications.index', compact('dailyNotifications'));
    }

    /**
     * Menampilkan data DailyNotification tertentu beserta newProgressReportHistories-nya.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $dailyNotification = DailyNotification::with(['newProgressReportHistories', 'users'])->find($id);

        if (!$dailyNotification) {
            return response()->json(['message' => 'DailyNotification not found'], 404);
        }

        // Mengambil daftar nama dokumen berdasarkan ID
        $documentKindNames = NewProgressReportDocumentKind::pluck('name', 'id');

        // Mengambil laporan terkait dan mengelompokkan berdasarkan jenis dokumen
        $documentview = $dailyNotification->newProgressReportHistories->groupBy(
            fn($report) => $documentKindNames[$report->documentkind_id] ?? 'Unknown'
        );

        return view('daily_notifications.show', compact('dailyNotification', 'documentview', 'id'));
    }









    public function downloadpdf($id)
    {
        // Cari data DailyNotification
        $dailyNotification = DailyNotification::with(['newProgressReportHistories', 'notifHarianUnit'])->find($id);
        if (!$dailyNotification) {
            return response()->json(['error' => 'DailyNotification not found'], 404);
        }

        // Update status menjadi "read"
        $dailyNotification->read_status = 'read';
        $userId = auth()->id();
        if (!$dailyNotification->users()->where('user_id', $userId)->exists()) {
            $dailyNotification->users()->attach($userId);
        }


        $dailyNotification->save();

        // Mendapatkan tanggal laporan
        $date = Carbon::parse($dailyNotification->day);
        $startTime = $date->copy()->subDay()->format('d-m-Y');
        $endTime = $date->format('d-m-Y');

        // Kirim ke WhatsApp
        $unit = $dailyNotification->notifHarianUnit->title ?? 'Unknown Unit';
        $message = "Laporan Expedisi Dokumen per " . Carbon::parse($dailyNotification->day)->format('d-m-Y') . " telah dikonfirmasi.";
        Wagroupnumber::ujisendunit($unit, $message);

        // Ambil data laporan harian
        $documentKindNames = NewProgressReportDocumentKind::pluck('name', 'id')->toArray();
        $updatedReports = $dailyNotification->newProgressReportHistories;
        $documentview = $updatedReports->groupBy(function ($report) use ($documentKindNames) {
            return $documentKindNames[$report->documentkind_id] ?? 'Unknown';
        });

        // Siapkan data untuk PDF
        $data = [
            'startTime' => Carbon::parse($dailyNotification->day)->subDay()->format('d-m-Y'),
            'endTime' => Carbon::parse($dailyNotification->day)->format('d-m-Y'),
            'documentview' => $documentview,
        ];

        // Buat PDF dari tampilan Blade
        $pdf = Pdf::loadView('newprogressreports.notifharian', $data);

        // Kirim notifikasi WhatsApp
        $unit = $dailyNotification->notifHarianUnit->title;

        $message = "📢 *Laporan Ekspedisi Dokumen - {$endTime}* 📢\n\n" .
            "✅ *Status:* Sudah dikonfirmasi\n" .
            "📌 *Unit:* {$unit}\n\n" .
            "📂 Dokumen telah diverifikasi dan tercatat.\n" .
            "🚀 *Terima kasih atas kerja sama dan dedikasi Anda!* 🙏😊\n\n" .
            "🔍 Jika ada pertanyaan, silakan hubungi kami.";

        Wagroupnumber::ujisendunit($unit, $message);

        // Kembalikan file PDF agar langsung terunduh
        return $pdf->download("Laporan_Expedisi_{$data['endTime']}.pdf");
    }

}