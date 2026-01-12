<div class="w-60 bg-gray-900 text-gray-100 flex flex-col shadow-lg h-screen fixed">
    <div class="px-6 py-4 text-2xl font-bold"><br>
        <img src="assets/gds-logo.png" alt="Logo" class="h-16 mx-auto" />
    </div>
<br><br>
    <nav class="flex-1 px-4">
        <ul>
            <li class="mb-2">
                <a href="leads.globaldigitsolutions.com" class="flex items-center px-4 py-3 space-x-3 hover:bg-gray-700 rounded">
                    <span class="text-xl">🏠</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="input" class="flex items-center px-4 py-3 space-x-3 hover:bg-gray-700 rounded">
                    <span class="text-xl">📝</span>
                    <span>Input</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="employee" class="flex items-center px-4 py-3 space-x-3 hover:bg-gray-700 rounded">
                    <span class="text-xl">👤</span>
                    <span>All employee</span>
                </a>
            </li>
            </li>
            <li class="mb-2">
                <a href="v2/All_leads.php" class="flex items-center px-4 py-3 space-x-3 hover:bg-gray-700 rounded">
                    <span class="text-xl">👤</span>
                    <span>All Leads</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="search_leads.php" class="flex items-center px-4 py-3 space-x-3 hover:bg-gray-700 rounded">
                    <span class="text-xl">👤</span>
                    <span>Search</span>
                </a>
            </li>
            
        </ul>
    </nav>

    <footer class="px-6 py-4 text-sm text-gray-400">
        <div class="flex flex-col items-center mb-12">
            <a id="auth-button"
                class="flex items-center justify-between text-lg shadow-lg rounded-full py-2 px-8 transition-all duration-300">
                <span id="auth-icon"></span>
                <span id="auth-text" class="font-normal"></span>
            </a>
        </div>
        <span>&copy; 2025 Globaldigitalsolutions.</span>
    </footer>

</div>
<script>
    // Simulated authentication check (replace with real authentication logic)
    const username = localStorage.getItem('username'); // Example: Fetch from localStorage
    const userId = localStorage.getItem('id'); // Example: Fetch from localStorage

    const authButton = document.getElementById('auth-button');
    const authIcon = document.getElementById('auth-icon');
    const authText = document.getElementById('auth-text');

    if (username && userId) {
        // User is logged in -> Show "Online" with green button
        authButton.href = "login";
        authButton.classList.add("bg-green-700", "text-white");
        authButton.classList.remove("bg-red-700");
        authText.innerText = "Login";

        // Insert check icon
        // authIcon.innerHTML = '<i class="fa-solid fa-check"></i>';
    } else {
        // User is not logged in -> Show "Login" with red button
        authButton.href = "login";
        authButton.classList.add("bg-red-700", "text-white");
        authButton.classList.remove("bg-green-700");
        authText.innerText = "Login";

        // Insert login icon
        authIcon.innerHTML = '<i class="fa-solid fa-right-to-bracket mr-2"></i>';

    }
</script>