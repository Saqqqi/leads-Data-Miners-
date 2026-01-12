<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <title>Monthly Leads</title>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(30, 41, 59, 0.5);
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.3);
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }
        .data-cell {
            max-width: 300px;
            min-width: 200px;
        }
        .glass-effect {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(148, 163, 184, 0.1);
        }
        .hover-glass:hover {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(12px);
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 8s ease infinite;
        }
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .button-glow:hover {
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.5);
        }
        .table-row-hover:hover {
            transform: translateX(4px);
            transition: all 0.3s ease;
        }
    </style>
</head>
<body class="flex min-h-screen text-white bg-gradient-to-br from-slate-900 via-gray-900 to-slate-800">
    <?php include('sidebar.php'); ?>

    <div class="flex-1 p-8 ml-64 overflow-y-auto custom-scrollbar">
        <!-- Header Section -->
        <div class="glass-effect rounded-2xl p-8 mb-8 shadow-2xl">
            <div class="flex justify-between items-center mb-8">
                <div class="space-x-4 flex items-center">
                    <button id="download-btn" class="group relative bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-600 hover:to-teal-500 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-emerald-500/30">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span class="relative">Download CSV</span>
                        </div>
                    </button>
                    <button onclick="window.location.reload();" class="group relative bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-blue-500/30">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-2 transition-transform group-hover:rotate-180 duration-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="relative">Refresh</span>
                        </div>
                    </button>
                </div>
                <h2 class="text-3xl font-bold animate-gradient bg-gradient-to-r from-white via-blue-100 to-white bg-clip-text text-transparent" id="employee-info"></h2>
            </div>

            <!-- Filter Section -->
            <div class="flex items-end gap-6">
                <div class="flex-1 max-w-xs">
                    <label for="date-filter" class="block text-sm font-medium text-blue-100 mb-2">Date Filter</label>
                    <select id="date-filter" class="w-full bg-slate-800/50 text-white rounded-xl border border-slate-600/50 py-3 px-4 focus:ring-2 focus:ring-blue-500/50 focus:border-transparent transition-all duration-300 shadow-inner hover:bg-slate-800/70">
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="this_month" selected>This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="custom_range">Custom Range</option>
                    </select>
                </div>

                <div id="custom-date-picker" class="hidden flex gap-6 items-center flex-1">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-blue-100 mb-2">Start Date</label>
                        <input type="date" id="start-date" class="w-full bg-slate-800/50 text-white rounded-xl border border-slate-600/50 py-3 px-4 focus:ring-2 focus:ring-blue-500/50 shadow-inner hover:bg-slate-800/70 transition-all duration-300">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-blue-100 mb-2">End Date</label>
                        <input type="date" id="end-date" class="w-full bg-slate-800/50 text-white rounded-xl border border-slate-600/50 py-3 px-4 focus:ring-2 focus:ring-blue-500/50 shadow-inner hover:bg-slate-800/70 transition-all duration-300">
                    </div>
                    <button id="apply-custom-range" class="bg-gradient-to-r from-blue-500 to-indigo-500 hover:from-blue-600 hover:to-indigo-600 text-white font-semibold py-3 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-blue-500/30 mt-7">
                        Apply Range
                    </button>
                </div>
            </div>
        </div>

        <!-- Table Section -->
        <div class="glass-effect rounded-2xl shadow-2xl overflow-hidden">
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-slate-900/90 to-slate-800/90">
                            <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 w-16">#</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100">Lead Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100">Location</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 data-cell">Phone Numbers</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 data-cell">Primary Number</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100 data-cell">Email</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-blue-100">Date</th>
                        </tr>
                    </thead>
                    <tbody id="leads-table-body" class="divide-y divide-slate-700/30"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const employeeId = urlParams.get('employee_id');
        const loginUserId = localStorage.getItem('id');
        let currentLeads = [];

        function formatData(data) {
            return data.split(',')
                .filter(item => item.trim())
                .map(item => `
                    <div class="px-3 py-2 rounded-xl bg-slate-700/30 hover:bg-slate-700/50 text-sm mb-2 break-all backdrop-blur-sm border border-slate-600/20 transition-all duration-300 transform hover:translate-x-1 hover:shadow-lg">
                        ${item.trim()}
                    </div>
                `).join('');
        }

        function fetchLeads(filter = 'this_month', startDate = '', endDate = '') {
            const apiUrl = `api/lead_listing_monthly.php?employee_id=${employeeId}&filter=${filter}` +
                (filter === 'custom_range' ? `&start_date=${startDate}&end_date=${endDate}` : '');

            fetch(apiUrl, {
                headers: { 'Authorization': `Bearer ${loginUserId}` }
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success' && data.data?.leads) {
                    document.getElementById('employee-info').textContent = data.data.employee_name;
                    currentLeads = data.data.leads;
                    
                    const rows = currentLeads.map((lead, index) => `
                        <tr class="hover:bg-slate-700/20 transition-all duration-300 table-row-hover backdrop-blur-sm">
                            <td class="px-6 py-4 text-slate-400 font-medium">${index + 1}</td>
                            <td class="px-6 py-4 font-semibold text-white">${lead.lead_name}</td>
                            <td class="px-6 py-4 text-slate-300">${lead.lead_location}</td>
                            <td class="px-6 py-4 data-cell">${formatData(lead.phone_numbers)}</td>
                            <td class="px-6 py-4 data-cell">${formatData(lead.primaryNumber)}</td>
                            <td class="px-6 py-4 data-cell">${formatData(lead.emails)}</td>
                            <td class="px-6 py-4 text-slate-300">${lead.lead_date}</td>
                        </tr>
                    `).join('');
                    
                    document.getElementById('leads-table-body').innerHTML = rows || '<tr><td colspan="7" class="px-6 py-8 text-center text-slate-500 text-sm">No leads found</td></tr>';
                }
            })
            .catch(err => console.error("Fetch Error:", err));
        }

        // Initial load
        fetchLeads();

        // Date filter handling
        document.getElementById('date-filter').addEventListener('change', function() {
            const filter = this.value;
            document.getElementById('custom-date-picker').classList.toggle('hidden', filter !== 'custom_range');
            if (filter !== 'custom_range') fetchLeads(filter);
        });

        // Custom date range handling
        document.getElementById('apply-custom-range').addEventListener('click', function() {
            const startDate = document.getElementById('start-date').value;
            const endDate = document.getElementById('end-date').value;
            if (startDate && endDate) fetchLeads('custom_range', startDate, endDate);
        });

        // Download handling
        document.getElementById('download-btn').addEventListener('click', function() {
            if (!currentLeads?.length) {
                alert("No data available to download");
                return;
            }

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'export_leads.php';
            
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'leads_data';
            input.value = JSON.stringify(currentLeads);
            
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });
    };
    </script>
</body>
</html>