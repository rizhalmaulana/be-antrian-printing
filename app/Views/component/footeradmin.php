
</div>
</main>
</div>

<!--   Core JS Files   -->
<script src="<?= base_url('js/core/popper.min.js') ?>"></script>
<script src="<?= base_url('js/core/bootstrap.min.js'); ?>"></script>
<script src="<?= base_url('js/plugins/perfect-scrollbar.min.js'); ?>"></script>
<script src="<?= base_url('js/plugins/smooth-scrollbar.min.js'); ?>"></script>
<script src="<?= base_url('js/plugins/chartjs.min.js'); ?>"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
<script src="<?=base_url('assets/js/argon-dashboard.min.js?v=2.0.4'); ?>"></script>
</body>

</html>

<script>
var ctx1 = document.getElementById("chart-line").getContext("2d");

var gradientStroke1 = ctx1.createLinearGradient(0, 230, 0, 50);

gradientStroke1.addColorStop(1, 'rgba(94, 114, 228, 0.2)');
gradientStroke1.addColorStop(0.2, 'rgba(94, 114, 228, 0.0)');
gradientStroke1.addColorStop(0, 'rgba(94, 114, 228, 0)');
new Chart(ctx1, {
    type: "line",
    data: {
        labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
        datasets: [{
            label: "Mobile apps",
            tension: 0.4,
            borderWidth: 0,
            pointRadius: 0,
            borderColor: "#5e72e4",
            backgroundColor: gradientStroke1,
            borderWidth: 3,
            fill: true,
            data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
            maxBarThickness: 6

        }],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false,
            }
        },
        interaction: {
            intersect: false,
            mode: 'index',
        },
        scales: {
            y: {
                grid: {
                    drawBorder: false,
                    display: true,
                    drawOnChartArea: true,
                    drawTicks: false,
                    borderDash: [5, 5]
                },
                ticks: {
                    display: true,
                    padding: 10,
                    color: '#fbfbfb',
                    font: {
                        size: 11,
                        family: "Open Sans",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
            x: {
                grid: {
                    drawBorder: false,
                    display: false,
                    drawOnChartArea: false,
                    drawTicks: false,
                    borderDash: [5, 5]
                },
                ticks: {
                    display: true,
                    color: '#ccc',
                    padding: 20,
                    font: {
                        size: 11,
                        family: "Open Sans",
                        style: 'normal',
                        lineHeight: 2
                    },
                }
            },
        },
    },
});
</script>
<script>
    $(document).ready(function(){
        var idAntrian = $("#id_antrian").val();

        $("#verifikasi-click").click(function(){
            Swal.fire({
                    title: 'Informasi!',
                    text: "Apa anda yakin ingin verifikasi antrian ini?",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Batal',
                    confirmButtonText: 'Ya, Verifikasi!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url('verifikasi-antrian/'); ?>" + idAntrian,
                            type: "POST",
                            dataType: "JSON",
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire(
                                        'Informasi!',
                                        'Antrian sudah berhasil diverifikasi.',
                                        'success'
                                    )
                                } else {
                                    Swal.fire(
                                        'Informasi!',
                                        'Antrian gagal diverifikasi.',
                                        'warning'
                                    )
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Informasi!',
                                    'Antrian gagal diverifikasi.',
                                    'warning'
                                )
                            }
                        });
                    }
                });
        });
    });

    $(document).ready(function(){
        var idAntrian = $("#id_antrian").val();
        
        $("#reminder-click").click(function(){
            Swal.fire({
                    title: 'Informasi!',
                    text: "Apa anda yakin ingin mengingatkan antrian ini ke user?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Kembali',
                    confirmButtonText: 'Ya, Ingatkan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url('cancel-antrian/'); ?>" + idAntrian,
                            type: "PUT",
                            dataType: "JSON",
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire(
                                        'Antrian Di Cancel!',
                                        'Antrian sudah berhasil dicancel.',
                                        'success'
                                    )
                                } else {
                                    Swal.fire(
                                        'Informasi!',
                                        'Antrian gagal dicancel.',
                                        'warning'
                                    )
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Informasi!',
                                    'Antrian gagal dicancel.',
                                    'warning'
                                )
                            }
                        });
                    }
                });
        });
    });

    $(document).ready(function(){
        var idAntrian = $("#id_antrian").val();
        
        $("#cancel-click").click(function(){
            Swal.fire({
                    title: 'Informasi!',
                    text: "Apa anda yakin ingin batalkan antrian ini?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Kembali',
                    confirmButtonText: 'Ya, Batalkan!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "<?= base_url('cancel-antrian/'); ?>" + idAntrian,
                            type: "PUT",
                            dataType: "JSON",
                            success: function(response) {
                                if (response.status) {
                                    Swal.fire(
                                        'Antrian Di Cancel!',
                                        'Antrian sudah berhasil dicancel.',
                                        'success'
                                    )
                                } else {
                                    Swal.fire(
                                        'Informasi!',
                                        'Antrian gagal dicancel.',
                                        'warning'
                                    )
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Informasi!',
                                    'Antrian gagal dicancel.',
                                    'warning'
                                )
                            }
                        });
                    }
                });
        });
    });
</script>
<script>
var win = navigator.platform.indexOf('Win') > -1;
if (win && document.querySelector('#sidenav-scrollbar')) {
    var options = {
        damping: '0.5'
    }
    Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
}
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>