// ====================================
// MODULE: Global: Popup
// ====================================
window.onload = function () {
    setTimeout(function () {
        document.getElementById('popupOverlay')?.classList.add('show');
    }, 500);
};

function closePopup() {
    const popupOverlay = document.getElementById('popupOverlay');
    if (popupOverlay) {
        popupOverlay.classList.remove('show');
        popupOverlay.style.zIndex = '0';
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