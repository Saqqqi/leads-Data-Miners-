<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Profiles</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-700 text-gray-800 flex">
    <?php include('sidebar.php'); ?>

    <div class="ml-64 w-full">
        <!-- Added w-full here -->
        <br><br>
        <!-- Main Heading -->
        <h1 class="text-4xl font-bold text-center text-white font-sans mb-8">
            All Employees
        </h1>

        <!-- Employee Cards Grid -->
        <div class="flex justify-center px-4">
            <!-- Added padding -->
            <div id="profile-container" class="grid 
        grid-cols-1 
        sm:grid-cols-2 
        md:grid-cols-2 
        lg:grid-cols-3 
        xl:grid-cols-3  
        2xl:grid-cols-5 
        gap-8 w-full max-w-screen-2xl">
                <!-- Adjusted columns and max-width -->
            </div>
        </div>
    </div>
    <script>
    const loginUserId = localStorage.getItem('id');

    function getInitials(name) {
        return name.split(' ').map(n => n[0]).join('').toUpperCase();
    }

    function fetchEmployeeProfiles() {
        fetch('api/employee_api.php', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${loginUserId}`
                }
            })
            .then(response => response.json())
            .then(data => {
                const profileContainer = document.getElementById('profile-container');
                profileContainer.innerHTML = ''; // Clear existing content

                if (data.status === 'success') {
                    if (data.role === 'admin' && Array.isArray(data.employees) && data.employees.length > 0) {
                        // Admin - Show All Employees
                        let employeesHTML = '';
                        data.employees.forEach(employee => {
                            employeesHTML += `
              <div class="rounded-lg border bg-gray-900 px-4 pt-8 pb-10 shadow-lg transition-transform transform hover:scale-105 hover:shadow-xl mb-6">
                <div class="relative mx-auto w-36 rounded-full">
                  
                  <div class="w-16 h-16 bg-cyan-600 text-white flex items-center justify-center text-2xl font-bold rounded-full mx-auto">
                    ${getInitials(employee.username)}
                  </div>
                </div>
                <h1 class="my-1 text-center text-xl leading-8 font-bold text-white">${employee.username}</h1>
                <h3 class="font-lg text-semibold text-center leading-6 text-gray-200">${employee.designation}</h3>
                <ul class="mt-3 divide-y rounded bg-gray-800 px-3 py-2 text-gray-200 shadow-sm hover:text-gray-100 hover:shadow">
                  <li class="flex items-center py-3 text-sm">
                    <span>Role</span>
                    <span class="ml-auto"><span class="rounded-full bg-cyan-200 px-2 py-1 text-xs font-medium text-green-800">${employee.role}</span></span>
                  </li>
                  <li class="flex items-center py-3 text-sm">
                    <button class="ml-auto text-cyan-500 text-center w-full hover:text-cyan-300 view-details" data-id="${employee.id}">View Monthly Record</button>
                  </li>
                </ul>
              </div>
            `;
                        });
                        profileContainer.innerHTML = employeesHTML;

                        // Add event listeners to "View Monthly Record" buttons
                        document.querySelectorAll('.view-details').forEach(button => {
                            button.addEventListener('click', function() {
                                const employeeId = this.getAttribute('data-id');
                                window.location.href =
                                    `/leads.globaldigitsolutions.com/monthly_leads?employee_id=${employeeId}`;
                            });
                        });

                    } else if (data.role === 'employee' && data.employee) {
                        // Employee - Show Their Own Profile
                        profileContainer.innerHTML = `
            <div class="rounded-lg border bg-gray-900 px-4 pt-8 pb-10 shadow-lg transition-transform transform hover:scale-105 hover:shadow-xl">
              <div class="relative mx-auto w-36 rounded-full">
                <span class="absolute right-0 m-3 h-3 w-3 rounded-full bg-green-500 ring-2 ring-green-300 ring-offset-2"></span>
                <div class="w-16 h-16 bg-cyan-600 text-white flex items-center justify-center text-2xl font-bold rounded-full mx-auto">
                  ${getInitials(data.employee.username)}
                </div>
              </div>
              <h1 class="my-1 text-center text-xl leading-8 font-bold text-white">${data.employee.username}</h1>
              <h3 class="font-lg text-semibold text-center leading-6 text-gray-200">${data.employee.designation}</h3>
              <ul class="mt-3 divide-y rounded bg-gray-800 px-3 py-2 text-gray-200 shadow-sm hover:text-gray-100 hover:shadow">
                <li class="flex items-center py-3 text-sm">
                  <span>Role</span>
                  <span class="ml-auto"><span class="rounded-full bg-cyan-200 px-2 py-1 text-xs font-medium text-green-800">${data.employee.role}</span></span>
                </li>
                <li class="flex items-center py-3 text-sm">
                  <a href="#" class="ml-auto text-cyan-500  text-center w-full hover:text-cyan-300">View Monthly Record</a>
                </li>
              </ul>
            </div>
          `;
                    } else {
                        // No records available
                        profileContainer.innerHTML =
                            `<p class="text-gray-600 text-center">No records available.</p>`;
                    }
                } else {
                    // Error handling
                    profileContainer.innerHTML =
                        `<p class="text-red-500 text-center">${data.message || "Failed to load profiles."}</p>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('profile-container').innerHTML =
                    `<p class="text-red-500 text-center">Failed to load profiles.</p>`;
            });
    }

    window.onload = fetchEmployeeProfiles;
    </script>
</body>

</html>