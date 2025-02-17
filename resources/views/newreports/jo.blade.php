@extends('layouts.universal')

@section('container2')
<div class="content-header">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb bg-white px-2 float-left">
                    <li class="breadcrumb-item"><a href="{{ route('newreports.index') }}">Progress Project</a></li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('container3')
<div id="download-decision-container" data-downloaddecision="{{ $download }}"></div>

<div align="center">
    <div class="col-9">


        <div class="card card-danger card-outline">
            <div class="card-header">
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
                <h3 class="card-title text-bold">Page Monitoring Jam Orang</h3>
            </div>

            <div class="card-body" id="content-wrapper">
                <div>
                    <label for="year-select">Pilih Tahun:</label>
                    <select id="year-select">
                        <option value="2025">2025</option>
                        <option value="2024">2024</option>
                        <option value="2023">2023</option>
                    </select>
                </div>

                <div>
                    <fieldset>
                        <legend>Pilih Project:</legend>
                        <button id="toggle-projects" type="button" class="btn btn-secondary btn-sm"
                            style="margin-bottom: 10px;">
                            Hide
                        </button>
                        <div id="projects-container" style="display: block;">
                            <label>
                                <input type="checkbox" id="all-projects-checkbox" checked> All Projects
                            </label>
                            <div style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px;">
                                @foreach($projectsData as $projectName => $projectData)
                                    <div style="flex: 1 1 calc(33.33% - 10px); display: flex; align-items: center;">
                                        <label style="flex: 1;">
                                            <input type="checkbox" class="project-checkbox" value="{{ $projectName }}"
                                                checked>
                                            {{ $projectName }}
                                        </label>

                                        <button type="button" class="btn bg-purple btn-sm" style="margin-left: 10px;"
                                            onclick="downloadProject('{{ $projectName }}')">
                                            All Time
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </fieldset>
                </div>






                <div id="ganttContainer" style="width: 100%; height: 400px;"></div>
            </div>
        </div>


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

    <script>
        function downloadProject(projectName) {
            if (!projectName) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Nama proyek tidak valid.',
                });
                return;
            }

            // Tampilkan loading sebelum memulai permintaan
            Swal.fire({
                title: 'Loading',
                text: 'Memuat data jam orang...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Lakukan permintaan AJAX untuk mendapatkan data workload
            fetch(`{{ route('newreports.workloadproject') }}?project=${encodeURIComponent(projectName)}`)
                .then(response => response.json())
                .then(data => {
                    // Tutup loading
                    Swal.close();

                    if (data.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.error,
                        });
                    } else {
                        // Urutkan data montly-year berdasarkan tanggal
                        const sortedData = data['montly-year'].sort((a, b) => a.date.localeCompare(b.date));

                        // Kelompokkan data berdasarkan tahun
                        const groupedByYear = sortedData.reduce((acc, entry) => {
                            const year = entry.date.split('-')[0];
                            if (!acc[year]) acc[year] = [];
                            acc[year].push(entry);
                            return acc;
                        }, {});

                        // Ambil semua tahun yang ada
                        const years = Object.keys(groupedByYear);

                        // Tampilkan tahun pertama
                        let currentYearIndex = 0;

                        function showYearData() {
                            const year = years[currentYearIndex];
                            const rows = groupedByYear[year]
                                .map(entry => `
                                                <tr>
                                                    <td>${entry.date}</td>
                                                    <td>${entry.workload.toFixed(2)}</td>
                                                </tr>
                                            `)
                                .join('');

                            const tableHTML = `
                                            <h3 style="margin: 10px 0;">Tahun: ${year}</h3>
                                            <table border="1" style="width:100%; text-align: left; border-collapse: collapse;">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Workload</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    ${rows}
                                                </tbody>
                                            </table>
                                        `;

                            // Tampilkan SweetAlert dengan tabel dan data workload
                            Swal.fire({
                                icon: 'info',
                                title: `Jam Orang untuk ${projectName}`,
                                html: `
                                                <p><strong>Total Jam Orang :</strong> ${data.totalworkload}</p>
                                                ${tableHTML}
                                            `,
                                showCancelButton: true,
                                confirmButtonText: 'Next Year',
                                cancelButtonText: 'Close',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    currentYearIndex = (currentYearIndex + 1) % years.length;
                                    showYearData();
                                }
                            });
                        }

                        // Tampilkan tahun pertama
                        showYearData();
                    }
                })
                .catch(error => {
                    // Tutup loading dan tampilkan pesan error
                    Swal.close();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Terjadi kesalahan saat memuat data.',
                    });
                    console.error(error);
                });
        }
    </script>










    <script>
        function adjustContentHeight() {
            const header = document.querySelector('.content-header');
            const footer = document.querySelector('.main-footer');
            const contentWrapper = document.getElementById('content-wrapper');

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


    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            adjustContentHeight();
            window.addEventListener('resize', adjustContentHeight);
            // Toggle Projects Container
            const toggleButton = document.getElementById('toggle-projects');
            const projectsContainer = document.getElementById('projects-container');

            toggleButton.addEventListener('click', () => {
                if (projectsContainer.style.display === 'none') {
                    projectsContainer.style.display = 'block';
                    toggleButton.textContent = 'Hide';
                } else {
                    projectsContainer.style.display = 'none';
                    toggleButton.textContent = 'Show';
                }
            });


            const data = [
                { project: '1164 PPCW BM 54 TON', date: '2025-01', workload: 2900 },
                { project: 'KCI', date: '2025-01', workload: 2900 },
                { project: 'KCI', date: '2025-02', workload: 3200 },
                { project: 'Retrofit', date: '2025-01', workload: 3000 },
                { project: 'Retrofit', date: '2025-02', workload: 3300 }
            ];

            const yearSelect = document.getElementById('year-select');
            const allProjectsCheckbox = document.getElementById('all-projects-checkbox');
            const projectCheckboxes = document.querySelectorAll('.project-checkbox');

            function updateAllProjectsCheckbox() {
                const allChecked = Array.from(projectCheckboxes).every(checkbox => checkbox.checked);
                allProjectsCheckbox.checked = allChecked;
            }

            function toggleProjectCheckboxes(state) {
                projectCheckboxes.forEach(checkbox => {
                    checkbox.checked = state;
                });
            }

            function renderChart(selectedYear) {
                // Tampilkan SweetAlert Loading
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching chart data, please wait.',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: `/areachart/hasil/chart?year=${selectedYear}`,
                    type: 'GET',
                    success: function (data) {
                        Swal.close(); // Tutup SweetAlert setelah berhasil mendapatkan data

                        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                        const selectedProjects = Array.from(projectCheckboxes)
                            .filter(checkbox => checkbox.checked)
                            .map(checkbox => checkbox.value);

                        const series = selectedProjects.map(project => {
                            const projectData = data.filter(item => item.project === project);
                            const workloadData = months.map((_, index) => {
                                const monthKey = `${selectedYear}-${String(index + 1).padStart(2, '0')}`;
                                const entry = projectData.find(item => item.date === monthKey);
                                // Bulatkan workload ke dua angka di belakang koma
                                return entry ? parseFloat(entry.workload.toFixed(2)) : null;
                            });
                            return {
                                name: project,
                                data: workloadData
                            };
                        });


                        Highcharts.chart('ganttContainer', {
                            chart: {
                                type: 'area'
                            },
                            title: {
                                text: `Jam Orang untuk Tahun ${selectedYear}`
                            },
                            xAxis: {
                                categories: months,
                                title: {
                                    text: 'Month'
                                }
                            },
                            yAxis: {
                                title: {
                                    text: 'Jam Orang'
                                }
                            },
                            tooltip: {
                                shared: true,
                                valueSuffix: ' hours'
                            },
                            plotOptions: {
                                area: {
                                    stacking: 'normal',
                                    lineColor: '#666666',
                                    lineWidth: 1,
                                    marker: {
                                        lineWidth: 1,
                                        lineColor: '#666666'
                                    }
                                }
                            },
                            legend: {
                                align: 'center',
                                verticalAlign: 'bottom',
                                layout: 'horizontal'
                            },
                            series: series
                        });
                    },
                    error: function (xhr, status, error) {
                        Swal.close(); // Tutup SweetAlert jika terjadi error
                        console.error('Failed to fetch chart data:', error);
                        Swal.fire('Error', 'Unable to load chart data. Please try again.', 'error');
                    }
                });
            }


            renderChart(yearSelect.value);

            yearSelect.addEventListener('change', function () {
                renderChart(this.value);
            });

            projectCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    updateAllProjectsCheckbox();
                    renderChart(yearSelect.value);
                });
            });

            allProjectsCheckbox.addEventListener('change', function () {
                toggleProjectCheckboxes(this.checked);
                renderChart(yearSelect.value);
            });
        });

    </script>
@endpush

@push('css')
    <style>
        #ganttContainer {
            width: 100%;
            height: 100%;
            overflow-x: auto;
            overflow-y: auto;
            white-space: nowrap;
        }
    </style>
    <style>
        #toggle-projects {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            padding: 5px 10px;
        }

        #toggle-projects:hover {
            background-color: #0056b3;
        }
    </style>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endpush