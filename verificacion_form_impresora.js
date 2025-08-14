// form_validation.js

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('printerForm');
    const serviceDateInput = document.getElementById('serviceDate');
    const tonerBlackInput = document.getElementById('tonerBlack');
    const tonerCyanInput = document.getElementById('tonerCyan');
    const tonerMagentaInput = document.getElementById('tonerMagenta');
    const tonerYellowInput = document.getElementById('tonerYellow');
    const pageCounterInput = document.getElementById('pageCounter');

    // Function to create and display an error message
    function displayError(inputElement, message) {
        let errorElement = inputElement.nextElementSibling;
        if (!errorElement || !errorElement.classList.contains('error-message')) {
            errorElement = document.createElement('p');
            errorElement.classList.add('error-message', 'text-red-500', 'text-sm', 'mt-1');
            inputElement.parentNode.insertBefore(errorElement, inputElement.nextSibling);
        }
        errorElement.textContent = message;
        inputElement.classList.add('border-red-500'); // Add red border
    }

    // Function to remove an error message
    function removeError(inputElement) {
        const errorElement = inputElement.nextElementSibling;
        if (errorElement && errorElement.classList.contains('error-message')) {
            errorElement.remove();
        }
        inputElement.classList.remove('border-red-500'); // Remove red border
    }

    // --- Validation Functions ---

    function validateServiceDate() {
        const selectedDate = new Date(serviceDateInput.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Normalize today's date to remove time component

        if (selectedDate > today) {
            displayError(serviceDateInput, 'La fecha no puede ser posterior a la fecha actual.');
            return false;
        } else {
            removeError(serviceDateInput);
            return true;
        }
    }

    function validateTonerLevel(inputElement) {
        const tonerValue = parseInt(inputElement.value, 10);
        const fieldName = inputElement.previousElementSibling.textContent.replace(' (%)', '').trim(); // Get label text

        if (isNaN(tonerValue) || tonerValue < 0 || tonerValue > 100) {
            displayError(inputElement, `El nivel de ${fieldName.toLowerCase()} debe estar entre 0 y 100.`);
            return false;
        } else {
            removeError(inputElement);
            return true;
        }
    }

    function validatePageCounter() {
        const pageCount = parseInt(pageCounterInput.value, 10);

        // Allow empty string for required validation by browser or other means if needed,
        // but validate if a value is present.
        if (pageCounterInput.value === '') {
            removeError(pageCounterInput); // Let 'required' handle empty
            return true;
        }

        if (isNaN(pageCount) || pageCount < 0 || pageCount > 300) {
            displayError(pageCounterInput, 'El contador de páginas no puede ser mayor a 300 y debe ser un número positivo.');
            return false;
        } else {
            removeError(pageCounterInput);
            return true;
        }
    }

    // --- Event Listeners for Real-time Validation ---

    serviceDateInput.addEventListener('change', validateServiceDate);
    serviceDateInput.addEventListener('input', validateServiceDate); // For browsers that might not fire 'change' immediately

    tonerBlackInput.addEventListener('input', () => validateTonerLevel(tonerBlackInput));
    tonerCyanInput.addEventListener('input', () => validateTonerLevel(tonerCyanInput));
    tonerMagentaInput.addEventListener('input', () => validateTonerLevel(tonerMagentaInput));
    tonerYellowInput.addEventListener('input', () => validateTonerLevel(tonerYellowInput));

    pageCounterInput.addEventListener('input', validatePageCounter);

    // --- Form Submission Validation ---
    form.addEventListener('submit', (event) => {
        let isFormValid = true;

        // Run all validations on submit
        const validations = [
            validateServiceDate(),
            validateTonerLevel(tonerBlackInput),
            validateTonerLevel(tonerCyanInput),
            validateTonerLevel(tonerMagentaInput),
            validateTonerLevel(tonerYellowInput),
            validatePageCounter()
        ];

        // If any validation is false, the form is not valid
        if (validations.includes(false)) {
            isFormValid = false;
        }

        // Also check if any required fields are empty (browser's default validation might handle this,
        // but explicit check adds robustness).
        form.querySelectorAll('[required]').forEach(input => {
            if (!input.value) {
                // If the browser doesn't show a message for required, you could add one here.
                // For now, rely on browser's default required validation pop-ups.
                isFormValid = false;
            }
        });

        if (!isFormValid) {
            event.preventDefault(); // Stop form submission
            alert('Por favor, corrige los errores en el formulario antes de enviarlo.');
        }
    });
});