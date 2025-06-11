<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;


class UserController extends Controller
{
    public function index(Request $request)
    {

        //UNTUK SEARCH
        $query = User::query();
        $query->select('users.*', 'roles.role_name');
        $query->join('roles', 'users.role_id', '=', 'roles.role_id');
        $query->orderBy('name');

        if (!empty($request->name)) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if (!empty($request->role_id)) {
            $query->where('users.role_id', $request->role_id);
        }
        $user = $query->paginate(10);

        $roles = DB::table('roles')->get();
        return view('user.index', compact('user', 'roles'));
    }

    public function store(Request $request)
    {
        $nik            = $request->nik;
        $name           = $request->name;
        $email          = $request->email;
        $role_id        = $request->role_id;
        $password       = Hash::make('123');

        try {
            $data = [
                'nik'       => $nik,
                'name'      => $name,
                'email'     => $email,
                'role_id'   => $role_id,
                'password' => $password
            ];


            // Menyimpan data yang ditambahkan
            $simpan = DB::table('users')->insert($data);

            if ($simpan) {
                return Redirect::back()->with(['success' => 'Data Berhasil Disimpan']);
            }
        } catch (\Exception $e) {
            // Logika ketika tambah data dan data sudah ada
            if ($e->getCode() == 23000) {
                $message = " Data Dengan NIK " . $nik . " Sudah Ada";
            } else {
                $message = $e->getMessage();
            }

            return Redirect::back()->with(['warning' => 'Data Gagal Disimpan. ' . $message]);
        }
    }


    // method untuk ubah data di admin
    public function edit(Request $request)
    {
        $nik = $request->nik;
        $roles = DB::table('roles')->get();
        $users = DB::table('users')->where('nik', $nik)->first();


        return view('user.edit', compact('roles', 'users'));
    }

    public function update(Request $request)
    {
        $nik        = $request->nik;
        $name       = $request->name;
        $email      = $request->email;
        $role_id    = $request->role_id;
        $password   = Hash::make('123'); // Jika ingin ganti password default

        try {
            $data = [
                'nik'       => $nik,
                'name'      => $name,
                'email'     => $email,
                'role_id'   => $role_id,
                'password'  => $password
            ];

            $update = DB::table('users')->where('nik', $nik)->update($data);

            if ($update) {
                return Redirect::back()->with(['success' => 'Data Berhasil Diupdate']);
            } else {
                return Redirect::back()->with(['warning' => 'Tidak ada perubahan data.']);
            }
        } catch (\Exception $e) {
            return Redirect::back()->with(['warning' => 'Data Gagal Diupdate']);
        }
    }


    // hapus data
    public function delete($nik)
    {
        $delete = DB::table('users')->where('nik', $nik)->delete();
        if ($delete) {
            return Redirect::back()->with(['success' => 'Data Berhasil Dihapus']);
        } else {
            return Redirect::back()->with(['warning' => 'Data Gagal Dihapus']);
        }
    }
}
