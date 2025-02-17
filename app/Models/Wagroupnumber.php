<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Wagroupnumber extends Model
{
    use HasFactory;

    protected $fillable = ['groupname', 'number', 'isverified'];


    public static function sendWaMessage($listnohp, $pesan)
    {
        // Initialize an array to store formatted phone numbers
        $formattedNumbers = [];

        foreach ($listnohp as $nohp) {

            if (strlen($nohp) < 15) {
                // Remove '+' character if present
                $nohp = ltrim($nohp, '+');

                // Replace '08' prefix with '628'
                if (substr($nohp, 0, 2) === '08') {
                    $nohp = '628' . substr($nohp, 2);
                } elseif (substr($nohp, 0, 3) !== '628') {
                    continue; // Skip invalid phone numbers
                }
            }


            // Add formatted number to the array
            $formattedNumbers[] = $nohp;
        }

        // Check if there are any valid phone numbers
        if (empty($formattedNumbers)) {
            return response()->json([
                'message' => 'Tidak ada nomor telepon yang valid'
            ], 400);
        }


        if (is_numeric($pesan)) {
            // Jika $pesan bisa diubah menjadi integer, kirim sebagai file
            $response = Http::post('https://diyloveheart.in/api/wamessages/post', [
                'phone_numbers' => $formattedNumbers,
                'message' => "" . $pesan, // Ubah ke integer
                'wamessagekind' => "file",
                'idtoken' => '2910219210291', // Replace with the correct ID token
                'accesstoken' => '37237232u32y', // Replace with the correct access token
            ]);
        } else {
            // Jika $pesan tidak bisa diubah menjadi integer, kirim sebagai teks
            $response = Http::post('https://diyloveheart.in/api/wamessages/post', [
                'phone_numbers' => $formattedNumbers,
                'message' => $pesan, // Kirim sebagai teks
                'wamessagekind' => "text",
                'idtoken' => '2910219210291', // Replace with the correct ID token
                'accesstoken' => '37237232u32y', // Replace with the correct access token
            ]);
        }


        // Check if the response is successful
        if ($response->successful()) {
            return response()->json([
                'message' => 'Data berhasil disimpan',
                'data' => $response->json()
            ], 201);
        } else {
            return response()->json([
                'message' => 'Gagal menyimpan data',
                'status' => $response->status(),
                'error' => $response->body()
            ], $response->status());
        }
    }

    public static function ujisendunit($unit, $message)
    {
        // Initialize an array to store phone numbers
        $arraynumber = [];

        switch ($unit) {

            case "Quality Engineering":
                $arraynumber[] = '120363375608982413';
                break;

            case "Electrical Engineering System":
                $arraynumber[] = '120363359225428796';
                break;

            case "Mechanical Engineering System":
                $arraynumber[] = '120363376758705413';
                break;

            case "Product Engineering":
                $arraynumber[] = '120363378278764767';
                break;

            case "Desain Mekanik & Interior":
                $arraynumber[] = '120363375414522511';
                break;
            case "Desain Carbody":
                $arraynumber[] = '120363354845494246';
                break;

            case "RAMS":
                $arraynumber[] = '120363376827304062';
                break;

            case "Desain Bogie & Wagon":
                $arraynumber[] = '120363358094687724';
                break;



            case "Desain Elektrik":
                $arraynumber[] = '120363357893948659';
                break;


            case "Preparation & Support":
                $arraynumber[] = '120363377589869848';
                break;

            case "Welding Technology":
                $arraynumber[] = '120363394496310257';
                break;

            case "Teknologi Proses":
                $arraynumber[] = '120363395166185110';
                break;



            case "QC INC":
                $arraynumber[] = '120363378030037045';
                break;

            case "PPO":
                $arraynumber[] = '120363380589312298';
                break;

            case "Produksi Finishing":
                $arraynumber[] = '120363379456107274';
                break;

            case "Pabrik Banyuwangi":
                $arraynumber[] = '120363377878364167';
                break;

            case "QC Banyuwangi":
                $arraynumber[] = '120363377473145803';
                break;

            case "Produksi Fabrikasi":
                $arraynumber[] = '120363380070486933';
                break;

            case "Teknologi Banyuwangi":
                $arraynumber[] = '120363380004952568';
                break;

            case "Shop Drawing":
                $arraynumber[] = '120363381781364599';
                break;





            default:
                // Retrieve all users with the specified 'rule' value equal to the $unit parameter
                $users = User::where('rule', $unit)->get();

                // Collect phone numbers from the retrieved users
                foreach ($users as $user) {
                    $arraynumber[] = $user->waphonenumber;
                }
                break;
        }

        // Send WhatsApp message using the sendWaMessage method
        return self::sendWaMessage($arraynumber, $message);
    }
}

