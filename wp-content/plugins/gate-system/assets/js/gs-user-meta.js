
document.addEventListener('DOMContentLoaded', function () { 
    const saveButton = document.getElementById('gs-save-meta');

    if (saveButton) {
        saveButton.addEventListener('click', function () {
            let postId = this.dataset.id;
            const fields = {};
            const inputs = document.querySelectorAll('.gs-meta-field');

            inputs.forEach(function (input) {
                const key = input.dataset.key;
                const val = input.value;
                fields[key] = val;
            });

            const status = document.getElementById('gs-save-status');
            status.textContent = 'Saving...';
            saveButton.disabled = true; 

            const formData = new FormData();
            formData.append('action', 'gs_save_user_meta');
            formData.append('nonce', gs_ajax.nonce);
            formData.append('post_id', postId);
            formData.append('fields', JSON.stringify(fields));

            fetch(gs_ajax.ajax_url, {
                method: 'POST',
                body: formData,
            })
                .then((res) => res.json())
                .then((response) => {
                    if (response.success) {
                        status.textContent = response.data.message;
                        
                        if (response.data.post_id) {
                            postId = response.data.post_id;
                            saveButton.dataset.id = postId;

                            // Optional: redirect to edit page if it was just created
                            if (window.location.href.includes('post-new.php')) {
                                window.location.href = `/wp-admin/post.php?post=${postId}&action=edit`;
                            }
                        }
                    } else {
                        status.textContent = 'Error: ' + response.data;
                    }
                })

        });
    }
});

// Add College Functionality
document.addEventListener('DOMContentLoaded', function () {
    const addBtn = document.getElementById('add-college-btn');
    const form = document.getElementById('add-college-form');
    const input = document.getElementById('new-college-name');
    const saveBtn = document.getElementById('save-college-btn');
    const cancelBtn = document.getElementById('cancel-college-btn');
    const select = document.getElementById('gs_college');

    if (!addBtn || !form || !input || !saveBtn || !select) return;

    addBtn.addEventListener('click', () => {
        form.style.display = 'block';
        input.focus();
    });

    cancelBtn.addEventListener('click', () => {
        form.style.display = 'none';
        input.value = '';
    });

    saveBtn.addEventListener('click', () => {
        const newCollege = input.value.trim();
        if (!newCollege) {
            alert('Please enter a college name.');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'gs_add_college');
        formData.append('nonce', gs_ajax.nonce);
        formData.append('college', newCollege);

        fetch(gs_ajax.ajax_url, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const option = document.createElement('option');
                option.value = data.data.college;
                option.text = data.data.college;
                option.selected = true;
                select.appendChild(option);

                alert('College added.');
                input.value = '';
                form.style.display = 'none';
            } else {
                alert('Error: ' + data.data);
            }
        })
        .catch(() => {
            alert('Something went wrong.');
        });
    });
});
