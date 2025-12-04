<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Usaha;
use App\Models\ProdukView;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        // Kategori untuk carousel
        $kategoris = KategoriProduk::all();

        // Produk terbaru + foto + jumlah like & view
        $randomProduks = Produk::with('fotoProduk')
            ->withCount([
                'likes as likes_count',
                'views as views_count',
            ])
            ->latest()
            ->take(8)
            ->get();

        return view('guest.pages.index', [
            'kategoris' => $kategoris,
            'randomProduks' => $randomProduks,
        ]);
    }

    public function productsByCategory($slug)
    {
        $kategori = KategoriProduk::where('slug', $slug)->firstOrFail();

        $produks = Produk::where('kategori_produk_id', $kategori->id)
            ->with('fotoProduk')
            ->withCount([
                'likes as likes_count',
                'views as views_count',
            ])
            ->get();

        return view('guest.pages.products', [
            'kategori' => $kategori,
            'produks' => $produks,
        ]);
    }

    public function katalog(Request $request)
    {
        $query = Produk::with('kategoriProduk', 'fotoProduk')
            ->withCount([
                'likes as likes_count',
                'views as views_count',
            ]);

        // SEARCH
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_produk', 'like', '%' . $searchTerm . '%')
                    ->orWhere('deskripsi', 'like', '%' . $searchTerm . '%')
                    ->orWhereHas('kategoriProduk', function ($kategoriQuery) use ($searchTerm) {
                        $kategoriQuery->where('nama_kategori_produk', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        // FILTER KATEGORI
        if ($request->filled('kategori')) {
            $query->whereHas('kategoriProduk', function ($q) use ($request) {
                $q->where('slug', $request->kategori);
            });
        }

        // FILTER USAHA
        if ($request->filled('usaha')) {
            $query->whereHas('usahaProduk.usaha', function ($q) use ($request) {
                $q->where('id', $request->usaha)
                    ->where('status_usaha', 'aktif');
            });
        }

        // FILTER HARGA
        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        // SORTING
        $urutkan = $request->input('urutkan', 'terbaru');
        switch ($urutkan) {
            case 'harga-rendah':
                $query->orderBy('harga', 'asc');
                break;
            case 'harga-tinggi':
                $query->orderBy('harga', 'desc');
                break;
            case 'populer':
                // misal: berdasarkan view terbanyak
                $query->orderBy('views_count', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $produks = $query->paginate(12)->withQueryString();
        $kategoris = KategoriProduk::all();

        return view('guest.pages.katalog', [
            'produks' => $produks,
            'kategoris' => $kategoris,
        ]);
    }

    public function singleProduct($slug)
    {
        $produk = Produk::with(['fotoProduk', 'reviews.user', 'reviews.media'])
            ->withCount([
                'likes as likes_count',
                'views as views_count',
            ])
            ->where('slug', $slug)
            ->firstOrFail();

        // Catat view per session (unique per session)
        $sessionId = session()->getId();
        ProdukView::firstOrCreate([
            'produk_id' => $produk->id,
            'session_id' => $sessionId,
        ]);

        $reviews = $produk->reviews()->latest()->with('user', 'media')->get();

        $randomProduks = Produk::with('fotoProduk')
            ->withCount([
                'likes as likes_count',
                'views as views_count',
            ])
            ->where('id', '!=', $produk->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('guest.pages.single-product', [
            'produk' => $produk,
            'reviews' => $reviews,
            'randomProduks' => $randomProduks,
        ]);
    }

    public function detailUsaha(Request $request, Usaha $usaha)
    {
        $usaha->load('pengerajins', 'produks');
        $previousProduct = null;

        if ($request->has('from_product')) {
            $previousProduct = Produk::where('slug', $request->from_product)->first();
        }

        return view('guest.pages.detail-usaha', [
            'usaha' => $usaha,
            'produks' => $usaha->produks,
            'previousProduct' => $previousProduct,
        ]);
    }

    public function about()
    {
        return view('guest.pages.about');
    }

    public function contact()
    {
        return view('guest.pages.contact');
    }
}
