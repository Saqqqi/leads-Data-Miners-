<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <title>Document</title>
</head>

<body class="flex bg-gray-700 text-white">
    <?php include('sidebar.php'); ?>

    <div class="flex-1 p-6 bg-gray-700 ml-64 overflow-y-auto">

        <div class="mb-8 w-full">
            <div class="flex justify-between items-center mb-4">
                <!-- Employee Info -->
                <div id="employee-info" class="flex-1">
                    <!-- Employee info will be populated here -->
                </div>
                <!-- Download Button -->

            </div>
        </div>
        <div class="flex justify-center items-center ">
            <button id="download-btn" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Download
            </button>
        </div>

        <div class="relative flex flex-col w-full overflow-auto bg-gray-900 shadow-md rounded-lg bg-clip-border mt-8">
            <table class="w-full text-left table-auto min-w-full z-0">
                <thead>
                    <tr>
                        <th class="p-4 border-b border-gray-600 bg-gray-800">
                            <p class="block text-sm font-normal leading-none text-gray-400">#</p>
                        </th>
                        <th class="p-4 border-b border-gray-600 bg-gray-800">
                            <p class="block text-sm font-normal leading-none text-gray-400">Owner Name</p>
                        </th>
                        <th class="p-4 border-b border-gray-600 bg-gray-800">
                            <p class="block text-sm font-normal leading-none text-gray-400">Location</p>
                        </th>
                        <th class="p-4 border-b border-gray-600 bg-gray-800">
                            <p class="block text-sm font-normal leading-none text-gray-400">Phone Numbers</p>
                        </th>
                        <th class="p-4 border-b border-gray-600 bg-gray-800">
                            <p class="block text-sm font-normal leading-none text-gray-400">Primary Numbers</p>
                        </th>
                        <th class="p-4 border-b border-gray-600 bg-gray-800">
                            <p class="block text-sm font-normal leading-none text-gray-400">Emails</p>
                        </th>
                        <th class="p-4 border-b border-gray-600 bg-gray-800">
                            <p class="block text-sm font-normal leading-none text-gray-400">data_source_link</p>
                        </th>
                    </tr>
                </thead>
                <tbody id="leads-table-body">

                </tbody>
            </table>
        </div>
    </div>

    <script>
        let employeeData = null;

    window.onload = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const employeeId = urlParams.get('employee_id');
        if (!employeeId) {
            alert('Employee ID is missing from the URL.');
            return;
        }
        const loginUserId = localStorage.getItem('id');
        let currentLeads = [];

        fetch(`api/lead_listing.php?employee_id=${employeeId}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${loginUserId}`
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    displayEmployeeInfo(data.data[0]);
                    currentLeads = data.data[0].today_leads;
                    displayLeads(currentLeads);
                    console.log("Current Leads:", currentLeads);
                } else {
                    alert('Failed to fetch employee and lead data');
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                alert('There was an error fetching the data.');
            });

        function displayEmployeeInfo(employee) {
            const employeeInfoDiv = document.getElementById('employee-info');
            employeeInfoDiv.innerHTML = `
                <div class="text-center p-6 bg-gray-800 rounded-lg shadow-lg flex flex-col items-center justify-center">
                    <div class="flex items-center justify-between gap-4 w-[100%]">
                        <p class="text-2xl text-gray-400 mb-2">Designation: <span class="font-medium text-green-600">${employee.designation}</span></p>
                        <p class="text-2xl text-gray-400 mb-2">Lead Count: <span class="font-medium text-green-600">${employee.lead_count}</span></p>
                    </div>
                    <p class="text-5xl mb-8 font-bold text-cyan-600 mb-4">${employee.employee_name}</p>
                </div>
            `;
        }

        function displayLeads(leads) {
            const tableBody = document.getElementById('leads-table-body');
            tableBody.innerHTML = '';

            leads.forEach((lead, index) => {
                const row = document.createElement('tr');
                row.classList.add('hover:bg-gray-700');

                const phoneNumbersList = lead.phone_numbers.split(',').map(number => `<li>${number}</li>`)
                    .join('');
                const primaryNumbersList = lead.primaryNumber.split(',').map(number => `<li>${number}</li>`)
                    .join('');
                const emailsList = lead.emails.split(',').map(email => `<li>${email}</li>`).join('');

                row.innerHTML = `
                    <td class="p-4 border-b border-gray-600">${index + 1}</td>
                    <td class="p-4 border-b border-gray-600">${lead.lead_name}</td>
                    <td class="p-4 border-b border-gray-600">${lead.lead_location}</td>
                    <td class="p-4 border-b border-gray-600">
                        <ul class="list-none pl-4">${phoneNumbersList}</ul>
                    </td>
                    <td class="p-4 border-b border-gray-600">
                        <ul class="list-none pl-4">${primaryNumbersList}</ul>
                    </td>
                    <td class="p-4 border-b border-gray-600">
                        <ul class="list-none pl-4">${emailsList}</ul>
                    </td>
                    <td class="p-4 border-b border-gray-600">${lead.data_source_link || 'N/A'}</td>
                `;

                tableBody.appendChild(row);
            });
        }

        document.getElementById('download-btn').addEventListener('click', function() {
            downloadCSV(employeeData);
        });


        function downloadCSV() {
            console.log("Downloading CSV with currentLeads:", currentLeads);
            if (!currentLeads || currentLeads.length === 0) {
                alert("No data available to download. Please wait for the data to load or check the API response.");
                return;
            }

            let csvContent = '\uFEFF';

            const headers = ['#', 'Owner Name', 'Location', 'Phone Numbers', 'Primary Numbers', 'Emails',
                'Data Source Link'
            ];
            csvContent += headers.join(',') + '\r\n';

            currentLeads.forEach((lead, index) => {
                const rowData = [
                    index + 1,
                    lead.lead_name,
                    lead.lead_location,
                    lead.phone_numbers,
                    lead.primaryNumber,
                    lead.emails,
                    lead.data_source_link || 'N/A'
                ];
                console.log("Row Data:", rowData);
                csvContent += rowData.map(data => `"${data}"`).join(',') + '\r\n';
            });

            console.log("CSV Content:", csvContent);

            const blob = new Blob([csvContent], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.setAttribute('href', url);
            const fileName = currentLeads.length > 0 && employeeData ?
                `${employeeData.employee_name.replace(/\s+/g, '_')}_leads.csv` :
                'leads_export.csv';

            link.setAttribute('download', fileName);

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        }
    };
    </script>
</body>

</html>