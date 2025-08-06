// Save user meta data via AJAX
document.addEventListener('DOMContentLoaded', () => {
    const saveBtn = document.getElementById('gs-save-meta');
    if (!saveBtn) return;

    saveBtn.addEventListener('click', () => {
        let postId = saveBtn.dataset.id;
        const fields = {};
        document.querySelectorAll('.gs-meta-field').forEach(input => fields[input.dataset.key] = input.value);

        const status = document.getElementById('gs-save-status');
        status.textContent = 'Saving...';
        saveBtn.disabled = true;

        const formData = new FormData();
        formData.append('action', 'gs_save_user_meta');
        formData.append('nonce', gs_ajax.nonce);
        formData.append('post_id', postId);
        formData.append('fields', JSON.stringify(fields));

        fetch(gs_ajax.ajax_url, { method: 'POST', body: formData })
            .then(res => res.json())
            .then(response => {
                if (response.success) {
                    status.textContent = response.data.message;
                    if (response.data.post_id) {
                        postId = response.data.post_id;
                        saveBtn.dataset.id = postId;
                        if (location.href.includes('post-new.php'))
                            location.href = `/wp-admin/post.php?post=${postId}&action=edit`;
                    }
                } else {
                    status.textContent = 'Error: ' + response.data;
                }
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
                    const opt = new Option(data.data.college, data.data.college, true, true);
                    select.appendChild(opt);
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
