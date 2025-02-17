<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MindMapModel;
use App\Models\MindMapModelKind;

class MindMapController extends Controller
{
    

    public function mindmapstore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:mind_map_models,id',
            'level' => 'nullable|exists:mind_map_model_kinds,id'
        ]);

        MindMapModel::create([
            'name' => $request->name,
            'parent_id' => $request->parent_id,
            'level' => $request->level
        ]);

        return redirect()->route('mindmap.index')->with('success', 'Node berhasil ditambahkan!');
    }

    public function mindmapkindstore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        MindMapModelKind::create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->route('mindmap.index')->with('success', 'Node berhasil ditambahkan!');
    }

    public function index()
    {
        // Mengambil semua node dengan relasi 'kind' dan 'children' melalui eager loading
        $mindMapNodes = MindMapModel::with(['kind', 'children'])->get();

        // Membuat array dari nodes dengan parent_id sebagai key
        $nodesByParentId = $mindMapNodes->groupBy('parent_id');

        // Mencari node pertama yang memiliki parent_id null (root node)
        $rootNode = $mindMapNodes->whereNull('parent_id')->first();

        // Membangun data mind map
        $mindMapData = $this->buildMindMapData($rootNode, $nodesByParentId);

        // Mengambil semua jenis mind map
        $kinds = MindMapModelKind::all();

        // Mengirimkan data ke view
        return view('mindmap.mindmap', compact('mindMapData', 'mindMapNodes', 'kinds'));
    }

    private function buildMindMapData($node, $nodesByParentId)
    {
        $data = [
            'meta' => [
                'name' => 'Mind Map Laravel',
                'author' => 'ChatGPT',
                'version' => '0.1'
            ],
            'format' => 'node_tree',
            'data' => $this->formatNode($node, $nodesByParentId)
        ];

        return $data;
    }

    
    private function formatNode($node, $nodesByParentId)
    {
        
        // Jika node tidak ditemukan, kembalikan null
        if (!$node) return null;

        // Ambil anak-anak dari node yang sedang diproses menggunakan array
        $children = isset($nodesByParentId[$node->id]) ? $nodesByParentId[$node->id] : collect();

        // Format node dengan informasi anak-anaknya secara rekursif
        $formattedNode = [
            'id' => $node->id,
            'topic' => $node->name,
            'children' => $children->map(function ($child) use ($nodesByParentId) {
                return $this->formatNode($child, $nodesByParentId);
            })->toArray()
        ];

        return $formattedNode;
    }



}
