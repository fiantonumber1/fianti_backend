<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class Preprocessing extends Model
{
    public function fetchDataFromApi($table_name)
    {
        $url = "https://api.materialkokoh.com/apishopee/postgetshopee.php";

        // Function to send POST request
        function requests_post($url, $data)
        {
            $options = array(
                'http' => array(
                    'header' => "Content-Type: application/x-www-form-urlencoded",
                    'method' => 'POST',
                    'content' => http_build_query($data),
                ),
            );
            $context = stream_context_create($options);
            return file_get_contents($url, false, $context);
        }

        // Retrieve data from the API
        $payload = array('table' => $table_name);
        $responseku = requests_post($url, $payload);
        $hasil = json_decode($responseku, true);

        // Create a list of items with 'None' for the 'balasan' field
        $list_push = array();
        foreach ($hasil as $item) {
            if (!isset($item['balasan'])) {
                $list_push[] = array('pertanyaan' => $item['pertanyaan'], 'balasan' => null);
            }
        }

        return $list_push;
    }
}
