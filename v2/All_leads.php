<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Employee Leads Dashboard</title>
</head>

<body class="bg-gray-950">
    <?php include('../sidebar.php'); ?>
    <div id="dashboard-container" class="max-w-7xl mx-auto rounded-2xl shadow-xl bg-gray-800 p-8">
        <h1 class="text-3xl font-extrabold text-white mb-6">All Employee Leads</h1>

        <!-- Tabs -->
        <div class="flex space-x-4 border-b-2 border-gray-200 mb-8">
            <button class="tab-btn px-6 py-3 font-semibold rounded-t-lg bg-blue-200 text-blue-700"
                data-tab="daily">Daily</button>
            <button
                class="tab-btn px-6 py-3 font-semibold rounded-t-lg bg-gray-100 hover:bg-blue-100 hover:text-blue-700"
                data-tab="weekly">Weekly</button>
            <button
                class="tab-btn px-6 py-3 font-semibold rounded-t-lg bg-gray-100 hover:bg-blue-100 hover:text-blue-700"
                data-tab="monthly">Monthly</button>
        </div>

        <!-- Tab Contents -->
        <div id="daily" class="tab-content">
            <h2 class="text-xl font-bold text-white mb-4">Daily Leads</h2>
            <div class="grid grid-cols-7 gap-4 mb-6" id="daily-leads"></div>
            <p class="text-md font-semibold text-gray-300">Total: <span class="text-indigo-600 text-lg"
                    id="daily-total">0</span></p>
        </div>
        <div id="weekly" class="tab-content hidden">
            <h2 class="text-xl font-bold text-white mb-4">Weekly Leads</h2>
            <div class="space-y-6" id="weekly-leads"></div>
            <p class="text-md font-semibold text-gray-300">Total: <span class="text-teal-600 text-lg"
                    id="weekly-total">0</span></p>
        </div>
        <div id="monthly" class="tab-content hidden">
            <h2 class="text-xl font-bold text-white mb-4">Monthly Leads</h2>
            <div class="grid grid-cols-4 gap-6 mb-6" id="monthly-leads"></div>
            <p class="text-md font-semibold text-gray-300">Total: <span class="text-purple-600 text-lg"
                    id="monthly-total">0</span></p>
        </div>
    </div>

    <div id="error-container" class="hidden max-w-7xl mx-auto rounded-2xl shadow-xl bg-gray-800 p-8 flex items-center justify-center">
    <p id="error-message" class="text-xl text-red-400 font-semibold"></p>
</div>
    <script>
    const apiUrl = '../api/All_leads.php';
    const tabs = {
        daily: loadDailyLeads,
        weekly: loadWeeklyLeads,
        monthly: loadMonthlyLeads
    };

    async function fetchData(action, params = '') {
        const userId = localStorage.getItem('id');
        const res = await fetch(`${apiUrl}?action=${action}${params}`, {
            headers: {
                'User-ID': userId,
                'Content-Type': 'application/json'
            }
        });

        const data = await res.json();

        // Check for access denied error
        if (data.error === "Access denied: Admin privileges required") {
            showError(data.error);
            throw new Error(data.error);
        }

        if (!res.ok) throw new Error('Failed to fetch data');
        return data;
    }

    function showError(message) {
        document.getElementById('dashboard-container').classList.add('hidden');
        const errorContainer = document.getElementById('error-container');
        errorContainer.classList.remove('hidden');
        document.getElementById('error-message').textContent = message;
    }

    // Create a lead card element
    function createLeadCard({
        classes,
        text,
        value,
        clickHandler
    }) {
        const div = document.createElement('div');
        div.className = `text-center ${classes} p-4 rounded-lg shadow-sm hover:shadow-md transition-all cursor-pointer`;
        div.innerHTML =
            `<p class='text-sm font-medium text-gray-600'>${text}</p><p class='text-lg font-bold'>${value}</p>`;
        div.addEventListener('click', clickHandler);
        return div;
    }

    // Load daily leads
    async function loadDailyLeads() {
        try {
            const data = await fetchData('daily');
            const container = document.getElementById('daily-leads');
            container.innerHTML = '';
            data.forEach(lead => {
                container.appendChild(createLeadCard({
                    classes: 'bg-gradient-to-br from-blue-50 to-indigo-50',
                    text: lead.date,
                    value: lead.daily_leads,
                    clickHandler: () => showModal('daily', lead.date, fetchData('by_date',
                        `&date=${lead.date}`))
                }));
            });
            document.getElementById('daily-total').textContent = (await fetchData('total')).total_leads;
        } catch (error) {
            console.error(error);
        }
    }

    // Load weekly leads
    async function loadWeeklyLeads() {
        try {
            const data = await fetchData('weekly');
            const container = document.getElementById('weekly-leads');
            container.innerHTML = '';
            const grouped = Object.groupBy(data, ({
                year,
                week_number
            }) => `${year}-${new Date(year, 0, 1 + (week_number - 1) * 7).getMonth() + 1}`);
            for (const [key, weeks] of Object.entries(grouped)) {
                const [year, month] = key.split('-');
                const monthDiv = document.createElement('div');
                monthDiv.innerHTML =
                    `<h3 class="text-lg font-semibold text-gray-300 mb-2">${new Date(year, month - 1).toLocaleString('default', { month: 'long' })} ${year}</h3>`;
                const grid = document.createElement('div');
                grid.className = 'grid grid-cols-4 gap-4';
                weeks.forEach(lead => {
                    grid.appendChild(createLeadCard({
                        classes: 'bg-gradient-to-br from-green-50 to-teal-50',
                        text: `Week ${lead.week_number}`,
                        value: lead.weekly_leads,
                        clickHandler: () => showModal('weekly', {
                            year,
                            week: lead.week_number
                        }, fetchData('by_week',
                            `&year=${year}&week=${lead.week_number}`))
                    }));
                });
                monthDiv.appendChild(grid);
                container.appendChild(monthDiv);
            }
            document.getElementById('weekly-total').textContent = (await fetchData('total')).total_leads;
        } catch (error) {
            console.error(error);
        }
    }

    // Load monthly leads
    async function loadMonthlyLeads() {
        try {
            const data = await fetchData('monthly');
            const container = document.getElementById('monthly-leads');
            container.innerHTML = '';
            data.forEach(lead => {
                container.appendChild(createLeadCard({
                    classes: 'bg-gradient-to-br from-purple-50 to-pink-50',
                    text: `${lead.month_name} ${lead.year}`,
                    value: lead.monthly_leads,
                    clickHandler: () => showModal('monthly', {
                        year: lead.year,
                        month: lead.month_number
                    }, fetchData('by_month',
                        `&year=${lead.year}&month=${lead.month_number}`))
                }));
            });
            document.getElementById('monthly-total').textContent = (await fetchData('total')).total_leads;
        } catch (error) {
            console.error(error);
        }
    }

    // Get date range for modal title
    function getDateRange(type, dateData) {
        if (type === 'daily') {
            return `${dateData}`;
        } else if (type === 'weekly') {
            const {
                year,
                week
            } = dateData;
            const start = new Date(year, 0, (week - 1) * 7 + 1);
            const end = new Date(start);
            end.setDate(start.getDate() + 6);
            return `Week ${week} (${year}) [${start.toISOString().split('T')[0]} - ${end.toISOString().split('T')[0]}]`;
        } else if (type === 'monthly') {
            const {
                year,
                month
            } = dateData;
            const start = new Date(year, month - 1, 1);
            const end = new Date(year, month, 0);
            return `${new Date(year, month - 1).toLocaleString('default', { month: 'long' })} ${year} [${start.toISOString().split('T')[0]} - ${end.toISOString().split('T')[0]}]`;
        }
    }

    // Show modal with lead details
    async function showModal(type, dateData, dataPromise) {
        try {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            const leads = await dataPromise;
            const title = getDateRange(type, dateData);
            modal.innerHTML = `
                <div class="bg-white rounded-lg p-6 w-full max-w-full max-h-[90vh] overflow-auto relative">
                    <button class="absolute top-2 right-2 text-2xl text-gray-600 hover:text-gray-800">×</button>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">${title} (${leads.length} leads)</h3>
                    <table class="w-full text-sm text-gray-700 table-auto">
    <thead class="bg-gray-100">
        <tr>${['Count', 'Date', 'Name', 'Location', 'Primary_Number', 'Phones', 'Emails', 'Employee', 'Source'].map(h => `<th class="py-2 px-4 whitespace-nowrap">${h}</th>`).join('')}
        </tr>
    </thead>
    <tbody>${leads.length ? leads.map((l, i) => `
        <tr class="border-b">
            <td class="py-2 px-4">${i + 1}</td>
            <td class="py-2 px-4">${l.date}</td>
            <td class="py-2 px-4">${l.name}</td>
            <td class="py-2 px-4 ${!l.location ? 'text-red-600' : ''}">${l.location || 'N/A'}</td>
            <td class="py-2 px-4">${l.primaryNumber}</td>
            <td class="py-2 px-4">${l.phone_numbers.split(',').join('<br>')}</td>
            <td class="py-2 px-4">${l.emails.split(',').join('<br>')}</td>
            <td class="py-2 px-4 ${!l.employee_name ? 'text-red-600' : ''}">${l.employee_name || 'N/A'}</td>
            <td class="py-2 px-4">
                <a href="${l.data_source_link || '#'}" target="_blank" class="text-blue-600 ${!l.data_source_link ? 'text-red-600' : ''}" title="${l.data_source_link || 'No link available'}">${l.data_source_link ? 'Link' : 'N/A'}</a>
            </td>
        </tr>`).join('') : '<tr><td colspan="10" class="py-2 px-4 text-center">No data available</td></tr>'}
    </tbody>
</table>
                    <div class="mt-4 flex space-x-4">
                        <button class="close-btn px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Close</button>
                        <button class="download-btn px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700" data-title="${title}">Download</button>
                    </div>
                </div>`;
            document.body.appendChild(modal);

            modal.querySelector('.close-btn').addEventListener('click', () => modal.remove());
            modal.querySelector('.download-btn').addEventListener('click', (e) => downloadExcel(e.target.dataset
                .title,
                modal.querySelector('table')));
            modal.querySelector('.absolute').addEventListener('click', () => modal.remove());
        } catch (error) {
            console.error(error);
        }
    }

    // Download data as CSV
    function downloadExcel(title, table) {
    const rows = Array.from(table.rows);
    const csv = rows.map(row => {
        const cells = Array.from(row.cells).map((cell, index) => {
            if (index === 8) { // "Source" column
                const link = cell.querySelector('a');
                const href = link ? link.href : '';
                // Treat these as "empty" or invalid links
                if (!href || 
                    href === '#' || 
                    href === 'http://localhost/leads/All_leads#' || 
                    href === 'http://localhost/leads/v2/All_leads.php#') {
                    return '"N/A"'; // Explicitly set to "N/A"
                }
                return `"${href.replace(/"/g, '""')}"`; // Return the actual link if valid
            }
            return `"${cell.innerText.replace(/"/g, '""')}"`;
        });
        return cells.join(',');
    }).join('\n');

    const blob = new Blob([csv], {
        type: 'text/csv'
    });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `${title.replace(/\s+/g, '_')}.csv`;
    link.click();
}
    // Initialize tab functionality
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.tab-btn').forEach(b => {
                b.classList.remove('bg-blue-200', 'text-blue-700');
                b.classList.add('bg-gray-100', 'text-gray-700');
            });
            document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));

            btn.classList.remove('bg-gray-100', 'text-gray-700');
            btn.classList.add('bg-blue-200', 'text-blue-700');
            document.getElementById(btn.dataset.tab).classList.remove('hidden');
            tabs[btn.dataset.tab]();
        });
    });

    // Load initial data
    loadDailyLeads();
    </script>
</body>

</html>