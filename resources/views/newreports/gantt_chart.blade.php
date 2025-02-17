<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Highcharts Gantt Chart</title>
    <style>
        #ganttContainer {
            width: 100%;
            height: 100%;
            overflow: auto;
            white-space: nowrap;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>

<body>
    <div class="form-group">
        <label for="projectSelector">Select Project:</label>
        <select id="projectSelector" class="form-control">
            <option value="" disabled {{ empty($project) ? 'selected' : '' }}>-- Choose Project --</option>
            @foreach($projectsData as $projectName => $projectData)
                <option value="{{ $projectName }}" {{ $projectName == $project ? 'selected' : '' }}>{{ $projectName }}
                </option>
            @endforeach
        </select>
    </div>

    <div id="ganttContainer" style="width: 100%; margin: 0 auto;"></div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/gantt/modules/gantt.js"></script>
    <script src="https://code.highcharts.com/gantt/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

    <script>
        // Adjust content height
        function adjustContentHeight() {
            const header = document.querySelector('.content-header');
            const footer = document.querySelector('.main-footer');
            const contentWrapper = document.getElementById('content-wrapper');

            if (!header || !footer || !contentWrapper) return;

            const headerHeight = header.offsetHeight || 0;
            const footerHeight = footer.offsetHeight || 0;
            const windowHeight = window.innerHeight;

            contentWrapper.style.minHeight = `${windowHeight - headerHeight - footerHeight}px`;
        }

        document.addEventListener('DOMContentLoaded', function () {
            adjustContentHeight();
            window.addEventListener('resize', adjustContentHeight);

            const projectSelector = document.getElementById('projectSelector');
            if (projectSelector) {
                const downloadDecision = document.getElementById('download-decision-container')?.dataset.downloaddecision || 'false';
                if (projectSelector.value) {
                    loadGanttChart(projectSelector.value, downloadDecision);
                }

                projectSelector.addEventListener('change', function () {
                    loadGanttChart(this.value, downloadDecision);
                });
            }
        });


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
                url: `/downloadganttchart/hasil/chart?projectName=${projectName}`,
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
                    Swal.fire('Error', 'Data proyek tidak ditemukan atau terjadi kesalahan.', 'error');
                }
            });
        }


    </script>
</body>

</html>