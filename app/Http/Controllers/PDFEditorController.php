<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class PDFEditorController extends Controller
{
    /**
     * Menampilkan formulir unggah PDF dan gambar.
     *
     * @return \Illuminate\View\View
     */
    public function form()
    {
        return view('pdf_form');
    }

    /**
     * Mengedit PDF dengan menambahkan gambar berdasarkan input dari frontend.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editPDF(Request $request)
    {
        // Validasi file dan input lainnya
        $request->validate([
            'pdf_file' => 'required|mimes:pdf|max:10240',
            'image_file' => 'required|image|max:5120',
            'top_position' => 'required|numeric',
            'left_position' => 'required|numeric',
            'image_width' => 'required|numeric',
            'image_height' => 'required|numeric',
            'scale' => 'nullable|numeric|min:0.1|max:10', // Validasi untuk skala
        ]);

        // Simpan file sementara
        $pdfPath = $request->file('pdf_file')->store('temp');
        $imagePath = $request->file('image_file')->store('temp');

        // Lokasi file di sistem
        $fullPdfPath = storage_path("app/$pdfPath");
        $fullImagePath = storage_path("app/$imagePath");

        // Inisialisasi FPDI
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($fullPdfPath);
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);

        // Tambahkan halaman pertama
        $pdf->addPage();
        $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

        // Hitung skala posisi dan dimensi gambar
        $frontendWidth = 600; // Lebar tampilan frontend (px)
        $scale = $size['width'] / $frontendWidth;

        $imagePosition = [
            'top' => $request->input('top_position') * $scale,
            'left' => $request->input('left_position') * $scale,
        ];

        // Ambil skala dari input (default = 1 jika tidak diatur)
        $userScale = $request->input('scale', 1);

        $imageWidth = $request->input('image_width') * $scale * $userScale;
        $imageHeight = $request->input('image_height') * $scale * $userScale;

        // Tambahkan gambar ke PDF
        $pdf->Image(
            $fullImagePath, 
            $imagePosition['left'], 
            $imagePosition['top'], 
            $imageWidth, 
            $imageHeight
        );

        // Simpan PDF yang sudah diedit
        $outputPath = 'public/edited_pdf.pdf';
        $pdf->Output('F', storage_path("app/$outputPath"));

        // Hapus file sementara
        Storage::delete($pdfPath);
        Storage::delete($imagePath);

        // Kirim tautan unduhan
        return response()->json([
            'download_url' => asset("storage/edited_pdf.pdf"),
        ]);
    }



    public function editAndDownloadPDF()
    {
        // Lokasi file PDF dan gambar
        $pdfPath = storage_path('app/coba.pdf');
        $imagePath = storage_path('app/coba.jpg');

        // Periksa apakah file tersedia
        if (!file_exists($pdfPath) || !file_exists($imagePath)) {
            return response()->json(['error' => 'File PDF atau gambar tidak ditemukan.'], 404);
        }

        // Inisialisasi FPDI
        $pdf = new Fpdi();
        $pageCount = $pdf->setSourceFile($pdfPath);
        $templateId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($templateId);

        // Tambahkan halaman pertama
        $pdf->addPage();
        $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

        $status ="approver";
        if($status=="checker"){
            // Koordinat dan dimensi gambar
            $imageWidth = 17.4; // Lebar gambar (dalam unit PDF, biasanya mm)
            $imageHeight = 17.4; // Tinggi gambar (dalam unit PDF, biasanya mm)
            $imageX = 114.5; // Posisi X (dari kiri halaman)
            $imageY = 48; // Posisi Y (dari atas halaman)
        }elseif($status=="approver"){
            // Koordinat dan dimensi gambar
            $imageWidth = 17.4; // Lebar gambar (dalam unit PDF, biasanya mm)
            $imageHeight = 17.4; // Tinggi gambar (dalam unit PDF, biasanya mm)
            $imageX = 165.5; // Posisi X (dari kiri halaman)
            $imageY = 48; // Posisi Y (dari atas halaman)
        }
        

        // Tambahkan gambar ke PDF menggunakan koordinat absolut
        $pdf->Image($imagePath, $imageX, $imageY, $imageWidth, $imageHeight);

        // Buat file PDF sementara di memori
        $outputPath = tempnam(sys_get_temp_dir(), 'edited_pdf');
        $pdf->Output('F', $outputPath);

        // Kirim file sebagai unduhan
        return response()->streamDownload(function () use ($outputPath) {
            readfile($outputPath);
        }, 'edited_pdf.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }



}