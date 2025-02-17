<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TelegramMessage;
use App\Models\User;
use Illuminate\Routing\Controller;
use App\Models\TelegramMessagesAccount;
use App\Http\Controllers\FileController;

class TelegramMessagesAccountController extends Controller
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

    public function index()
    {
        $accounts = TelegramMessagesAccount::all();
        return view('telegram_messages_accounts.index', compact('accounts'));
    }


    public function create()
    {
        return view('telegram_messages_accounts.create');
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'account' => 'required|string|max:255',
            'telegram_id' => 'required|string|max:255',
        ]);

        TelegramMessagesAccount::create($validatedData);

        return redirect()->route('telegram_messages_accounts.index')->with('success', 'Account created successfully.');
    }


    public function show(TelegramMessagesAccount $telegramMessagesAccount)
    {
        return view('telegram_messages_accounts.show', compact('telegramMessagesAccount'));
    }


    public function edit(TelegramMessagesAccount $telegramMessagesAccount)
    {
        return view('telegram_messages_accounts.edit', compact('telegramMessagesAccount'));
    }


    public function update(Request $request, TelegramMessagesAccount $telegramMessagesAccount)
    {
        $validatedData = $request->validate([
            'account' => 'required|string|max:255',
            'telegram_id' => 'required|string|max:255',
        ]);

        $telegramMessagesAccount->update($validatedData);

        return redirect()->route('telegram_messages_accounts.index')->with('success', 'Account updated successfully.');
    }


    public function destroy(TelegramMessagesAccount $telegramMessagesAccount)
    {
        $telegramMessagesAccount->delete();

        return redirect()->route('telegram_messages_accounts.index')->with('success', 'Account deleted successfully.');
    }

    public function runtelegram()
    {
        // Ambil semua TelegramMessage yang statusnya null
        $alltelegram = TelegramMessage::where('status', '=', null)->get();

        // Ambil ID dan account untuk semua TelegramMessagesAccount yang diperlukan
        $accountIds = $alltelegram->pluck('telegram_messages_accounts_id')->unique();
        $accounts = TelegramMessagesAccount::whereIn('id', $accountIds)->pluck('account', 'id');

        // Array untuk menyimpan hasil sukses
        $successMessages = [];

        foreach ($alltelegram as $telegram) {
            $unitAccount = $accounts->get($telegram->telegram_messages_accounts_id);

            if ($unitAccount) {
                try {
                    if ($telegram->message_kind == "text") {
                        // Panggil method untuk mengirim pesan
                        $this->bottelegramController->informasichatbot($telegram->message, $unitAccount, $telegram->message_kind);
                    } else {
                        $this->bottelegramController->informasichatbot($telegram->array_message, $unitAccount, $telegram->message_kind);
                    }

                    // Update status menjadi 'terkirim' setelah pesan berhasil dikirim
                    $telegram->update(['status' => 'terkirim']);

                    // Tambahkan data sukses ke array
                    $successMessages[] = [
                        'id' => $telegram->id,
                        'message' => $telegram->message,
                        'account' => $unitAccount,
                        'message_kind' => $telegram->message_kind
                    ];
                } catch (\Exception $e) {
                    // Tangani error jika ada masalah
                    // Anda dapat memutuskan apa yang akan dilakukan di sini,
                    // misalnya log error atau menambahkan pesan error ke array.
                }
            }
        }

        // Kembalikan JSON dengan data sukses
        return response()->json([
            'success' => true,
            'messages' => $successMessages
        ]);
    }



    public function ujiwa()
    {
        $nohp = "081515814752";
        $pesan = "dasar ujicoba diy";
        $result = Wagroupnumber::sendWaMessage($nohp, $pesan);
        return $result;
    }





}
