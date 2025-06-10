<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
        integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
    body {
        background: #1F2937;
        /* Darker grey background (gray-500) */
        font-family: 'Inter', sans-serif;
    }

    .popup-card {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #10B981;
        color: white;
        padding: 1.5rem;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
        z-index: 1000;
        animation: slideIn 0.3s ease-out;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
        }

        to {
            transform: translateX(0);
        }
    }

    .error-messagess {
        transition: opacity 0.3s ease, transform 0.3s ease;
        transform-origin: top;
    }

    .error-message {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fee2e2;
        color: #dc2626;
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid #f87171;
        animation: fadeIn 0.3s ease;
        margin-bottom: 8px;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .tab-button {
        transition: all 0.2s ease;
        background: #030712;
        /* Medium grey (gray-400) */
        color: #1f2937;
    }

    .tab-button:hover {
        background: #1F2937;
        /* Darker grey on hover (gray-500) */
        border-radius: 8px;
    }

    input:focus {
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        border-color: #3b82f6;
    }

    .form-container {
        background: #030712;
        /* Medium grey background for form (gray-400) */
        border: 1px solid #1F2937;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .btn-primary {
        background: #10B981;
        /* Dark grey for buttons (gray-600) */
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #374151;
        /* Darker grey on hover (gray-700) */
        transform: translateY(-1px);
    }

    .btn-primary:active {
        transform: translateY(0);
    }

    .btn-danger {
        background: #ef4444;
        transition: all 0.3s ease;
    }

    .btn-danger:hover {
        background: #dc2626;
    }

    input,
    .input-field {
        background: rgb(255, 255, 255);
        /* Light grey for input fields (gray-300) */
        border: 1px solid #1F2937;
        color: #1f2937;
        padding: 10px;
        font-size: 14px;
        width: 100%;
        border-radius: 8px;
        transition: border-color 0.2s ease;
    }

    input:disabled {
        background: #030712;
        color: #4b5563;
    }

    label {
        color: white;
        font-weight: 500;
    }

    h2,
    h3 {
        color: white;
    }

    label {}

    /* Responsive adjustments */
    @media (min-width: 1024px) {
        .input-container {
            max-width: 420px;
            /* Shortened width for laptop view */
        }

        .grid-cols-1 {
            grid-template-columns: repeat(1, minmax(0, 1fr));
        }

        .md\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .md\:grid-cols-3 {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .input-field {
            padding: 10px;
            font-size: 16px;
        }

        .btn-primary,
        .btn-danger {
            padding: 10px 14px;
            font-size: 14px;
        }
    }

    /* Ensure no overlap with proper spacing */
    </style>
</head>

<body class="flex min-h-screen">
    <?php include('sidebar.php'); ?>

    <!-- Success Popup -->
    <div id="successPopup" class="popup-card" style="display: none;">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle text-2xl"></i>
            <div>
                <h3 class="text-lg font-bold">Success!</h3>
                <p id="popupMessage" class="mt-1 text-sm"></p>
            </div>
        </div>
    </div>

    <div id="main-content" class="flex-1 p-6 ml-0 lg:ml-60">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
            <h2 class="text-3xl font-bold mb-4 sm:mb-0">Register New Lead</h2>
            <button class="btn-primary text-white font-bold py-2 px-4 rounded-lg" onclick="window.location.reload();">
                Refresh
            </button>
        </div>

        <!-- User Form -->
        <form id="user-form" class="form-container space-y-4 p-6 mb-8">
            <h3 class="text-red-600 bg-red-50 p-3 rounded-lg font-medium">Restricted Fields</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="input-container">
                    <label for="user_name" class="text-sm font-medium">Name</label>
                    <input type="text" id="user_name" name="user_name" class="input-field w-full cursor-not-allowed"
                        readonly />
                </div>
                <div class="input-container">
                    <label for="user_designation" class="text-sm font-medium">Designation</label>
                    <input type="text" id="user_designation" name="user_designation"
                        class="input-field w-full cursor-not-allowed" readonly />
                </div>
            </div>
        </form>

        <!-- Leads Form -->
        <div>
            <h2 class="text-2xl font-bold mb-4">Lead Form</h2>
            <form id="leads-form" class="form-container space-y-4 p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="input-container">
                        <label for="lead_name" class="text-sm font-medium">Name</label>
                        <input type="text" id="lead_name" name="lead_name" class="input-field w-full" required />
                    </div>
                    <div class="input-container">
                        <label for="lead_date" class="text-sm font-medium">Date</label>
                        <input type="date" id="lead_date" name="lead_date" class="input-field w-full" required />
                    </div>
                    <div class="input-container">
                        <label for="lead_location" class="text-sm font-medium">Location</label>
                        <input type="text" id="lead_location" name="lead_location" class="input-field w-full"
                            required />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium">Primary Phone Numbers</label>
                        <div id="primary-phone-fields" class="space-y-2 mt-2">
                            <div class="input-container">
                                <div id="primary-error" class="error-message hidden"></div>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="primary_phones[]" placeholder="Enter primary phone number"
                                        class="input-field w-full" required
                                        oninput="validatePrimaryPhoneNumber(this)" />
                                    <button type="button" class="p-2 btn-danger text-white rounded-md remove-btn"
                                        onclick="removePrimaryPhoneField(this)" style="display: none;">-</button>
                                    <button type="button" class="p-2 btn-primary text-white rounded-md add-btn"
                                        onclick="addPrimaryPhoneField()">+</button>
                                </div>
                            </div>
                        </div>

                        <label class="text-sm font-medium mt-2">Additional Phone Numbers</label>
                        <div id="phone-fields" class="space-y-2 mt-2">
                            <div class="input-container">
                                <div id="additional-error" class="error-message hidden"></div>
                                <div class="flex items-center gap-2">
                                    <input type="text" name="phone_numbers[]" placeholder="Enter phone number"
                                        class="input-field w-full" required oninput="validatePhoneNumber(this)" />
                                    <button type="button" class="p-2 btn-danger text-white rounded-md remove-btn"
                                        onclick="removePhoneField(this)" style="display: none;">-</button>
                                    <button type="button" class="p-2 btn-primary text-white rounded-md add-btn"
                                        onclick="addPhoneField()">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="text-sm font-medium">Email Addresses</label>
                        <div id="email-fields" class="space-y-2 mt-2">
                            <div class="input-container">
                                <div id="email-error" class="error-message hidden"></div>
                                <div class="flex items-center gap-2">
                                    <input type="email" name="emails[]" placeholder="Enter email address"
                                        class="input-field w-full" required oninput="validateEmail(this)" />
                                    <button type="button" class="p-2 btn-danger text-white rounded-md remove-btn"
                                        onclick="removeEmailField(this)" style="display: none;">-</button>
                                    <button type="button" class="p-2 btn-primary text-white rounded-md add-btn"
                                        onclick="addEmailField()">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lead Source Tabs -->
                <div class="mt-4">
                    <label class="text-sm font-medium">Lead Source</label>
                    <div class="border-b border-gray-600">
                        <div class="flex space-x-2 overflow-x-auto">
                            <button type="button" class="tab-button flex-1 py-2 px-4 text-center text-sm"
                                onclick="openTab(event, 'linkedin')">LinkedIn</button>
                            <button type="button" class="tab-button flex-1 py-2 px-4 text-center text-sm"
                                onclick="openTab(event, 'portal')">Portal</button>
                            <button type="button" class="tab-button flex-1 py-2 px-4 text-center text-sm"
                                onclick="openTab(event, 'facebook')">Facebook</button>
                            <button type="button" class="tab-button flex-1 py-2 px-4 text-center text-sm"
                                onclick="openTab(event, 'instagram')">Instagram</button>
                            <button type="button" class="tab-button flex-1 py-2 px-4 text-center text-sm"
                                onclick="openTab(event, 'youtube')">Youtube</button>
                        </div>
                    </div>

                    <div id="linkedin" class="tab-content hidden mt-4">
                        <label for="linkedin_url" class="text-sm font-medium">LinkedIn URL</label>
                        <input type="url" id="linkedin_url" name="linkedin_url" class="input-field w-full"
                            placeholder="Enter LinkedIn URL" />
                    </div>
                    <div id="portal" class="tab-content hidden mt-4">
                        <label for="portal_url" class="text-sm font-medium">Portal URL</label>
                        <input type="url" id="portal_url" name="portal_url" class="input-field w-full"
                            placeholder="Enter Portal URL" />
                    </div>
                    <div id="facebook" class="tab-content hidden mt-4">
                        <label for="facebook_url" class="text-sm font-medium">Facebook URL</label>
                        <input type="url" id="facebook_url" name="facebook_url" class="input-field w-full"
                            placeholder="Enter Facebook URL" />
                    </div>
                    <div id="instagram" class="tab-content hidden mt-4">
                        <label for="instagram_url" class="text-sm font-medium">Instagram URL</label>
                        <input type="url" id="instagram_url" name="instagram_url" class="input-field w-full"
                            placeholder="Enter Instagram URL" />
                    </div>
                    <div id="youtube" class="tab-content hidden mt-4">
                        <label for="youtube_url" class="text-sm font-medium">Youtube URL</label>
                        <input type="url" id="youtube_url" name="youtube_url" class="input-field w-full"
                            placeholder="Enter Youtube URL" />
                    </div>
                </div>

                <div class="error-messagess" style="display: none;"></div>
            </form>
        </div>

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="button" onclick="submitForm(event)"
                class="btn-primary text-white py-3 px-6 rounded-lg w-full md:w-auto">
                Submit Form
            </button>
        </div>
    </div>

    <script>
    let phoneExists = false;
    let emailExists = false;
    let phoneDuplicateExists = false;
    let sameNumberExists = false;

    // Add Primary Phone Field
    const addPrimaryPhoneField = () => {
        const container = document.getElementById('primary-phone-fields');
        const fieldCount = container.querySelectorAll('.input-container').length;
        if (fieldCount >= 5) {
            showError(container.querySelector('.input-container'), "Maximum 5 primary phone numbers allowed",
                'primary-error');
            return;
        }

        const field = document.createElement('div');
        field.classList.add('input-container');
        field.innerHTML = `
                <div id="primary-error-${fieldCount + 1}" class="error-message hidden"></div>
                <div class="flex items-center gap-2">
                    <input type="text" name="primary_phones[]" placeholder="Enter primary phone number"
                           class="input-field w-full"
                           required oninput="validatePrimaryPhoneNumber(this)" />
                    <button type="button" class="p-2 btn-danger text-white rounded-md remove-btn" onclick="removePrimaryPhoneField(this)" style="display: none;">-</button>
                    <button type="button" class="p-2 btn-primary text-white rounded-md add-btn" onclick="addPrimaryPhoneField()" style="display: none;">+</button>
                </div>
            `;
        container.appendChild(field);
        toggleAddRemoveButtons();
        clearPreviousErrors();
    };

    // Remove Primary Phone Field
    const removePrimaryPhoneField = (button) => {
        button.parentNode.parentNode.remove();
        toggleAddRemoveButtons();
        clearPreviousErrors();
        phoneExists = false;
        phoneDuplicateExists = false;
        sameNumberExists = false;
    };

    // Validate Primary Phone Number
    const validatePrimaryPhoneNumber = (input) => {
        const phoneValue = input.value.trim();
        clearError(input);

        if (!phoneValue) {
            phoneExists = false;
            return;
        }

        // Allow only digits (0-9), no spaces, brackets, or special characters
        const phoneRegex = /^[0-9]+$/;
        if (!phoneRegex.test(phoneValue)) {
            showError(input, "Phone number must contain only digits, no spaces, brackets, or special characters",
                'primary-error');
            return;
        }

        // Check for exactly 10 digits
        if (phoneValue.length !== 10) {
            showError(input, "Phone number must contain exactly 10 digits", 'primary-error');
            return;
        }

        checkPrimaryPhoneExistence(input);
    };

    // Add Additional Phone Field
    const addPhoneField = () => {
        const container = document.getElementById('phone-fields');
        const fieldCount = container.querySelectorAll('.input-container').length;
        if (fieldCount >= 5) {
            showError(container.querySelector('.input-container'), "Maximum 5 additional phone numbers allowed",
                'additional-error');
            return;
        }

        const field = document.createElement('div');
        field.classList.add('input-container');
        field.innerHTML = `
                <div id="additional-error-${fieldCount + 1}" class="error-message hidden"></div>
                <div class="flex items-center gap-2">
                    <input type="text" name="phone_numbers[]" placeholder="Enter phone number"
                           class="input-field w-full"
                           required oninput="validatePhoneNumber(this)" />
                    <button type="button" class="p-2 btn-danger text-white rounded-md remove-btn" onclick="removePhoneField(this)" style="display: none;">-</button>
                    <button type="button" class="p-2 btn-primary text-white rounded-md add-btn" onclick="addPhoneField()" style="display: none;">+</button>
                </div>
            `;
        container.appendChild(field);
        toggleAddRemoveButtons();
        clearPreviousErrors();
    };

    // Remove Additional Phone Field
    const removePhoneField = (button) => {
        button.parentNode.parentNode.remove();
        toggleAddRemoveButtons();
        clearPreviousErrors();
        phoneDuplicateExists = false;
        sameNumberExists = false;
    };

    // Validate Additional Phone Number
    const validatePhoneNumber = (input) => {
        const phoneValue = input.value.trim();
        clearError(input);

        if (!phoneValue) {
            phoneDuplicateExists = false;
            return;
        }

        // Allow only digits (0-9), no spaces, brackets, or special characters
        const phoneRegex = /^[0-9]+$/;
        if (!phoneRegex.test(phoneValue)) {
            showError(input, "Phone number must contain only digits, no spaces, brackets, or special characters",
                'additional-error');
            return;
        }

        // Check for exactly 10 digits
        if (phoneValue.length !== 10) {
            showError(input, "Phone number must contain exactly 10 digits", 'additional-error');
            return;
        }

        checkPhoneExistence(input);
    };

    // Add Email Field
    const addEmailField = () => {
        const container = document.getElementById('email-fields');
        const fieldCount = container.querySelectorAll('.input-container').length;
        if (fieldCount >= 5) {
            showError(container.querySelector('.input-container'), "Maximum 5 email addresses allowed",
                'email-error');
            return;
        }

        const field = document.createElement('div');
        field.classList.add('input-container');
        field.innerHTML = `
                <div id="email-error-${fieldCount + 1}" class="error-message hidden"></div>
                <div class="flex items-center gap-2">
                    <input type="email" name="emails[]" placeholder="Enter email address"
                           class="input-field w-full"
                           required oninput="validateEmail(this)" />
                    <button type="button" class="p-2 btn-danger text-white rounded-md remove-btn" onclick="removeEmailField(this)" style="display: none;">-</button>
                    <button type="button" class="p-2 btn-primary text-white rounded-md add-btn" onclick="addEmailField()" style="display: none;">+</button>
                </div>
            `;
        container.appendChild(field);
        toggleAddRemoveButtons();
        clearPreviousErrors();
    };

    // Remove Email Field
    const removeEmailField = (button) => {
        button.parentNode.parentNode.remove();
        toggleAddRemoveButtons();
        clearPreviousErrors();
        emailExists = false;
    };

    // Validate Email
    const validateEmail = (input) => {
        const emailValue = input.value.trim();
        clearError(input);

        if (!emailValue) {
            emailExists = false;
            return;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(emailValue)) {
            showError(input, "Invalid email format", 'email-error');
            return;
        }

        checkEmailExistence(input);
    };

    // Check Email Existence
    const checkEmailExistence = (input) => {
        fetch(`api/api.php?query=${encodeURIComponent(input.value.trim())}&type=email`)
            .then(response => response.json())
            .then(data => {
                console.log('Email API response:', data);
                emailExists = data.status === 'exists';
                if (emailExists) {
                    showError(input, data.message || "Email already exists", 'email-error');
                } else {
                    clearError(input);
                }
            })
            .catch(() => showError(input, "Validation service unavailable", 'email-error'));
    };

    // Check Phone Existence (for additional phones)
    const checkPhoneExistence = (input) => {
        const number = input.value.trim();
        if (!number) {
            phoneDuplicateExists = false;
            clearError(input);
            return;
        }

        const primaryPhoneUrl = `api/api.php?query=${encodeURIComponent(number)}&type=primaryphone`;
        const phoneUrl = `api/api.php?query=${encodeURIComponent(number)}&type=phone`;

        Promise.all([
                fetch(primaryPhoneUrl).then(response => response.json()),
                fetch(phoneUrl).then(response => response.json())
            ])
            .then(([primaryPhoneData, phoneData]) => {
                console.log('Primary Phone API response:', primaryPhoneData);
                console.log('Additional Phone API response:', phoneData);
                const existsInPrimaryPhone = primaryPhoneData.status === 'exists';
                const existsInPhone = phoneData.status === 'exists';

                if (existsInPrimaryPhone) {
                    phoneDuplicateExists = true;
                    showError(input, "This number already exists as a primary phone number",
                        'additional-error');
                } else if (existsInPhone) {
                    phoneDuplicateExists = true;
                    showError(input, "This number already exists as an additional phone number",
                        'additional-error');
                } else {
                    phoneDuplicateExists = false;
                    clearError(input);
                }
            })
            .catch(() => {
                showError(input, "Validation service unavailable", 'additional-error');
            });
    };

    // Check Primary Phone Existence
    // Check Primary Phone Existence
    const checkPrimaryPhoneExistence = (input) => {
        const number = input.value.trim();
        if (!number) {
            phoneExists = false;
            clearError(input);
            return;
        }

        const primaryPhoneUrl = `api/api.php?query=${encodeURIComponent(number)}&type=primaryphone`;
        const phoneUrl = `api/api.php?query=${encodeURIComponent(number)}&type=phone`;

        Promise.all([
                fetch(primaryPhoneUrl).then(response => response.json()),
                fetch(phoneUrl).then(response => response.json())
            ])
            .then(([primaryPhoneData, phoneData]) => {
                console.log('Primary Phone API response:', primaryPhoneData);
                console.log('Additional Phone API response:', phoneData);
                const existsInPrimaryPhone = primaryPhoneData.status === 'exists';
                const existsInPhone = phoneData.status === 'exists';

                if (existsInPrimaryPhone) {
                    phoneExists = true;
                    showError(input, "This number already exists as a primary phone number", 'primary-error');
                } else if (existsInPhone) {
                    phoneExists = true;
                    showError(input, "This number already exists as an additional phone number",
                        'primary-error');
                } else {
                    phoneExists = false;
                    clearError(input);
                }
            })
            .catch(() => {
                showError(input, "Validation service unavailable", 'primary-error');
            });
    };

    // Check for duplicates between primary and additional phone numbers
    const checkSameNumberInForm = () => {
        const primaryPhoneInputs = document.querySelectorAll('input[name="primary_phones[]"]');
        const additionalPhoneInputs = document.querySelectorAll('input[name="phone_numbers[]"]');

        const primaryNumbers = Array.from(primaryPhoneInputs)
            .map(input => input.value.trim())
            .filter(num => num !== '');
        const additionalNumbers = Array.from(additionalPhoneInputs)
            .map(input => input.value.trim())
            .filter(num => num !== '');

        sameNumberExists = primaryNumbers.some(primaryNum =>
            additionalNumbers.includes(primaryNum)
        );

        if (sameNumberExists) {
            leadError("Same number added twice in primary and additional fields");
            return true; // Indicate duplicates found
        }
        return false; // No duplicates found
    };

    // Form Submission
    const submitForm = (event) => {
        event.preventDefault();
        clearPreviousErrors();

        // Reset flags before validation
        phoneExists = false;
        phoneDuplicateExists = false;
        emailExists = false;
        sameNumberExists = false;

        // First, check for duplicates within the form
        if (checkSameNumberInForm()) {
            return; // Stop submission if same number is found in primary and additional
        }

        // Proceed with database validation for primary phones, additional phones, and emails
        const primaryPhoneInputs = document.querySelectorAll('input[name="primary_phones[]"]');
        const additionalPhoneInputs = document.querySelectorAll('input[name="phone_numbers[]"]');
        const emailInputs = document.querySelectorAll('input[name="emails[]"]');

        const validationPromises = [];

        primaryPhoneInputs.forEach(input => {
            if (input.value.trim()) {
                validationPromises.push(new Promise((resolve) => {
                    checkPrimaryPhoneExistence(input);
                    setTimeout(() => resolve(), 500);
                }));
            }
        });

        additionalPhoneInputs.forEach(input => {
            if (input.value.trim()) {
                validationPromises.push(new Promise((resolve) => {
                    checkPhoneExistence(input);
                    setTimeout(() => resolve(), 500);
                }));
            }
        });

        emailInputs.forEach(input => {
            if (input.value.trim()) {
                validationPromises.push(new Promise((resolve) => {
                    checkEmailExistence(input);
                    setTimeout(() => resolve(), 500);
                }));
            }
        });

        Promise.all(validationPromises)
            .then(() => {
                console.log('Validation complete:', {
                    phoneExists,
                    phoneDuplicateExists,
                    emailExists,
                    sameNumberExists
                });

                if (phoneExists) {
                    leadError("Primary phone number already exists in the database");
                    return;
                }

                if (phoneDuplicateExists) {
                    leadError("Additional phone number already exists in the database");
                    return;
                }

                if (emailExists) {
                    leadError("Email address already exists");
                    return;
                }

                if (!validateForm()) {
                    leadError("Please correct the form errors");
                    return;
                }

                const userForm = document.getElementById("user-form");
                const leadsForm = document.getElementById("leads-form");
                if (!userForm || !leadsForm) {
                    leadError("Form not found");
                    return;
                }

                const formData = new FormData();
                new FormData(userForm).forEach((value, key) => {
                    if (value.trim() !== "") formData.append(key, value);
                });
                new FormData(leadsForm).forEach((value, key) => {
                    if (value.trim() !== "") formData.append(key, value);
                });

                const leadSources = ["linkedin_url", "portal_url", "facebook_url", "instagram_url",
                    "youtube_url"
                ];
                let dataSourceLink = "";
                leadSources.forEach(source => {
                    const field = document.getElementById(source);
                    if (field && field.value.trim() !== "") {
                        dataSourceLink += (dataSourceLink ? ", " : "") + field.value.trim();
                    }
                });
                if (dataSourceLink) {
                    formData.append("data_source_link", dataSourceLink);
                }

                const user_id = localStorage.getItem('id');
                if (!user_id) {
                    leadError('User ID is missing');
                    return;
                }

                formData.append('user_id', user_id);

                fetch('api/api.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Form submission response:', data);
                        if (data.status === 'error') {
                            leadError(data.message || "Form submission failed");
                            if (data.errors) {
                                data.errors.forEach(error => leadError(error.message));
                            }
                        } else {
                            showSuccessPopup(data.message || "Form submitted successfully");
                            leadsForm.reset();
                            clearLeadFields();
                            clearPreviousErrors();
                        }
                    })
                    .catch(error => {
                        console.error('Submission error:', error);
                        leadError('An error occurred while submitting the form');
                    });
            });
    };

    // Show Error (Modified to show above the input)
    const showError = (input, message, errorIdPrefix = 'error') => {
        if (message === "An error occurred while submitting the form.") return;

        clearError(input);
        const errorDiv = input.parentNode.parentNode.querySelector(`[id*="${errorIdPrefix}"]`);
        errorDiv.classList.remove('hidden');
        errorDiv.innerHTML = `
                <i class="fas fa-exclamation-circle"></i>
                <span>${message}</span>
            `;
        input.classList.add('border-red-500');
    };

    // Clear Error
    const clearError = (input) => {
        const errorDiv = input.parentNode.parentNode.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.classList.add('hidden');
            errorDiv.innerHTML = '';
        }
        input.classList.remove('border-red-500');
    };

    // Lead Error
    const leadError = (message) => {
        if (message === "An error occurred while submitting the form") return;

        const errorContainer = document.querySelector('.error-messagess');
        errorContainer.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>${message}</span>
                </div>
            `;
        errorContainer.style.display = 'block';

        // Auto-hide error after 5 seconds
        setTimeout(() => {
            errorContainer.style.display = 'none';
            errorContainer.innerHTML = '';
        }, 5000);
    };

    // Clear Previous Errors
    const clearPreviousErrors = () => {
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(error => {
            error.classList.add('hidden');
            error.innerHTML = '';
        });
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => input.classList.remove('border-red-500'));
        const errorContainer = document.querySelector('.error-messagess');
        errorContainer.style.display = 'none';
        errorContainer.innerHTML = '';
    };

    // Clear Lead Fields
    const clearLeadFields = () => {
        const phoneFields = document.querySelectorAll('#phone-fields > .input-container');
        const emailFields = document.querySelectorAll('#email-fields > .input-container');

        phoneFields.forEach(field => field.remove());
        emailFields.forEach(field => field.remove());

        // Recreate initial fields
        addPhoneField();
        addEmailField();
        toggleAddRemoveButtons();
    };

    // Show Success Popup
    const showSuccessPopup = (message) => {
        const popup = document.getElementById('successPopup');
        const popupMessage = document.getElementById('popupMessage');
        popupMessage.innerText = message;
        popup.style.display = 'block';

        // Auto-hide popup after 3 seconds
        setTimeout(() => {
            popup.style.display = 'none';
        }, 3000);
    };

    // Toggle Add/Remove Buttons
    const toggleAddRemoveButtons = () => {
        // Helper function to manage buttons for a given section
        const manageButtonsForSection = (fields) => {
            Array.from(fields).forEach((field, index) => {
                const addBtn = field.querySelector('.add-btn');
                const removeBtn = field.querySelector('.remove-btn');

                // Ensure both buttons exist
                if (addBtn && removeBtn) {
                    // Show the add button only on the last field
                    addBtn.style.display = index === fields.length - 1 ? 'block' : 'none';
                    // Show the remove button on all fields except the last one, but only if there are multiple fields
                    removeBtn.style.display = (fields.length > 1 && index !== fields.length - 1) ?
                        'block' : 'none';
                }
            });
        };

        // Manage buttons for each section independently
        const primaryPhoneFields = document.getElementById('primary-phone-fields').children;
        const phoneFields = document.getElementById('phone-fields').children;
        const emailFields = document.getElementById('email-fields').children;

        manageButtonsForSection(primaryPhoneFields);
        manageButtonsForSection(phoneFields);
        manageButtonsForSection(emailFields);
    };

    // Form Validation
    const validateForm = () => {
        let isValid = true;

        // Validate email fields
        const emailInputs = document.querySelectorAll('input[type="email"]');
        emailInputs.forEach(input => {
            const emailValue = input.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (emailValue && !emailRegex.test(emailValue)) {
                showError(input, "Invalid email address", 'email-error');
                isValid = false;
            }
        });

        // Validate phone fields (both primary and additional)
        const phoneInputs = document.querySelectorAll(
            'input[name="primary_phones[]"], input[name="phone_numbers[]"]');
        phoneInputs.forEach(input => {
            const phoneValue = input.value.trim();
            const phoneRegex = /^[0-9]+$/;

            if (phoneValue) {
                if (!phoneRegex.test(phoneValue)) {
                    showError(input,
                        "Phone number must contain only digits, no spaces, brackets, or special characters",
                        input.name.includes('primary') ? 'primary-error' : 'additional-error');
                    isValid = false;
                }

                if (phoneValue.length !== 10) {
                    showError(input, "Phone number must contain exactly 10 digits",
                        input.name.includes('primary') ? 'primary-error' : 'additional-error');
                    isValid = false;
                }
            }
        });

        return isValid;
    };

    // Window Onload
    window.onload = () => {
        const username = localStorage.getItem('username');
        const designation = localStorage.getItem('designation');
        if (username && designation) {
            document.getElementById('user_name').value = username;
            document.getElementById('user_designation').value = designation;
        }
        toggleAddRemoveButtons(); // Ensure buttons are correctly set on page load
    };

    // Tab Switching
    const openTab = (event, tabName) => {
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => content.classList.add('hidden'));

        const tabs = document.querySelectorAll('.tab-button');
        tabs.forEach(tab => tab.classList.remove('border-b-4', 'border-blue-500', 'rounded-full'));

        document.getElementById(tabName).classList.remove('hidden');
        event.currentTarget.classList.add('border-b-4', 'border-blue-500', 'rounded-full');
    };

    // DOM Content Loaded
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("leads-form");
        if (form) {
            form.addEventListener("submit", submitForm);
        }
    });
    </script>
</body>

</html>