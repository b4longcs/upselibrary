jQuery(document).ready(function($) {

     // Show modal helper
    function showModal() {
        $('#rrs-modal').removeClass('modal-hidden');
    }

    // Hide modal helper
    function hideModal() {
        $('#rrs-modal').addClass('modal-hidden');
    }

    // Show success popup helper
    function showSuccess(message) {
        $('#rrs-success-message').text(message);
        $('#rrs-success-popup').removeClass('modal-hidden');
    }

    // Hide success popup helper
    function hideSuccess() {
        $('#rrs-success-popup').addClass('modal-hidden');
    }

    // Open modal when clicking "Reserve a Room"
    $('#rrs-open-modal').on('click', showModal);

    // Close modal on cancel button
    $('#rrs-close-modal').on('click', hideModal);

    // Close modal when clicking outside modal content (overlay)
    $(document).on('click', function(e) {
        if ($(e.target).is('#rrs-modal')) {
        hideModal();
        }
    });

    // Handle form submission
    $('#rrs-reservation-form').on('submit', function(e) {
        e.preventDefault();

        var formData = $(this).serialize();
        formData += '&action=submit_reservation&nonce=' + rrs_ajax.nonce;

        $.post(rrs_ajax.ajax_url, formData, function(response) {
        if (response.success) {
            // Hide the modal form
            hideModal();

            // Show the success popup with message
            showSuccess(response.data.message);

            // Reset the form for next use
            $('#rrs-reservation-form')[0].reset();

        } else {
            $('#rrs-response')
            .html('<p>' + response.data.message + '</p>')
            .css('color', 'red');
        }
        });
    });

    // Hide success popup when clicking OK
    $('#rrs-ok-button').on('click', hideSuccess);



    /**
     * Fetch reserved times for the selected room & date and disable them.
     */
    function fetchReservedTimes(room, date) {
        if (!room || !date) return;

        $.get(rrs_ajax.ajax_url, {
            action: 'get_approved_reservations',
            nonce: rrs_ajax.nonce,
            room: room
        }, function(response) {
            if (!response.success) return;

            const reservedTimes = response.data
                .filter(event => event.start.startsWith(date))
                .map(event => {
                    const hour = new Date(event.start).getHours();
                    return `${hour}:00`;
                });

            $('#rrs-time-dropdown option').each(function () {
                const value = $(this).val();
                if (reservedTimes.includes(value)) {
                    $(this)
                        .prop('disabled', true)
                        .text($(this).text().replace(' (Booked)', '') + ' (Booked)');
                } else {
                    $(this)
                        .prop('disabled', false)
                        .text($(this).text().replace(' (Booked)', ''));
                }
            });
        });
    }

    /**
     * Re-check time availability whenever room or date changes.
     */
    $('#rrs-room-dropdown, #rrs-date-picker').on('change', function () {
        const room = $('#rrs-room-dropdown').val();
        const date = $('#rrs-date-picker').val();
        fetchReservedTimes(room, date);
    });

    /**
     * Initialize FullCalendar
     */
    const calendarEl = document.getElementById('rrs-calendar');
    let calendar;

    function loadCalendar(room = 'Room 1') {
        if (calendar) calendar.destroy();

        calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            themeSystem: 'standard',

            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },

            hiddenDays: [0, 6],
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

            eventTimeFormat: {
                hour: 'numeric',
                minute: '2-digit',
                meridiem: 'short'
            },

            eventDisplay: 'block',
            eventColor: '#4CAF50',

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

            dateClick: function(info) {
                if (calendar.view.type === 'timeGridDay' || calendar.view.type === 'timeGridWeek') {
                    const selectedDate = info.dateStr.split('T')[0];
                    const selectedTime = info.dateStr.split('T')[1].substring(0, 5);

                    $('#rrs-modal').addClass('show').hide().fadeIn();
                    $('#rrs-reservation-form input[name="date"]').val(selectedDate);
                    $('#rrs-reservation-form select[name="time"]').val(selectedTime);
                    fetchReservedTimes($('#rrs-room-dropdown').val(), selectedDate); // fetch conflicts
                } else {
                    calendar.changeView('timeGridDay', info.dateStr);
                }
            }
        });

        calendar.render();
    }

    // Initial calendar load
    loadCalendar();

    // Reload calendar when room selection changes
    $('#rrs-room-select').on('change', function() {
        loadCalendar(this.value);
    });

    // Open modal
    $('#rrs-open-modal').on('click', function() {
        $('#rrs-modal').addClass('show').hide().fadeIn();
    });

    // Close modal on cancel
    $('#rrs-close-modal').on('click', function() {
        $('#rrs-modal').fadeOut(function() {
            $(this).removeClass('show');
        });
    });

    // Close modal on overlay click
    $(document).on('click', function(e) {
        if ($(e.target).is('#rrs-modal')) {
            $('#rrs-modal').fadeOut(function() {
                $(this).removeClass('show');
            });
        }
    });

    // Initialize Flatpickr
    flatpickr("#rrs-date-picker", {
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        minDate: "today",
        disableMobile: true
    });

});
