<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class EkspedisiController extends Controller
{
    public function index()
    {
        return view('ekpedisi.upload');
    }



    public function sendPdfToNode(Request $request)
    {
        // Validasi bahwa setiap file adalah PDF dengan ukuran maksimal 2MB
        $request->validate([
            'file.*' => 'required|file|mimes:pdf|max:2048',
        ]);

        // Ambil semua file yang di-upload
        $files = $request->file('file');

        // Inisialisasi Guzzle Client
        $client = new Client();
        $multipart = [];

        foreach ($files as $file) {
            $multipart[] = [
                'name' => 'files',  // Sesuai dengan API di server Node.js
                'contents' => fopen($file->getPathname(), 'r'),
                'filename' => $file->getClientOriginalName()
            ];
        }

        try {
            // Kirim file ke server Express.js
            $uploadResponse = $client->post('http://147.93.103.168:5000/api/upload', [
                'multipart' => $multipart
            ]);

            // Ambil respons dari server Express.js
            $uploadResponseBody = json_decode($uploadResponse->getBody(), true);

            // Pastikan file berhasil diunggah sebelum lanjut ke proses
            if (!isset($uploadResponseBody['files']) || empty($uploadResponseBody['files'])) {
                return response()->json([
                    'message' => 'Gagal mengunggah file ke server.',
                ], 500);
            }

            // Kirim permintaan untuk memproses file di server Express.js
            $processResponse = $client->post('http://147.93.103.168:5000/api/process', []);

            // Ambil respons dari proses
            $processResponseBody = json_decode($processResponse->getBody(), true);

            // Pastikan proses berhasil sebelum lanjut ke download
            if (isset($processResponseBody['error'])) {
                return response()->json([
                    'message' => 'Gagal memproses file.',
                    'error' => $processResponseBody['error'],
                ], 500);
            }

            // Kirim permintaan untuk mengunduh file hasil proses
            $downloadResponse = $client->get('http://147.93.103.168:5000/api/download');

            // Pastikan file hasil proses tersedia
            if ($downloadResponse->getStatusCode() != 200) {
                return response()->json([
                    'message' => 'Gagal mengunduh file hasil proses.',
                    'error' => 'File hasil proses tidak ditemukan.',
                ], 500);
            }

            // Ambil file hasil download
            $downloadedFile = $downloadResponse->getBody();
            $filename = 'processed_file_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            // Kirim file ke user untuk diunduh
            return response()->stream(
                function () use ($downloadedFile) {
                    echo $downloadedFile;
                },
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => "attachment; filename=\"$filename\"",
                ]
            );
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat mengunggah, memproses, atau mengunduh file.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
