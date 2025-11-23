<?php

namespace App\Http\Controllers\Admin;
use App\Models\Pengerajin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PengerajinController extends Controller
{
    public function index()
    {
        $dataPengerajin = Pengerajin::all(); // atau bisa juga pakai paginate()

        return view("admin.pengerajin.index-pengerajin", [
            "pengerajins" => $dataPengerajin,
        ]);
    }

    public function create()
    {
        return view("admin.pengerajin.create-pengerajin");
    }

    public function edit($id)
    {
        $pengerajin = Pengerajin::findOrFail($id);
        return view("admin.pengerajin.edit-pengerajin", compact("pengerajin"));
    }

    public function store(Request $request)
    {
        $request->validate([
            "kode_pengerajin" => "required|string|max:255",
            "nama_pengerajin" => "required|string|max:255",
            "jk_pengerajin" => "required|string|max:10",
            "usia_pengerajin" => "required|integer",
            "telp_pengerajin" => "required|string|max:15",
            "email_pengerajin" => "required|email|max:255",
            "alamat_pengerajin" => "required|string|max:255",
        ]);

        // Simpan data ke database
        Pengerajin::create($request->all());

        return redirect()
            ->route("admin.pengerajin-index")
            ->with("success", "Pengerajin berhasil ditambahkan.");
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            "kode_pengerajin" => "required|string|max:255",
            "nama_pengerajin" => "required|string|max:255",
            "jk_pengerajin" => "required|string|max:10",
            "usia_pengerajin" => "required|integer",
            "telp_pengerajin" => "required|string|max:15",
            "email_pengerajin" => "required|email|max:255",
            "alamat_pengerajin" => "required|string|max:255",
        ]);

        Pengerajin::where("id", $id)->update($data);

        return redirect()
            ->route("admin.pengerajin-index")
            ->with("success", "Data Pengerajin berhasil diupdate.");
    }

    public function destroy($id)
    {
        $pengerajin = Pengerajin::findOrFail($id);
        $pengerajin->delete();

        return redirect()
            ->route("admin.pengerajin-index")
            ->with("success", "Pengerajin berhasil dihapus.");
    }
}
