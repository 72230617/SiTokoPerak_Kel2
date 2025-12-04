<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Usaha;
use App\Models\User;
use App\Models\KategoriProduk;

class LaporanUsahaController extends Controller
{
    /**
     * Dashboard laporan utama
     * URL: /admin/laporan-usaha
     * Route name: admin.laporan.index
     */
    public function index(Request $request)
    {
        // ---------- 1. DATA FILTER (TAHUN / BULAN / USAHA / KATEGORI / USER) ----------
        $currentYear = now()->year;
        $startYear = $currentYear - 5;

        $tahunList = range($startYear, $currentYear);
        rsort($tahunList);

        $bulanList = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $usahaList = Usaha::all();
        $kategoriList = KategoriProduk::all();
        $userList = User::all();

        // ---------- 2. BASE QUERY UTAMA (TANPA USAHA) ----------
        // Query ini dipakai untuk:
        // - total transaksi
        // - total pendapatan
        // - top produk
        // - top user
        // - top kategori
        //
        // SENGAJA tidak join ke usaha/usaha_produk supaya tidak ngedobel baris.
        $base = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('produk', 'order_items.produk_id', '=', 'produk.id')
            ->leftJoin('kategori_produk', 'produk.kategori_produk_id', '=', 'kategori_produk.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id');

        // ---------- 3. APLIKASI FILTER (kecuali usaha) ----------
        if ($request->filled('tahun')) {
            $base->whereYear('orders.created_at', $request->tahun);
        }

        if ($request->filled('bulan')) {
            $base->whereMonth('orders.created_at', $request->bulan);
        }

        if ($request->filled('kategori_id')) {
            $base->where('kategori_produk.id', $request->kategori_id);
        }

        if ($request->filled('user_id')) {
            $base->where('users.id', $request->user_id);
        }

        // Filter usaha: baru join ke usaha kalau memang difilter
        if ($request->filled('usaha_id')) {
            $base->join('usaha_produk', 'order_items.usaha_produk_id', '=', 'usaha_produk.id')
                ->join('usaha', 'usaha_produk.usaha_id', '=', 'usaha.id')
                ->where('usaha.id', $request->usaha_id);
        }
        // supaya bisa dipakai berkali-kali
        $baseQuery = clone $base;

        // ---------- 4. METRIC ATAS ----------
        // Total transaksi (jumlah order unik)
        $totalTransaksi = (clone $baseQuery)
            ->distinct('orders.id')
            ->count('orders.id');

        // Total pendapatan -> SEKARANG TIDAK KEDOBEL
        $totalPendapatan = (clone $baseQuery)
            ->selectRaw('SUM(order_items.quantity * order_items.price_at_purchase) as total')
            ->value('total') ?? 0;

        // ---------- 5. PENDAPATAN PER USAHA (BUTUH JOIN USAHA) ----------
        // Query khusus untuk chart pendapatan usaha
        $baseUsaha = DB::table('orders')
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('produk', 'order_items.produk_id', '=', 'produk.id')
            ->join('usaha_produk', 'order_items.usaha_produk_id', '=', 'usaha_produk.id') // ⬅️ ini penting
            ->join('usaha', 'usaha_produk.usaha_id', '=', 'usaha.id')
            ->leftJoin('kategori_produk', 'produk.kategori_produk_id', '=', 'kategori_produk.id')
            ->leftJoin('users', 'orders.user_id', '=', 'users.id');


        // filter yang sama
        if ($request->filled('tahun')) {
            $baseUsaha->whereYear('orders.created_at', $request->tahun);
        }
        if ($request->filled('bulan')) {
            $baseUsaha->whereMonth('orders.created_at', $request->bulan);
        }
        if ($request->filled('kategori_id')) {
            $baseUsaha->where('kategori_produk.id', $request->kategori_id);
        }
        if ($request->filled('user_id')) {
            $baseUsaha->where('users.id', $request->user_id);
        }
        if ($request->filled('usaha_id')) {
            $baseUsaha->where('usaha.id', $request->usaha_id);
        }

        $pendapatanPerUsaha = (clone $baseUsaha)
            ->selectRaw('usaha.nama_usaha, SUM(order_items.quantity * order_items.price_at_purchase) as total')
            ->groupBy('usaha.id', 'usaha.nama_usaha')
            ->orderByDesc('total')
            ->limit(3)
            ->get();

        $pendapatanChart = [
            'labels' => $pendapatanPerUsaha->pluck('nama_usaha'),
            'data' => $pendapatanPerUsaha->pluck('total'),
        ];

        // ---------- 6. TOP PRODUK TERLARIS (METRIC + CHART) ----------
        $topProdukRow = (clone $baseQuery)
            ->selectRaw('produk.nama_produk, SUM(order_items.quantity) as total_qty')
            ->groupBy('produk.id', 'produk.nama_produk')
            ->orderByDesc('total_qty')
            ->first();

        $topProduk = $topProdukRow->nama_produk ?? null;

        $produkTerlaris = (clone $baseQuery)
            ->selectRaw('produk.nama_produk, SUM(order_items.quantity) as total_qty')
            ->groupBy('produk.id', 'produk.nama_produk')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->get();

        $produkTerlarisChart = [
            'labels' => $produkTerlaris->pluck('nama_produk'),
            'data' => $produkTerlaris->pluck('total_qty'),
        ];

        // ---------- 7. TOP USER AKTIF ----------
        $userAktifRow = (clone $baseQuery)
            ->selectRaw('users.username, COUNT(DISTINCT orders.id) as total_transaksi')
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_transaksi')
            ->first();

        $userAktif = $userAktifRow->username ?? null;

        $userAktifList = (clone $baseQuery)
            ->selectRaw('users.username, COUNT(DISTINCT orders.id) as total_transaksi')
            ->groupBy('users.id', 'users.username')
            ->orderByDesc('total_transaksi')
            ->limit(3)
            ->get();

        $transaksiUserChart = [
            'labels' => $userAktifList->pluck('username'),
            'data' => $userAktifList->pluck('total_transaksi'),
        ];

        // ---------- 8. TOP 3 KATEGORI PRODUK ----------
        $kategoriTerjual = (clone $baseQuery)
            ->selectRaw('kategori_produk.nama_kategori_produk, SUM(order_items.quantity) as total_qty')
            ->groupBy('kategori_produk.id', 'kategori_produk.nama_kategori_produk')
            ->orderByDesc('total_qty')
            ->limit(3)
            ->get();

        $kategoriChart = [
            'labels' => $kategoriTerjual->pluck('nama_kategori_produk'),
            'data' => $kategoriTerjual->pluck('total_qty'),
        ];

        // ---------- 9. PRODUK FAVORITE & VIEWS ----------
        $produkFavorite = DB::table('produk_likes as pl')
            ->join('produk as p', 'p.id', '=', 'pl.produk_id')
            ->selectRaw('p.nama_produk, COUNT(pl.id) as total_like')
            ->groupBy('p.id', 'p.nama_produk')
            ->orderByDesc('total_like')
            ->limit(3)
            ->get();

        $produkFavoriteChart = [
            'labels' => $produkFavorite->pluck('nama_produk'),
            'data' => $produkFavorite->pluck('total_like'),
        ];

        $produkViews = DB::table('produk_views as pv')
            ->join('produk as p', 'p.id', '=', 'pv.produk_id')
            ->selectRaw('p.nama_produk, COUNT(pv.id) as total_view')
            ->groupBy('p.id', 'p.nama_produk')
            ->orderByDesc('total_view')
            ->limit(3)
            ->get();

        $produkViewChart = [
            'labels' => $produkViews->pluck('nama_produk'),
            'data' => $produkViews->pluck('total_view'),
        ];

        // ---------- 10. RETURN KE VIEW ----------
        return view('admin.laporan_usaha.laporan', compact(
            'tahunList',
            'bulanList',
            'usahaList',
            'kategoriList',
            'userList',
            'totalTransaksi',
            'totalPendapatan',
            'pendapatanChart',
            'produkTerlarisChart',
            'produkFavoriteChart',
            'produkViewChart',
            'transaksiUserChart',
            'kategoriChart',
            'topProduk',
            'userAktif',
        ));
    }

}
