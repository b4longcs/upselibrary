document.addEventListener('DOMContentLoaded', function () {
    const saveButton = document.getElementById('gs-save-meta');

    if (saveButton) {
        saveButton.addEventListener('click', function () {
            const postId = this.dataset.id;
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
                        status.textContent = 'Saved successfully!';
                    } else {
                        status.textContent = 'Error: ' + response.data;
                    }
                })
                .catch((err) => {
                    status.textContent = 'Error: ' + err.message;
                });
        });
    }
});
