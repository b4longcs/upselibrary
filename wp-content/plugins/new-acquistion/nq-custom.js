// Admin + Frontend logic
jQuery(document).ready(function ($) {
    // ADMIN: Media upload
    $(document).on('click', '.upload-na', function (e) {
        e.preventDefault();
        const button = $(this);
        const container = button.closest('.na-entry');
        const input = container.find('.na-images');
        const preview = container.find('.na-preview');

        const uploader = wp.media({
            title: 'Select Book Images',
            button: { text: 'Use Selected Images' },
            multiple: true
        });

        uploader.on('select', function () {
            const urls = uploader.state().get('selection').map(att => att.toJSON().url);
            input.val(urls.join(','));
            preview.empty();
            urls.forEach(url => {
                preview.append(`<div class="na-thumb" style="background-image:url('${url}')"></div>`);
            });
        });

        uploader.open();
    });

    // ADMIN: Add entry
    $('.add-na-entry').on('click', function () {
        const i = $('.na-entry').length;
        $('#na-entries').append(`
            <div class="na-entry">
                <input type="text" name="na_entries[${i}][date]" placeholder="Acquisition Date" required>
                <button class="button upload-na">Upload Images</button>
                <input type="hidden" class="na-images" name="na_entries[${i}][images]" value="">
                <div class="na-preview"></div>
            </div>
        `);
    });

    // FRONTEND: Accordion toggle with persistent, reusable logic
    document.querySelectorAll(".na-toggle").forEach(btn => {
        btn.addEventListener("click", () => {
            const content = btn.nextElementSibling;
            const isExpanded = btn.getAttribute("aria-expanded") === "true";

            content.classList.remove("expanding", "collapsing");

            if (isExpanded) {
                // COLLAPSE
                btn.setAttribute("aria-expanded", "false");

                // Set current height before collapse
                content.style.maxHeight = content.scrollHeight + 'px';

                // Force reflow
                void content.offsetHeight;

                content.classList.add("collapsing");
                content.style.maxHeight = '0';
                content.style.opacity = '0';

                content.addEventListener("transitionend", function handler(e) {
                    if (e.propertyName === "max-height") {
                        content.classList.remove("collapsing", "na-visible");
                        content.style.visibility = "hidden";
                        content.style.maxHeight = '';
                        content.removeEventListener("transitionend", handler);
                    }
                }, { once: true });

            } else {
                // EXPAND
                btn.setAttribute("aria-expanded", "true");

                content.style.visibility = "visible";
                content.classList.add("na-visible", "expanding");
                content.style.opacity = '1';

                // Set height to scrollHeight to animate
                content.style.maxHeight = content.scrollHeight + 'px';

                content.addEventListener("transitionend", function handler(e) {
                    if (e.propertyName === "max-height") {
                        content.classList.remove("expanding");
                        content.style.maxHeight = 'none'; // allow natural height
                        content.removeEventListener("transitionend", handler);
                    }
                }, { once: true });
            }
        });
    });


});
