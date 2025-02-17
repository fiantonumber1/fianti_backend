<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\RespondedMessage;
use App\Models\TelegramMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\FileController;
use Telegram\Bot\FileUpload\InputFile;
use Telegram\Bot\Laravel\Facades\Telegram;
use Spatie\DbDumper\Databases\MySql;


class OtomasiController extends Controller
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
    private $telegram_api_url = 'https://api.telegram.org/bot6932879805:AAGcZyniuYjiP7m29xg7EDXJjztZRCxc378/';

    // public function getUpdatesTelegramCommand()
    // {
    //     $response = Http::get($this->telegram_api_url . 'getUpdates');
    //     $updates = $response->json()['result'];

    //     foreach ($updates as $update) {
    //         if (isset($update['message'])) {
    //             $message = $update['message'];

    //             if (
    //                 isset($message['message_id']) &&
    //                 isset($message['chat']['id']) &&
    //                 isset($message['date']) &&
    //                 isset($message['text'])
    //             ) {
    //                 $message_id = $message['message_id'];
    //                 $chat_id = $message['chat']['id'];
    //                 $date = $message['date'];
    //                 $text = $message['text'];

    //                 if(strpos($text, '\memo') !== false){
    //                     $text1=str_replace("\memo ","",$text);
    //                     $memonumber= substr($text1, 0, 14);  // Mengambil 8 huruf pertama dari $text1
    //                     $text= $this->memoinfo($memonumber);
    //                 }

    //                 if(strpos($text, '\memo') !== false){
    //                     // Cek apakah pesan sudah dibalas
    //                     $existingMessage = RespondedMessage::where('message_id', $message_id)->first();
    //                     if (!$existingMessage) {
    //                         // Balas pesan
    //                         $this->sendMessage($chat_id, $text);

    //                         // Simpan pesan yang telah dibalas
    //                         RespondedMessage::create([
    //                             'message_id' => $message_id,
    //                             'chat_id' => $chat_id,
    //                             'date' => date('Y-m-d H:i:s', $date)
    //                         ]);
    //                     }
    //                 }

    //             }
    //         }
    //     }
    // }


    private function sendMessage($chat_id, $text)
    {
        Telegram::sendMessage([
            'chat_id' => $chat_id,
            'text' => $text
        ]);
    }







    public function run_simpandatabaseinka()
    {
        $host = config('inka.DB_HOST_INKA');
        $port = config('inka.DB_PORT_INKA');
        $database = config('inka.DB_DATABASE_INKA');
        $username = config('inka.DB_USERNAME_INKA');
        $password = config('inka.DB_PASSWORD_INKA');

        // Direktori untuk menyimpan file backup
        $backupDirectory = storage_path('app/public/backupsdatabase');

        // Buat folder pencadangan jika belum ada
        if (!file_exists($backupDirectory)) {
            mkdir($backupDirectory, 0777, true);
        }

        // Cek dan hapus file backup lama jika lebih dari 5
        $files = glob($backupDirectory . '/*.sql'); // Mengambil semua file .sql di direktori backup
        if (count($files) >= 5) {
            // Urutkan file berdasarkan waktu (terbaru pertama)
            usort($files, function ($a, $b) {
                return filemtime($b) - filemtime($a); // Membandingkan berdasarkan waktu modifikasi file
            });

            // Hapus file yang lebih lama dari 5 file terbaru
            $filesToDelete = array_slice($files, 5); // Ambil file yang lebih dari 5
            foreach ($filesToDelete as $file) {
                unlink($file); // Menghapus file backup lama
            }
        }

        // Nama file pencadangan
        $backupFileName = 'inka_' . date('Y-m-d_H-i-s') . '.sql';
        $backupFilePath = $backupDirectory . '/' . $backupFileName; // Simpan di direktori yang benar

        try {
            MySql::create()
                ->setDbName($database)
                ->setUserName($username)
                ->setPassword($password)
                ->setHost($host)
                ->setPort($port)
                ->dumpToFile($backupFilePath);

            return response()->json(['success' => 'Backup created successfully.']);
        } catch (\Exception $e) {
            \Log::error('Backup failed: ' . $e->getMessage());
            return response()->json(['error' => 'Backup failed.'], 500);
        }
    }

    public function download_last_backup()
    {
        // Direktori untuk file backup
        $backupDirectory = storage_path('app/public/backupsdatabase');

        // Ambil semua file .sql di direktori backup
        $files = glob($backupDirectory . '/*.sql');

        // Periksa apakah ada file backup
        if (count($files) == 0) {
            return response()->json(['error' => 'No backups found.'], 404);
        }

        // Urutkan file berdasarkan waktu (terbaru pertama)
        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a); // Membandingkan berdasarkan waktu modifikasi file
        });

        // Ambil file backup terbaru
        $latestBackup = $files[0];

        // Set header untuk mendownload file
        return response()->download($latestBackup);
    }












}
