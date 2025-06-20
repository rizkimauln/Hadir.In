<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class PresensiController extends Controller
{
    public function create()
    {
        $hariini = date("Y-m-d");
        $nik = Auth::guard('karyawan')->user()->nik;
        $cek = DB::table('presensi')->where('tgl_presensi', $hariini)->where('nik', $nik)->count();
        return view('presensi.create', compact('cek'));
    }

    public function store(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_presensi = date("Y-m-d");
        $jam = date("H:i:s");
        $latitudekantor = -6.931259213116736;
        $longitudekantor = 107.71826067763392;
        $lokasi = $request->lokasi;
        $lokasiuser = explode(",", $lokasi);
        $latitudeuser = -6.931259213116736;
        $longitudeuser = 107.71826067763392;
        // $latitudeuser = $lokasiuser[0];
        // $longitudeuser = $lokasiuser[1];

        $jarak = $this->distance($latitudekantor, $longitudekantor, $latitudeuser, $longitudeuser);
        $radius = round($jarak["meters"]);

        $cek = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->count();
        if ($cek > 0) {
            $ket = 'out';
        } else {
            $ket = 'in';
        }
        $image = $request->image;
        $folderPath = "public/uploads/absensi/";
        $formatName = $nik . "-" . $tgl_presensi . "-" . $ket;
        $image_parts = explode(";base64", $image);
        $image_base64 = base64_decode($image_parts[1]);
        $fileName = $formatName . ".png";
        $file = $folderPath . $fileName;



        // Mengecek Jarak Untuk absen
        if ($radius > 100) {
            echo "error|Maaf Anda Diluar Radius, Jarak Anda " . $radius . " Meter Dari Kantor";
        } else {
            if ($cek > 0) {
                $data_pulang = [
                    'jam_out' => $jam,
                    'foto_out' => $fileName,
                    'lokasi_out' => $lokasi,
                ];
                $update = DB::table('presensi')->where('tgl_presensi', $tgl_presensi)->where('nik', $nik)->update($data_pulang);
                if ($update) {
                    echo "success|Terimakasih, Hati-Hati di Jalan|out";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Gagal Absen, Segera Hubungi IT|out";
                }
            } else {
                $data = [
                    'nik' => $nik,
                    'tgl_presensi' => $tgl_presensi,
                    'jam_in' => $jam,
                    'foto_in' => $fileName,
                    'lokasi_in' => $lokasi,
                ];
                $simpan = DB::table('presensi')->insert($data);
                if ($simpan) {
                    echo "success|Terimakasih, Selamat Bekerja|in";
                    Storage::put($file, $image_base64);
                } else {
                    echo "error|Gagal Absen, Segera Hubungi IT|in";
                }
            }
        }
    }

    //Menghitung Jarak Kordinat
    function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $miles = (sin(deg2rad($lat1)) * sin(deg2rad($lat2))) + (cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('meters');
    }

    // method edit prodile
    public function editprofile()
    {

        $nik = Auth::guard('karyawan')->user()->nik;
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();
        return view('presensi.editprofile', compact('karyawan'));
    }

    // update profile
    public function updateprofile(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $nama_lengkap = $request->nama_lengkap;
        $no_hp = $request->no_hp;
        $password = Hash::make($request->password);
        $karyawan = DB::table('karyawan')->where('nik', $nik)->first();

        // Update Foto
        if ($request->hasFile('foto')) {
            $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
        } else {
            $foto = $karyawan->foto;
        }

        // Jika password kosong maka tidak diupdate
        if (empty($request->password)) {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'foto' => $foto
            ];
        } else {
            $data = [
                'nama_lengkap' => $nama_lengkap,
                'no_hp' => $no_hp,
                'password' => $password,
                'foto' => $foto
            ];
        }

        // Update database
        $update = DB::table('karyawan')->where('nik', $nik)->update($data);

        if ($update) {
            if ($request->hasFile('foto')) {
                $folderPath = "public/uploads/karyawan/";

                //  Hapus foto lama jika ada dan beda dengan foto default
                if ($karyawan->foto && Storage::exists($folderPath . $karyawan->foto)) {
                    Storage::delete($folderPath . $karyawan->foto);
                }

                // Simpan foto baru
                $request->file('foto')->storeAs($folderPath, $foto);
            }

            return Redirect::back()->with(['success' => 'Data Berhasil Dirubah']);
        } else {
            return Redirect::back()->with(['error' => 'Data Gagal Dirubah']);
        }
    }

    // public function updateprofile(request $request)
    // {
    //     $nik = Auth::guard('karyawan')->user()->nik;
    //     $nama_lengkap = $request->nama_lengkap;
    //     $no_hp = $request->no_hp;
    //     $password = Hash::make($request->password);
    //     $karyawan = DB::table('karyawan')->where('nik', $nik)->first();


    //     //Update Foto
    //     if ($request->hasFile('foto')) {
    //         $foto = $nik . "." . $request->file('foto')->getClientOriginalExtension();
    //     } else {
    //         $foto = $karyawan->foto;
    //     }

    //     //jika pass kosong maka tidak di update pass nya
    //     if (empty($request->password)) {
    //         $data = [
    //             'nama_lengkap' => $nama_lengkap,
    //             'no_hp' => $no_hp,
    //             'foto' => $foto
    //         ];
    //     } else {
    //         $data = [
    //             'nama_lengkap' => $nama_lengkap,
    //             'no_hp' => $no_hp,
    //             'password' => $password,
    //             'foto' => $foto
    //         ];
    //     }

    //     // logika upload Data & foto
    //     $update = DB::table('karyawan')->where('nik', $nik)->update($data);
    //     if ($update) {
    //         if ($request->hasFile('foto')) {
    //             $folderPath = "public/uploads/karyawan/";
    //             $request->file('foto')->storeAs($folderPath, $foto);
    //         }
    //         return Redirect::back()->with(['success' => 'Data Berhasil Dirubah']);
    //     } else {
    //         return Redirect::back()->with(['error' => 'Data Gagal Dirubah']);
    //     }
    // }



    // Distory 
    public function histori()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.histori', compact('namabulan'));
    }

    public function gethistori(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $nik = Auth::guard('karyawan')->user()->nik;

        $histori = DB::table('presensi')
            ->whereRaw('MONTH(tgl_presensi) = ' . $bulan)
            ->whereRaw('YEAR(tgl_presensi) = ' . $tahun)
            ->where('nik', $nik)
            ->orderBy('tgl_presensi')
            ->get();

        return view('presensi.gethistori', compact('histori'));
    }



    // mengambil data izin dari database
    public function izin()
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $dataizin = DB::table('pengajuan_izin')->where('nik', $nik)->get();
        return view('presensi.izin', compact('dataizin'));
    }

    public function buatizin()
    {
        return view('presensi.buatizin');
    }

    // Menyimpan izin ke database
    public function storeIzin(Request $request)
    {
        $nik = Auth::guard('karyawan')->user()->nik;
        $tgl_izin = $request->tgl_izin;
        $status = $request->status;
        $keterangan = $request->keterangan;

        $data = [
            'nik' => $nik,
            'tgl_izin' => $tgl_izin,
            'status' => $status,
            'keterangan' => $keterangan
        ];

        $simpan = DB::table('pengajuan_izin')->insert($data);
        if ($simpan) {
            return redirect('/presensi/izin')->with(['success' => 'Data Berhasil Disimpan']);
        } else {
            return redirect('/presensi/izin')->with(['error' => 'Data Gagal Disimpan']);
        }
    }

    // MONITORING PRESENSI BY ADMIN
    public function monitoring()
    {
        return view('presensi.monitoring');
    }

    public function getpresensi(Request $request)
    {
        $tanggal = $request->tanggal;
        $presensi = DB::table('presensi')
            ->select('presensi.*', 'nama_lengkap', 'nama_dept')
            ->join('karyawan', 'presensi.nik', 'karyawan.nik')
            ->join('departemen', 'karyawan.kode_dept', 'departemen.kode_dept')
            ->where('tgl_presensi', $tanggal)
            ->get();

        return view('presensi.getpresensi', compact('presensi'));
    }


    // Laporan presensi by admin
    public function laporan()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->orderBy('nama_lengkap')->get();
        return view('presensi.laporan', compact('namabulan', 'karyawan'));
    }

    // public function cetaklaporan(Request $request)
    // {
    //     $nik = $request->nik;
    //     $bulan = $request->bulan;
    //     $tahun = $request->tahun;
    //     $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
    //     $karyawan = DB::table('karyawan')->where('nik', $nik)
    //         ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
    //         ->first();

    //     $presensi = DB::table('presensi')
    //         ->where('nik', $nik)
    //         ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
    //         ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
    //         ->orderBy('tgl_presensi')
    //         ->get();
    //     return view('presensi.cetaklaporan', compact('nik', 'bulan', 'tahun', 'namabulan', 'karyawan', 'presensi'));
    // }


    public function cetaklaporan(Request $request)
    {
        $nik = $request->nik;
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        $karyawan = DB::table('karyawan')->where('nik', $nik)
            ->join('departemen', 'karyawan.kode_dept', '=', 'departemen.kode_dept')
            ->first();

        $presensi = DB::table('presensi')
            ->where('nik', $nik)
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->orderBy('tgl_presensi')
            ->get();


        $izinSakit = DB::table('pengajuan_izin')
            ->select('nik', 'tgl_izin', 'status', 'status_pengajuan')
            ->whereMonth('tgl_izin', $bulan)
            ->whereYear('tgl_izin', $tahun)
            ->get();

        $url = "https://dayoffapi.vercel.app/api?month=$bulan&year=$tahun";
        $response = Http::get($url);

        // Jika ingin semua termasuk cuti bersama:
        $liburNasional = collect($response->json())->pluck('tanggal')->toArray();

        // Jika ingin pisahkan cuti bersama dan hari libur nasional:
        $liburMerah = collect($response->json())
            ->filter(fn($item) => $item['is_cuti'] === false)
            ->pluck('tanggal')
            ->map(fn($tgl) => date('Y-m-d', strtotime($tgl)))
            ->toArray();


        $cutiBersama = collect($response->json())
            ->filter(fn($item) => $item['is_cuti'] === true)
            ->pluck('tanggal')
            ->toArray();

        return view('presensi.cetaklaporan', compact('nik', 'bulan', 'tahun', 'namabulan', 'karyawan', 'presensi', 'izinSakit', 'liburNasional', 'liburMerah', 'cutiBersama'));
    }


    // Rekap By Admin
    public function rekap()
    {
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        return view('presensi.rekap', compact('namabulan'));
    }

    public function cetakrekap(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $namabulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // Ambil data presensi bulanan
        $rekap = DB::table('presensi')
            ->selectRaw('presensi.nik,nama_lengkap,
            MAX(IF(DAY(tgl_presensi) = 1,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_1,
            MAX(IF(DAY(tgl_presensi) = 2,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_2,
            MAX(IF(DAY(tgl_presensi) = 3,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_3,
            MAX(IF(DAY(tgl_presensi) = 4,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_4,
            MAX(IF(DAY(tgl_presensi) = 5,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_5,
            MAX(IF(DAY(tgl_presensi) = 6,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_6,
            MAX(IF(DAY(tgl_presensi) = 7,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_7,
            MAX(IF(DAY(tgl_presensi) = 8,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_8,
            MAX(IF(DAY(tgl_presensi) = 9,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_9,
            MAX(IF(DAY(tgl_presensi) = 10,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_10,
            MAX(IF(DAY(tgl_presensi) = 11,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_11,
            MAX(IF(DAY(tgl_presensi) = 12,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_12,
            MAX(IF(DAY(tgl_presensi) = 13,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_13,
            MAX(IF(DAY(tgl_presensi) = 14,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_14,
            MAX(IF(DAY(tgl_presensi) = 15,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_15,
            MAX(IF(DAY(tgl_presensi) = 16,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_16,
            MAX(IF(DAY(tgl_presensi) = 17,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_17,
            MAX(IF(DAY(tgl_presensi) = 18,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_18,
            MAX(IF(DAY(tgl_presensi) = 19,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_19,
            MAX(IF(DAY(tgl_presensi) = 20,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_20,
            MAX(IF(DAY(tgl_presensi) = 21,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_21,
            MAX(IF(DAY(tgl_presensi) = 22,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_22,
            MAX(IF(DAY(tgl_presensi) = 23,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_23,
            MAX(IF(DAY(tgl_presensi) = 24,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_24,
            MAX(IF(DAY(tgl_presensi) = 25,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_25,
            MAX(IF(DAY(tgl_presensi) = 26,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_26,
            MAX(IF(DAY(tgl_presensi) = 27,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_27,
            MAX(IF(DAY(tgl_presensi) = 28,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_28,
            MAX(IF(DAY(tgl_presensi) = 29,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_29,
            MAX(IF(DAY(tgl_presensi) = 30,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_30,
            MAX(IF(DAY(tgl_presensi) = 31,CONCAT(jam_in,"-",IFNULL(jam_out,"00:00:00")),"")) as tgl_31')
            ->join('karyawan', 'presensi.nik', '=', 'karyawan.nik')
            ->whereRaw('MONTH(tgl_presensi)="' . $bulan . '"')
            ->whereRaw('YEAR(tgl_presensi)="' . $tahun . '"')
            ->groupByRaw('presensi.nik,nama_lengkap')
            ->get();

        // Ambil data izin dan sakit
        $izinSakit = DB::table('pengajuan_izin')
            ->select('nik', 'tgl_izin', 'status', 'status_pengajuan')
            ->whereMonth('tgl_izin', $bulan)
            ->whereYear('tgl_izin', $tahun)
            ->get();

        // Ambil hari libur nasional dari API
        $url = "https://dayoffapi.vercel.app/api?month=$bulan&year=$tahun";
        $response = Http::get($url);

        // Jika ingin semua termasuk cuti bersama:
        $liburNasional = collect($response->json())->pluck('tanggal')->toArray();

        // Jika ingin pisahkan cuti bersama dan hari libur nasional:
        $liburMerah = collect($response->json())
            ->filter(fn($item) => $item['is_cuti'] === false)
            ->pluck('tanggal')
            ->map(fn($tgl) => date('Y-m-d', strtotime($tgl)))
            ->toArray();


        $cutiBersama = collect($response->json())
            ->filter(fn($item) => $item['is_cuti'] === true)
            ->pluck('tanggal')
            ->toArray();

        return view('presensi.cetakrekap', compact(
            'bulan',
            'tahun',
            'namabulan',
            'rekap',
            'izinSakit',
            'liburNasional',
            'liburMerah',
            'cutiBersama'
        ));
    }


    public function izinsakit()
    {
        $izinsakit = DB::table('pengajuan_izin')
            ->join('karyawan', 'pengajuan_izin.nik', '=', 'karyawan.nik')
            ->orderBy('tgl_izin', 'desc')
            ->paginate(10);
        return view('presensi.izinsakit', compact('izinsakit'));
    }

    public function terimaizinsakit(Request $request)
    {
        $status_pengajuan = $request->status_pengajuan;
        $id_izinsakit_form = $request->id_izinsakit_form;

        $update = DB::table('pengajuan_izin')
            ->where('id', $id_izinsakit_form)
            ->update([
                'status_pengajuan' => $status_pengajuan
            ]);

        if ($update) {
            return Redirect::back()->with('success', 'Data Berhasil Di Update');
        } else {
            return Redirect::back()->with('warning', 'Data Gagal Di Update');
        }
    }

    public function batalkanizinsakit($id)
    {
        $update = DB::table('pengajuan_izin')->where('id', $id)->update([
            'status_pengajuan' => 0
        ]);

        if ($update) {
            return Redirect::back()->with('success', 'Data Berhasil Di Update');
        } else {
            return Redirect::back()->with('warning', 'Data Gagal Di Update');
        }
    }

    public function cekpengajuanizin(Request $request)
    {
        $tgl_izin = $request->tgl_izin;
        $nik = Auth::guard('karyawan')->user()->nik;

        $cek = DB::table('pengajuan_izin')
            ->where('nik', $nik)
            ->where('tgl_izin', $tgl_izin)
            ->count();

        return $cek;
    }
}
