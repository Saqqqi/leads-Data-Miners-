<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attractive Neumorphic Leads Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <style>
    .neumorphic {
        background: linear-gradient(145deg, #1f2937, #111827);
        box-shadow: 10px 10px 20px rgba(0, 0, 0, 0.25),
            -10px -10px 20px rgba(255, 255, 255, 0.05);
        border-radius: 1.5rem;
    }

    .neumorphic-card {
        background: linear-gradient(145deg, #374151, #1f2937);
        box-shadow: 6px 6px 12px rgba(0, 0, 0, 0.2),
            -6px -6px 12px rgba(255, 255, 255, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .neumorphic-card:hover {
        transform: translateY(-5px);
        box-shadow: 8px 8px 16px rgba(0, 0, 0, 0.3),
            -8px -8px 16px rgba(255, 255, 255, 0.1);
    }

    .neumorphic-input {
        background: linear-gradient(145deg, #374151, #1f2937);
        box-shadow: inset 4px 4px 8px rgba(0, 0, 0, 0.2),
            inset -4px -4px 8px rgba(255, 255, 255, 0.08);
        border: none;
        outline: none;
    }

    .gradient-bg {
        background: linear-gradient(145deg, #1f2937, #111827);
    }

    .delete-btn {
        background: linear-gradient(145deg, #dc2626, #b91c1c);
        box-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2), -2px -2px 4px rgba(255, 255, 255, 0.1);
    }

    .delete-btn:hover {
        background: linear-gradient(145deg, #b91c1c, #dc2626);
    }

    .sidebar {
        width: 16rem;
        flex-shrink: 0;
        height: 100vh;
        position: sticky;
        top: 0;
        background: #1a202c;
        padding: 1rem;
        z-index: 10;
    }

    .main-content {
        margin-left: 16rem;
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
    }

    .responsive-table {
        width: 100%;
        min-width: 0;
    }

    .responsive-table th,
    .responsive-table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 150px;
        padding: 0.75rem 1rem;
    }

    .no-results {
        color: #ef4444;
        /* Red text */
        font-size: 1.125rem;
        /* Larger text */
        margin-top: 1rem;
    }

    @media (max-width: 1024px) {
        .sidebar {
            width: 12rem;
        }

        .main-content {
            margin-left: 12rem;
        }

        .responsive-table th,
        .responsive-table td {
            max-width: 100px;
        }
    }

    @media (max-width: 768px) {
        .sidebar {
            width: 0;
            display: none;
        }

        .main-content {
            margin-left: 0;
            padding: 0.5rem;
        }

        .responsive-table th,
        .responsive-table td {
            max-width: 80px;
        }
    }
    </style>
    <script>
    // Global deleteLead function
    async function deleteLead(id) {
        try {
            const response = await fetch(`api/search.php?action=delete&id=${id}`, {
                method: 'POST'
            });
            const data = await response.json();
            
            if (data.status === 'success') {
                Toastify({
                    text: "Lead deleted successfully",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #00b09b, #96c93d)",
                    }
                }).showToast();
                
                // Refresh the current search
                const phoneInput = document.getElementById('phone_search');
                const emailInput = document.getElementById('email_search');
                
                if (phoneInput && phoneInput.value) {
                    const formData = new FormData();
                    formData.append('phone_search', phoneInput.value);
                    await fetchResults(formData, 'phone');
                }
                if (emailInput && emailInput.value) {
                    const formData = new FormData();
                    formData.append('email_search', emailInput.value);
                    await fetchResults(formData, 'email');
                }
            } else {
                Toastify({
                    text: data.error || "Error deleting lead",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff0000, #ff4444)",
                    }
                }).showToast();
            }
        } catch (error) {
            console.error('Error deleting lead:', error);
            Toastify({
                text: "Error deleting lead",
                duration: 3000,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff0000, #ff4444)",
                }
            }).showToast();
        }
    }

    // Function to handle phone search
    async function handlePhoneSearch() {
        const phoneInput = document.getElementById('phone_search');
        const formData = new FormData();
        formData.append('phone_search', phoneInput.value);
        await fetchResults(formData, 'phone');
    }

    // Function to handle email search
    async function handleEmailSearch() {
        const emailInput = document.getElementById('email_search');
        const formData = new FormData();
        formData.append('email_search', emailInput.value);
        await fetchResults(formData, 'email');
    }

    // Function to fetch and display search results
    async function fetchResults(formData, type) {
        try {
            const userId = localStorage.getItem('id');
            if (!userId) {
                window.location.href = 'login.php';
                return;
            }
            formData.append('userId', userId);

            const searchParams = new URLSearchParams();
            for (const [key, value] of formData.entries()) {
                searchParams.append(key, value);
            }

            const response = await fetch('api/search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: searchParams.toString()
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();
            
            if (data.error === "Access denied: Admin privileges required") {
                Toastify({
                    text: "Access denied: Admin privileges required",
                    duration: 3000,
                    gravity: "top",
                    position: "right",
                    style: {
                        background: "linear-gradient(to right, #ff0000, #ff4444)",
                    }
                }).showToast();
                return;
            }

            // Clear previous results
            const tableId = type === 'phone' ? 'phone-results' : 'email-results';
            const tableBody = document.querySelector(`#${tableId} tbody`);
            tableBody.innerHTML = '';

            // Display new results
            const results = type === 'phone' ? data.phone_results : data.email_results;
            if (results && results.length > 0) {
                results.forEach((lead, index) => {
                    const row = document.createElement('tr');
                    row.className = 'border-t border-gray-700 hover:bg-gray-800';
                    row.innerHTML = `
                        <td class="px-6 py-4">${index + 1}</td>
                        <td class="px-6 py-4">${lead.date || 'N/A'}</td>
                        <td class="px-6 py-4">${lead.name || 'N/A'}</td>
                        <td class="px-6 py-4">${lead.location || 'N/A'}</td>
                        <td class="px-6 py-4">${lead.primaryNumber || 'N/A'}</td>
                        <td class="px-6 py-4">${lead.phone_numbers ? lead.phone_numbers.replace(/[\n,;]+/g, '<br>') : 'N/A'}</td>
                        <td class="px-6 py-4">${lead.emails ? lead.emails.replace(/[\n,;]+/g, '<br>') : 'N/A'}</td>
                        <td class="px-6 py-4">${lead.employee || 'N/A'}</td>
                        <td class="px-6 py-4">${lead.data_source_link || 'N/A'}</td>
                        <td class="px-6 py-4">
                            <button class="delete-btn px-3 py-1 text-white rounded-lg hover:bg-red-700 transition-colors" data-lead-id="${lead.id}">Delete</button>
                        </td>
                    `;
                    tableBody.appendChild(row);
                });

                // Add event listeners to delete buttons
                tableBody.querySelectorAll('.delete-btn').forEach(button => {
                    button.addEventListener('click', function(e) {
                        e.preventDefault();
                        const leadId = this.getAttribute('data-lead-id');
                        deleteLead(leadId);
                    });
                });
            } else {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="10" class="px-6 py-4 text-center text-red-400">No results found</td>`;
                tableBody.appendChild(row);
            }
        } catch (error) {
            console.error('Error:', error);
            Toastify({
                text: "Error fetching results. Please try again.",
                duration: 3000,
                gravity: "top",
                position: "right",
                style: {
                    background: "linear-gradient(to right, #ff0000, #ff4444)",
                }
            }).showToast();
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
   
        const userId = localStorage.getItem('id');

        if (!userId) {
            window.location.href = 'login.php';
            return;
        }

        // Function to check user role
        async function checkUserRole() {
            try {
                const response = await fetch(`api/search.php?userId=${userId}`);
                const data = await response.json();
                
                if (data.error === "Access denied: Admin privileges required") {
    
                    document.querySelectorAll('.neumorphic').forEach(section => {
                        section.style.display = 'none';
                    });
                    
                    Toastify({
                        text: "Access denied: Admin privileges required",
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        style: {
                            background: "linear-gradient(to right, #ff0000, #ff4444)",
                        }
                    }).showToast();
                    
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                    return false;
                }
                return true;
            } catch (error) {
                console.error('Error checking user role:', error);
                return false;
            }
        }

        async function initializePage() {
            const isAdmin = await checkUserRole();
            if (!isAdmin) {
                return;
            }
        }

        initializePage();
    });
    </script>
</head>

<body class="min-h-screen gradient-bg text-gray-200 font-sans flex">

    <?php include('sidebar.php'); ?>

    <main class="main-content p-8">
   
        <div class="neumorphic p-6 mb-8">
            <h3 class="text-xl font-semibold text-white mb-6 tracking-wide">Search Leads by Phone Number</h3>
            <div class="mb-6 flex space-x-4">
                <input type="text" id="phone_search" placeholder="Enter phone number..."
                    class="neumorphic-input w-full max-w-md p-3 rounded-lg text-gray-200 placeholder-gray-400">
                <button type="button" onclick="handlePhoneSearch()"
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                    Search
                </button>
            </div>
            <div class="overflow-x-auto neumorphic-card p-4 rounded-lg">
                <table class="w-full text-sm text-left text-gray-300 responsive-table" id="phone-results">
                    <thead class="text-xs uppercase text-gray-400 bg-gray-700 rounded-t-lg">
                        <tr>
                            <th class="px-6 py-4">Count</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Location</th>
                            <th class="px-6 py-4">Primary</th>
                            <th class="px-6 py-4">Phones</th>
                            <th class="px-6 py-4">Emails</th>
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Source</th>
                            <th class="px-6 py-4">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <div class="neumorphic p-6">
            <h3 class="text-xl font-semibold text-white mb-6 tracking-wide">Search Leads by Email</h3>
            <div class="mb-6 flex space-x-4">
                <input type="text" id="email_search" placeholder="Enter email..."
                    class="neumorphic-input w-full max-w-md p-3 rounded-lg text-gray-200 placeholder-gray-400">
                <button type="button" onclick="handleEmailSearch()"
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition duration-200">
                    Search
                </button>
            </div>
            <div class="overflow-x-auto neumorphic-card p-4 rounded-lg">
                <table class="w-full text-sm text-left text-gray-300 responsive-table" id="email-results">
                    <thead class="text-xs uppercase text-gray-400 bg-gray-700 rounded-t-lg">
                        <tr>
                            <th class="px-6 py-4">Count</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Location</th>
                            <th class="px-6 py-4">Primary</th>
                            <th class="px-6 py-4">Phones</th>
                            <th class="px-6 py-4">Emails</th>
                            <th class="px-6 py-4">Employee</th>
                            <th class="px-6 py-4">Source</th>
                            <th class="px-6 py-4">Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </main>
</body>

</html>