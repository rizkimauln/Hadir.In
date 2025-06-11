@extends('layouts.admin.tabler')
<title>Users</title>

<style>
    .btn-icon {
        width: 25px;
        height: 25px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 5px;
        background-color: #17a2b8;
        /* untuk edit */
        border: none;
        color: white;
        padding: 0;
    }

    .btn-icon.delete-confirm {
        background-color: #dc3545;
        /* untuk delete */
    }

    /* .icon {
        width: 50px;
        height: 50px;
    } */
</style>


@section('content')
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <!-- Page pre-title -->
                <h2 class="page-title">
                    Data Users
                </h2>
            </div>
            <!-- Page title actions -->
        </div>
    </div>
    <div class="page-body">
        <div class="container-xl">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    @if (Session::get('success'))
                                    <div class="alert alert-success">
                                        {{ Session::get('success') }}
                                    </div>
                                    @endif

                                    @if (Session::get('warning'))
                                    <div class="alert alert-warning">
                                        {{ Session::get('warning') }}
                                    </div>
                                    @endif
                                </div>
                            </div>


                            <!-- Untuk Button Tambah (CRUD) -->
                            <!-- //mb-2 di hilangkan ketika nanti menambahkan fitur searc -->
                            <div class="row mt-2, mb-2">
                                <div class="col-12">
                                    <a href="#" class="btn btn-primary" id="btnTambahuser">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-plus">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                            <path d="M12 5l0 14" />
                                            <path d="M5 12l14 0" />
                                        </svg>
                                        Tambah Data</a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <form action="/user" method="GET">
                                        <div class="row">
                                            <!-- Input Nama Karyawan tetap di kiri -->
                                            <div class="col-6">
                                                <div class="form-group">
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="Nama Users">
                                                </div>
                                            </div>

                                            <!-- Dropdown + Tombol di kanan tengah -->
                                            <div class="col-6 d-flex justify-content-end align-items-center">
                                                <form action="" method="GET" class="d-flex align-items-center">
                                                    <div class="form-group me-2">
                                                        <select name="role_id" id="role_id" class="form-select" style="width: 200px;">
                                                            <option value="">Roles</option>
                                                            @foreach ($roles as $d)
                                                            <option {{ request('role_idt') == $d->role_id ? 'selected' : '' }} value="{{ $d->role_id }}">{{ $d->role_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <button type="submit" class="btn btn-primary d-flex align-items-center">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1">
                                                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                            <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
                                                            <path d="M21 21l-6 -6" />
                                                        </svg>
                                                        Cari
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                    </form>
                                </div>
                            </div>


                            <div class="row mt-2">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>NO</th>
                                                <th>NIK</th>
                                                <th>NAMA</th>
                                                <th>EMAIL</th>
                                                <th>ROLE</th>
                                                <th>AKSI</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($user as $d )
                                            <tr>
                                                <td>{{ $loop->iteration + $user->firstItem()-1 }}</td>
                                                <td>{{ $d->nik }}</td>
                                                <td>{{ $d->name }}</td>
                                                <td>{{ $d->email }}</td>
                                                <td>{{ $d->role_name }}</td>
                                                <td>
                                                    <div class="btn-group" style="display: flex; gap: 5px;">
                                                        <!-- Tombol Edit -->
                                                        <a href="#" class="edit btn-icon" nik="{{ $d->nik }}">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
                                                                <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 -2.97l-8.415 8.385v3h3l8.385 -8.415z" />
                                                                <path d="M16 5l3 3" />
                                                            </svg>
                                                        </a>

                                                        <!-- Tombol Delete -->
                                                        <form action="/user/{{ $d->nik }}/delete" method="POST">
                                                            @csrf
                                                            <button type="submit" class="btn-icon delete-confirm">
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="icon" viewBox="0 0 24 24" fill="currentColor">
                                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                    <path
                                                                        d="M20 6a1 1 0 0 1 .117 1.993l-.117 .007h-.081l-.919 11a3 3 0 0 1 -2.824 2.995l-.176 .005h-8c-1.598 0 -2.904 -1.249 -2.992 -2.75l-.005 -.167l-.923 -11.083h-.08a1 1 0 0 1 -.117 -1.993l.117 -.007h16z" />
                                                                    <path
                                                                        d="M14 2a2 2 0 0 1 2 2a1 1 0 0 1 -1.993 .117l-.007 -.117h-4l-.007 .117a1 1 0 0 1 -1.993 -.117a2 2 0 0 1 1.85 -1.995l.15 -.005h4z" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                </div>
                            </div>

                            {{ $user-> links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>


<!-- Pop UP tambah karyawan -->
<div class="modal modal-blur fade" id="modal-inputuser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="/user/store" method="POST" id="frmuser" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                    </svg>
                                </span>
                                <input type="text" value="" id="nik" class="form-control" name="nik" placeholder="NIK">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-1">
                                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0"></path>
                                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
                                    </svg>
                                </span>
                                <input type="text" value="" id="name" class="form-control" name="name" placeholder="Nama Users">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="input-icon mb-3">
                                <span class="input-icon-addon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/user -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-weight">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M12 6m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
                                        <path d="M6.835 9h10.33a1 1 0 0 1 .984 .821l1.637 9a1 1 0 0 1 -.984 1.179h-13.604a1 1 0 0 1 -.984 -1.179l1.637 -9a1 1 0 0 1 .984 -.821z" />
                                    </svg>
                                </span>
                                <input type="text" value="" id="email" class="form-control" name="email" placeholder="Email">
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-12">
                            <select name="role_id" id="role_id" class="form-select">
                                <option value="">Roles</option>
                                @foreach ($roles as $d)
                                <option {{ Request::get('role_id') == $d->role_id ? 'selected' : '' }} value="{{ $d->role_id }}">
                                    {{ $d->role_name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="from-group">
                                <button class="btn btn-primary w-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-send">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                        <path d="M10 14l11 -11" />
                                        <path d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                    </svg>
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- modal edit data karyawan -->
<div class="modal modal-blur fade" id="modal-edituser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Data Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body " id="loadeditform">
            </div>
        </div>
    </div>
</div>

@endsection

@push('myscript')
<script>
    $(function() {
        $("#btnTambahuser").click(function() {
            $("#modal-inputuser").modal("show");
        });

        $('.edit').click(function() {
            var nik = $(this).attr('nik');
            $.ajax({
                type: 'POST',
                url: '/user/edit',
                cache: false,
                data: {
                    _token: '{{ csrf_token() }}',
                    nik: nik
                },
                success: function(respond) {
                    $('#loadeditform').html(respond);
                }
            });
            $('#modal-edituser').modal('show');
        });

        $('.delete-confirm').click(function(e) {
            var form = $(this).closest('form');
            e.preventDefault()
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, hapus saja!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });

        });


        // $('#frmuser').submit(function(e) {
        //     e.preventDefault(); // prevent form submit langsung

        //     var nik = $('#nik').val();
        //     var name = $('#name').val();
        //     var email = $('#email').val();
        //     var role_id = $('#frmuser').find('#role_id').val();

        //     if (!nik || !name || !email || !role_id ) {
        //         Swal.fire({
        //             icon: 'warning',
        //             title: 'Data Belum Lengkap',
        //             text: 'Harap isi semua data dengan lengkap!',
        //             confirmButtonText: 'OK'
        //         });
        //         return false;
        //     }
        //     // Jika semua data terisi, submit form
        //     this.submit();
        // });

    });
</script>
@endpush