document.addEventListener('DOMContentLoaded', () => {
    const activeContainer = document.getElementById('na-active-entries');
    const archivedContainer = document.getElementById('na-archived-entries');
    const addEntryBtn = document.querySelector('.add-na-entry');

    const getToday = () => new Date().toISOString().split('T')[0];

    function createThumbnail(url) {
        const safeURL = encodeURI(url);
        const thumb = document.createElement('div');
        thumb.className = 'na-thumb';
        thumb.style.backgroundImage = `url('${safeURL}')`;

        const btn = document.createElement('button');
        btn.className = 'na-remove';
        btn.type = 'button';
        btn.setAttribute('aria-label', 'Remove image');
        btn.textContent = '×';

        thumb.appendChild(btn);
        return thumb;
    }

    function openMediaUploader(container) {
        const input = container.querySelector('.na-images');
        const preview = container.querySelector('.na-preview');

        const uploader = wp.media({
            title: 'Select Book Images',
            button: { text: 'Use Selected Images' },
            multiple: true
        });

        uploader.on('select', () => {
            const urls = uploader.state().get('selection')
                .map(att => att.toJSON().url)
                .filter(url => /^https?:\/\/[^<>"'()]+$/.test(url));

            input.value = urls.join(',');
            preview.innerHTML = '';
            urls.forEach(url => preview.appendChild(createThumbnail(url)));
        });

        uploader.open();
    }

    function removeImage(thumb) {
        const entry = thumb.closest('.na-entry');
        const input = entry.querySelector('.na-images');
        const preview = entry.querySelector('.na-preview');

        const urls = input.value.split(',').filter(Boolean);
        const index = [...preview.children].indexOf(thumb);

        if (index > -1) {
            urls.splice(index, 1);
            input.value = urls.join(',');
            thumb.remove();
        }
    }

    function createEntry() {
        const id = Date.now();
        const wrapper = document.createElement('div');
        wrapper.className = 'na-entry';

        wrapper.innerHTML = `
            <label style="display:block;margin-bottom:5px;">
                <input type="checkbox" name="na_entries[${id}][archived]" class="na-archive-checkbox">
                Archive this item
            </label>
            <button type="button" class="delete-na-entry button-link" style="float:right;color:red;">Delete Entry</button>
            <input type="date" name="na_entries[${id}][date]" value="${getToday()}" required>
            <button type="button" class="upload-na button">Upload Images</button>
            <input type="hidden" class="na-images" name="na_entries[${id}][images]" value="">
            <div class="na-preview"></div>
        `;

        activeContainer.prepend(wrapper);
    }

    function moveEntry(entry, archived) {
        (archived ? archivedContainer : activeContainer).appendChild(entry);
    }

    // Single event delegation for all interactions
    document.addEventListener('click', e => {
        const target = e.target;

        if (target.matches('.upload-na')) {
            openMediaUploader(target.closest('.na-entry'));
        }

        if (target.matches('.na-remove')) {
            removeImage(target.closest('.na-thumb'));
        }

        if (target.matches('.delete-na-entry')) {
            if (confirm('Are you sure you want to delete this entry?')) {
                target.closest('.na-entry').remove();
            }
        }
    });

    document.addEventListener('change', e => {
        if (e.target.matches('.na-archive-checkbox')) {
            const entry = e.target.closest('.na-entry');
            moveEntry(entry, e.target.checked);
        }
    });

    addEntryBtn?.addEventListener('click', createEntry);
});
