<!-- jQuery -->
<script src="{{ asset('adminlte3/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 4 -->
<script src="{{ asset('adminlte3/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<!-- DataTables  & Plugins -->
<script src="{{ asset('adminlte3/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/pdfmake/pdfmake.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/pdfmake/vfs_fonts.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.print.min.js') }}"></script>
<script src="{{ asset('adminlte3/plugins/datatables-buttons/js/buttons.colVis.min.js') }}"></script>
<!-- AdminLTE App -->
<script src="{{ asset('adminlte3/dist/js/adminlte.min.js') }}"></script>
<!-- Page specific script -->
<!-- Donat Chart -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chart.js"></script> 
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0-rc"></script>  

<script>
    $(function () {
        @foreach ($revisiall as $key => $revisi)
        $('#example2-{{ $key }}').DataTable({
            "paging": true,
            "lengthChange": false,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true
        });
        @endforeach
    });
</script>

<script>
    $(function () {
        //Enable check and uncheck all functionality
        $('#checkAll').click(function () {
            var clicks = $(this).data('clicks');
            if (clicks) {
                //Uncheck all checkboxes
                $('input[name="document_ids[]"]').prop('checked', false);
                $(this).find('i').removeClass('fa-check-square').addClass('fa-square');
            } else {
                //Check first 10 checkboxes
                $('input[name="document_ids[]"]:lt(10)').prop('checked', true);
                $(this).find('i').removeClass('fa-square').addClass('fa-check-square');
            }
            $(this).data('clicks', !clicks);
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
        function showDocumentSummary(informasidokumenencoded, ringkasan, documentId) {
            var documentData = JSON.parse(ringkasan);
            var documentInfo = JSON.parse(informasidokumenencoded);

            // Construct document information section
            var documentInfoHTML = `
                <div style="text-align: center;">
                    <p style="font-weight: bold; font-size: 24px;">INFORMASI MEMO</p>
                </div>
                <div style="padding: 20px; font-size: 16px;">
                    <p style="font-weight: bold;">Kepada Yth,</p>
                    <ol>
            `;

            // Construct list of PICs
            for (var i = 0; i < documentData.length; i++) {
                var pic = documentData[i].pic;
                documentInfoHTML += `<li>${pic}</li>`;
            }

            // Add closing tags for list
            documentInfoHTML += `
                    </ol>
                    <hr style="margin-top: 20px;">
                    <div style="padding: 20px;">
                        <p style="font-size: 16px;">Kami sampaikan informasi dokumen berikut:</p>
                        <table style="width: 100%; margin-bottom: 20px;">
                            <tr>
                                <td style="font-weight: bold; width: 30%">Jenis Dokumen:</td>
                                <td>${documentInfo['category']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Nama Memo:</td>
                                <td>${documentInfo['documentname']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Nomor Memo:</td>
                                <td>${documentInfo['documentnumber']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Jenis Memo:</td>
                                <td>${documentInfo['memokind']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Asal Memo:</td>
                                <td>${documentInfo['memoorigin']}</td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold">Status Dokumen:</td>
                                <td>${documentInfo['documentstatus']}</td>
                            </tr>
                        </table>
                    </div>
                `;

            // Construct table header
            var tableHeaderHTML = `
                <thead>
                    <tr>
                        <th style="border: 1px solid #000; padding: 8px;">Pic</th>
                        <th style="border: 1px solid #000; padding: 8px;">Nama Penulis</th>
                        <th style="border: 1px solid #000; padding: 8px;">Email</th>
                        <th style="border: 1px solid #000; padding: 8px;">Status Feedback</th>
                        <th style="border: 1px solid #000; padding: 8px;">Kategori</th>
                        <th style="border: 1px solid #000; padding: 8px;">Sudah Dibaca</th>
                        <th style="border: 1px solid #000; padding: 8px;">Hasil Review</th>
                    </tr>
                </thead>`;

            // Construct table body
            var tableBodyHTML = '<tbody>';
            for (var i = 0; i < documentData.length; i++) {
                var pic = documentData[i].pic;
                var level = documentData[i].level;
                var userInformation = documentData[i].userinformations;

                // Filter out specific conditions
                if ((pic !== "MTPR" || level !== "pembukadokumen") && (pic !== "Product Engineering" || level !== "signature")) {
                    // Construct table row
                    var tableRowHTML = `
                        <tr>
                            <td style="border: 1px solid #000; padding: 8px;">${pic}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['nama penulis']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['email']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['conditionoffile']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['conditionoffile2']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['sudahdibaca']}</td>
                            <td style="border: 1px solid #000; padding: 8px;">${userInformation['hasilreview']}</td>
                        </tr>`;
                    
                    tableBodyHTML += tableRowHTML;
                }
            }
            tableBodyHTML += '</tbody>';

            // Construct the complete HTML content
            var htmlContent = `
                <div style="padding: 20px;">
                    ${documentInfoHTML}
                    <div style="overflow-x: auto;">
                        <table style="border-collapse: collapse; width: 100%; font-size: 16px;">
                            <caption style="caption-side: top; text-align: center; font-weight: bold; font-size: 20px; margin-bottom: 10px;">Feedback</caption>
                            ${tableHeaderHTML}
                            ${tableBodyHTML}
                        </table>
                    </div>
                </div>
                <img src="{{ asset('images/INKAICON.png') }}" alt="Company Logo" class="company-logo" style="position: absolute; top: 10px; right: 10px; width: 80px; height: 80px; object-fit: contain;">`;

            // Show SweetAlert2 modal with close and print buttons
            Swal.fire({
                html: htmlContent,
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, print it!",
                cancelButtonText: "Close",
                width: '90%', // Lebar modal 90%
                padding: '2rem', // Padding konten modal
                customClass: {
                    image: 'img-fluid rounded-circle' // Menggunakan kelas Bootstrap untuk memastikan gambar perusahaan responsif
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Printed!",
                        text: "Your file has been printed.",
                        icon: "success"
                    });
                    printDocumentSummary(documentId);
                }
            });
    }

    function printDocumentSummary(documentId) {
        // Get the URL for the PDF
        var pdfUrl = "{{ url('document/memo') }}/" + documentId + "/pdf";

        // Open the PDF URL in a new window/tab for printing
        window.open(pdfUrl, '_blank');
    }

    function toggleDocumentStatus(button) {
        var documentId = $(button).data('document-id');
        var currentStatus = $(button).data('document-status');
        var newStatus = currentStatus === 'Terbuka' ? 'Tertutup' : 'Terbuka';

        // Konfirmasi SweetAlert
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda akan mengubah status dokumen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, ubah status!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Mengirim permintaan AJAX untuk mengubah status dokumen
                $.ajax({
                    url: "{{ url('document/memo') }}/" + documentId + "/update-document-status",
                    type: "PUT", // Menggunakan metode PUT karena mengubah data
                    data: {
                        _token: '{{ csrf_token() }}',
                    },
                    success: function(response) {
                        // Update tampilan tombol
                        $(button).removeClass('document-status-button-' + currentStatus.toLowerCase()).addClass('document-status-button-' + newStatus.toLowerCase());
                        $(button).data('document-status', newStatus);
                        $(button).attr('title', newStatus);

                        // Update ikon di dalam tombol
                        var iconClass = newStatus === 'Terbuka' ? 'fas fa-times-circle' : 'fas fa-check-circle';
                        $(button).find('i').removeClass().addClass(iconClass);
                        
                        // Update teks status
                        $(button).find('span').text(newStatus);

                        // Perubahan warna tombol sesuai dengan status baru
                        if (newStatus === 'Terbuka') {
                            $(button).removeClass('btn-success').addClass('btn-danger');
                        } else {
                            $(button).removeClass('btn-danger').addClass('btn-success');
                        }

                        // Tampilkan pesan sukses
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Status dokumen berhasil diubah.",
                            icon: "success"
                        });
                    },
                    error: function(xhr, status, error) {
                        // Tampilkan pesan error
                        Swal.fire({
                            title: "Gagal!",
                            text: "Gagal mengubah status dokumen.",
                            icon: "error"
                        });
                    }
                });
            }
        });
    }



        function confirmDelete(documentId) {
            // Konfirmasi SweetAlert
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda akan menghapus dokumen ini.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                            title: "Berhasil!",
                            text: "Status Anda berhasil diubah.",
                            icon: "success"
                        });
                    // Redirect ke URL hapus dengan mengganti {id} dengan id dokumen yang sesuai
                    var Url = "{{ url('document/memo') }}/" + documentId + "/destroyget";

                    // Redirect ke URL untuk mengubah status dokumen
                    window.location.href = Url;
                }
            });
        }

        
</script>


<script type="text/javascript">
    $(document).ready(function(){
        @foreach ($revisiall as $keyan => $revisi)
            var doughnutChartData{{$keyan}} = {
                labels: ['{{$revisi['jumlah']['terbuka']}} Memo Terbuka', '{{$revisi['jumlah']['tertutup']}} Memo Tertutup'],
                datasets: [{
                    label: "slices",
                    borderWidth: 3,
                    data: [{{$revisi['persentase']['terbuka']}}, {{$revisi['persentase']['tertutup']}}],
                    backgroundColor: ['#f56954', '#00a65a'],
                    borderColor: '#fff'
                }]
            };

            var doughnutChartOptions{{$keyan}} = {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    datalabels: {
                        color: 'white',
                        font: { size: 35 },
                        formatter: function (value, context) {
                            return Math.round(value) + '%';
                        },
                    },
                    title: {
                        display: true,
                        text: "Reported Memo Progress ({{ str_replace('_', ' ', $keyan) }})",
                        color: "#D6001C",
                        font: { family: "AvenirNextLTW01-Regular", size: 35, style: 'normal' }
                    },
                    legend: {
                        display: true,
                        labels: { font: { size: 16 } }
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var label = data.labels[tooltipItem.index] || '';
                            var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            return label + ': ' + Math.round(value) + '%';
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: true } },
                    y: { grid: { display: true, drawBorder: true } },
                },
                elements: { point: { radius: 0 } },
            };

            var ctx{{$keyan}} = document.getElementById("canvas3-detailed-{{$keyan}}").getContext("2d");
            window.myDoughnut{{$keyan}} = new Chart(ctx{{$keyan}}, {
                plugins: [ChartDataLabels],
                type: "doughnut",
                data: doughnutChartData{{$keyan}},
                options: doughnutChartOptions{{$keyan}}
            });

            var doughnutChartDataBOM{{$keyan}} = {
                labels: ['{{$revisi['jumlahprogressreport']['terselesaikan']}} Komponen Terselesaikan', '{{$revisi['jumlahprogressreport']['tidak terselesaikan']}} Komponen Belum Terselesaikan'],
                datasets: [{
                    label: "slices",
                    borderWidth: 3,
                    data: [{{$revisi['persentaseprogressreport']['terselesaikan']}}, {{$revisi['persentaseprogressreport']['tidak terselesaikan']}}],
                    backgroundColor: ['#00a65a','#f56954'],
                    borderColor: '#fff'
                }]
            };

            var doughnutChartOptionsBOM{{$keyan}} = {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    datalabels: {
                        color: 'white',
                        font: { size: 35 },
                        formatter: function (value, context) {
                            return Math.round(value) + '%';
                        },
                    },
                    title: {
                        display: true,
                        text: "Reported BOM Progress ({{ str_replace('_', ' ', $keyan) }})",
                        color: "#D6001C",
                        font: { family: "AvenirNextLTW01-Regular", size: 35, style: 'normal' }
                    },
                    legend: {
                        display: true,
                        labels: { font: { size: 16 } }
                    }
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            var label = data.labels[tooltipItem.index] || '';
                            var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                            var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                            return label + ': ' + Math.round(value) + '%';
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false, drawBorder: true } },
                    y: { grid: { display: true, drawBorder: true } },
                },
                elements: { point: { radius: 0 } },
            };

            var ctxBOM{{$keyan}} = document.getElementById("canvas31-detailed-{{$keyan}}").getContext("2d");
            window.myDoughnutBOM{{$keyan}} = new Chart(ctxBOM{{$keyan}}, {
                plugins: [ChartDataLabels],
                type: "doughnut",
                data: doughnutChartDataBOM{{$keyan}},
                options: doughnutChartOptionsBOM{{$keyan}}
            });





            var doughnutChartDataProgressreport{{$keyan}} = {
            labels: ['{{$revisi['jumlahprogressreport']['terselesaikan']}} Dokumen Terselesaikan', '{{$revisi['jumlahprogressreport']['tidak terselesaikan']}} Dokumen Belum Terselesaikan'],    
            datasets: [{
                label: "slices",
                borderWidth: 3,
                data: [{{$revisi['persentaseprogressreport']['terselesaikan']}}, {{$revisi['persentaseprogressreport']['tidak terselesaikan']}}],
                backgroundColor: ['#00a65a','#f56954'],
                borderColor: '#fff'
            }]
        };

        var doughnutChartOptionsProgressreport{{$keyan}} = {
            maintainAspectRatio: false,
            responsive: true,
            plugins: {
                datalabels: {
                    color: 'white',
                    font: { size: 35 },
                    formatter: function (value, context) {
                        return Math.round(value) + '%';
                    },
                },
                title: {
                    display: true,
                    text: "Reported Document Progress ({{ str_replace('_', ' ', $keyan) }})",
                    color: "#D6001C",
                    font: { family: "AvenirNextLTW01-Regular", size: 35, style: 'normal' }
                },
                legend: {
                    display: true,
                    labels: { font: { size: 16 } }
                }
            },
            tooltips: {
                callbacks: {
                    label: function (tooltipItem, data) {
                        var label = data.labels[tooltipItem.index] || '';
                        var datasetLabel = data.datasets[tooltipItem.datasetIndex].label || '';
                        var value = data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index];
                        return label + ': ' + Math.round(value) + '%';
                    }
                }
            },
            scales: {
                x: { grid: { display: false, drawBorder: true } },
                y: { grid: { display: true, drawBorder: true } },
            },
            elements: { point: { radius: 0 } },
        };

        var ctxProgressreport{{$keyan}} = document.getElementById("canvas3-progressreport-detailed-{{$keyan}}").getContext("2d");
        window.myDoughnutProgressreport{{$keyan}} = new Chart(ctxProgressreport{{$keyan}}, {
            plugins: [ChartDataLabels],
            type: "doughnut",
            data: doughnutChartDataProgressreport{{$keyan}},
            options: doughnutChartOptionsProgressreport{{$keyan}}
        });
        @endforeach
    });
</script>



<script>
    @foreach ($revisiall as $keyan => $revisi)
        // Initialize DayPilot Scheduler
        var dp = new DayPilot.Scheduler("scheduler-{{ $keyan }}");

        // Set the start date to today
        dp.startDate = DayPilot.Date.today();
        // Define the number of days to display (1 day in this case)
        dp.days = 1;
        
        // Define business hours from 06:00 AM to 11:00 PM
        dp.businessBeginsHour = 6;
        dp.businessEndsHour = 23;
        dp.businessWeekends = true;
        dp.showNonBusiness = false;
        
        
        // Define time headers with 2-hour intervals and custom date format
        dp.timeHeaders = [
            { groupBy: "Month", format: "dd/MM/yyyy", height: 40 }, // Adjust height
            { groupBy: "Day", format: "dd/MM/yyyy", height: 40 },   // Adjust height
            { groupBy: "Hour", format: "H:mm", height: 40 }          // Adjust height
        ];

        // Set event height
        dp.eventHeight = 75;  // Adjust the height of event boxes
        
        // Set cell dimensions if needed
        dp.cellWidth = 60;    // Adjust cell width if necessary
        dp.cellWidthMin = 60; // Minimum cell width
        dp.cellHeight = 75;   // Adjust cell height if necessary

        // Load resources (e.g., rooms)
        dp.resources = [
            @foreach($ruangrapat as $room)
                @if($room!="All")
                    {name: "{{ $room }}", id: "{{ str_replace(['.', ' '], ['-', '_'], $room) }}"},
                @endif
            @endforeach
        ];

        // Load events
        dp.events.list = @json($events);

        // Event handler for creating new events
        dp.onTimeRangeSelected = function (args) {
            var name = prompt("New event name:", "Event");
            dp.clearSelection();
            if (!name) return;

            var e = {
                start: args.start,
                end: args.end,
                id: DayPilot.guid(),
                text: name,
                resource: args.resource
            };

            dp.events.add(e);

            // Send data to the server to save the event
            DayPilot.Http.ajax({
                url: "/events/create",
                data: e,
                success: function(ajax) {
                    var response = ajax.data;
                    if (response && response.result) {
                        e.id = response.id; // Update id with server response
                        dp.message("Created: " + response.message);
                    }
                },
                error: function(ajax) {
                    dp.message("Saving failed");
                }
            });
        };

        // Event handler for clicking on an event
        // Event handler for clicking on an event
        dp.onEventClick = function(args) {
            var eventId = args.e.id;
            var url = "{{ route('events.show', ':id') }}".replace(':id', eventId);
            window.location.href = url;
        };

        // Configure the bubble to display event details on hover
        // Configure the bubble to display event details on hover
        dp.bubble = new DayPilot.Bubble({
            onLoad: function(args) {
                var ev = args.source;
                args.async = true;

                var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', ev.id());

                setTimeout(function() {
                    args.html = `
                        <div style='font-weight:bold'>${ev.text()}</div>
                        <div>Start: ${ev.start().toString("MM/dd/yyyy HH:mm")}</div>
                        <div>End: ${ev.end().toString("MM/dd/yyyy HH:mm")}</div>
                        <div><a href='${eventUrl}' target='_blank'>View Event</a></div>`;
                    args.loaded();
                }, 500);
            }
            });

            // Customize event rendering
            dp.onBeforeEventRender = function(args) {
            var start = new DayPilot.Date(args.e.start);
            var end = new DayPilot.Date(args.e.end);
            var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', args.e.id);

            // Define the HTML content for the event
            args.e.html = `
                <div class='calendar_white_event_inner' style='background-color: #e1f5fe; padding: 5px; border-radius: 5px;'>
                    <div style='font-weight:bold; color: #333;'>${args.e.text}</div>
                    <div style='color: #777;'>${start.toString("HH:mm")} - ${end.toString("HH:mm")}</div>
                    <div style='color: #777;'>Pic: ${args.e.pic}</div>
                    <div><a href='${eventUrl}' target='_blank'>View Event</a></div>
                </div>
            `;

            // Set the event bar color
            args.e.barColor = "#e1f5fe";

            // Set the tooltip text
            args.e.toolTip = "Event from " + start.toString("HH:mm") + " to " + end.toString("HH:mm");
            };

            // Initialize the scheduler
            dp.init();
    @endforeach
</script>   