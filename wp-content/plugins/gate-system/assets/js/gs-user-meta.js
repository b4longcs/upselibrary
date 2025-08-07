// Save user meta data via AJAX
document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('gs-save-meta');
    if (!saveBtn) return;

    saveBtn.addEventListener('click', () => {
        let postId = saveBtn.dataset.id;
        const fields = {};
        document.querySelectorAll('.gs-meta-field').forEach(input => fields[input.dataset.key] = input.value.trim());

        const status = document.getElementById('gs-save-status');
        status.textContent = 'Saving...';
        status.style.color = ''; // Reset color
        saveBtn.disabled = true;

        // ✅ Require barcode
        if (!fields.barcode) {
            status.textContent = '❌ Barcode is required.';
            status.style.color = 'red';
            saveBtn.disabled = false;
            return;
        }

        const formData = new FormData();
        formData.append('action', 'gs_save_user_meta');
        formData.append('nonce', gs_ajax.nonce);
        formData.append('post_id', postId);
        formData.append('fields', JSON.stringify(fields));

        fetch(gs_ajax.ajax_url, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(response => {
                saveBtn.disabled = false;

                if (response.success) {
                    status.textContent = response.data.message;
                    status.style.color = 'green';

                    if (response.data.post_id) {
                        postId = response.data.post_id;
                        saveBtn.dataset.id = postId;

                        // Redirect only if this is a new post
                        if (location.href.includes('post-new.php'))
                            location.href = `/wp-admin/post.php?post=${postId}&action=edit`;
                    }
                } else {
                    // Show detailed duplicate error
                    if (typeof response.data === 'object' && response.data.duplicate_name) {
                        status.textContent = `❌ Barcode already exists for "${response.data.duplicate_name}".`;
                    } else {
                        status.textContent = '❌ ' + (response.data.message || 'Unknown error occurred.');
                    }
                    status.style.color = 'red';
                }
            })
            .catch(() => {
                status.textContent = '❌ Failed to save. Please try again.';
                status.style.color = 'red';
                saveBtn.disabled = false;
            });
    });
});

// College management logic (add/delete/list)
document.addEventListener('DOMContentLoaded', () => {
    const $ = id => document.getElementById(id);
    const addBtn = $('add-college-btn'), cancelBtn = $('cancel-college-btn'), saveBtn = $('save-college-btn');
    const select = $('gs_college'), list = $('college-list'), form = $('add-college-form');
    const input = $('new-college-name'), wrapper = $('college-list-wrapper'), toggleBtn = $('toggle-college-list');

    if (![addBtn, cancelBtn, saveBtn, select, form, input, list, wrapper, toggleBtn].every(Boolean)) return;

    addBtn.onclick = () => (form.style.display = 'block', input.focus());
    cancelBtn.onclick = () => (form.style.display = 'none', input.value = '');

    saveBtn.onclick = () => {
        const college = input.value.trim();
        if (!college) return alert('Please enter a college name.');

        const formData = new FormData();
        formData.append('action', 'gs_add_college');
        formData.append('nonce', gs_ajax.nonce);
        formData.append('college', college);

        fetch(gs_ajax.ajax_url, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const newOption = new Option(data.data.college, data.data.college);

                    // Insert alphabetically into the select element
                    let inserted = false;
                    const options = Array.from(select.options);

                    for (let i = 0; i < options.length; i++) {
                        if (options[i].value.toLowerCase().localeCompare(newOption.value.toLowerCase()) > 0) {
                            select.insertBefore(newOption, options[i]);
                            inserted = true;
                            break;
                        }
                    }
                    if (!inserted) select.appendChild(newOption);

                    // Optionally set it as selected
                    newOption.selected = true;

                    alert('College added.');
                    input.value = '';
                    form.style.display = 'none';
                    loadList();
                } else alert('Error: ' + data.data);
            })
            .catch(() => alert('Something went wrong.'));
    };

    toggleBtn.onclick = () => {
        const shown = wrapper.style.display === 'block';
        wrapper.style.display = shown ? 'none' : 'block';
        toggleBtn.textContent = shown ? 'See All College List' : 'Hide College List';
    };

    const loadList = () => {
        const options = [...select.querySelectorAll('option')].filter(opt => opt.value);
        list.innerHTML = `<ul style="margin-top:10px;padding-left:15px;">${
            options.map(opt =>
                `<li style="margin-bottom:6px;"><span>${opt.value}</span>
                 <button class="delete-college" data-college="${opt.value}" style="color:red;margin-left:10px;">Delete</button></li>`
            ).join('')
        }</ul>`;

        list.querySelectorAll('.delete-college').forEach(btn =>
            btn.onclick = () => {
                const college = btn.dataset.college;
                if (!confirm(`Delete "${college}"?`)) return;

                const formData = new FormData();
                formData.append('action', 'gs_delete_college');
                formData.append('nonce', gs_ajax.nonce);
                formData.append('college', college);

                fetch(gs_ajax.ajax_url, { method: 'POST', body: formData })
                    .then(res => res.json())
                    .then(response => {
                        if (response.success) {
                            const opt = select.querySelector(`option[value="${college}"]`);
                            if (opt) opt.remove();
                            loadList();
                        } else alert(response.data || 'Failed to delete college.');
                    });
            }
        );
    };

    loadList();
});
