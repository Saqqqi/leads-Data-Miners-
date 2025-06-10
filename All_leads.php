<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Employee Leads Dashboard</title>
</head>

<body class=" bg-gray-950">
<?php include('sidebar.php'); ?>
    
    <!-- Adjusted Container Size and Styling -->
    <div class=" max-w-7xl mx-auto rounded-2xl shadow-xl bg-gray-800 overflow-hidden transform transition-all hover:shadow-2xl">
        <div class="p-8">
            <!-- Employee Name with Gradient -->
            <h1 class="text-3xl font-extrabold  bg-clip-text text-white mb-6">John Doe</h1>

            <!-- Tabs with Modern Styling -->
            <div class="flex space-x-4 border-b-2 border-gray-200 mb-8">
                <button class="tab-button px-6 py-3 text-gray-700 font-semibold rounded-t-lg bg-gray-100 hover:bg-blue-100 hover:text-blue-700 focus:outline-none active" data-tab="daily">Daily</button>
                <button class="tab-button px-6 py-3 text-gray-700 font-semibold rounded-t-lg bg-gray-100 hover:bg-blue-100 hover:text-blue-700 focus:outline-none" data-tab="weekly">Weekly</button>
                <button class="tab-button px-6 py-3 text-gray-700 font-semibold rounded-t-lg bg-gray-100 hover:bg-blue-100 hover:text-blue-700 focus:outline-none" data-tab="monthly">Monthly</button>
            </div>

            <!-- Tab Content -->
            <div id="daily" class="tab-content">
                <h2 class="text-xl font-bold text-white mb-4">Daily Leads (March 2025)</h2>
                <div class="grid grid-cols-7 gap-4 mb-6">
                    <?php
                    for ($day = 1; $day <= 31; $day++) {
                        $leads = rand(5, 20);
                        echo "
                        <div class='text-center bg-gradient-to-br from-blue-50 to-indigo-50 p-4 rounded-lg shadow-sm hover:shadow-md transition-all'>
                            <p class='text-sm font-medium text-gray-500'>$day</p>
                            <p class='text-lg font-bold text-indigo-600'>$leads</p>
                            <button class='mt-2 text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors'>Details</button>
                        </div>";
                    }
                    ?>
                </div>
                <p class="text-md font-semibold text-gray-700">Total Leads: <span class="text-indigo-600 text-lg">245</span></p>
            </div>

            <div id="weekly" class="tab-content hidden">
                <h2 class="text-xl font-bold text-white-800 mb-4">Weekly Leads (March 2025)</h2>
                <div class="grid grid-cols-4 gap-6 mb-6">
                    <?php
                    for ($week = 1; $week <= 4; $week++) {
                        $leads = rand(50, 100);
                        echo "
                        <div class='text-center bg-gradient-to-br from-green-50 to-teal-50 p-5 rounded-xl shadow-md hover:shadow-lg transition-all'>
                            <p class='text-md font-medium text-gray-600'>Week $week</p>
                            <p class='text-2xl font-bold text-teal-600'>$leads</p>
                            <button class='mt-3 text-sm font-semibold text-teal-600 hover:text-teal-800 transition-colors'>Details</button>
                        </div>";
                    }
                    ?>
                </div>
                <p class="text-md font-semibold text-gray-700">Total Leads: <span class="text-teal-600 text-lg">245</span></p>
            </div>

            <div id="monthly" class="tab-content hidden">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Monthly Leads (2025)</h2>
                <div class="grid grid-cols-4 gap-6 mb-6">
                    <?php
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    foreach ($months as $index => $month) {
                        $leads = rand(200, 400);
                        echo "
                        <div class='text-center bg-gradient-to-br from-purple-50 to-pink-50 p-5 rounded-xl shadow-md hover:shadow-lg transition-all'>
                            <p class='text-md font-medium text-gray-600'>$month</p>
                            <p class='text-2xl font-bold text-purple-600'>$leads</p>
                            <button class='mt-3 text-sm font-semibold text-purple-600 hover:text-purple-800 transition-colors'>Details</button>
                        </div>";
                    }
                    ?>
                </div>
                <p class="text-md font-semibold text-gray-700">Total Leads: <span class="text-purple-600 text-lg">3250</span></p>
            </div>
        </div>
    </div>

    <script>
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                tabButtons.forEach(btn => {
                    btn.classList.remove('bg-blue-200', 'text-blue-700', 'border-blue-500');
                    btn.classList.add('bg-gray-100', 'text-gray-700');
                });
                
                tabContents.forEach(content => content.classList.add('hidden'));

                button.classList.remove('bg-gray-100', 'text-gray-700');
                button.classList.add('bg-blue-200', 'text-blue-700', 'border-blue-500');

                const tabId = button.getAttribute('data-tab');
                document.getElementById(tabId).classList.remove('hidden');
            });
        });
    </script>
</body>
</html>