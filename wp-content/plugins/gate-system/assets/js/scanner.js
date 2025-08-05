let selectedVoice = null;

// Load voices and select preferred voice
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

// Estimate speech duration (optional utility)
function estimateSpeechDuration(text, rate = 0.9) {
    const words = text.trim().split(/\s+/).length;
    const avgWPM = 180;
    const timeInMinutes = words / avgWPM;
    return (timeInMinutes * 60000) / rate;
}

// Speak text with fallback and retry
window.speak = function (text, onEndCallback = null) {
    if (!window.speechSynthesis) return;

    // Cancel current speech
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
            if (typeof onEndCallback === 'function') {
                onEndCallback();
            }
        };

        speechSynthesis.speak(msg);

        // Retry if not triggered
        setTimeout(() => {
            if (!spoke) {
                speechSynthesis.speak(msg);
            }
        }, 500);
    }, 250); // Slight delay to ensure cancel completes
};

document.addEventListener('DOMContentLoaded', async function () {
    const input = document.getElementById('scanner-input');
    const modal = document.getElementById('scanner-modal');
    const overlay = document.getElementById('scanner-overlay');
    const message = document.getElementById('scanner-message');
    const successDetails = document.getElementById('scanner-success-details');
    const failMessage = document.getElementById('scanner-fail-message');
    const nameField = document.getElementById('scanner-name');
    const courseField = document.getElementById('scanner-course');
    const collegeField = document.getElementById('scanner-college');

    // Load voices and set preferred one
    const voices = await loadVoices();
    selectedVoice = voices.find(v => v.lang === 'en-US' && v.name.includes('Google')) ||
                    voices.find(v => v.lang.startsWith('en'));

    // Pre-warm speech engine with a silent utterance
    const warmup = new SpeechSynthesisUtterance(' ');
    warmup.volume = 0;
    speechSynthesis.speak(warmup);

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

    input.addEventListener('change', function () {
        const barcode = input.value.trim();
        input.value = '';

        if (!barcode) return;

        message.textContent = 'Processing...';

        fetch(gs_frontend.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'gs_scan_user',
                barcode: barcode,
                nonce: gs_frontend.nonce
            })
        })
        .then(res => res.json())
        .then(data => {
            showModal(data.success, data.success ? data.data : null);
        })
        .catch(() => {
            showModal(false);
        });
    });

    // Carousel
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
