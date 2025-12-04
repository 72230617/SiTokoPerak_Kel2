<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProdukView;

class ProdukViewController extends Controller
{
    public function store(Request $request, $produkId)
    {
        $sessionId = session()->getId();

        // 1 baris per session per produk
        ProdukView::firstOrCreate([
            'produk_id' => $produkId,
            'session_id' => $sessionId,
        ]);

        $totalViews = ProdukView::where('produk_id', $produkId)->count();

        return response()->json([
            'success' => true,
            'totalViews' => $totalViews,
        ]);
    }
}
