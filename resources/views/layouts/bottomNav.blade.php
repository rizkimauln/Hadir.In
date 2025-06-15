 <!-- App Bottom Menu -->
    <div class="appBottomMenu">
        <a href="/dasboard" class="item {{ request()->is('dasboard') ? 'active' : '' }}">
            <div class="col">
                <ion-icon name="home-outline"></ion-icon>
                <strong>Home</strong>
            </div>
        </a>
        <a href="/presensi/histori" class="item {{ request()->is('presensi/histori') ? 'active' : '' }}">
            <div class="col">
                <ion-icon name="document-text-outline"></ion-icon>
                <strong>History</strong>
            </div>
        </a>
        <a href="/presensi/create" class="item {{ request()->is('presensi/create') ? 'active' : '' }}">
            <div class="col">
                <ion-icon name="finger-print-outline"></ion-icon>
                <strong>Absen</strong>
            </div>
        </a>
        <a href="/presensi/izin" class="item {{ request()->is('presensi/izin') ? 'active' : '' }}">
            <div class="col">
                <ion-icon name="calendar-outline"></ion-icon>
                <strong>Izin</strong>
            </div>
        </a>
        <a href="/editprofile" class="item {{ request()->is('editprofile') ? 'active' : '' }}">
            <div class="col">
                <ion-icon name="people-outline" role="img" class="md hydrated" aria-label="people outline"></ion-icon>
                <strong>Profile</strong>
            </div>
        </a>
    </div>
    <!-- * App Bottom Menu -->
