<?php
// Register User Log submenu under gs_user post type
add_action('admin_menu', function () {
    add_submenu_page(
        'edit.php?post_type=gs_user',
        'User Log',
        'User Log',
        'manage_options',
        'user-log',
        'gs_render_user_log_page'
    );
});

// Render the User Log admin page
function gs_render_user_log_page() {
    $log_path = plugin_dir_path(__DIR__) . 'gate-logs.csv';
    $logs = [];

    if (file_exists($log_path)) {
        $rows = array_map('str_getcsv', file($log_path));
        foreach ($rows as $row) {
            $logs[] = array_map('trim', array_map(function ($v) {
                return trim($v, '"');
            }, $row));
        }
        $logs = array_reverse($logs);
    }

    echo '<div class="wrap"><h1>User Log</h1>';
    ?>
    <style>
        .gs-filters { margin: 15px 0; display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }
        .gs-filters select, .gs-filters input[type="text"], .gs-filters input[type="date"] {
            padding: 5px 8px;
            font-size: 14px;
            min-width: 150px;
        }
        table.gs-table th, table.gs-table td {
            text-align: left;
            padding: 8px;
            border: 1px solid #ddd;
        }
        table.gs-table {
            margin-top: 10px;
            width: 100%;
            border-collapse: collapse;
        }
        table.gs-table thead {
            background-color: #f1f1f1;
        }
        .gs-search { flex-grow: 1; }
    </style>

    <div class="gs-filters">
        <input type="date" id="filter-start-date">
        <input type="date" id="filter-end-date">
        <select id="filter-type">
            <option value="">All Types</option>
            <?php
            $types = array_unique(array_filter(array_column($logs, 3)));
            foreach ($types as $type) {
                echo '<option value="' . esc_attr($type) . '">' . esc_html($type) . '</option>';
            }
            ?>
        </select>
        <input type="text" id="filter-search" class="gs-search" placeholder="Search by name or barcode...">
    </div>

    <table class="gs-table widefat fixed striped" id="log-table">
        <thead><tr>
            <th>Name</th><th>College</th><th>Course</th><th>Type</th><th>Barcode</th><th>Time In</th><th>Time</th>
        </tr></thead>
        <tbody>
        <?php foreach ($logs as $log):
            $name = esc_html($log[0] ?? '');
            $college = esc_html($log[1] ?? '');
            $course = esc_html($log[2] ?? '');
            $type = esc_html($log[3] ?? '');
            $barcode = esc_html($log[4] ?? '');

            $timeInRaw = $log[5] ?? '';
            $timeInPH = $timeOnlyPH = '';

            if (!empty($timeInRaw)) {
                try {
                    $utc = new DateTime($timeInRaw, new DateTimeZone('UTC'));
                    $utc->setTimezone(new DateTimeZone('Asia/Manila'));
                    $timeInPH = esc_html($utc->format('F d, Y'));
                    $timeOnlyPH = esc_html($utc->format('g:i A'));
                } catch (Exception $e) {
                    $timeInPH = esc_html($timeInRaw);
                }
            }
            ?>
            <tr>
                <td><?= $name ?></td><td><?= $college ?></td><td><?= $course ?></td>
                <td><?= $type ?></td><td><?= $barcode ?></td>
                <td><?= $timeInPH ?></td><td><?= $timeOnlyPH ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div id="pagination" style="margin-top: 15px;"></div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const rawRows = <?= json_encode($logs, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const tableBody = document.querySelector('#log-table tbody');
        const pagination = document.getElementById('pagination');
        const maxPerPage = 30;

        const startInput = document.getElementById('filter-start-date');
        const endInput = document.getElementById('filter-end-date');
        const typeFilter = document.getElementById('filter-type');
        const searchInput = document.getElementById('filter-search');

        let filtered = [...rawRows];
        let currentPage = 1;

        const formatRow = row => `
            <tr>
                <td>${row[0] || ''}</td><td>${row[1] || ''}</td><td>${row[2] || ''}</td>
                <td>${row[3] || ''}</td><td>${row[4] || ''}</td><td>${row[5] || ''}</td><td>${row[6] || ''}</td>
            </tr>`;

        const paginate = data => data.slice((currentPage - 1) * maxPerPage, currentPage * maxPerPage);

        function renderPagination(total) {
            const pageCount = Math.ceil(total / maxPerPage);
            pagination.innerHTML = '';
            if (pageCount <= 1) return;

            for (let i = 1; i <= pageCount; i++) {
                const btn = document.createElement('button');
                btn.textContent = i;
                btn.style.marginRight = '5px';
                btn.disabled = (i === currentPage);
                btn.addEventListener('click', () => {
                    currentPage = i;
                    renderTable();
                });
                pagination.appendChild(btn);
            }
        }

        function renderTable() {
            const paginated = paginate(filtered);
            tableBody.innerHTML = paginated.map(formatRow).join('');
            renderPagination(filtered.length);
        }

        function applyFilters() {
            const startDate = startInput.value ? new Date(startInput.value) : null;
            const endDate = endInput.value ? new Date(endInput.value + 'T23:59:59') : null;
            const typeVal = typeFilter.value.toLowerCase();
            const searchVal = searchInput.value.toLowerCase();

            filtered = rawRows.filter(row => {
                const timeIn = row[5] ? new Date(row[5]) : null;
                const matchDate = (!startDate || !endDate) || (timeIn && timeIn >= startDate && timeIn <= endDate);
                const matchType = !typeVal || (row[3] || '').toLowerCase().includes(typeVal);
                const matchSearch = !searchVal || (row[0] || '').toLowerCase().includes(searchVal) || (row[4] || '').toLowerCase().includes(searchVal);
                return matchDate && matchType && matchSearch;
            });

            currentPage = 1;
            renderTable();
        }

        [startInput, endInput, typeFilter, searchInput].forEach(el => el.addEventListener('input', applyFilters));

        renderTable();
    });
    </script>
    <?php
    echo '</div>';
}

// Get all logs from CSV as associative arrays
function gs_get_all_logs() {
    $log_path = plugin_dir_path(__DIR__) . '../../exports/gate-logs.csv';
    if (!file_exists($log_path)) return [];

    $rows = array_map('str_getcsv', file($log_path));
    $header = array_map('strtolower', array_shift($rows));
    $logs = [];

    foreach ($rows as $row) {
        $logs[] = array_combine($header, $row);
    }

    return $logs;
}
