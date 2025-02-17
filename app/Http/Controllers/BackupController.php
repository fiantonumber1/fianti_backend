<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    public function runBackup()
    {
        // Mengatur waktu eksekusi maksimum dan waktu tunggu (timeout) menjadi tidak terbatas
        ini_set('max_execution_time', 0);
        ini_set('request_terminate_timeout', 0);

        // Mencatat waktu mulai backup
        $backupStartTime = microtime(true);

        // Path ke direktori storage/app Laravel yang ingin di-backup
        $storagePath = storage_path('app');
        $zipPath = storage_path('app/backup.zip');

        // Buat file zip dari direktori storage/app
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === true) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storagePath),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $name => $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($storagePath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();
        } else {
            return response()->json(['message' => 'Failed to create zip file.'], 500);
        }

        // Menghitung waktu yang diperlukan untuk backup
        $backupExecutionTime = microtime(true) - $backupStartTime;

        // Menghitung ukuran file dalam MB
        $fileSizeMB = filesize($zipPath) / (1024 * 1024); // Convert bytes to MB

        // Kembalikan URL untuk mendownload file backup, ukuran file (MB), dan waktu eksekusi backup (detik)
        return response()->json([
            'url' => url('download-backup'),
            'backup_execution_time' => $backupExecutionTime,
            'file_size_mb' => round($fileSizeMB, 2) // Round to 2 decimal places
        ]);
    }

    public function downloadBackup()
    {
        // Mengatur waktu eksekusi maksimum dan waktu tunggu (timeout) menjadi tidak terbatas
        ini_set('max_execution_time', 0);
        ini_set('request_terminate_timeout', 0);

        // Path ke file backup
        $filePath = storage_path('app/backup.zip');

        if (file_exists($filePath)) {
            // Mendapatkan ukuran file
            $fileSize = filesize($filePath);

            // Mencatat waktu mulai download
            $downloadStartTime = microtime(true);

            // Mengirimkan file untuk di-download
            $response = Response::download($filePath, 'backup.zip');

            // Menghitung waktu yang diperlukan untuk download
            $downloadExecutionTime = microtime(true) - $downloadStartTime;

            // Menambahkan informasi ukuran file dan waktu download ke dalam header respons
            $response->headers->add([
                'Content-Length' => $fileSize, // Ukuran file dalam bytes
                'Download-Execution-Time' => $downloadExecutionTime // Waktu eksekusi download dalam detik
            ]);

            return $response;
        } else {
            return response()->json(['message' => 'Backup file not found.'], 404);
        }
    }

    
}
