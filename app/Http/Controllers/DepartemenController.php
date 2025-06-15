<?php

namespace App\Http\Controllers;

use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;


class DepartemenController extends Controller
{
    // manggil data di database
    public function index(Request $request)
    {
        $nama_dept = $request->nama_dept;
        $query = Departemen::query();
        $query->select('*');
        $departemen = $query->get();
        if (!empty($nama_dept)) {
            $query->where('nama_dept', 'like', '%' . $nama_dept . '%');
        }
        // $departemen = $query->get();
        $departemen = $query->paginate(10);
        // $departemen = DB::table('departemen')->orderBy('kode_dept')->get();
        return view('departemen.index', compact('departemen'));
    }

    // proses tambah data
    public function store(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $nama_dept = $request->nama_dept;

        // Cek apakah nama_dept atau kode_dept sudah ada
        $cek = DB::table('departemen')
            ->where('nama_dept', $nama_dept)
            ->orWhere('kode_dept', $kode_dept)
            ->first();

        if ($cek) {
            return redirect()->back()->with('warning', 'Data dengan nama atau kode "' . $nama_dept . ' / ' . $kode_dept . '" sudah ada!');
        }


        $data = [
            'kode_dept' => $kode_dept,
            'nama_dept' => $nama_dept,
        ];

        $simpan = DB::table('departemen')->insert($data);

        if ($simpan) {
            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } else {
            return redirect()->back()->with('warning', 'Data gagal disimpan!');
        }
    }


    // edit data
    public function edit(Request $request)
    {
        $kode_dept = $request->kode_dept;
        $departemen = DB::table('departemen')->where('kode_dept', $kode_dept)->first();
        return view('departemen.edit', compact('departemen'));
    }


    // update data
    public function update($kode_dept, Request $request)
    {
        $kode_dept_baru = $request->kode_dept_baru;
        $nama_dept = $request->nama_dept;


        $cekkode_dept = DB::table('departemen')->where('kode_dept', $kode_dept_baru)->count();
        if ($cekkode_dept > 0) {
            return Redirect::back()->with(['warning' => 'Kode Departemen Sudah Ada']);
        }

        $data = [
            'kode_dept' => $kode_dept_baru,
            'nama_dept' => $nama_dept
        ];

        $update = DB::table('departemen')->where('kode_dept', $kode_dept)->update($data);
        if ($update) {
            return Redirect::back()->with('success', 'Data Berhasil Di Update');
        } else {
            return Redirect::back()->with('warning', 'Data Gagal Di Update');
        }
    }

    // hapus data
    public function delete($kode_dept)
    {
        // Cek apakah kode_dept digunakan di tabel karyawan
        $dipakai = DB::table('karyawan')->where('kode_dept', $kode_dept)->exists();

        if ($dipakai) {
            return Redirect::back()->with('warning', 'Data gagal dihapus karena masih digunakan di tabel karyawan.');
        }

        // Lanjutkan hapus jika tidak digunakan
        $hapus = DB::table('departemen')->where('kode_dept', $kode_dept)->delete();

        if ($hapus) {
            return Redirect::back()->with('success', 'Data berhasil dihapus.');
        } else {
            return Redirect::back()->with('warning', 'Data gagal dihapus.');
        }
    }
}
