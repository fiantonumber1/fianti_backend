<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;
use App\Models\TelegramMessagesAccount;

class BotTelegramController extends Controller
{
    
    public function setWebhook()
    {
        $token = config('bot.webhook_url');

        $response = Telegram::setWebhook(['url' => $token]);
        dd($response);
    }
    
    public function commandHandlerWebHook()
    {
        $updates = Telegram::commandsHandler(true);
        return Telegram::sendMessage([
            'chat_id' => 7064508130,
            'text' => 'Halo ' ,
        ]);
        
    }

    public function informasichatbotfuture($message, $roomname, $jenispesan)
    {
        // Cari chat ID berdasarkan roomname menggunakan model TelegramMessagesAccount
        $telegramAccount = TelegramMessagesAccount::where('account', $roomname)->first();
        
        // Jika roomname ditemukan, dapatkan chat_id dari telegram_id
        if ($telegramAccount) {
            $chatid = $telegramAccount->telegram_id;
            sleep(10); // Delay selama 10 detik

            $updates = Telegram::commandsHandler(true);
            
            // Kirim pesan berdasarkan jenis pesan
            if ($jenispesan == "text") {
                // Kirim pesan teks langsung
                return Telegram::sendMessage([
                    'chat_id' => $chatid,
                    'text' => $message,
                ]);
            } elseif ($jenispesan == "pdf" || $jenispesan == "sql") {
                // Kirim file PDF atau SQL
                $pesan = json_decode($message, true);
                $inputFile = InputFile::create($pesan["link"], $pesan["namafile"]);
                return Telegram::sendDocument([
                    'chat_id' => $chatid,
                    'document' => $inputFile,
                ]);
            } elseif ($jenispesan == "document") {
                // Simpan pesan dalam file teks sementara
                $textFilePath = 'temporary_message.txt';
                file_put_contents(storage_path('app/'.$textFilePath), $message);

                // Create InputFile object dari file yang disimpan
                $inputFile = InputFile::create(storage_path('app/'.$textFilePath), $textFilePath);

                // Kirim dokumen menggunakan sendDocument dengan parameter document
                return Telegram::sendDocument([
                    'chat_id' => $chatid,
                    'document' => $inputFile,
                ]);
            }
        } else {
            // Jika roomname tidak ditemukan, return error atau handle sesuai kebutuhan
            return response()->json(['error' => 'Room name not found'], 404);
        }
    }
    
    public function informasichatbot($message, $roomname, $jenispesan)
    {
        $chatid = null;
        $informasi=[];
        $informasi["EngineeringOffice Notif"]=-4182432670;
        $informasi["Product Engineering"]=-4198400936;
        $informasi["Mechanical Engineering System"]=-4119524947;
        $informasi["Electrical Engineering System"]=-4167786260;
        $informasi["Desain Mekanik & Interior"]=-4185377771;
        $informasi["Desain Bogie & Wagon"]=-4177114589;
        $informasi["Desain Carbody"]=-4109358061;
        $informasi["Desain Elektrik"]=-4110317119;
        $informasi["Preparation & Support"]=-4149864532;
        $informasi["Shop Drawing"]=-4133766074;
        $informasi["Teknologi Proses"]=-4108868680;
        $informasi["RAMS"]=-4179548004;
        $informasi["Welding Technology"]=-4132751208;
        $informasi["Quality Engineering"]=-4147225904;
        // $informasi["DataBackup"]=-4282757955;
        // $informasi["Backup Data INKA"]=-4282757955;
        $informasi["PPO"]= -4531388107;
        $informasi["Produksi Fabrikasi"]= -4536862809;
        $informasi["Produksi Finishing"]= -4538506305;

    
        if(isset($informasi[$roomname])){
            $chatid = $informasi[$roomname];
            sleep(10); // Delay selama 10 detik
            $updates = Telegram::commandsHandler(true);
            if($jenispesan == "text"){
                // Kirim pesan teks langsung
                return Telegram::sendMessage([
                    'chat_id' => $chatid,
                    'text' => $message,
                ]);
            }elseif($jenispesan == "pdf" || $jenispesan == "sql"){
                // Kirim pesan teks langsung
                $pesan = json_decode($message,true);
                $inputFile = InputFile::create($pesan["link"], $pesan["namafile"]);
                return Telegram::sendDocument([
                    'chat_id' => $chatid,
                    'document' => $inputFile,
                ]);
            } elseif($jenispesan == "document"){
                // Simpan pesan dalam file teks sementara
                $textFilePath = 'temporary_message.txt';
                file_put_contents(storage_path('app/'.$textFilePath), $message);
    
                // Create InputFile object dari file yang disimpan
                $inputFile = InputFile::create(storage_path('app/'.$textFilePath), $textFilePath);
    
                // Kirim dokumen menggunakan sendDocument dengan parameter document
                return Telegram::sendDocument([
                    'chat_id' => $chatid,
                    'document' => $inputFile,
                ]);
            }
        }
    }



    

}
