@php
function selisih($jam_masuk, $jam_keluar)
{
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
@endphp
@foreach ( $presensi as $d )
@php
$foto_in= Storage::url('uploads/absensi/' .$d->foto_in);
$foto_out = Storage::url('uploads/absensi/' . $d->foto_out);
@endphp
<tr>
    <td>{{ $loop->iteration }}</td>
    <td>{{ $d->nik }}</td>
    <td>{{ $d->nama_lengkap }}</td>
    <td>{{ $d->nama_dept }}</td>
    <td>{{ $d->jam_in }}</td>
    <td>
        <img src="{{ url($foto_in) }}" class="avatar" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
    </td>
    <td>{!! $d->jam_out != null? $d->jam_out : '<span class="badge bg-danger">Belum Absen</span>' !!}</td>
    <td>
        @if ($d->jam_out != null)
        <img src="{{ url($foto_out) }}" class="avatar" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
        @else
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-hourglass-low">
            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
            <path d="M6.5 17h11" />
            <path d="M6 20v-2a6 6 0 1 1 12 0v2a1 1 0 0 1 -1 1h-10a1 1 0 0 1 -1 -1z" />
            <path d="M6 4v2a6 6 0 1 0 12 0v-2a1 1 0 0 0 -1 -1h-10a1 1 0 0 0 -1 1z" />
        </svg>
        @endif
    </td>
    <td>
        @if ($d-> jam_in > '08:00')
        @php
        $jamterlambat = selisih('08:00:00', $d->jam_in)
        @endphp
        <span class="badge bg-danger">Terlambat {{ $jamterlambat }}</span>
        @else
        <span class="badge bg-success">Tepat Waktu</span>
        @endif
    </td>
</tr>

@endforeach