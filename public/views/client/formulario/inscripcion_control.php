<?php
ob_start(); ?>
<div class="d-flex flex-column w-100 p-4 align-items-center">
    <div class="p-4 align-content-center d-flex flex-column align-items-center w-75">
        <h2 class="custom-heading text-center mb-1">Formulario Control Estadisticos Servicios Alimentacion</h2>
        <hr class="hr-heading">
    </div>
    <div>
        <?php
        $width = 128;
        $height = 128;
        include __DIR__ . '/../../components/reloj.php'; ?>
    </div>

    <form id="myForm" class="row g-3 w-50">
        <!-- Aquí van los campos del formulario -->
        <div class="col-6">
            <label for="inputDate" class="form-label">Fecha</label>
            <input type="date" id="inputDate" name="inputDate" class="form-control" required>
            <div class="invalid-feedback">
                Debe seleccionar una fecha.
            </div>
        </div>
        <div class="col-6">
            <label for="inputSelect" class="form-label">Seleccione el Servicio</label>
            <select id="inputSelect" name="selectItems[]" class="form-select" multiple>
                <option value="siservi">Servicios Sistema SISERVI</option>
                <option value="dieta">Servicios Sistema DIETA</option>
            </select>
        </div>
        <div id="itemsContainer" class="row row-cols-1 row-cols-md-4 g-3 col-12">
            <!-- Dynamic content will be inserted here -->
        </div>
        <div class="col-12">
            <input type="hidden" id="serviceCounts" name="serviceCounts">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>

    <?php
    $content = ob_get_clean();
    include __DIR__ . '/../../layout/layout.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script>
        document.getElementById('inputSelect').addEventListener('change', function() {
            const selectedOptions = Array.from(this.selectedOptions).map(option => option.value);
            const container = document.getElementById('itemsContainer');

            const ListaServicios = [{
                    "name": "siservi",
                    "servicios": [{
                            "name": "Desayuno",
                            "code": "CN-001"
                        },
                        {
                            "name": "Almuerzo",
                            "code": "CN-002"
                        },
                        {
                            "name": "Cena",
                            "code": "CN-003"
                        },
                        {
                            "name": "Refaccion",
                            "code": "CN-004"
                        }
                    ]
                },
                {
                    "name": "dieta",
                    "servicios": [{
                            "name": "Desayuno",
                            "code": "CN-005"
                        },
                        {
                            "name": "Almuerzo",
                            "code": "CN-006"
                        },
                        {
                            "name": "Cena",
                            "code": "CN-007"
                        },
                        {
                            "name": "Merienda Desayuno",
                            "code": "CNM-004"
                        },
                        {
                            "name": "Merienda Almuerzo",
                            "code": "CNM-005"
                        },
                        {
                            "name": "Merienda Cena",
                            "code": "CNM-006"
                        }
                    ]
                }
            ];

            const existingCodes = Array.from(container.querySelectorAll('input[name="serviceCode[]"]'))
                .map(input => input.value);

            const codesToAdd = new Set();

            selectedOptions.forEach(optionName => {
                ListaServicios.filter(servicio => servicio.name === optionName).forEach(sistemas => {
                    sistemas.servicios.forEach(option => {
                        codesToAdd.add(option.code);
                    });
                });
            });

            const allCodesExist = Array.from(codesToAdd).every(code => existingCodes.includes(code));

            if (!allCodesExist) {

                selectedOptions.forEach(optionName => {
                    ListaServicios.filter(servicio => servicio.name === optionName).forEach(sistemas => {
                        const parentDiv = document.createElement('div');
                        parentDiv.classList.add('mb-3', 'w-100');

                        const wrapperDiv = document.createElement('div');
                        wrapperDiv.classList.add('border', 'p-3', 'mb-2');

                        const rowDiv = document.createElement('div');
                        rowDiv.classList.add('row', 'mt-3');

                        const trashIcon = document.createElement('span');
                        trashIcon.classList.add('trash-icon', 'mb-2');
                        trashIcon.innerHTML = '<i class="fas fa-trash"></i>';
                        wrapperDiv.appendChild(trashIcon);

                        sistemas.servicios.forEach(option => {
                            if (!existingCodes.includes(option.code)) {
                                const colDiv = document.createElement('div');
                                colDiv.classList.add('col-md-4', 'mb-3');
                                colDiv.innerHTML = `
                                    <div class="input-group">
                                        <span class="input-group-text">${option.name}</span>
                                        <input type="hidden" name="serviceCode[]" class="form-control" value="${option.code}" readonly>
                                        <input type="number" name="serviceCount[]" class="form-control" placeholder="Ingrese la cantidad" required>
                                        <div class="invalid-feedback">
                                            Debe ingresar la cantidad
                                        </div>
                                    </div>
                                `;
                                rowDiv.appendChild(colDiv);
                                existingCodes.push(option.code);
                            }
                        });

                        wrapperDiv.appendChild(rowDiv);

                        parentDiv.appendChild(wrapperDiv);

                        container.appendChild(parentDiv);

                        trashIcon.addEventListener('click', function() {
                            parentDiv.remove();
                            const codesToRemove = Array.from(parentDiv.querySelectorAll('input[name="serviceCode[]"]'))
                                .map(input => input.value);
                            codesToRemove.forEach(code => {
                                const index = existingCodes.indexOf(code);
                                if (index > -1) {
                                    existingCodes.splice(index, 1);
                                }
                            });
                        });
                    });
                });

                container.addEventListener('input', function(event) {
                    if (event.target.name === 'serviceCount[]') {
                        updateServiceCounts();
                    }
                });
            }


            selectedOptions.forEach(optionName => {
                ListaServicios.filter(servicio => servicio.name === optionName).forEach(sistemas => {
                    sistemas.servicios.forEach(option => {
                        if (!existingCodes.includes(option.code)) {
                            const listItem = document.createElement('li');
                            listItem.classList.add('list-group-item');
                            listItem.innerText = option.name;
                            listGroup.appendChild(listItem);
                        }
                    });
                });
            });
        });

        let serviceCounts = [];

        function updateServiceCounts() {
            serviceCounts = Array.from(document.querySelectorAll('input[name="serviceCount[]"]')).map(input => {
                const codeInput = input.previousElementSibling;
                return {
                    code: codeInput.value,
                    count: input.value
                };
            });
            document.getElementById('serviceCounts').value = JSON.stringify(serviceCounts);
        }

        function validateInput(input) {
            if (input.name === 'serviceCount[]') {
                // Check if input is valid (not empty or positive number)
                if (input.value === '' || isNaN(input.value) || parseInt(input.value) <= 0) {
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            } else if (input.name === 'inputDate') {
                // Check if date input is valid (not empty)
                if (input.value === '') {
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            }
        }

        function validateForm() {
            let isValid = true;

            // Validate date input
            const dateInput = document.getElementById('inputDate');
            validateInput(dateInput);
            if (dateInput.classList.contains('is-invalid')) {
                isValid = false;
            }

            // Validate serviceCount[] inputs
            const serviceCountInputs = document.querySelectorAll('input[name="serviceCount[]"]');
            serviceCountInputs.forEach(input => {
                validateInput(input);
                if (input.classList.contains('is-invalid')) {
                    isValid = false;
                }
            });

            if (!isValid) {
                alert('Por favor, complete todos los campos requeridos correctamente.');
            }

            return isValid;
        }

        // Add focus and blur event listeners to all serviceCount[] inputs
        document.addEventListener('focusin', function(event) {
            if (event.target.name === 'serviceCount[]' || event.target.name === 'inputDate') {
                event.target.classList.remove('is-invalid');
            }
        });

        document.addEventListener('focusout', function(event) {
            if (event.target.name === 'serviceCount[]' || event.target.name === 'inputDate') {
                validateInput(event.target);
            }
        });

        document.getElementById('myForm').addEventListener('submit', function(event) {
            event.preventDefault();
            if (validateForm()) {
                updateServiceCounts();

                const formData = new FormData(this);

                const data = {};
                formData.forEach((value, key) => {
                    if (!data[key]) {
                        data[key] = [];
                    }
                    data[key].push(value);
                });

                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        console.log(`Key: ${key}`);
                        data[key].forEach((value, index) => {
                            console.log(`  Value ${index + 1}: ${value}`);
                        });
                    }
                }
                console.log(data);
            }
        });
    </script>
</div>
