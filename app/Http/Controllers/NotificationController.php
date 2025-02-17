<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Category;
use App\Models\Unit;
use App\Models\User;
use App\Models\TelegramMessagesAccount;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class NotificationController extends Controller
{

    public function showByDivisi($namaDivisi)
    {
        // Assuming 'category_name' is the correct column name in the 'Category' model
        $category = Category::where('category_name', 'unitunderpe')->pluck('category_member');
        $categoryprojectbaru = json_decode($category, true)[0];
        $categoryproject = trim($categoryprojectbaru, '"'); // Remove the extra double quotes
        $allunitunderpe = json_decode($categoryproject, true);

        $namaDivisi = auth()->user()->rule;

        $unit = Unit::where('name', $namaDivisi)->first();

        if (!$unit) {
            return view('showinformation.info', ['message' => 'Unit tidak ditemukan']);
        }

        // Eager load the 'memo' relationship in the Notification model
        $notifs = Notification::where('idunit', $unit->id)
            ->with('memo') // Eager loading the 'memo' relationship
            ->orderBy('created_at', 'desc')
            ->get();

        if ($notifs->isEmpty()) {
            return view('showinformation.info', ['message' => 'Tidak ada data untuk unit ini']);
        }

        return view('notification.mailbox', compact("namaDivisi", 'notifs'));
    }


    public function sendwa(Request $request)
    {
        $listnohp = $request->input('phonenumbers');
        $pesan = $request->input('pesan');
        $senderName = $request->input('sender_name'); // Ambil nama pengirim dari request

        try {
            // Send WhatsApp message with sender name
            Wagroupnumber::sendWaMessage($listnohp, "$pesan\n\nDikirim oleh: $senderName");

            // Return a JSON response indicating success
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim.'
            ], 200);

        } catch (\Exception $e) {
            // Handle any exceptions that may occur and return a JSON error response
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengirim pesan: ' . $e->getMessage()
            ], 500);
        }
    }


    public function viewsendwa()
    {
        // Ambil nama pengguna yang diautentikasi
        $senderName = auth()->user()->name ?? 'Anonim';

        // Ambil data user (name dan waphonenumber) dari database, hanya yang memiliki waphonenumber
        $userphonebook = User::whereNotNull('waphonenumber')
            ->select('name', 'waphonenumber')
            ->get();

        // Kirimkan nama pengirim dan data user ke view
        return view('notification.sendwa', compact('senderName', 'userphonebook'));
    }




}