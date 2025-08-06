// Gate System Frontend: Speech, Modal, Scanner, and Carousel
let selectedVoice = null;

// Load available voices
function loadVoices() {
    return new Promise((resolve) => {
        const voices = speechSynthesis.getVoices();
        if (voices.length) {
            resolve(voices);
        } else {
            speechSynthesis.onvoiceschanged = () => resolve(speechSynthesis.getVoices());
        }
    });
}

// Estimate speech duration
function estimateSpeechDuration(text, rate = 0.9) {
    const words = text.trim().split(/\s+/).length;
    const avgWPM = 180;
    return (words / avgWPM) * 60000 / rate;
}

// Speak text with fallback
window.speak = function (text, onEndCallback = null) {
    if (!window.speechSynthesis) return;
    window.speechSynthesis.cancel();

    setTimeout(() => {
        const msg = new SpeechSynthesisUtterance(text);
        msg.voice = selectedVoice;
        msg.lang = 'en-US';
        msg.volume = 1;
        msg.rate = 1;
        msg.pitch = 1;

        let spoke = false;
        msg.onstart = () => { spoke = true; };
        msg.onend = () => {
            if (typeof onEndCallback === 'function') onEndCallback();
        };

        speechSynthesis.speak(msg);
        setTimeout(() => {
            if (!spoke) speechSynthesis.speak(msg);
        }, 500);
    }, 250);
};

document.addEventListener('DOMContentLoaded', async () => {
    const input = document.getElementById('scanner-input');
    const modal = document.getElementById('scanner-modal');
    const overlay = document.getElementById('scanner-overlay');
    const message = document.getElementById('scanner-message');
    const successDetails = document.getElementById('scanner-success-details');
    const failMessage = document.getElementById('scanner-fail-message');
    const nameField = document.getElementById('scanner-name');
    const courseField = document.getElementById('scanner-course');
    const collegeField = document.getElementById('scanner-college');

    // Init voices
    const voices = await loadVoices();
    selectedVoice = voices.find(v => v.lang === 'en-US' && v.name.includes('Google')) ||
                    voices.find(v => v.lang.startsWith('en'));

    // Warmup
    speechSynthesis.cancel();
    setTimeout(() => {
        const warmup = new SpeechSynthesisUtterance(' ');
        warmup.volume = 0;
        speechSynthesis.speak(warmup);
    }, 100);

    // Show modal
    function showModal(success, data = {}) {
        modal.classList.remove('hidden');
        overlay.classList.add('active');

        requestAnimationFrame(() => {
            if (success) {
                message.textContent = 'Welcome!';
                nameField.value = data.name || '';
                courseField.value = data.course || '';
                collegeField.value = data.college || '';
                successDetails.classList.remove('hidden');
                failMessage.classList.add('hidden');
            } else {
                message.textContent = 'Access Denied';
                nameField.value = '';
                courseField.value = '';
                collegeField.value = '';
                successDetails.classList.add('hidden');
                failMessage.classList.remove('hidden');
            }

            const voiceText = success ? 'Hello, Welcome!' : 'Sorry, no record found';
            window.speak(voiceText, closeModal);
        });

        function closeModal() {
            modal.classList.add('hidden');
            overlay.classList.remove('active');
            message.textContent = '';
            nameField.value = '';
            courseField.value = '';
            collegeField.value = '';
            successDetails.classList.add('hidden');
            failMessage.classList.add('hidden');
        }
    }

    // Handle scan
    input.addEventListener('change', () => {
        const barcode = input.value.trim();
        input.value = '';
        if (!barcode) return;

        message.textContent = 'Processing...';

        fetch(gs_frontend.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'gs_scan_user',
                barcode,
                nonce: gs_frontend.nonce
            })
        })
        .then(res => res.json())
        .then(data => showModal(data.success, data.success ? data.data : null))
        .catch(() => showModal(false));
    });

    // Init carousel
    const slides = document.querySelectorAll('.carousel-slide');
    let index = 0;
    if (slides.length > 0) {
        slides[index].classList.add('active');
        setInterval(() => {
            slides[index].classList.remove('active');
            index = (index + 1) % slides.length;
            slides[index].classList.add('active');
        }, 4000);
    }
});

// Live date/time
function updateDateTime() {
    const date = new Date();
    const dateOptions = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };

    let hours = date.getHours();
    const minutes = date.getMinutes();
    const seconds = date.getSeconds();
    const ampm = hours >= 12 ? 'PM' : 'AM';

    const hour12 = hours % 12 || 12;

    const paddedMinutes = minutes.toString().padStart(2, '0');
    const paddedSeconds = seconds.toString().padStart(2, '0');

    document.getElementById('current-date').textContent = date.toLocaleDateString('en-US', dateOptions);
    document.getElementById('time-hm').textContent = `${hour12}:${paddedMinutes}:`;
    document.getElementById('time-s').textContent = paddedSeconds;
    document.getElementById('time-ampm').textContent = ` ${ampm}`;
}
setInterval(updateDateTime, 1000);
updateDateTime();



