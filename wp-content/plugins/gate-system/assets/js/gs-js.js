document.addEventListener("DOMContentLoaded", () => {
    // Simulate fullscreen look immediately (optional)
    document.body.style.margin = "0";
    document.body.style.padding = "0";
    document.documentElement.style.height = "100%";
    document.body.style.height = "100%";

    // Listen for first user interaction to trigger fullscreen
    function requestFullscreen() {
        const el = document.documentElement;
        if (el.requestFullscreen) {
            el.requestFullscreen();
        } else if (el.webkitRequestFullscreen) { // Safari
            el.webkitRequestFullscreen();
        } else if (el.msRequestFullscreen) { // IE/Edge
            el.msRequestFullscreen();
        }
        document.removeEventListener("click", requestFullscreen);
        document.removeEventListener("keydown", requestFullscreen);
    }

    // Request fullscreen on first click or keydown
    document.addEventListener("click", requestFullscreen);
    document.addEventListener("keydown", requestFullscreen);
});
