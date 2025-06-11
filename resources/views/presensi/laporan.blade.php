@extends('layouts.admin.tabler')
<title>Laporan</title>

@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <h2 class="page-title">
                    Laporan
                </h2>
            </div>
            <!-- Page title actions -->
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row">

                <!-- Form Cetak Laporan Presensi -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-center mb-3">Laporan Presensi</h4>
                            <form action="/presensi/cetaklaporan" target="_blank" method="POST" id="frmlaporan">
                                @csrf
                                <div class="row mb-2">
                                    <div class="col-6">
                                        <select name="bulan" id="bulan" class="form-select">
                                            <option value="">Bulan</option>
                                            @for ($i = 1; $i <= 12; $i++)
                                                <option value="{{ $i }}" {{ date("m") == $i ? 'selected' : '' }}>
                                                {{ $namabulan[$i] }}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <select name="tahun" id="tahun" class="form-select">
                                            <option value="">Tahun</option>
                                            @php
                                            $tahunmulai = 2022;
                                            $tahunskrg = date("Y");
                                            @endphp
                                            @for ($tahun = $tahunmulai; $tahun <= $tahunskrg; $tahun++)
                                                <option value="{{ $tahun }}" {{ date("Y") == $tahun ? 'selected' : '' }}>
                                                {{ $tahun }}
                                                </option>
                                                @endfor
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <select name="nik" id="nik" class="form-select">
                                        <option value="">Karyawan</option>
                                        @foreach ($karyawan as $d)
                                        <option value="{{ $d->nik }}">{{ $d->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <button type="submit" name="cetak" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                            <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                        </svg>
                                        Cetak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>


                <!-- Form Cetak Rekap Presensi -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="text-center mb-3">Rekap Presensi</h4>
                            <form action="/presensi/cetakrekap" target="_blank" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <select name="bulan" id="bulan" class="form-select">
                                        <option value="">Bulan</option>
                                        @for ($i = 1; $i <= 12; $i++)
                                            <option value="{{ $i }}" {{ date("m") == $i ? 'selected' : '' }}>
                                            {{ $namabulan[$i] }}
                                            </option>
                                            @endfor
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <select name="tahun" id="tahun" class="form-select">
                                        <option value="">Tahun</option>
                                        @php
                                        $tahunmulai = 2022;
                                        $tahunskrg = date("Y");
                                        @endphp
                                        @for ($tahun = $tahunmulai; $tahun <= $tahunskrg; $tahun++)
                                            <option value="{{ $tahun }}" {{ date("Y") == $tahun ? 'selected' : '' }}>
                                            {{ $tahun }}
                                            </option>
                                            @endfor
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <button type="submit" name="cetak" class="btn btn-primary w-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="icon icon-tabler icons-tabler-outline icon-tabler-printer">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path
                                                d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
                                            <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
                                            <path
                                                d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
                                        </svg>
                                        Cetak
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endsection

    @push ('myscript')
    <script>
        $('#frmlaporan').submit(function(e) {
            e.preventDefault();

            var nik = $('#nik').val();

            if (!nik) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Data Belum Lengkap',
                    text: 'Harap pilih Karyawan terlebih dahulu!',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            this.submit();
        });
    </script>

    @endpush