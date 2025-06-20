jQuery(function ($) {

    const createThumb = (url, index) => `
        <div class="na-thumb" style="background-image:url('${url}')">
            <span class="na-remove" data-index="${index}">&times;</span>
        </div>
    `;

    function openMediaUploader($container) {
        const $input = $container.find('.na-images');
        const $preview = $container.find('.na-preview');

        const uploader = wp.media({
            title: 'Select Book Images',
            button: { text: 'Use Selected Images' },
            multiple: true
        });

        uploader.on('select', () => {
            const urls = uploader.state().get('selection').map(att => att.toJSON().url);
            $input.val(urls.join(','));
            $preview.empty().append(urls.map(createThumb).join(''));
        });

        uploader.open();
    }

    function removeImageFromPreview($thumb) {
        const $container = $thumb.closest('.na-entry');
        const $input = $container.find('.na-images');
        const $preview = $container.find('.na-preview');

        const urls = $input.val().split(',').filter(Boolean);
        const index = $preview.find('.na-thumb').index($thumb);

        if (index > -1) {
            urls.splice(index, 1);
            $input.val(urls.join(','));
            $thumb.remove();
        }
    }

    $(document).on('click', '.upload-na', function (e) {
        e.preventDefault();
        openMediaUploader($(this).closest('.na-entry'));
    });

    $(document).on('click', '.na-remove', function () {
        removeImageFromPreview($(this).closest('.na-thumb'));
    });

    $('.add-na-entry').on('click', function () {
        const id = Date.now();
        $('#na-entries').prepend(`
            <div class="na-entry">
                <input type="text" name="na_entries[${id}][date]" placeholder="Acquisition Date" required>
                <button class="button upload-na">Upload Images</button>
                <input type="hidden" class="na-images" name="na_entries[${id}][images]" value="">
                <div class="na-preview"></div>
            </div>
        `);
    });

});
