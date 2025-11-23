<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Usaha;
use App\Models\FotoProduk;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function index()
    {
        $produks = Produk::with("kategoriProduk", "fotoProduk")->get();
        return view("guest.pages.index", [
            "produks" => $produks,
        ]);
    }

    public function productsByCategory($slug)
    {
        $kategori = KategoriProduk::where("slug", $slug)->firstOrFail();
        $produks = Produk::where("kategori_produk_id", $kategori->id)->get();

        return view("guest.pages.products", [
            "kategori" => $kategori,
            "produks" => $produks,
        ]);
    }

    public function katalog(Request $request)
    {
        // Memuat relasi yang dibutuhkan untuk efisiensi
        $query = Produk::with("kategoriProduk", "fotoProduk");

        // -- LOGIKA PENCARIAN (SEARCH) --
        if ($request->filled("search")) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where("nama_produk", "like", "%" . $searchTerm . "%")
                    ->orWhere("deskripsi", "like", "%" . $searchTerm . "%")
                    ->orWhereHas("kategoriProduk", function (
                        $kategoriQuery,
                    ) use ($searchTerm) {
                        $kategoriQuery->where(
                            "nama_kategori_produk",
                            "like",
                            "%" . $searchTerm . "%",
                        );
                    });
            });
        }

        // -- LOGIKA FILTER KATEGORI --
        if ($request->filled("kategori")) {
            $query->whereHas("kategoriProduk", function ($q) use ($request) {
                $q->where("slug", $request->kategori);
            });
        }

        // -- LOGIKA FILTER USAHA (Toko) --
        if ($request->filled("usaha")) {
            $query->whereHas("usahaProduk.usaha", function ($q) use ($request) {
                $q->where("id", $request->usaha)->where(
                    "status_usaha",
                    "aktif",
                ); // hanya toko aktif
            });
        }

        // -- LOGIKA FILTER HARGA --
        if ($request->filled("min_harga")) {
            $query->where("harga", ">=", $request->min_harga);
        }
        if ($request->filled("max_harga")) {
            $query->where("harga", "<=", $request->max_harga);
        }

        // -- LOGIKA PENGURUTAN (SORT) --
        $urutkan = $request->input("urutkan", "terbaru");
        switch ($urutkan) {
            case "harga-rendah":
                $query->orderBy("harga", "asc");
                break;
            case "harga-tinggi":
                $query->orderBy("harga", "desc");
                break;
            default:
                $query->latest();
                break;
        }

        $produks = $query->paginate(12)->withQueryString();

        $kategoris = KategoriProduk::all();

        return view("guest.pages.katalog", [
            "produks" => $produks,
            "kategoris" => $kategoris,
        ]);
    }

    public function singleProduct($slug)
    {
        $produk = Produk::where("slug", $slug)->firstOrFail();
        $reviews = $produk->reviews()->latest()->with("user")->get();

        $randomProduks = Produk::with("fotoProduk")
            ->where("id", "!=", $produk->id) // Exclude the current product
            ->inRandomOrder()
            ->limit(4) // Get 4 random products
            ->get();
        return view("guest.pages.single-product", [
            "produk" => $produk,
            "reviews" => $reviews,
            "randomProduks" => $randomProduks,
        ]);
    }

    public function detailUsaha(Request $request, Usaha $usaha)
    {
        $usaha->load("pengerajins", "produks");
        $previousProduct = null;

        if ($request->has("from_product")) {
            $previousProduct = Produk::where(
                "slug",
                $request->from_product,
            )->first();
        }

        return view("guest.pages.detail-usaha", [
            "usaha" => $usaha,
            "produks" => $usaha->produks,
            "previousProduct" => $previousProduct,
        ]);
    }

    public function about()
    {
        return view("guest.pages.about");
    }
    public function contact()
    {
        return view("guest.pages.contact");
    }
}
