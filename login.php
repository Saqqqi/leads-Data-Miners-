<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.0.0-alpha.1/dist/tailwind.min.css" rel="stylesheet">
    <style>
        input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }

        .form-container {
            height: 400px;
        }

        .form-content {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
    </style>
</head>

<body class="bg-gray-800 text-gray-100">

    <div class="w-full h-screen bg-gray-900 p-8 flex justify-center items-center">
        <div class="bg-gray-800 p-8 rounded-lg w-full md:w-1/2 form-container">
            <div class="flex items-center justify-center m-auto mb-8">
            <img src="assets/gds-logo.png" alt="logo">
            </div>
            <!-- <h2 class="text-3xl font-bold text-center text-white mb-8">LOGIN</h2> -->
            <form id="login-form" class="space-y-4">
                <!-- Secret Key Field -->

                <div class="flex space-x-2 justify-center mb-4">
                    <input type="text" id="secret-key-1" name="secret-key-1" maxlength="1"
                        class="w-16 h-16 text-center text-lg bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none"
                        required oninput="moveFocus(2)" onkeydown="moveFocusBack(0, event)">
                    <input type="text" id="secret-key-2" name="secret-key-2" maxlength="1"
                        class="w-16 h-16 text-center text-lg bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none"
                        required oninput="moveFocus(3)" onkeydown="moveFocusBack(1, event)">
                    <input type="text" id="secret-key-3" name="secret-key-3" maxlength="1"
                        class="w-16 h-16 text-center text-lg bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none"
                        required oninput="moveFocus(4)" onkeydown="moveFocusBack(2, event)">
                    <input type="text" id="secret-key-4" name="secret-key-4" maxlength="1"
                        class="w-16 h-16 text-center text-lg bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none"
                        required oninput="moveFocus(5)" onkeydown="moveFocusBack(3, event)">
                    <input type="text" id="secret-key-5" name="secret-key-5" maxlength="1"
                        class="w-16 h-16 text-center text-lg bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none"
                        required oninput="moveFocus(6)" onkeydown="moveFocusBack(4, event)">
                    <input type="text" id="secret-key-6" name="secret-key-6" maxlength="1"
                        class="w-16 h-16 text-center text-lg bg-gray-700 text-white border border-gray-600 rounded-md focus:outline-none"
                        required onkeydown="moveFocusBack(5, event)">
                </div>


                <p id="secret-key-error" class="text-red-500 text-sm hidden">Invalid Secret Key. Please enter a valid
                    6-digit key.</p>

                <!-- Login Button -->
                <div class="text-center mt-8">
                    <button type="submit" id="login-btn"
                        class="w-1/4 mt-4 bg-cyan-500 text-black font-semibold py-4 px-8 rounded-md hover:bg-cyan-600 uppercase text-xl drop-shadow-md ">
                        Login
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
        function moveFocus(nextBoxIndex) {
            const nextBox = document.getElementById(`secret-key-${nextBoxIndex}`);
            if (nextBox) {
                nextBox.focus();
            }
        }

        function moveFocusBack(prevBoxIndex, event) {
            if (event.key === 'Backspace') {
                const currentBox = document.getElementById(`secret-key-${prevBoxIndex + 1}`);
                const prevBox = document.getElementById(`secret-key-${prevBoxIndex}`);

                // Clear the current input's value if it exists
                if (currentBox && currentBox.value.length > 0) {
                    currentBox.value = ''; // Clear the current input
                } else if (prevBox) {
                    // If the current input is empty, move focus to the previous input
                    prevBox.focus();
                }
            }
        }

        document.getElementById("login-form").addEventListener("submit", function (event) {
            // Prevent default form submission
            event.preventDefault();

            const secretKey =
                document.getElementById("secret-key-1").value +
                document.getElementById("secret-key-2").value +
                document.getElementById("secret-key-3").value +
                document.getElementById("secret-key-4").value +
                document.getElementById("secret-key-5").value +
                document.getElementById("secret-key-6").value;

            const messageText = document.getElementById("message-text");
            const messageDiv = document.getElementById("message");

            // Check if all boxes are filled
            if (secretKey.length !== 6 || isNaN(secretKey)) {
                document.getElementById("secret-key-error").classList.remove("hidden");
                messageText.innerHTML = "Please enter a valid 6-digit secret key.";
                messageDiv.classList.add("text-red-500");
                messageDiv.classList.remove("text-green-500");
                return;
            }

            // Hide error message if the key is valid
            document.getElementById("secret-key-error").classList.add("hidden");

            // Show loading message
            messageText.innerHTML = "Processing...";
            messageDiv.classList.remove("hidden");

            const formData = new FormData();
            formData.append("secret-key", secretKey);

            fetch('api/login.php', {  // Changed to match your script
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

                        // Store username and designation in localStorage
                        localStorage.setItem('username', data.username);
                        localStorage.setItem('designation', data.designation);
                        localStorage.setItem('id', data.id);

                        // Redirect to the dashboard
                        setTimeout(() => {
                            window.location.href = '/'; // Replace with your dashboard URL
                        }, 1500);  // Redirect after a brief delay (optional)
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