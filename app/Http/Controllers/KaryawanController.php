<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;


class KaryawanController extends Controller
{
    public function index(Request $request)
    {

        //UNTUK SEARCH
        $query = Karyawan::query();
        $query->select('karyawan.*', 'nama_dept');
        $query->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept');
        $query->orderBy('nama_lengkap');

        if (!empty($request->nama_karyawan)) {
            $query->where('nama_lengkap', 'like', '%' . $request->nama_karyawan . '%');
        }
        if (!empty($request->kode_dept)) {
            $query->where('karyawan.kode_dept', $request->kode_dept);
        }
        $karyawan = $query->paginate(10);



        $departemen = DB::table('departemen')->get();
        return view('karyawan.index', compact('karyawan', 'departemen'));
    }

    public function store(Request $request)
    {
        $nik          = $request->nik;
        $nama_lengkap = $request->nama_lengkap;
        $jabatan      = $request->jabatan;
        $no_hp        = $request->no_hp;
        $kode_dept    = $request->kode_dept;
        $password = Hash::make('123');


        if ($request->hasFile('foto')) {
            $foto = $nik . '.' . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = null;
        }

        try {
            $data = [
                'nik'          => $nik,
                'nama_lengkap' => $nama_lengkap,
                'jabatan'      => $jabatan,
                'no_hp'        => $no_hp,
                'kode_dept'    => $kode_dept,
                'foto'         => $foto,
                'password' => $password
            ];
            //menyimpan data yang di tambahkan
            $simpan = DB::table('karyawan')->insert($data);
            if ($simpan) {
                if ($request->hasFile('foto')) {
                    $folderPath = 'public/uploads/karyawan';
                    $foto = $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            // logik ketika tambah data dan data sudah ada
            if ($e->getCode() == 23000) {
                $message = " Data Dengan NIK " . $nik . " Sudah Ada";
            }
            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan' . $message]);
        }
    }


    // method untuk ubah data di admin
    public function edit(Request $request)
    {
        $nik = $request->nik;
        $departemen = DB::table('departemen')->get();
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();


        return view('karyawan.edit', compact('departemen', 'karyawan'));
    }

    public function update($nik, Request $request)
    {
        $nik_baru       = $request->nik_baru;
        $nama_lengkap   = $request->nama_lengkap;
        $jabatan        = $request->jabatan;
        $no_hp          = $request->no_hp;
        $kode_dept      = $request->kode_dept;
        $password       = Hash::make('123');
        $old_foto       = $request->old_foto;
    
        if ($request->hasFile('foto')) {
            $foto = $nik_baru . '.' . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $old_foto;
        }
    
        // Perbaikan: cek nik_baru tapi abaikan data dengan nik lama
        $ceknik = DB::table('karyawan')
                    ->where('nik', $nik_baru)
                    ->where('nik', '!=', $nik)
                    ->count();
    
        if ($ceknik > 0) {
            return Redirect::back()->with(['warning' => 'NIK Sudah Ada']);
        }
    
        try {
            $data = [
                'nik'           => $nik_baru,
                'nama_lengkap'  => $nama_lengkap,
                'jabatan'       => $jabatan,
                'no_hp'         => $no_hp,
                'kode_dept'     => $kode_dept,
                'foto'          => $foto,
                'password'      => $password
            ];
    
            $update = DB::table('karyawan')->where('nik', $nik)->update($data);
    
            if ($update) {
                if ($request->hasFile('foto')) {
                    $folderPath = 'public/uploads/karyawan/';
                    $folderPathOld = 'public/uploads/karyawan/' . $old_foto;
                    Storage::delete($folderPathOld);
                    $request->file('foto')->storeAs($folderPath, $foto);
                }
                return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
            }
    
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }

    // hapus data
    public function delete($nik)
    {
        $delete = DB::table('karyawan')->where('nik', $nik)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
