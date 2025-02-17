<script>
    $(function () {
        function ini_events(ele) {
            ele.each(function () {
                var eventObject = {
                    title: $.trim($(this).text())
                };
                $(this).data('eventObject', eventObject);
                $(this).draggable({
                    zIndex: 1070,
                    revert: true,
                    revertDuration: 0
                });
            });
        }

        function initializeCalendar(key, events) {
            var calendarEl = document.querySelector(`#calendar-${key}`);
            var calendarKey = calendarEl.getAttribute('data-calendar-key');

            if (!calendarEl) {
                console.error('Calendar element not found for key:', key);
                return;
            }

            // Destroy any existing calendar instance to prevent duplication
            if (calendarEl._fullCalendar) {
                calendarEl._fullCalendar.destroy();
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                themeSystem: 'bootstrap',
                events: events,
                editable: true,
                droppable: true,
                drop: function(info) {
                    var checkbox = document.getElementById('drop-remove');
                    if (checkbox && checkbox.checked) {
                        info.draggedEl.parentNode.removeChild(info.draggedEl);
                    }
                },
                slotLabelFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false // Use 24-hour format
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: false // Use 24-hour format
                }
            });

            // Store the calendar instance on the DOM element for later access
            calendarEl._fullCalendar = calendar;

            calendar.render();
        }

        function renderCalendarAndTable(key) {
            var events = @json($revisiall)[key].events.map(function(event) {
                return {
                    title: event.title,
                    start: event.start,
                    end: event.end ? event.end : null,
                    color: event.color,
                    url: '{{ route('events.show', $event->id) }}'
                };
            });

            initializeCalendar(key, events);
            ini_events($(`#external-events-${key} div.external-event`));

            // Initialize DataTable
            if ($.fn.DataTable.isDataTable(`#example2-${key}`)) {
                $(`#example2-${key}`).DataTable().destroy();
            }

            $(`#example2-${key}`).DataTable({
                paging: true,
                lengthChange: false,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false,
                responsive: true
            });
        }

        // Initial rendering for the first tab
        renderCalendarAndTable("{{ array_key_first($revisiall) }}");

        // Re-render calendar and table when tab is shown
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            var key = $(e.target).attr('aria-controls').replace('custom-tabs-one-', '');
            // Use setTimeout to ensure the tab content is fully visible
            setTimeout(function() {
                renderCalendarAndTable(key);
            }, 50);
        });
    });
</script>
