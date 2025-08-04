// Global variable for selected voice
let selectedVoice = null;

// Load available voices
window.speechSynthesis.onvoiceschanged = () => {
    const voices = window.speechSynthesis.getVoices();
    selectedVoice = voices.find(v => v.lang === 'en-US' && v.name.includes('Google'));

    // Fallback to any English voice
    if (!selectedVoice) {
        selectedVoice = voices.find(v => v.lang.startsWith('en'));
    }
};

// Make speak function globally accessible
window.speak = function(text) {
    if (!window.speechSynthesis || !selectedVoice) return;

    const msg = new SpeechSynthesisUtterance(text);
    msg.voice = selectedVoice;
    msg.lang = 'en-US';
    msg.volume = 1;
    msg.rate = 1;
    msg.pitch = 1;
    window.speechSynthesis.cancel();
    window.speechSynthesis.speak(msg);
};



document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('scanner-input');
    const modal = document.getElementById('scanner-modal');
    const overlay = document.getElementById('scanner-overlay');
    const message = document.getElementById('scanner-message');
    const successDetails = document.getElementById('scanner-success-details');
    const failMessage = document.getElementById('scanner-fail-message');
    const nameField = document.getElementById('scanner-name');
    const courseField = document.getElementById('scanner-course');
    const collegeField = document.getElementById('scanner-college');

    if (speechSynthesis.onvoiceschanged !== undefined) {
        speechSynthesis.onvoiceschanged = () => {
            const voices = speechSynthesis.getVoices();
            selectedVoice = voices.find(v => v.lang === 'en-US' && v.name.includes('Google')) ||
                            voices.find(v => v.lang.startsWith('en'));
        };
    }

    // Also force voice loading
    speechSynthesis.getVoices(); // This triggers `voiceschanged` on most browsers

    function showModal(success, data = {}) {

        input.addEventListener('change', function () {
            const barcode = input.value.trim();
            input.value = ''; // clear input

            if (!barcode) return;

            // Optional: show a processing indicator (not full modal)
            // message.textContent = 'Processing...';

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


        modal.classList.remove('hidden');
        overlay.classList.add('active');

        requestAnimationFrame(() => {
            // Fill modal content
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

            // Voice message
            const voiceText = success ? 'Welcome to UPSE Library' : 'Sorry, no record found';

            const speakNow = () => {
                const msg = new SpeechSynthesisUtterance(voiceText);
                msg.lang = 'en-US';
                msg.volume = 1;
                msg.rate = 1;
                msg.pitch = 1;

                if (selectedVoice) {
                    msg.voice = selectedVoice;
                }

                speechSynthesis.cancel();
                speechSynthesis.speak(msg);

                msg.onend = () => closeModal();
            };

            setTimeout(speakNow, 100);
            // Fallback close if voice is blocked
            setTimeout(() => closeModal(), 3000);
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
        input.value = ''; // clear input

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
