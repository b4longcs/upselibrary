document.addEventListener('DOMContentLoaded', () => {
    // Containers for active and archived entries
    const activeContainer = document.getElementById('na-active-entries');
    const archivedContainer = document.getElementById('na-archived-entries');

    // "Add Entry" button
    const addEntryBtn = document.querySelector('.add-na-entry');

    // Utility: Get today's date in YYYY-MM-DD format
    const getToday = () => new Date().toISOString().split('T')[0];

    // Create a thumbnail element for an image
    const createThumbnail = (url) => {
        const thumb = document.createElement('div');
        thumb.className = 'na-thumb';
        thumb.style.backgroundImage = `url('${encodeURI(url)}')`;

        const removeBtn = document.createElement('button');
        removeBtn.className = 'na-remove';
        removeBtn.type = 'button';
        removeBtn.setAttribute('aria-label', 'Remove image');
        removeBtn.textContent = '×';

        thumb.appendChild(removeBtn);
        return thumb;
    };

    // Open WordPress media uploader and populate preview/images
    const openMediaUploader = (container) => {
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
                .filter(url => /^https?:\/\/[^<>"'()]+$/.test(url)); // Basic URL validation

            input.value = urls.join(',');
            preview.innerHTML = '';
            urls.forEach(url => preview.appendChild(createThumbnail(url)));
        });

        uploader.open();
    };

    // Remove a thumbnail and update hidden input
    const removeImage = (thumb) => {
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
    };

    // Create a new acquisition entry and add it to active container
    const createEntry = () => {
        const id = Date.now();
        const wrapper = document.createElement('div');
        wrapper.className = 'na-entry';

        // HTML structure for a new entry
        wrapper.innerHTML = `
            <button type="button" class="delete-na-entry button-link" style="float:right;color:red;">Delete Entry</button>
            <input type="date" name="na_entries[${id}][date]" value="${getToday()}" required>
            <button type="button" class="upload-na button">Upload Images</button>
            <input type="hidden" class="na-images" name="na_entries[${id}][images]" value="">
            <div class="na-preview"></div>
        `;

        activeContainer.prepend(wrapper);
        enhanceDatePickers(); // Apply flatpickr to date input
    };

    // Move an entry between active and archived containers
    const moveEntry = (entry, isArchived) => {
        (isArchived ? archivedContainer : activeContainer).appendChild(entry);
    };

    // Initialize flatpickr (if available) on all date inputs
    const enhanceDatePickers = () => {
        if (typeof flatpickr === 'undefined') return;

        document.querySelectorAll('.na-entry input[type="date"]:not([data-flatpickr-applied])')
            .forEach(input => {
                input.dataset.flatpickrApplied = 'true';
                flatpickr(input, {
                    dateFormat: "Y-m-d",
                    altInput: true,
                    altFormat: "F j, Y",
                    allowInput: true
                });
            });
    };

    // Apply flatpickr on page load
    enhanceDatePickers();

    const observer = new MutationObserver(enhanceDatePickers);
    observer.observe(activeContainer, { childList: true, subtree: true });
    observer.observe(archivedContainer, { childList: true, subtree: true });

    // Event delegation: handle all button actions
    document.addEventListener('click', (e) => {
        const target = e.target;

        // Upload images
        if (target.matches('.upload-na')) {
            openMediaUploader(target.closest('.na-entry'));
        }

        // Remove an image
        if (target.matches('.na-remove')) {
            removeImage(target.closest('.na-thumb'));
        }

        // Delete entry
        if (target.matches('.delete-na-entry')) {
            if (confirm('Are you sure you want to delete this entry?')) {
                target.closest('.na-entry')?.remove();
            }
        }

        // Archive or Restore entry
        if (target.matches('.archive-btn')) {
            const entry = target.closest('.na-entry');
            const checkbox = entry.querySelector('.na-archive-checkbox');

            checkbox.checked = !checkbox.checked;
            const isArchived = checkbox.checked;

            // Update button label and style
            target.textContent = isArchived ? 'Restore' : 'Archive';
            target.dataset.archived = isArchived ? '1' : '0';
            target.classList.toggle('restore-btn', isArchived);
            target.classList.toggle('archive-only-btn', !isArchived);

            // Move the entry between sections
            moveEntry(entry, isArchived);
        }
    });

    // Add new entry button
    addEntryBtn?.addEventListener('click', createEntry);
});
