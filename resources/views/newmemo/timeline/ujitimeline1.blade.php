<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Timeline</title>
    <link rel="stylesheet" href="{{ asset('timelinecss/style.css') }}">
    <style>
        /* Additional styles for undone (future) events */
        .undone {
            opacity: 0.5; /* Make undone events appear gray */
        }

        .undone .eventTitle,
        .undone .eventAuthor,
        .undone .time {
            color: rgba(162, 164, 163, 0.7); /* Set text color to gray */
        }

        .undone circle {
            fill: rgba(162, 164, 163, 0.7); /* Gray circle */
        }
    </style>
</head>
<body>
    <div class="Timeline">

        @php
            // Define event data
            $events = [
                [
                    'date' => '2024-10-11',
                    'title' => 'Memo dibuka',
                    'author' => 'Dadang',
                    'time' => 'MTPR',
                    'status' => 'done'
                ],
                [
                    'date' => '2024-10-12',
                    'title' => 'Bagi dokumen',
                    'author' => 'Cahaya',
                    'time' => 'PE',
                    'status' => 'done'
                ],
                [
                    'date' => '2024-10-13',
                    'title' => 'Feedback',
                    'author' => 'Unit Terakhir',
                    'time' => 'QE',
                    'status' => 'done'
                ],
                [
                    'date' => '2024-10-14',
                    'title' => 'SM Approved',
                    'author' => 'Hermawan',
                    'time' => 'SMD',
                    'status' => 'done'
                ],
                [
                    'date' => '',
                    'title' => 'MTPR Approved',
                    'author' => '',
                    'time' => 'MTPR',
                    'status' => 'undone'
                ],
            ];
            $status = 'open'; // Change this to 'closed' to test the other scenario
            // Calculate duration between events
            $durations = [];

            for ($i = 0; $i < count($events) - 1; $i++) {
                // Periksa apakah kedua event memiliki tanggal yang valid sebelum menghitung durasi
                if (!empty($events[$i]['date']) && !empty($events[$i + 1]['date'])) {
                    try {
                        $startEventDateTime = \Carbon\Carbon::createFromFormat('Y-m-d', $events[$i]['date']);
                        $endEventDateTime = \Carbon\Carbon::createFromFormat('Y-m-d', $events[$i + 1]['date']);
                        $duration = $startEventDateTime->diffInDays($endEventDateTime);
                        $durations[] = $duration;
                    } catch (\Exception $e) {
                        // Tangani kesalahan parsing tanggal jika ada
                        // Misalnya: echo "Kesalahan format tanggal: " . $e->getMessage();
                    }
                } else {
                    // Jika tanggal event kosong (misalnya future event), tambahkan placeholder atau abaikan
                    $durations[] = null; // atau bisa gunakan nilai default seperti 0
                }
            }

        @endphp

        <!-- Timeline line -->
        <svg height="5" width="200">
            <line x1="0" y1="0" x2="200" y2="0" style="stroke:#004165;stroke-width:5" />
            Sorry, your browser does not support inline SVG.
        </svg>

        <!-- Loop through events to display -->
        @foreach ($events as $index => $event)
            <!-- Add 'undone' class if event is not done (future event) -->
            <div class="event{{ $index + 1 }} {{ $event['status'] === 'undone' ? 'undone' : '' }}">
                <div class="eventBubble">
                    <div class="eventTime">
                        @if ($event['date'])
                            <div class="DayDigit">{{ \Carbon\Carbon::parse($event['date'])->format('d') }}</div>
                            <div class="Day">
                                {{ \Carbon\Carbon::parse($event['date'])->format('l') }}
                                <div class="MonthYear">{{ \Carbon\Carbon::parse($event['date'])->format('F Y') }}</div>
                            </div>
                        @else
                            <div class="DayDigit">--</div>
                            <div class="Day">
                                TBD
                                <div class="MonthYear"></div>
                            </div>
                        @endif
                    </div>
                    <div class="eventTitle">{{ $event['title'] }}</div>
                </div>
                <div class="eventAuthor">by {{ $event['author'] ?: 'TBD' }}</div>
                <svg height="20" width="20">
                    <circle cx="10" cy="11" r="5" fill="{{ $event['status'] === 'undone' ? 'rgba(162, 164, 163, 0.7)' : '#004165' }}" />
                </svg>
                <div class="time">{{ $event['time'] }}</div>
            </div>

            <!-- Timeline line -->
            @if ($index < count($events) - 1)
                <svg height="5" width="100">
                    <line x1="0" y1="0" x2="100" y2="0" style="stroke:#004165;stroke-width:5" />
                    Sorry, your browser does not support inline SVG.
                </svg>
                <div class="now">{{ $durations[$index] }} days ago</div>
            @endif
        @endforeach

        <!-- Position NOW indicator if status is open -->
        @if ($status === 'open')
            <svg height="5" width="50">
                <line x1="0" y1="0" x2="50" y2="0" style="stroke:#004165;stroke-width:5" />
                Sorry, your browser does not support inline SVG.
            </svg>
            <div class="now">NOW</div>
        @endif

        <!-- Final Timeline Line -->
        <svg height="5" width="50">
            <line x1="0" y1="0" x2="50" y2="0" style="stroke:#004165;stroke-width:5" />
        </svg>
        <svg height="20" width="42">
            <line x1="1" y1="0" x2="1" y2="20" style="stroke:#004165;stroke-width:2" />
            <circle cx="11" cy="10" r="3" fill="#004165" />
            <circle cx="21" cy="10" r="3" fill="#004165" />
            <circle cx="31" cy="10" r="3" fill="#004165" />
            <line x1="41" y1="0" x2="41" y2="20" style="stroke:#004165;stroke-width:2" />
        </svg>

    </div>
</body>
</html>
