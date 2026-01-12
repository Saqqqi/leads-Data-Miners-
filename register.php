<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0-alpha.1/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-800 text-gray-100">

    <div class="w-full h-screen bg-gray-900 p-8 flex justify-center items-center">
        <div class="bg-gray-800 p-8 rounded-lg w-full md:w-1/2">
            <h2 class="text-3xl font-bold text-center text-white mb-6">Register</h2>
            <form id="register-form" class="space-y-4">
                <!-- Name Field -->
                <div>
                    <label for="username" class="block text-white font-medium mb-1">Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username"
                        class="w-full p-2 border border-gray-700 rounded-md bg-gray-800 text-sm text-white focus:ring focus:ring-blue-500 focus:outline-none"
                        required>
                </div>

                <!-- CNIC Field (with max length of 16 characters) -->
                <div>
                    <label for="cnic" class="block text-white font-medium mb-1">CNIC</label>
                    <input type="text" id="cnic" name="cnic" maxlength="13" placeholder="Enter CNIC Number"
                        class="w-full p-2 border border-gray-700 rounded-md bg-gray-800 text-sm text-white focus:ring focus:ring-blue-500 focus:outline-none"
                        required>
                    <p id="cnic-error" class="text-red-500 text-sm hidden">Please enter a valid CNIC number (13 digits
                        only).</p>
                    <p id="cnic-length-error" class="text-red-500 text-sm hidden">CNIC must be exactly 13 digits long.
                    </p>
                </div>

                <!-- Designation Dropdown -->
                <div>
                    <label for="designation" class="block text-white font-medium mb-1">Designation</label>
                    <select id="designation" name="designation"
                        class="w-full p-2 border border-gray-700 rounded-md bg-gray-800 text-sm text-white focus:ring focus:ring-blue-500 focus:outline-none">
                        <option value="lead">Lead</option>
                        <option value="mining">Mining</option>
                        <option value="mindhive">Mindhive</option>
                    </select>
                </div>
                <div>
                    <label for="role" class="block text-white font-medium mb-1">Role</label>
                    <select id="role" name="role"
                        class="w-full p-2 border border-gray-700 rounded-md bg-gray-800 text-sm text-white focus:ring focus:ring-blue-500 focus:outline-none">
                        <option value="employee">Employee</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" id="submit-btn"
                        class="w-full bg-blue-600 text-white p-2 rounded-md hover:bg-blue-700 text-sm focus:ring focus:ring-blue-500 focus:outline-none"
                        disabled>
                        Register
                    </button>
                </div>
            </form>

            <!-- Error and Success Messages -->
            <div id="message" class="mt-4 text-center text-white hidden">
                <p id="message-text"></p>
            </div>
        </div>
    </div>

    <script>
        // Get references to the form and input elements
        const cnicInput = document.getElementById("cnic");
        const submitBtn = document.getElementById("submit-btn");
        const cnicError = document.getElementById("cnic-error");
        const cnicLengthError = document.getElementById("cnic-length-error");

        // Event listener for the CNIC input field
        cnicInput.addEventListener("input", function () {
            const cnicValue = cnicInput.value;
            const isValidCNIC = /^\d{13}$/.test(cnicValue); // Validate CNIC: 16 digits only

            if (cnicValue.length === 13 && isValidCNIC) {
                // Enable the submit button if CNIC is valid
                submitBtn.disabled = false;
                cnicError.classList.add("hidden");
                cnicLengthError.classList.add("hidden");
            } else {
                // Disable the submit button if CNIC is not valid
                submitBtn.disabled = true;

                if (cnicValue.length < 16) {
                    cnicLengthError.classList.remove("hidden");
                    cnicError.classList.add("hidden");
                } else if (!isValidCNIC) {
                    cnicError.classList.remove("hidden");
                    cnicLengthError.classList.add("hidden");
                }
            }
        });

        // Form submission with AJAX
        document.getElementById("register-form").addEventListener("submit", function (event) {
            // Prevent default form submission
            event.preventDefault();

            const formData = new FormData(this);
            const messageText = document.getElementById("message-text");
            const messageDiv = document.getElementById("message");

            // Show loading message
            messageText.innerHTML = "Processing...";
            messageDiv.classList.remove("hidden");

            fetch('api/register.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Show success message
                        messageText.innerHTML = data.message;
                        messageDiv.classList.add("text-green-500");
                        messageDiv.classList.remove("text-red-500");
                    } else {
                        // Show error message
                        messageText.innerHTML = data.message;
                        messageDiv.classList.add("text-red-500");
                        messageDiv.classList.remove("text-green-500");
                    }
                })
                .catch(error => {
                    messageText.innerHTML = 'Something went wrong. Please try again.';
                    messageDiv.classList.add("text-red-500");
                    messageDiv.classList.remove("text-green-500");
                });
        });
    </script>

</body>

</html>