// ====================================
// MODULE: Global: Popup
// ====================================
window.onload = function () {
    setTimeout(function () {
        const popup = document.getElementById('popupOverlay');
        if (popup) {
            popup.classList.add('show');
            popup.style.pointerEvents = 'auto'; // Enable interaction
        }
    }, 300);
};


function closePopup() {
    const popupOverlay = document.getElementById('popupOverlay');
    if (popupOverlay) {
        popupOverlay.classList.remove('show');
        popupOverlay.style.pointerEvents = 'none';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const overlay = document.getElementById('popupOverlay');
    if (overlay) {
        overlay.addEventListener('click', function (event) {
            if (event.target === this) {
                closePopup();
            }
        });
    }
});
// ====================================