let phoneExists = false;
let emailExists = false;
let phoneDuplicateExists = false;
let sameNumberExists = false;

// Add Primary Phone Field
const addPrimaryPhoneField = () => {
    const container = document.getElementById('primary-phone-fields');
    const fieldCount = container.querySelectorAll('.input-container').length;
    if (fieldCount >= 5) {
        showError(container.querySelector('.input-container'), "Maximum 5 primary phone numbers allowed", 'primary-error');
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

    const phoneRegex = /^[0-9()+-\s]{10,15}$/;
    if (!phoneRegex.test(phoneValue)) {
        showError(input, "Invalid phone number format", 'primary-error');
        return;
    }

    const digitCount = phoneValue.replace(/[^0-9]/g, '').length;
    if (digitCount !== 10) {
        showError(input, "Phone number must contain 10 digits", 'primary-error');
        return;
    }

    checkPrimaryPhoneExistence(input);
};

// Add Additional Phone Field
const addPhoneField = () => {
    const container = document.getElementById('phone-fields');
    const fieldCount = container.querySelectorAll('.input-container').length;
    if (fieldCount >= 5) {
        showError(container.querySelector('.input-container'), "Maximum 5 additional phone numbers allowed", 'additional-error');
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

    const phoneRegex = /^[0-9()+-\s]{10,15}$/;
    if (!phoneRegex.test(phoneValue)) {
        showError(input, "Invalid phone number format", 'additional-error');
        return;
    }

    const digitCount = phoneValue.replace(/[^0-9]/g, '').length;
    if (digitCount !== 10) {
        showError(input, "Phone number must contain 10 digits", 'additional-error');
        return;
    }

    checkPhoneExistence(input);
};

// Add Email Field
const addEmailField = () => {
    const container = document.getElementById('email-fields');
    const fieldCount = container.querySelectorAll('.input-container').length;
    if (fieldCount >= 5) {
        showError(container.querySelector('.input-container'), "Maximum 5 email addresses allowed", 'email-error');
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
                showError(input, "This number already exists as a primary phone number", 'additional-error');
            } else if (existsInPhone) {
                phoneDuplicateExists = true;
                showError(input, "This number already exists as an additional phone number", 'additional-error');
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
                showError(input, "This number already exists as an additional phone number", 'primary-error');
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
        .map(input => input.value.trim().replace(/[^0-9]/g, ''))
        .filter(num => num !== '');
    const additionalNumbers = Array.from(additionalPhoneInputs)
        .map(input => input.value.trim().replace(/[^0-9]/g, ''))
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
            console.log('Validation complete:', { phoneExists, phoneDuplicateExists, emailExists, sameNumberExists });

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

            const leadSources = ["linkedin_url", "portal_url", "facebook_url", "instagram_url", "youtube_url"];
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
                removeBtn.style.display = (fields.length > 1 && index !== fields.length - 1) ? 'block' : 'none';
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
    const phoneInputs = document.querySelectorAll('input[name="primary_phones[]"], input[name="phone_numbers[]"]');
    phoneInputs.forEach(input => {
        const phoneValue = input.value.trim();
        const phoneRegex = /^[0-9()+-\s]{10,15}$/;

        if (phoneValue) {
            if (!phoneRegex.test(phoneValue)) {
                showError(input, "Invalid phone number format", input.name.includes('primary') ? 'primary-error' : 'additional-error');
                isValid = false;
            }

            const digitCount = phoneValue.replace(/[^0-9]/g, '').length;
            if (digitCount !== 10) {
                showError(input, "Phone number must contain 10 digits", input.name.includes('primary') ? 'primary-error' : 'additional-error');
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
document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("leads-form");
    if (form) {
        form.addEventListener("submit", submitForm);
    }
});