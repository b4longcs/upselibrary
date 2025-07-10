jQuery(document).ready(function($) {

    /**
     * Handle reservation form submission via AJAX.
     */
    $('#rrs-reservation-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        formData += '&action=submit_reservation&nonce=' + rrs_ajax.nonce;

        $.post(rrs_ajax.ajax_url, formData, function(response) {
            $('#rrs-response')
                .html(response.data.message)
                .css('color', response.success ? 'green' : 'red');
        });
    });

    /**
     * Initialize FullCalendar
     */
    const calendarEl = document.getElementById('rrs-calendar');
    let calendar;

    /**
     * Load the calendar view with reservations filtered by selected room.
     * @param {string} room - The selected room (default: "Room 1").
     */
    function loadCalendar(room = 'Room 1') {
        if (calendar) calendar.destroy(); // Reinitialize on room change

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'standard',

            // Calendar toolbar
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },

            // Time & day configuration
            hiddenDays: [0, 6], // Sunday & Saturday hidden
            nowIndicator: true,
            allDaySlot: false,
            slotMinTime: "08:00:00",
            slotMaxTime: "17:00:00",
            slotDuration: "01:00:00",
            slotLabelInterval: "01:00",
            dayMaxEvents: true,
            eventOverlap: false,
            selectable: false,
            showNonCurrentDates: false,
            fixedWeekCount: false,

            // View-specific formatting
            views: {
                timeGridWeek: {
                    titleFormat: { year: 'numeric', month: 'short', day: 'numeric' },
                    slotLabelFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
                },
                timeGridDay: {
                    titleFormat: { weekday: 'long', month: 'short', day: 'numeric' },
                    slotLabelFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' },
                }
            },

            // Event time display format
            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },

            eventDisplay: 'block',
            eventColor: '#4CAF50',

            /**
             * Fetch reservation events from the server.
             */
            events: function(fetchInfo, successCallback, failureCallback) {
                $.get(rrs_ajax.ajax_url, {
                    action: 'get_approved_reservations',
                    room: room,
                    nonce: rrs_ajax.nonce
                }, function(response) {
                    if (response.success) {
                        successCallback(response.data);
                    } else {
                        failureCallback();
                    }
                });
            },

            /**
             * Handle date click in the calendar.
             * - In week/day view: open modal with prefilled date/time
             * - In month view: switch to day view
             */
            dateClick: function(info) {
                if (calendar.view.type === 'timeGridDay' || calendar.view.type === 'timeGridWeek') {
                    const selectedDate = info.dateStr.split('T')[0];
                    const selectedTime = info.dateStr.split('T')[1].substring(0, 5);

                    $('#rrs-modal').addClass('show').hide().fadeIn();
                    $('#rrs-reservation-form input[name="date"]').val(selectedDate);
                    $('#rrs-reservation-form select[name="time"]').val(selectedTime);
                } else {
                    calendar.changeView('timeGridDay', info.dateStr);
                }
            }
        });

        calendar.render();
    }

    // Initial calendar load
    loadCalendar();

    /**
     * Reload calendar when room selection changes.
     */
    $('#rrs-room-select').on('change', function() {
        loadCalendar(this.value);
    });

    /**
     * Open modal when clicking the "Reserve a Room" button.
     */
    $('#rrs-open-modal').on('click', function() {
        $('#rrs-modal').addClass('show').hide().fadeIn();
    });

    /**
     * Close modal when clicking "Cancel" button.
     */
    $('#rrs-close-modal').on('click', function() {
        $('#rrs-modal').fadeOut(function() {
            $(this).removeClass('show');
        });
    });

    /**
     * Close modal if clicking outside the form (on overlay).
     */
    $(document).on('click', function(e) {
        if ($(e.target).is('#rrs-modal')) {
            $('#rrs-modal').fadeOut(function() {
                $(this).removeClass('show');
            });
        }
    });

});
