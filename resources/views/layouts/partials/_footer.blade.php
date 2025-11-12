<footer class="main-footer">
    <!-- To the right -->
    <div class="float-right d-none d-sm-inline">
        <span id="digital-date"></span> | 
        <span id="digital-clock"></span>
    </div>

    <!-- Default to the left -->
    <strong>
        Copyright &copy; {{ now()->year }}
        <a href="#">IT {{ config('app.name', 'Laravel') }}</a>.
    </strong> All rights reserved.
</footer>

<script>
    function updateDateTime() {
        const now = new Date();

        const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
        const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni",
                       "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        const tanggal = `${hari[now.getDay()]}, ${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}`;

        const jam = now.toLocaleTimeString('id-ID', { hour12: false });

        document.getElementById('digital-date').textContent = tanggal;
        document.getElementById('digital-clock').textContent = jam;
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>
