document.addEventListener('DOMContentLoaded', () => {
  'use strict';

  // Cache DOM elements safely
  const modal = document.getElementById('rrs-modal');
  const successPopup = document.getElementById('rrs-success-popup');
  const successMessage = document.getElementById('rrs-success-message');
  const form = document.getElementById('rrs-reservation-form');
  const response = document.getElementById('rrs-response');
  const roomSelect = document.querySelector('select[name="room"]');
  const dateInput = document.getElementById('rrs-date-picker');
  const timeDropdown = document.getElementById('rrs-time-dropdown');
  const calendarEl = document.getElementById('rrs-calendar');

  if (!modal || !successPopup || !successMessage || !form || !response || !roomSelect || !dateInput || !timeDropdown || !calendarEl) {
    console.error('One or more required DOM elements are missing.');
    return;
  }

  // Utility: Escape HTML to prevent XSS
  function escapeHTML(str) {
    return String(str).replace(/[&<>"'`=\/]/g, s => {
      return ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#39;',
        '`': '&#x60;',
        '=': '&#x3D;',
        '/': '&#x2F;',
      })[s];
    });
  }

  // Fade in/out helpers with Promises for better control
  function fadeIn(el, duration = 400) {
    return new Promise(resolve => {
      el.style.opacity = 0;
      el.style.display = '';

      let last = performance.now();

      function tick(now) {
        const delta = now - last;
        last = now;

        let opacity = parseFloat(el.style.opacity);
        opacity += delta / duration;
        if (opacity >= 1) {
          el.style.opacity = 1;
          resolve();
          return;
        }
        el.style.opacity = opacity;
        requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    });
  }

  function fadeOut(el, duration = 400) {
    return new Promise(resolve => {
      el.style.opacity = 1;

      let last = performance.now();

      function tick(now) {
        const delta = now - last;
        last = now;

        let opacity = parseFloat(el.style.opacity);
        opacity -= delta / duration;
        if (opacity <= 0) {
          el.style.opacity = 0;
          el.style.display = 'none';
          resolve();
          return;
        }
        el.style.opacity = opacity;
        requestAnimationFrame(tick);
      }
      requestAnimationFrame(tick);
    });
  }

  // Show modal helper
  async function showModal() {
    modal.classList.remove('modal-hidden');
    modal.classList.add('show');
    modal.style.display = 'none';
    await fadeIn(modal);
  }

  // Hide modal helper
  async function hideModal() {
    await fadeOut(modal);
    modal.classList.add('modal-hidden');
    modal.classList.remove('show');
  }

  // Show success popup with safe content
  function showSuccess(message) {
    successMessage.textContent = message; // safer than innerHTML
    successPopup.classList.remove('modal-hidden');
  }

  // Hide success popup
  function hideSuccess() {
    successPopup.classList.add('modal-hidden');
  }

  // Validate inputs: basic sanitation & trimming
  function sanitizeInput(str) {
    return typeof str === 'string' ? str.trim() : '';
  }

  // Fetch reserved times for the selected room & date
  async function fetchReservedTimes(room, date) {
    room = sanitizeInput(room);
    date = sanitizeInput(date);

    if (!room || !date) return;

    try {
      const params = new URLSearchParams({
        action: 'get_approved_reservations',
        nonce: rrs_ajax.nonce,
        room: room,
      });

      const res = await fetch(`${rrs_ajax.ajax_url}?${params.toString()}`, {
        credentials: 'same-origin',
      });
      if (!res.ok) throw new Error('Network response was not ok');

      const response = await res.json();
      if (!response.success || !Array.isArray(response.data)) return;

      const reservedTimes = response.data
        .filter(event => event.start.startsWith(date))
        .map(event => `${new Date(event.start).getHours()}:00`);

      for (const option of timeDropdown.options) {
        const val = option.value;
        const baseText = option.text.replace(' (Slot Taken)', '');

        if (reservedTimes.includes(val)) {
          option.disabled = true;
          option.text = `${baseText} (Slot Taken)`;
        } else {
          option.disabled = false;
          option.text = baseText;
        }
      }
    } catch (error) {
      console.error('Error fetching reserved times:', error);
    }
  }

  // Handle form submission securely
  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const formData = new FormData(form);
    formData.append('action', 'submit_reservation');
    formData.append('nonce', rrs_ajax.nonce);

    try {
      const res = await fetch(rrs_ajax.ajax_url, {
        method: 'POST',
        credentials: 'same-origin',
        body: formData,
      });

      if (!res.ok) throw new Error('Network response was not ok');

      const response = await res.json();

      if (response.success) {
        await hideModal();
        showSuccess(escapeHTML(response.data.message || 'Reservation successful.'));
        form.reset();
        response.style.color = '';
        response.innerHTML = '';
      } else {
        response.style.color = 'red';
        response.innerHTML = `<p>${escapeHTML(response.data.message || 'An error occurred.')}</p>`;
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      response.style.color = 'red';
      response.textContent = 'Submission failed. Please try again.';
    }
  });

  // Calendar initialization with security in mind
  let calendar;

  function loadCalendar(room = 'Room 1') {
    room = sanitizeInput(room);

    if (calendar) calendar.destroy();

    calendar = new FullCalendar.Calendar(calendarEl, {
      initialView: 'dayGridMonth',
      themeSystem: 'standard',
      headerToolbar: {
        left: 'prev,next today',
        center: 'title',
        right: 'dayGridMonth,timeGridWeek,timeGridDay',
      },
      hiddenDays: [0, 6],
      nowIndicator: true,
      allDaySlot: false,
      slotMinTime: '08:00:00',
      slotMaxTime: '17:00:00',
      slotDuration: '01:00:00',
      slotLabelInterval: '01:00',
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
        },
      },
      eventTimeFormat: {
        hour: 'numeric',
        minute: '2-digit',
        meridiem: 'short',
      },
      eventDisplay: 'block',
      eventColor: '#4CAF50',
      events(fetchInfo, successCallback, failureCallback) {
        const params = new URLSearchParams({
          action: 'get_approved_reservations',
          room: room,
          nonce: rrs_ajax.nonce,
        });

        fetch(`${rrs_ajax.ajax_url}?${params.toString()}`, {
          credentials: 'same-origin',
        })
          .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
          })
          .then(response => {
            if (response.success) successCallback(response.data);
            else failureCallback();
          })
          .catch(() => failureCallback());
      },
      dateClick(info) {
        if (['timeGridDay', 'timeGridWeek'].includes(calendar.view.type)) {
          const [selectedDate, selectedTime] = info.dateStr.split('T');
          showModal();
          form.querySelector('input[name="date"]').value = selectedDate || '';
          form.querySelector('select[name="time"]').value = (selectedTime || '').substring(0, 5);
          fetchReservedTimes(roomSelect.value, selectedDate);
        } else {
          calendar.changeView('timeGridDay', info.dateStr);
        }
      },
    });

    calendar.render();
  }

  // Event Bindings
  document.getElementById('rrs-open-modal').addEventListener('click', showModal);
  document.getElementById('rrs-close-modal').addEventListener('click', hideModal);
  document.getElementById('rrs-ok-button').addEventListener('click', hideSuccess);
  document.getElementById('rrs-room-select').addEventListener('change', (e) => {
    loadCalendar(e.target.value);
  });

  document.addEventListener('click', (e) => {
    if (e.target === modal) {
      hideModal();
    }
  });

  [roomSelect, dateInput].forEach(el => {
    el.addEventListener('change', () => {
      fetchReservedTimes(roomSelect.value, dateInput.value);
    });
  });

  // Initialize flatpickr with safe defaults
  if (typeof flatpickr === 'function') {
    flatpickr('#rrs-date-picker', {
      altInput: true,
      altFormat: 'F j, Y',
      dateFormat: 'Y-m-d',
      minDate: 'today',
      disableMobile: true,
    });
  } else {
    console.warn('flatpickr is not loaded');
  }

  // Initial calendar load
  loadCalendar();
});
