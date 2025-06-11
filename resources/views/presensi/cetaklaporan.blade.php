<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>
        Laporan Karyawan {{ $karyawan->nama_lengkap }}, {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}
    </title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <style>
        @page {
            size: A4
        }

        #title {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
            font-weight: bold;
        }

        .tabeldatakaryawan {
            margin-top: 20px;
        }

        .tabelpresensi {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        .tabelpresensi tr th {
            border: 1px solid #000000;
            padding: 3px;
            background-color: #E8EBEA;
        }

        .tabelpresensi tr td {
            border: 1px solid #000000;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>

<body class="A4">

    @php
    use Carbon\Carbon;

    if (!function_exists('selisih')) {
        function selisih($jam_masuk, $jam_keluar) {
            list($h, $m, $s) = explode(":", $jam_masuk);
            $dtAwal = mktime($h, $m, $s, "1", "1", "1");
            list($h, $m, $s) = explode(":", $jam_keluar);
            $dtAkhir = mktime($h, $m, $s, "1", "1", "1");
            $dtSelisih = $dtAkhir - $dtAwal;
            $totalmenit = $dtSelisih / 60;
            $jam = explode(".", $totalmenit / 60);
            $sisamenit = ($totalmenit / 60) - $jam[0];
            $sisamenit2 = $sisamenit * 60;
            $jml_jam = $jam[0];
            return $jml_jam . ":" . round($sisamenit2);
        }
    }

    $jumlahhari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);
    $today = date('Y-m-d');

    // Siapkan data presensi dan izin
    $dataPresensi = [];
    foreach ($presensi as $p) {
        $dataPresensi[$p->tgl_presensi] = $p;
    }

    $dataIzin = [];
    foreach ($izinSakit as $i) {
        if ($i->status_pengajuan == 1) {
            $dataIzin[$i->tgl_izin] = $i;
        }
    }

    // Buat daftar data presensi harian lengkap (isi semua tanggal)
    $rekapData = [];
    for ($i = 1; $i <= $jumlahhari; $i++) {
        $tanggal = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        $presensiHarian = $dataPresensi[$tanggal] ?? null;
        $izinHarian = $dataIzin[$tanggal] ?? null;

        $status = "-";
        if ($presensiHarian) {
            $status = $presensiHarian->jam_in > '08:00:00' ? "Terlambat " . selisih('08:00:00', $presensiHarian->jam_in) : "Tepat Waktu";
        } elseif ($izinHarian) {
            $status = ucfirst($izinHarian->status);
        } elseif ($tanggal < $today) {
            $status = "A";
        }

        $rekapData[] = [
            'no' => $i,
            'tanggal' => $tanggal,
            'jam_in' => $presensiHarian->jam_in ?? null,
            'jam_out' => $presensiHarian->jam_out ?? null,
            'foto_in' => $presensiHarian->foto_in ?? null,
            'foto_out' => $presensiHarian->foto_out ?? null,
            'status' => $status
        ];
    }

    // Bagi data untuk pagination: 18 baris pertama, 20 baris halaman selanjutnya
    $chunks = collect();
    $chunks->push(collect($rekapData)->slice(0, 16));
    $remaining = collect($rekapData)->slice(18);
    if ($remaining->count() > 0) {
        $chunks = $chunks->merge($remaining->chunk(20));
    }
    @endphp

    @foreach($chunks as $index => $chunk)
    <section class="sheet padding-10mm">
        @if($index == 0)
        <table style="width: 100%">
            <tr>
                <td align="center">
                    <span id="title">
                        LAPORAN PRESENSI KARYAWAN<br>
                        PERIODE {{ strtoupper( $namabulan[$bulan]) }} {{ $tahun }}<br>
                    </span>
                </td>
            </tr>
        </table>

        <table class="tabeldatakaryawan">
            <tr>
                <td rowspan="6">
                    @php
                    $path = Storage::url('uploads/karyawan/'.$karyawan->foto);
                    @endphp
                    <img src="{{ url($path) }}" alt="" style="width: 90px; height: 110px; object-fit: cover; display: block; margin: 0 auto;">
                </td>
            </tr>
            <tr>
                <td style="padding-left: 10px">NIK</td>
                <td>:</td>
                <td>{{ $karyawan->nik }}</td>
            </tr>
            <tr>
                <td style="padding-left: 10px">Nama Karyawan</td>
                <td>:</td>
                <td>{{ $karyawan->nama_lengkap }}</td>
            </tr>
            <tr>
                <td style="padding-left: 10px">Jabatan</td>
                <td>:</td>
                <td>{{ $karyawan->jabatan }}</td>
            </tr>
            <tr>
                <td style="padding-left: 10px">Departemen</td>
                <td>:</td>
                <td>{{ $karyawan->nama_dept }}</td>
            </tr>
            <tr>
                <td style="padding-left: 10px">No HP</td>
                <td>:</td>
                <td>{{ $karyawan->no_hp }}</td>
            </tr>
        </table>
        @endif

        <!-- Tabel Presensi -->
        <table class="tabelpresensi">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Foto</th>
                <th>Jam Pulang</th>
                <th>Foto</th>
                <th>Keterangan</th>
            </tr>
            @foreach($chunk as $d)
            <tr>
                <td>{{ $d['no'] }}</td>
                <td>{{ \Carbon\Carbon::parse($d['tanggal'])->format('d-m-Y') }}</td>
                <td>{{ $d['jam_in'] ?? '-' }}</td>
                <td>
                    @if ($d['foto_in'])
                    <img src="{{ url(Storage::url('uploads/absensi/'.$d['foto_in'])) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                    @else
                    <img src="{{ asset('assets/img/No Foto.jpg') }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                    @endif
                </td>
                <td>
                    @if ($d['jam_out'])
                    {{ $d['jam_out'] }}
                    @elseif($d['jam_in'])
                    Belum Absen
                    @else
                    -
                    @endif
                </td>
                <td>
                    @if ($d['foto_out'])
                    <img src="{{ url(Storage::url('uploads/absensi/'.$d['foto_out'])) }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                    @else
                    <img src="{{ asset('assets/img/No Foto.jpg') }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                    @endif
                </td>
                <td>{{ $d['status'] }}</td>
            </tr>
            @endforeach
        </table>

        @if($loop->last)
        <div style="width: 200px; margin-left: auto; text-align: left; margin-top: 20px;">
            <p style="margin: 0; line-height: 1.2;">Bandung, {{ date('d-m-Y') }}</p>
            <p style="margin: 0; line-height: 1.2;">Manager HRD</p>
            <p style="margin-top: 60px;">....................................</p>
        </div>
        @endif
    </section>
    @endforeach

    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>
