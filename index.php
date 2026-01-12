<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<script>
// Function to update the welcome message with the username from localStorage
function updateWelcomeMessage() {
    // Get the username from localStorage (assuming it's stored under the key "username")
    const username = localStorage.getItem("username") || "Admin"; // Default to "Admin" if not found
    const welcomeElement = document.getElementById("welcomeMessage");

    // Update the text content
    welcomeElement.textContent = `Welcome, ${username}!`;
    const refreshButton = document.getElementById("refreshButton");

    // Add click event listener
    refreshButton.addEventListener("click", function() {
        location.reload(); // Reload the page
    });
}

// Call the function when the page loads
window.onload = updateWelcomeMessage;
</script>

<body class="bg-gray-800 font-sans">
    <?php include('sidebar.php'); ?>
    <!-- Dashboard Container -->
    <div class="max-w-7xl mx-auto p-6 ml-64">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white">Leads Dashboard</h1>
            <div class="flex items-center space-x-4">
                <p id="welcomeMessage" class="text-lg text-red-500">Welcome, Admin!</p>
                <button class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 transition"
                    onclick="location.reload()">Refresh</button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Leads Card -->
            <div class="bg-gray-950 border border-gray-400 p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <h3 class="text-xl font-semibold text-white">Total Leads</h3>
                <p id="total-leads" class="text-4xl font-bold text-cyan-400 mt-2">0</p>
                <div class="mt-4">
                    <div class="w-full bg-gray-600 rounded-full h-2">
                        <div class="bg-cyan-400 h-2 rounded-full" style="width: 100%;"></div>
                    </div>
                    <p class="text-sm text-gray-300 mt-2">+12% from last month</p>
                </div>
            </div>

            <!-- Weekly Leads Card -->
            <div class="bg-gray-950 border border-gray-400 p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <h3 class="text-xl font-semibold text-white">Weekly Leads</h3>
                <p id="weekly-leads" class="text-4xl font-bold text-green-400 mt-2">0</p>
                <div class="mt-4">
                    <div class="w-full bg-gray-600 rounded-full h-2">
                        <div class="bg-green-400 h-2 rounded-full" style="width: 100%;"></div>
                    </div>
                    <p class="text-sm text-gray-300 mt-2">+8% from last week</p>
                </div>
            </div>

            <!-- Monthly Leads Card -->
            <div class="bg-gray-950 border border-gray-400 p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <h3 class="text-xl font-semibold text-white">Monthly Leads</h3>
                <p id="monthly-leads" class="text-4xl font-bold text-purple-400 mt-2">0</p>
                <div class="mt-4">
                    <div class="w-full bg-gray-600 rounded-full h-2">
                        <div class="bg-purple-400 h-2 rounded-full" style="width: 100%;"></div>
                    </div>
                    <p class="text-sm text-gray-300 mt-2">+15% from last month</p>
                </div>
            </div>
        </div>

        <!-- Employees Leads Table -->
        <div class="bg-gray-950 border border-gray-400 rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Employees Leads</h2>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-gray-700">
                    <thead>
                        <tr class="bg-gray-600">
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Employee</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Designation</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Daily Leads</th>
                            <!-- <th class="py-3 px-6 text-left text-sm font-semibold text-white">Monthly Leads</th> -->
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Progress</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody id="employee-leads-table" class="divide-y divide-gray-600">
                        <!-- Employee data will be dynamically inserted here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Charts and Activity Feed -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Weekly Leads Chart -->
            <div class="bg-gray-950 border border-gray-400 rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-6">Weekly Leads Trend</h2>
                <div class="h-64 bg-gray-600 rounded-lg flex items-center justify-center">
                    <p class="text-gray-400">Chart Placeholder</p>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="bg-gray-950 border border-gray-400 rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-6">Recent Activity</h2>
                <div id="activity-feed" class="space-y-4">
                    <!-- Activity data will be dynamically inserted here -->
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-400">
            &copy; 2025 GDS Leads Dashboard. All rights reserved.
        </div>
    </div>

    <script>
    const loginUserId = localStorage.getItem('id');

    function displayErrorMessage(message, employeeName, leadCount) {
        document.getElementById('main-content').style.display = 'none';

        const errorContainer = document.createElement('div');
        errorContainer.classList.add(
            'flex', 'ml-96', 'justify-center', 'items-center', 'text-red-500', 'h-screen', 'bg-gray-800',
            'text-white', 'text-xl', 'font-bold'
        );

        errorContainer.innerHTML = `
                <div class="text-center">
                    <p>${message}</p>
                    ${employeeName ? `<p>Logged-in Employee: ${employeeName}</p>` : ''}
                    ${leadCount !== undefined ? `<p>Your Leads Count: ${leadCount}</p>` : ''}
                </div>
            `;

        document.body.appendChild(errorContainer);
    }

    fetch('api/lead_listing.php', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${loginUserId}`
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data);
            if (data.status === 'error') {
                displayErrorMessage(data.message, data.employee_name, data.lead_count);
            } else {
                // Update the stats cards
                document.getElementById('total-leads').textContent = data.total_leads || 0;
                document.getElementById('weekly-leads').textContent = data.current_week_leads || 0;
                document.getElementById('monthly-leads').textContent = data.current_month_leads || 0;

                // Update the employee leads table
                const employeeLeadsTable = document.getElementById('employee-leads-table');
                employeeLeadsTable.innerHTML = '';

                data.data.forEach(employee => {
                    const row = document.createElement('tr');
                    row.classList.add('hover:bg-gray-600', 'transition');
                    row.innerHTML = `
                        <td class="py-4 px-6 text-sm text-white">${employee.employee_name}</td>
                        <td class="py-4 px-6 text-sm text-gray-300">${employee.designation}</td>
                        <td class="py-4 px-6 text-sm text-gray-300">${employee.lead_count}</td>
                        
                        <td class="py-4 px-6">
                            <div class="w-full bg-gray-600 rounded-full h-2">
                                <div class="bg-cyan-400 h-2 rounded-full" style="width: 100%;"></div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                           <button class="view-daily text-sm text-cyan-400 hover:text-cyan-300" data-employee-id="${employee.owner_id}">View Daily leads</button>

                        </td>
                    `;
                    employeeLeadsTable.appendChild(row);
                    document.querySelectorAll('.view-daily').forEach(button => {
                        button.addEventListener('click', function() {
                            const employeeId = this.getAttribute('data-employee-id');
                            window.location.href =
                                `/daily_leads?employee_id=${employeeId}`;
                        });
                    });

                });

                // Update the activity feed
                const activityFeed = document.getElementById('activity-feed');
                activityFeed.innerHTML = '';

                data.data.forEach(employee => {
                    const activity = document.createElement('div');
                    activity.classList.add('flex', 'items-center', 'space-x-4');
                    activity.innerHTML = `
                        <div class="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center text-white">
                            <span>${employee.employee_name[0]}</span>
                        </div>
                        <div>
                            <p class="text-sm text-white">${employee.latest_lead.employee_name} new leads.</p>
                            <p class="text-xs text-gray-400">${employee.latest_lead.time_ago}</p>
                        </div>
                    `;
                    activityFeed.appendChild(activity);
                });
            }
        })
        .catch(error => console.error('Error fetching employee data:', error));
    </script>
</body>

</html>