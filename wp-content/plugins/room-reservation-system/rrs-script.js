jQuery(document).ready(function($) {

    // Cache DOM elements
    const $modal = $('#rrs-modal');
    const $successPopup = $('#rrs-success-popup');
    const $successMessage = $('#rrs-success-message');
    const $form = $('#rrs-reservation-form');
    const $response = $('#rrs-response');
    const $roomSelect = $('select[name="room"]');
    const $dateInput = $('#rrs-date-picker');
    const $timeDropdown = $('#rrs-time-dropdown');
    const $calendarEl = document.getElementById('rrs-calendar');

    // Show modal helper
    function showModal() {
        $modal.removeClass('modal-hidden').addClass('show').hide().fadeIn();
    }

    // Hide modal helper
    function hideModal() {
        $modal.fadeOut(function() {
            $(this).addClass('modal-hidden').removeClass('show');
        });
    }

    // Show success popup
    function showSuccess(message) {
        $successMessage.text(message);
        $successPopup.removeClass('modal-hidden');
    }

    // Hide success popup
    function hideSuccess() {
        $successPopup.addClass('modal-hidden');
    }

    // Fetch reserved times for the selected room & date
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
                .map(event => `${new Date(event.start).getHours()}:00`);

            $timeDropdown.find('option').each(function () {
                const $option = $(this);
                const val = $option.val();
                const text = $option.text().replace(' (Slot Taken)', '');

                $option.prop('disabled', reservedTimes.includes(val));
                $option.text(reservedTimes.includes(val) ? `${text} (Slot Taken)` : text);
            });
        });
    }

    // Handle form submission
    $form.on('submit', function(e) {
        e.preventDefault();

        let formData = $form.serialize();
        formData += `&action=submit_reservation&nonce=${rrs_ajax.nonce}`;

        $.post(rrs_ajax.ajax_url, formData, function(response) {
            if (response.success) {
                hideModal();
                showSuccess(response.data.message);
                $form[0].reset();
            } else {
                $response.html(`<p>${response.data.message}</p>`).css('color', 'red');
            }
        });
    });

    // Calendar initialization
    let calendar;

    function loadCalendar(room = 'Room 1') {
        if (calendar) calendar.destroy();

        calendar = new FullCalendar.Calendar($calendarEl, {
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
                    slotLabelFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' }
                },
                timeGridDay: {
                    titleFormat: { weekday: 'long', month: 'short', day: 'numeric' },
                    slotLabelFormat: { hour: 'numeric', minute: '2-digit', meridiem: 'short' }
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
                    response.success ? successCallback(response.data) : failureCallback();
                });
            },
            dateClick: function(info) {
                if (['timeGridDay', 'timeGridWeek'].includes(calendar.view.type)) {
                    const [selectedDate, selectedTime] = info.dateStr.split('T');
                    showModal();
                    $form.find('input[name="date"]').val(selectedDate);
                    $form.find('select[name="time"]').val(selectedTime.substring(0, 5));
                    fetchReservedTimes($roomSelect.val(), selectedDate);
                } else {
                    calendar.changeView('timeGridDay', info.dateStr);
                }
            }
        });

        calendar.render();
    }

    // Event Bindings
    $('#rrs-open-modal').on('click', showModal);
    $('#rrs-close-modal').on('click', hideModal);
    $('#rrs-ok-button').on('click', hideSuccess);
    $('#rrs-room-select').on('change', function() {
        loadCalendar(this.value);
    });

    $(document).on('click', function(e) {
        if ($(e.target).is($modal)) {
            hideModal();
        }
    });

    // Re-check time availability on input changes
    $roomSelect.add($dateInput).on('change', function() {
        fetchReservedTimes($roomSelect.val(), $dateInput.val());
    });

    // Initialize flatpickr
    flatpickr("#rrs-date-picker", {
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        minDate: "today",
        disableMobile: true
    });

    // Initial calendar load
    loadCalendar();

});
