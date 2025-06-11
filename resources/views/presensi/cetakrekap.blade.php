<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>
        Rekap Karyawan {{ strtoupper($namabulan[$bulan]) }} {{ $tahun }}
    </title>


    <!-- Normalize or reset CSS with your favorite library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css">

    <!-- Load paper.css for happy printing -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.4.1/paper.css">

    <!-- Set page size here: A5, A4 or A3 -->
    <!-- Set also "landscape" if you need -->
    <style>
        @page {
            size: A4 landscape
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
            font-size: 10px;
        }

        .tabelpresensi tr td {
            border: 1px solid #000000;
            padding: 2px;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }

    </style>
</head>

<body class="A4 landscape">
    @php
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

    // Chunking data seperti laporan presensi harian
    $chunks = collect();
    $chunks->push($rekap->slice(0, 18));
    $remaining = $rekap->slice(18);
    if ($remaining->count() > 0) {
    $chunks = $chunks->merge($remaining->chunk(20));
    }
    @endphp

    @foreach($chunks as $index => $chunk)
    <section class="sheet padding-10mm">
        <table style="width: 100%">
            <tr>
                <td align="center">
                    <span id="title">
                        REKAP PRESENSI KARYAWAN<br>
                        PERIODE {{ strtoupper( $namabulan[$bulan]) }} {{ $tahun }}<br>
                    </span>
                </td>
            </tr>
        </table>
        <table class="tabelpresensi">
            <tr>
                <th rowspan="2">NIK</th>
                <th rowspan="2">Nama Karyawan</th>
                <th colspan="31">Tanggal</th>
                <th rowspan="2">H</th>
                <th rowspan="2">T</th>
                <th rowspan="2">I</th>
                <th rowspan="2">S</th>
                <th rowspan="2">A</th>
            </tr>
            <tr>
                @for ($i = 1; $i <= 31; $i++)
                    <th>{{ $i }}</th>
                    @endfor
            </tr>

            @foreach ($chunk as $d)
            <tr>
                <td>{{ $d->nik }}</td>
                <td>{{ $d->nama_lengkap }}</td>
                @php
                $totalhadir = 0;
                $totalizin = 0;
                $totalsakit = 0;
                $totalalpa = 0;
                $totalterlambat = 0;
                @endphp
                @for ($i = 1; $i <= 31; $i++)
                    @php
                    $tglKey='tgl_' . $i;
                    $tanggal=$tahun . '-' . str_pad($bulan, 2, '0' , STR_PAD_LEFT) . '-' . str_pad($i, 2, '0' , STR_PAD_LEFT);
                    $today=date('Y-m-d');
                    $hadirData=$d->$tglKey;
                    $izin = $izinSakit->where('nik', $d->nik)->where('tgl_izin', $tanggal)->first();
                    $kode = '';
                    @endphp

                    @if ($hadirData)
                    @php
                    $jam = explode('-', $hadirData);
                    $jam_masuk = $jam[0];
                    $jam_pulang = $jam[1];
                    $totalhadir++;
                    $terlambat = ($jam_masuk > '08:00');
                    if ($terlambat) $totalterlambat++;
                    $kode = 'H';
                    @endphp
                    <td><strong style="color: {{ $terlambat ? 'red' : 'black' }}">{{ $kode }}</strong></td>

                    @elseif ($izin)
                    @if ($izin->status_pengajuan == 1)
                    @php
                    if ($izin->status == 'i') {
                    $totalizin++;
                    $kode = 'I';
                    } elseif ($izin->status == 's') {
                    $totalsakit++;
                    $kode = 'S';
                    }
                    @endphp
                    <td><strong>{{ $kode }}</strong></td>
                    @elseif ($tanggal < $today)
                        @php
                        $totalalpa++;
                        $kode='A' ;
                        @endphp
                        <td><strong>{{ $kode }}</strong></td>
                        @else
                        <td></td>
                        @endif

                        @elseif ($tanggal < $today)
                            @php
                            $totalalpa++;
                            $kode='A' ;
                            @endphp
                            <td><strong>{{ $kode }}</strong></td>
                            @else
                            <td></td>
                            @endif
                            @endfor
                            <td>{{ $totalhadir }}</td>
                            <td>{{ $totalterlambat }}</td>
                            <td>{{ $totalizin }}</td>
                            <td>{{ $totalsakit }}</td>
                            <td>{{ $totalalpa }}</td>
            </tr>
            @endforeach
        </table>

        @if($loop->last)
        <br>
        <div style="width: 200px; margin-left: auto; text-align: left;">
            <p style="margin: 0; line-height: 1.2;">Bandung, {{ date('d-m-Y') }}</p>
            <p style="margin: 0; line-height: 1.2;">Manager HRD</p>
            <br><br><br>
            <p style="margin: 0;">....................................</p>
        </div>
        @endif
    </section>
    @endforeach

    <script type="text/javascript">
        window.print();
    </script>
</body>

</html>