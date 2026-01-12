<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leads Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-800 font-sans">
    <?php include('sidebar.php'); ?>
    <!-- Dashboard Container -->
    <div class="max-w-7xl mx-auto p-6 ml-64">

        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-white">Leads Dashboard</h1>
            <div class="flex items-center space-x-4">
                <p class="text-lg text-gray-200">Welcome, Admin!</p>
                <button
                    class="bg-cyan-600 text-white px-4 py-2 rounded-lg hover:bg-cyan-700 transition">Refresh</button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Total Leads Card -->
            <div class="bg-gray-700 p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <h3 class="text-xl font-semibold text-white">Total Leads</h3>
                <p class="text-4xl font-bold text-cyan-400 mt-2">1,234</p>
                <div class="mt-4">
                    <div class="w-full bg-gray-600 rounded-full h-2">
                        <div class="bg-cyan-400 h-2 rounded-full" style="width: 75%;"></div>
                    </div>
                    <p class="text-sm text-gray-300 mt-2">+12% from last month</p>
                </div>
            </div>

            <!-- Weekly Leads Card -->
            <div class="bg-gray-700 p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <h3 class="text-xl font-semibold text-white">Weekly Leads</h3>
                <p class="text-4xl font-bold text-green-400 mt-2">256</p>
                <div class="mt-4">
                    <div class="w-full bg-gray-600 rounded-full h-2">
                        <div class="bg-green-400 h-2 rounded-full" style="width: 60%;"></div>
                    </div>
                    <p class="text-sm text-gray-300 mt-2">+8% from last week</p>
                </div>
            </div>

            <!-- Monthly Leads Card -->
            <div class="bg-gray-700 p-6 rounded-lg shadow-lg hover:shadow-xl transition">
                <h3 class="text-xl font-semibold text-white">Monthly Leads</h3>
                <p class="text-4xl font-bold text-purple-400 mt-2">789</p>
                <div class="mt-4">
                    <div class="w-full bg-gray-600 rounded-full h-2">
                        <div class="bg-purple-400 h-2 rounded-full" style="width: 85%;"></div>
                    </div>
                    <p class="text-sm text-gray-300 mt-2">+15% from last month</p>
                </div>
            </div>
        </div>

        <!-- Employees Leads Table -->
        <div class="bg-gray-700 rounded-lg shadow-lg p-6 mb-8">
            <h2 class="text-2xl font-bold text-white mb-6">Employees Leads</h2>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full bg-gray-700">
                    <thead>
                        <tr class="bg-gray-600">
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Employee</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Designation</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Weekly Leads</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Monthly Leads</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Progress</th>
                            <th class="py-3 px-6 text-left text-sm font-semibold text-white">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-600">
                        <!-- Employee 1 -->
                        <tr class="hover:bg-gray-600 transition">
                            <td class="py-4 px-6 text-sm text-white">John Doe</td>
                            <td class="py-4 px-6 text-sm text-gray-300">Sales Manager</td>
                            <td class="py-4 px-6 text-sm text-gray-300">45</td>
                            <td class="py-4 px-6 text-sm text-gray-300">189</td>
                            <td class="py-4 px-6">
                                <div class="w-full bg-gray-600 rounded-full h-2">
                                    <div class="bg-cyan-400 h-2 rounded-full" style="width: 75%;"></div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <button class="text-sm text-cyan-400 hover:text-cyan-300">View Details</button>
                            </td>
                        </tr>

                        <!-- Employee 2 -->
                        <tr class="hover:bg-gray-600 transition">
                            <td class="py-4 px-6 text-sm text-white">Jane Smith</td>
                            <td class="py-4 px-6 text-sm text-gray-300">Marketing Executive</td>
                            <td class="py-4 px-6 text-sm text-gray-300">32</td>
                            <td class="py-4 px-6 text-sm text-gray-300">145</td>
                            <td class="py-4 px-6">
                                <div class="w-full bg-gray-600 rounded-full h-2">
                                    <div class="bg-green-400 h-2 rounded-full" style="width: 60%;"></div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <button class="text-sm text-green-400 hover:text-green-300">View Details</button>
                            </td>
                        </tr>

                        <!-- Employee 3 -->
                        <tr class="hover:bg-gray-600 transition">
                            <td class="py-4 px-6 text-sm text-white">Alice Johnson</td>
                            <td class="py-4 px-6 text-sm text-gray-300">Business Analyst</td>
                            <td class="py-4 px-6 text-sm text-gray-300">28</td>
                            <td class="py-4 px-6 text-sm text-gray-300">120</td>
                            <td class="py-4 px-6">
                                <div class="w-full bg-gray-600 rounded-full h-2">
                                    <div class="bg-purple-400 h-2 rounded-full" style="width: 85%;"></div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <button class="text-sm text-purple-400 hover:text-purple-300">View Details</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Charts and Activity Feed -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Weekly Leads Chart -->
            <div class="bg-gray-700 rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-6">Weekly Leads Trend</h2>
                <div class="h-64 bg-gray-600 rounded-lg flex items-center justify-center">
                    <p class="text-gray-400">Chart Placeholder</p>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="bg-gray-700 rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-white mb-6">Recent Activity</h2>
                <div class="space-y-4">
                    <!-- Activity 1 -->
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-cyan-600 rounded-full flex items-center justify-center text-white">
                            <span>JD</span>
                        </div>
                        <div>
                            <p class="text-sm text-white">John Doe added 5 new leads.</p>
                            <p class="text-xs text-gray-400">2 hours ago</p>
                        </div>
                    </div>

                    <!-- Activity 2 -->
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white">
                            <span>JS</span>
                        </div>
                        <div>
                            <p class="text-sm text-white">Jane Smith updated 3 leads.</p>
                            <p class="text-xs text-gray-400">4 hours ago</p>
                        </div>
                    </div>

                    <!-- Activity 3 -->
                    <div class="flex items-center space-x-4">
                        <div class="w-10 h-10 bg-purple-600 rounded-full flex items-center justify-center text-white">
                            <span>AJ</span>
                        </div>
                        <div>
                            <p class="text-sm text-white">Alice Johnson closed 2 deals.</p>
                            <p class="text-xs text-gray-400">1 day ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-400">
            &copy; 2023 Leads Dashboard. All rights reserved.
        </div>
    </div>

</body>

</html>

<script>
const loginUserId = localStorage.getItem('id');

function displayErrorMessage(message, employeeName, leadCount) {
    document.getElementById('main-content').style.display = 'none';

    const errorContainer = document.createElement('div');


    errorContainer.innerHTML = `
        
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
        console.log(data)
        if (data.status === 'error') {
            displayErrorMessage(data.message, data.employee_name, data.lead_count);
        } else {
            renderEmployeeLeads(data.data);
            displayDate(data.date_range.start);
        }
    })
    .catch(error => console.error('Error fetching employee data:', error));

function renderEmployeeLeads(data) {
    const employeeListContainer = document.getElementById('employee-list');
    employeeListContainer.innerHTML = '';

    data.forEach(employee => {
        const employeeCard = document.createElement('div');




        employeeListContainer.appendChild(employeeCard);

        // Add event listener for the "View Daily" button
        const viewDailyButton = employeeCard.querySelector('.view-daily');
        viewDailyButton.addEventListener('click', function() {
            // Get the employee ID from the button's data-attribute
            const employeeId = this.getAttribute('data-employee-id');

            // Redirect to the new page with the employee_id as a query parameter
            window.location.href = `/leads/daily_leads?employee_id=${employeeId}`;
        });
    });
}

// Function to display the date dynamically
function displayDate(date) {
    const dateElement = document.getElementById('today-date');
    const formattedDate = new Date(date).toLocaleDateString(); // Format the date to a readable format
    dateElement.textContent = 'Date: ' + formattedDate;
}
</script>