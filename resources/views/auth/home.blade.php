@extends('layouts.universal')


@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}">Dashboard</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')
{{-- Tempat menyimpan nilai dowloaddecision --}}
{{-- Tempat menyimpan nilai dowloaddecision --}}
<div id="download-decision-container" data-downloaddecision="{{ $download }}"></div>




<div class="card card-danger card-outline">
    <div class="card-header">
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
        <h3 class="card-title text-bold">Dashboard <span class="badge badge-info ml-1"></span></h3>
    </div>
    <div class="card-header">
        <!-- Tabs for project selection -->
        <ul class="nav nav-tabs" id="projectTab" role="tablist">
            @foreach($projectsData as $projectName => $projectData)
                <li class="nav-item">
                    <a class="nav-link {{ $projectName == $project ? 'active' : '' }}" id="{{ $projectName }}-tab"
                        data-toggle="tab" href="#{{ $projectName }}" role="tab" aria-controls="{{ $projectName }}"
                        aria-selected="{{ $projectName == $project ? 'true' : 'false' }}">
                        {{ $projectName }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="tab-content" id="projectTabContent">
        @foreach($projectsData as $projectName => $projectData)
            <div class="tab-pane fade {{ $projectName == $project ? 'show active' : '' }}" id="{{ $projectName }}"
                role="tabpanel" aria-labelledby="{{ $projectName }}-tab">
                <section class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">

                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Ruang Rapat Hari Ini</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="scheduler-{{ $projectName }}" style="height: 600px; width: 100%;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="card card-danger">
                                    <div class="card-header">
                                        <h3 class="card-title">Progress Dokumen</h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <button type="button" class="btn btn-tool" data-card-widget="remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div id="ganttContainer"></div>
                                    </div>
                                </div>
                            </div>

                        </div>


                    </div>
                </section>
            </div>
        @endforeach
    </div>




</div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/gantt/modules/gantt.js"></script>
    <script src="https://code.highcharts.com/gantt/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    <script src="https://code.highcharts.com/modules/oldie.js"></script>
    <script src="https://code.highcharts.com/modules/pattern-fill.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="{{ asset('schedulerdaypilot/js/daypilot/daypilot-all.min.css') }}" type="text/javascript"></script>

    <!-- helper libraries -->
    <script src="{{ asset('schedulerdaypilot/js/jquery/jquery-1.9.1.min.js') }}" type="text/javascript"></script>

    <!-- daypilot libraries -->
    <script src="{{ asset('schedulerdaypilot/js/daypilot/daypilot-all.min.js') }}" type="text/javascript"></script>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function adjustContentHeight() {
                const header = document.querySelector('.content-header');
                const footer = document.querySelector('.main-footer');
                const contentWrapper = document.getElementById('content-wrapper-arif');

                if (!header || !footer || !contentWrapper) return;

                const headerHeight = header.offsetHeight || 0;
                const footerHeight = footer.offsetHeight || 0;
                const windowHeight = window.innerHeight;

                const computedStyle = window.getComputedStyle(contentWrapper);
                const paddingTop = parseFloat(computedStyle.paddingTop) || 0;
                const paddingBottom = parseFloat(computedStyle.paddingBottom) || 0;

                const contentHeight = windowHeight - headerHeight - footerHeight - paddingTop - paddingBottom;
                contentWrapper.style.minHeight = `${contentHeight}px`;
            }

            adjustContentHeight();
            window.addEventListener('resize', adjustContentHeight);

            const downloadDecisionContainer = document.getElementById('download-decision-container');
            const downloadDecision = downloadDecisionContainer?.dataset.downloaddecision || 'false';

            // Pilih tab pertama saat halaman dimuat
            const firstTab = document.querySelector('.nav-tabs .nav-link');
            if (firstTab) {
                firstTab.classList.add('active');
                const firstTabId = firstTab.id.replace('-tab', '');
                document.getElementById(firstTabId).classList.add('show', 'active');

                // Load Gantt chart untuk pilihan pertama
                loadGanttChart(firstTabId, downloadDecision);
            }

            document.querySelectorAll('.nav-tabs .nav-link').forEach(tab => {
                tab.addEventListener('click', function () {
                    loadGanttChart(this.id.replace('-tab', ''), downloadDecision);
                });
            });
        });
    </script>

    <script>


        function loadGanttChart(projectName, downloaddecision) {
            Swal.fire({
                title: 'Loading...',
                text: 'Memuat data proyek, harap tunggu.',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: `/ganttcharttenminutes/hasil/chart?projectName=${projectName}`,
                type: 'GET',
                success: function (projectData) {
                    const formattedData = [];
                    const formattedData2 = [];
                    let minDate = Infinity;
                    let maxDate = -Infinity;

                    projectData.forEach(item => {
                        const startDate = Date.UTC(...item.start);
                        const endDate = Date.UTC(...item.end);

                        formattedData.push({
                            ...item,
                            start: startDate,
                            end: endDate,
                            color: item.color,
                            completed: item.completed,
                            y: 0, // Tentukan nilai Y untuk Series 1
                        });
                        minDate = Math.min(minDate, startDate);
                        maxDate = Math.max(maxDate, endDate);

                        if (item.start_real && item.end_real && item.color_real && item.completed_real) {
                            const startReal = Date.UTC(...item.start_real);
                            const endReal = Date.UTC(...item.end_real);

                            formattedData2.push({
                                ...item,
                                start: startReal,
                                end: endReal,
                                color: item.color_real,
                                completed: item.completed_real,
                                sinkronstatus: item.sinkronstatus ?? "",
                                y: 1, // Tentukan nilai Y untuk Series 2 (Realisasi) untuk menghindari tabrakan
                                pointPadding: 0.3 // Menyesuaikan jarak antar points pada series 2
                            });

                            minDate = Math.min(minDate, startReal);
                            maxDate = Math.max(maxDate, endReal);
                        }
                    });

                    const ganttChart = Highcharts.ganttChart('ganttContainer', {
                        exporting: {
                            enabled: true,
                            buttons: {
                                contextButton: {
                                    menuItems: ['downloadXLS', 'downloadPDF', 'printChart', 'viewData', 'hideData', 'viewFullscreen', 'downloadPNG']
                                }
                            }
                        },
                        lang: {
                            downloadXLS: "Download XLS",
                            downloadPNG: "Download PNG",
                            downloadPDF: "Download PDF",
                            viewData: "Lihat Data",
                            viewFullscreen: "Full View",
                            hideData: "Sembunyikan Data",
                            printChart: "Print",
                        },
                        chart: {
                            events: {
                                load() {
                                    let chart = this;
                                    chart.series[0].points.forEach((point, index) => {
                                        if (index < 2) {
                                            point.graphic.translate(0, -25);
                                            point.dataLabel.text.translate(0, -25);
                                        }
                                    });

                                    chart.series[1].points.forEach((point, index) => {
                                        if (index < 2) {
                                            point.graphic.translate(0, -25);
                                            point.dataLabel.text.translate(0, -25);
                                        }
                                    });
                                    // Ekspor chart ke PDF setelah load
                                    chart.exportChart({
                                        type: 'application/pdf',
                                        filename: `${projectName}_Gantt_Chart_Automatically_Exported`
                                    });
                                }
                            },
                            height: 600
                        },
                        title: {
                            text: `${projectName}`
                        },
                        tooltip: {
                            formatter: function () {
                                var releasedCountAsync = this.point.real_Releasedcount ? this.point.real_Releasedcount - this.point.Releasedcount : 0;
                                var unreleasedCountAsync = this.point.real_Unreleasedcount ? this.point.real_Unreleasedcount - this.point.Unreleasedcount : 0;

                                var tooltipContent = `<span>${this.point.name}</span>: <br>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                Rencana: <b>${Highcharts.dateFormat('%e. %b %Y', this.point.start)}</b> - <b>${Highcharts.dateFormat('%e. %b %Y', this.point.end)}</b><br>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                Rilis (Sinkron): <b>${this.point.Releasedcount}</b><br>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                Belum Rilis (Sinkron): <b>${this.point.Unreleasedcount}</b><br>`;

                                if (this.point.real_Releasedcount !== undefined && this.point.real_Unreleasedcount !== undefined) {
                                    tooltipContent += `<br>Rilis (Asinkron): <b>${(this.point.real_Releasedcount - this.point.Releasedcount) || 0}</b>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                <br>Belum Rilis (Asinkron): <b>${(this.point.real_Unreleasedcount - this.point.Unreleasedcount) || 0}</b>`;
                                }

                                return tooltipContent;
                            }
                        },
                        series: [
                            {
                                name: `${projectName} Project Rencana`,
                                data: formattedData,
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        fontSize: '21px',
                                        fontWeight: 'bold',
                                        color: '#000000',
                                    },
                                    formatter: function () {
                                        return `${this.point.completed.amount * 100}%`;
                                    }
                                },
                            },
                            {
                                name: `${projectName} Project Realisasi`,
                                data: formattedData2,
                                pointPlacement: 0.5,
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        fontSize: '16px',
                                        fontWeight: 'bold',
                                        color: '#000000',
                                    },
                                    formatter: function () {
                                        return `${this.point.completed.amount * 100}% ${this.point.sinkronstatus}`;
                                    }
                                },
                            }
                        ],
                        xAxis: {
                            currentDateIndicator: true,
                            scrollbar: {
                                enabled: true
                            },
                            min: minDate,
                            max: maxDate,
                            events: {
                                afterSetExtremes: function () {
                                    if (this.min === minDate && this.max === maxDate) {
                                        // Set a reset zoom button here if needed
                                    }
                                }
                            }
                        },
                        yAxis: {
                            scrollbar: {
                                enabled: false // Tidak menampilkan scrollbar pada sumbu Y
                            },
                            uniqueNames: true, // Pastikan setiap nama unik pada Y-axis
                            gapSize: 25, // Jarak antar data
                            reversedStacks: false, // Hindari tumpukan berurutan
                            grid: {
                                columns: [
                                    {
                                        title: { text: 'Part' }, // Judul kolom
                                        categories: formattedData.map(item => item.name) // Data kategori dari `formattedData`
                                    },
                                ]
                            },
                            title: {
                                text: 'Tasks' // Judul sumbu Y
                            },
                            labels: {
                                style: {
                                    fontSize: '14px', // Ukuran font label
                                    fontWeight: 'bold',
                                    color: '#333333' // Warna label
                                },
                                formatter: function () {
                                    return this.value; // Menampilkan nilai kategori
                                }
                            },
                            gridLineColor: '#e6e6e6', // Warna garis grid
                            gridLineWidth: 1, // Ketebalan garis grid
                            tickWidth: 1, // Lebar tick
                            tickColor: '#cccccc' // Warna tick
                        },
                        navigator: {
                            enabled: false,
                            liveRedraw: false,
                            series: {
                                accessibility: {
                                    enabled: false
                                }
                            },
                        },
                        rangeSelector: {
                            enabled: true,
                        },
                        credits: {
                            enabled: false,
                        },
                        legend: {
                            enabled: true,
                        },
                        chart: {
                            turboThreshold: 5000,
                            events: {
                                render: function () {
                                    const container = document.getElementById('ganttContainer');
                                    if (container.scrollTop !== 0) {
                                        container.scrollTop = 0;
                                    }
                                }
                            }
                        }
                    });

                    // Langsung trigger download PDF saat halaman diakses

                    if (downloaddecision === 'true') {
                        ganttChart.exportChart({
                            type: 'application/pdf',
                            filename: `${projectName}_Gantt_Chart_Automatically_Exported`
                        });
                    }



                    Swal.close();
                },
                error: function () {
                    Swal.close();

                    // Tetap render Gantt Chart kosong jika terjadi error
                    Highcharts.ganttChart('ganttContainer', {
                        title: { text: 'Tidak ada data tersedia' },
                        series: [],
                        xAxis: {
                            min: Date.UTC(2023, 0, 1),
                            max: Date.UTC(2023, 11, 31)
                        },
                        yAxis: {
                            title: { text: 'Tasks' }
                        },
                        credits: { enabled: false }
                    });
                }

            });
        }


    </script>

    <script>
        @foreach($projectsData as $projectName => $projectData)
            // Initialize DayPilot Scheduler
            var dp = new DayPilot.Scheduler("scheduler-{{ $projectName }}");

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
                                                @if($room != "All")
                                                    { name: "{{ $room }}", id: "{{ str_replace(['.', ' '], ['-', '_'], $room) }}" },
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
                    success: function (ajax) {
                        var response = ajax.data;
                        if (response && response.result) {
                            e.id = response.id; // Update id with server response
                            dp.message("Created: " + response.message);
                        }
                    },
                    error: function (ajax) {
                        dp.message("Saving failed");
                    }
                });
            };

            // Event handler for clicking on an event
            // Event handler for clicking on an event
            dp.onEventClick = function (args) {
                var eventId = args.e.id;
                var url = "{{ route('events.show', ':id') }}".replace(':id', eventId);
                window.location.href = url;
            };

            // Configure the bubble to display event details on hover
            // Configure the bubble to display event details on hover
            dp.bubble = new DayPilot.Bubble({
                onLoad: function (args) {
                    var ev = args.source;
                    args.async = true;

                    var eventUrl = "{{ route('events.show', ':id') }}".replace(':id', ev.id());

                    setTimeout(function () {
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
            dp.onBeforeEventRender = function (args) {
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




@endpush


@push('css')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

@endpush