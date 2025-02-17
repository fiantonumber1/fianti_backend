<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use App\Models\ChatInForum;
use App\Models\CollectFile;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;


class ForumController extends Controller
{
    public function index()
    {
        $forums = Forum::all();
        return view('forums.index', compact('forums'));
    }

    public function create()
    {
        return view('forums.create');
    }

    public function show(Request $request, $id)
    {
        $specialUserIds = [2];
        $forum = Forum::findOrFail($id);
        $isSpecialUser = in_array(auth()->id(), $specialUserIds);

        if ($forum->password === "") {
            $conversationMessages = $forum->chats;
            $lastMessageId = $conversationMessages->last()->id ?? 0;
            return view('forums.show', compact('forum', 'conversationMessages', 'isSpecialUser', 'lastMessageId'));
        }

        if ($request->has('password')) {
            if ($request->password === $forum->password) {
                $conversationMessages = $forum->chats;
                $lastMessageId = $conversationMessages->last()->id ?? 0;
                return view('forums.show', compact('forum', 'conversationMessages', 'isSpecialUser', 'lastMessageId'));
            } else {
                return redirect()->back()->withErrors(['password' => 'Incorrect password.']);
            }
        }

        return view('forums.password', compact('forum'));
    }


    public function loadChats(Request $request, $id)
    {
        $forum = Forum::findOrFail($id);
        if ($request->has('password') && $request->password === $forum->password) {
            $conversationMessages = $forum->chats;
            return view('forums.chats', compact('conversationMessages'))->render();
        }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function loadNewChats(Request $request, $id)
    {
        $forum = Forum::findOrFail($id);
        $lastMessageId = $request->last_message_id;

        $newMessages = ChatInForum::where('forum_id', $id)
                                    ->where('id', '>', $lastMessageId)
                                    ->get();

        return view('forums.new_chats', compact('newMessages'))->render();
    }

    public function store(Request $request)
    {
        $forum = Forum::create($request->only('topic', 'description', 'password'));
        return redirect()->route('forums.index')->with('success', 'Forum created successfully.');;
    }

    public function storeChat(Request $request, $forumId)
    {
    
        // Simpan chat
        $chat = new ChatInForum();
        $chat->user_id = $request->user_id;
        $chat->chat = $request->chat;
        $chat->forum_id = $forumId;
        // Check for playyoutube command
        if (strpos($request->chat, '\playyoutube_') === 0) {
            $url = trim(str_replace('\playyoutube_', '', $request->chat));
            $chat->chat_type = 'youtube';
            $chat->chat = $url;
        }

        $chat->save();

        // Ambil nama pengguna dari objek autentikasi
        $useronly = auth()->user()->name;

        if ($request->hasFile('filenames')) {
            foreach ($request->file('filenames') as $file) {
                $filename = $file->getClientOriginalName();
                $filenameWithoutExtension = pathinfo($filename, PATHINFO_FILENAME);
                $fileFormat = $file->getClientOriginalExtension();
                $filenameWithUserAndFormat = $filenameWithoutExtension . '_' . $useronly . '.' . $fileFormat;
                $count = 1;
                $filename = $filenameWithUserAndFormat;

                while (CollectFile::where('filename', $filename)->exists()) {
                    $filename = $filenameWithoutExtension . '_' . $useronly . '_' . $count . '.' . $fileFormat;
                    $count++;
                }

                $path = $file->storeAs('uploads', $filename);

                // Simpan file terkait
                $hazardLogFile = new CollectFile();
                $hazardLogFile->filename = $filename;
                $hazardLogFile->link = $path;
                $hazardLogFile->collectable_id = $chat->id; // Menghubungkan file dengan feedback
                $hazardLogFile->collectable_type = ChatInForum::class; // Tipe polimorfik
                $hazardLogFile->save();
            }
        }

        return redirect()->back()->with('success', 'Chat added successfully');
    }

}
