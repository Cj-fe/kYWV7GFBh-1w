<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector(".form");
        const nextBtn = form.querySelector(".nextBtn");
        const backBtn = form.querySelector(".backBtn");
        const formSections = form.querySelectorAll(".form-section");

        let currentSection = 0;

        function showSection(index) {
            formSections.forEach((section, i) => {
                if (i === index) {
                    section.classList.add("active");
                    section.classList.remove("hidden");
                } else {
                    section.classList.remove("active");
                    section.classList.add("hidden");
                }
            });
        }

        nextBtn.addEventListener("click", function () {
            if (validateInputs(formSections[currentSection])) {
                currentSection++;
                showSection(currentSection);
            } else {
                document.body.classList.add('no-scroll'); // Disable scrolling
                Swal.fire({
                    title: 'Incomplete Form',
                    text: 'Please fill in all required fields before proceeding.',
                    icon: 'warning',
                    confirmButtonText: 'Ok',
                    confirmButtonColor: '#3085d6',
                }).then(() => {
                    document.body.classList.remove('no-scroll'); // Re-enable scrolling
                });
            }
        });

        backBtn.addEventListener("click", function () {
            currentSection--;
            showSection(currentSection);
        });

        function validateInputs(section) {
            const inputs = section.querySelectorAll("input[required], select[required]");
            let isValid = true;
            let firstInvalidInput = null;

            inputs.forEach(input => {
                if (!input.value.trim()) {
                    isValid = false;
                    input.style.borderColor = "red";
                    if (!firstInvalidInput) {
                        firstInvalidInput = input;
                    }
                } else {
                    input.style.borderColor = "";
                }
            });

            if (firstInvalidInput) {
                firstInvalidInput.focus();
            }

            return isValid;
        }

        function toggleEmploymentFields() {
            const selectedStatus = document.getElementById('work-status').value;
            if (selectedStatus === 'Employed') {
                document.querySelectorAll('#job_satisfaction, #employment_location, #work_employment_status, #name_company, #first_employment_date, #date_for_current_employment, #type_of_work, #work_position, #current_monthly_income, #work_related, #work_classification').forEach(field => {
                    field.closest('.input-field').style.display = 'block';
                    field.setAttribute('required', true);
                });
            } else {
                document.querySelectorAll('#job_satisfaction, #employment_location, #work_employment_status, #name_company, #first_employment_date, #date_for_current_employment, #type_of_work, #work_position, #current_monthly_income, #work_related, #work_classification').forEach(field => {
                    field.closest('.input-field').style.display = 'none';
                    field.removeAttribute('required');
                });
            }
        }

        document.getElementById('work-status').addEventListener('change', toggleEmploymentFields);
        toggleEmploymentFields();

        function displayImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    document.getElementById('profileDisplay').style.backgroundImage = `url(${e.target.result})`;
                    document.querySelector('.custom-label').style.display = 'none';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        document.getElementById('profileImageInput').addEventListener('change', function () {
            displayImage(this);
        });

        // Initialize the form
        showSection(currentSection);

        // Improve user experience by showing which fields are required
        document.querySelectorAll('input[required], select[required]').forEach(element => {
            element.addEventListener('blur', function () {
                if (!this.value.trim()) {
                    this.style.borderColor = "red";
                } else {
                    this.style.borderColor = "";
                }
            });
        });
    });


    document.addEventListener('DOMContentLoaded', function () {
        var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeModal'), {
            keyboard: false
        });

        welcomeModal.show();

        // Remove any inline padding-right that Bootstrap might add
        document.body.style.paddingRight = '';
        document.querySelector('.modal').style.paddingRight = '';

        // Ensure body doesn't scroll when modal is open
        var modalElement = document.getElementById('welcomeModal');
        modalElement.addEventListener('show.bs.modal', function () {
            document.body.classList.add('modal-open');
        });
        modalElement.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open');
        });
    });


</script>



<script>
    // Create a custom alert element
    function createCustomAlert(type, message) {
        const alertElement = document.createElement('div');
        const icon = type === 'success' ?
            `<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
            <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
        </svg>` :
            `<svg class="crossmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
            <circle class="crossmark__circle" cx="26" cy="26" r="25" fill="none"/>
            <path class="crossmark__path" fill="none" d="M16 16 36 36 M36 16 16 36"/>
        </svg>`;

        alertElement.innerHTML = `
        <div class="custom-alert ${type}">
            ${icon}
            <span class="message">${message}</span>
        </div>
    `;
        document.body.appendChild(alertElement);

        // Remove the alert after 3 seconds
        setTimeout(() => {
            alertElement.remove();
        }, 3000);
    }

    const style = document.createElement('style');
    style.textContent = `
    .custom-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        color: white;
        padding: 10px 15px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        z-index: 2000;
        max-width: 350px;
        width: 20%;
    }
    .custom-alert.success {
        background-color: #4CAF50;
    }
    .custom-alert.error {
        background-color: #F44336;
    }
    .custom-alert .message {
        margin-left: 10px;
        word-wrap: break-word;
        line-height: 1.4;
    }
    .checkmark, .crossmark {
        width: 20px;
        height: 20px;
        min-width: 20px;
        border-radius: 50%;
        stroke-width: 2;
        stroke: #fff;
        stroke-miterlimit: 10;
        animation: scale .3s ease-in-out .9s both;
    }
    .checkmark__circle, .crossmark__circle {
        stroke-dasharray: 166;
        stroke-dashoffset: 166;
        stroke-width: 2;
        stroke-miterlimit: 10;
        stroke: #fff;
        fill: none;
        animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;
    }
    .checkmark__check, .crossmark__path {
        transform-origin: 50% 50%;
        stroke-dasharray: 48;
        stroke-dashoffset: 48;
        animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;
    }
    @keyframes stroke { 100% { stroke-dashoffset: 0; } }
    @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
`;
    document.head.appendChild(style);

    // The form submission code remains the same as in the previous example

    // Modify your form submission code
    const form = document.querySelector('form');
    form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    createCustomAlert('success', 'Success');
                    setTimeout(() => {
                        window.location.href = '../index.php';
                    }, 3000);
                } else {
                    createCustomAlert('error', data.message || 'An error occurred');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                createCustomAlert('error', 'An unexpected error occurred. Please try again.');
            });
    });
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Check for errors in the session and display a SweetAlert if an error exists
        <?php if (isset($_SESSION['error'])): ?>
            Swal.fire({
                icon: 'error',
                title: 'Invalid Alumni ID',
                text: '<?php echo $_SESSION['error']; ?>',
                confirmButtonText: 'OK'
            }).then(function () {
                // Clear the error after displaying the alert
                <?php unset($_SESSION['error']); ?>
            });
        <?php endif; ?>

        // Your existing script to handle regions, provinces, cities, barangays...
        const regionUrl = 'json/regions.json';
        const provinceUrl = 'json/provinces.json';
        const cityUrl = 'json/municipalities.json';
        const barangayUrl = 'json/barangays.json';

        let provincesData = [];
        let citiesData = [];
        let barangaysData = [];

        function setSelectedValue(selectElement, value) {
            if (value) {
                const option = selectElement.querySelector(`option[value="${value}"]`);
                if (option) {
                    option.selected = true;
                }
            }
        }

        // Fetch and populate regions
        fetch(regionUrl)
            .then(response => response.json())
            .then(data => {
                const regionSelect = document.getElementById('regionSelect');
                regionSelect.innerHTML = '<option value="">Select a region</option>';
                data.forEach(region => {
                    const option = document.createElement('option');
                    option.value = region.designation;
                    option.textContent = region.name;
                    regionSelect.appendChild(option);
                });
                setSelectedValue(regionSelect, regionSelect.dataset.currentValue);
                $('.selectpicker').selectpicker('refresh');
                regionSelect.dispatchEvent(new Event('change'));
            })
            .catch(error => console.error('Error fetching regions:', error));

        // Fetch provinces data
        fetch(provinceUrl)
            .then(response => response.json())
            .then(data => {
                provincesData = data;
            })
            .catch(error => console.error('Error fetching provinces:', error));

        // Fetch cities data
        fetch(cityUrl)
            .then(response => response.json())
            .then(data => {
                citiesData = data;
            })
            .catch(error => console.error('Error fetching cities:', error));

        // Fetch barangays data
        fetch(barangayUrl)
            .then(response => response.json())
            .then(data => {
                barangaysData = data;
            })
            .catch(error => console.error('Error fetching barangays:', error));

        // Region change handler
        document.getElementById('regionSelect').addEventListener('change', function () {
            const selectedRegion = this.value;
            const provinceSelect = document.getElementById('provinceSelect');
            provinceSelect.innerHTML = '';

            if (selectedRegion) {
                const filteredProvinces = provincesData.filter(province => province.region === selectedRegion);
                if (filteredProvinces.length > 0) {
                    filteredProvinces.forEach(province => {
                        const option = document.createElement('option');
                        option.value = province.name;
                        option.textContent = province.name;
                        provinceSelect.appendChild(option);
                    });
                    provinceSelect.disabled = false;
                } else {
                    provinceSelect.innerHTML = '<option value="">No provinces available</option>';
                    provinceSelect.disabled = true;
                }
            } else {
                provinceSelect.innerHTML = '<option value="">Select a region first</option>';
                provinceSelect.disabled = true;
            }
            setSelectedValue(provinceSelect, provinceSelect.dataset.currentValue);
            provinceSelect.dispatchEvent(new Event('change'));
            $('.selectpicker').selectpicker('refresh');
        });

        // Province change handler
        document.getElementById('provinceSelect').addEventListener('change', function () {
            const selectedProvince = this.value;
            const citySelect = document.getElementById('citySelect');
            citySelect.innerHTML = '';

            if (selectedProvince) {
                const filteredCities = citiesData.filter(city => city.province === selectedProvince);
                if (filteredCities.length > 0) {
                    filteredCities.forEach(city => {
                        const option = document.createElement('option');
                        option.value = city.name;
                        option.textContent = city.name;
                        citySelect.appendChild(option);
                    });
                    citySelect.disabled = false;
                } else {
                    citySelect.innerHTML = '<option value="">No cities available</option>';
                    citySelect.disabled = true;
                }
            } else {
                citySelect.innerHTML = '<option value="">Select a province first</option>';
                citySelect.disabled = true;
            }
            setSelectedValue(citySelect, citySelect.dataset.currentValue);
            citySelect.dispatchEvent(new Event('change'));
            $('.selectpicker').selectpicker('refresh');
        });

        // City change handler
        document.getElementById('citySelect').addEventListener('change', function () {
            const selectedCity = this.value;
            const barangaySelect = document.getElementById('barangaySelect');
            barangaySelect.innerHTML = '';

            if (selectedCity) {
                const filteredBarangays = barangaysData.filter(barangay => barangay.citymun === selectedCity);
                if (filteredBarangays.length > 0) {
                    filteredBarangays.forEach(barangay => {
                        const option = document.createElement('option');
                        option.value = barangay.name;
                        option.textContent = barangay.name;
                        barangaySelect.appendChild(option);
                    });
                    barangaySelect.disabled = false;
                } else {
                    barangaySelect.innerHTML = '<option value="">No barangays available</option>';
                    barangaySelect.disabled = true;
                }
            } else {
                barangaySelect.innerHTML = '<option value="">Select a city/municipality first</option>';
                barangaySelect.disabled = true;
            }
            setSelectedValue(barangaySelect, barangaySelect.dataset.currentValue);
            $('.selectpicker').selectpicker('refresh');
        });

        // Form submission handler

    });
</script>
