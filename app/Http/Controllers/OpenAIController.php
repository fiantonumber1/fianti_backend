<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OpenAIController extends Controller
{
    // Use the OpenAI API key and URL from the environment file
    private $apiKey;
    private $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        // Retrieve the API key from the .env file
        $this->apiKey = env('OPENAI_API_KEY');
    }

    /**
     * Handle the request to check doctor validity using OpenAI's API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkDoctorValidity(Request $request)
    {
        $userInput = $request->input('user_input'); // Get user input from request

        if (!$userInput) {
            return response()->json(['error' => 'User input is required.'], 400);
        }

        try {
            // Make POST request to OpenAI API
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->post($this->apiUrl, [
                        'model' => 'gpt-4o',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => 'Anda adalah AI dokter yang bisa memverifikasi dokter dan memberikan informasi medis.'
                            ],
                            [
                                'role' => 'user',
                                'content' => $userInput
                            ]
                        ],
                        'temperature' => 0.7,
                    ]);

            if ($response->successful()) {
                $data = $response->json();
                $message = trim($data['choices'][0]['message']['content']);
                return response()->json(['message' => $message]);
            } else {
                return response()->json(['error' => 'Gagal mendapatkan validasi. Coba lagi nanti.'], 500);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
