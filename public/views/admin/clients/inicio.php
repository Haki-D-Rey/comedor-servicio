<?php
ob_start(); // Inicia el almacenamiento en búfer de salida
?>
<div class="d-flex flex-column align-items-center w-100 p-4">
    <div class="p-4 align-content-center d-flex flex-column align-items-center w-50">
        <h2 class="custom-heading text-center mb-1">Listado de Clientes</h2>
        <hr class="hr-heading">
    </div>

    <!-- Modal -->
    <div class="modal modal-xl fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form id="formularioEditarClientes">
                <div class="modal-content">
                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Editar Informacion del Cliente</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <!-- Modal Body (Formulario) -->
                    <div class="modal-body">

                        <!-- Fila 1 (2 columnas) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nombres" class="form-label">Nombres</label>
                                <input type="text" class="form-control" name="nombres" id="nombres" placeholder="Ingresa tus nombres" required>
                            </div>
                            <div class="col-md-6">
                                <label for="apellidos" class="form-label">Apellidos</label>
                                <input type="text" class="form-control" name="apellidos" id="apellidos" placeholder="Ingresa tus apellidos" required>
                            </div>
                        </div>

                        <!-- Fila 2 (2 columnas) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="cat-departamento" class="form-label">Catalogo Departamento</label>
                                <select class="form-select" name="id_departamento" id="cat-departamento" required>
                                    <option selected>Seleccione el Departamento</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="cat-cargo" class="form-label">Catalogo Cargo</label>
                                <select class="form-select" name="id_cargo" id="cat-cargo" required>
                                    <option selected>Seleccione el Cargo</option>
                                </select>
                            </div>
                        </div>

                        <!-- Fila 3 (2 columnas) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="correo" class="form-label">Correo</label>
                                <input type="email" class="form-control" name="correo" id="correo" placeholder="Ingrese su correo electronico" required>
                            </div>
                            <div class="col-md-6">
                                <label for="codigo" class="form-label">Codigo de Empleado</label>
                                <input type="text" class="form-control" name="clie_docnum" id="codigo" placeholder="Ingrese el codigo de empleado" required>
                            </div>
                        </div>

                        <!-- Fila 4 (3 columnas) -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="estado" class="form-label">Estado Cliente</label>
                                <input type="checkbox" class="form-check-input" name="estado" id="estado">
                            </div>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="d-flex flex-row justify-content-center m-4 column-gap-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-success" id="btn-updateclient">Actualizar</button>
                    </div>

                </div>
            </form>
        </div>
    </div>


    <table id="tablaListaClientes" class="display" style="width:100%">
        <thead>
            <tr>
                <th>id</th>
                <th>nombres</th>
                <th>apellidos</th>
                <th>id_departamento_nombre</th>
                <th>id_cargo_nombre</th>
                <th>correo</th>
                <th>clie_docnum</th>
                <th>fecha_creacion</th>
            </tr>
        </thead>
        <tbody>
            <!-- Aquí se cargarán los datos dinámicamente con DataTables -->
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean(); // Obtiene el contenido del búfer y limpia el búfer
include __DIR__ . '/../../layout/layout.php'; // Incluye el layout
?>
<script type="module" src="/assets/js/Dashboard/Admin/clientes.js"></script>
<script>
    window.endpointAjaxServerSideList = "<?php echo $routeParser->urlFor('admin_ajax.server-side-query'); ?>";
    window.endpointListCatalogDepartamento = "<?php echo $routeParser->urlFor('departamentos.list_all'); ?>";
    window.endpointListCatalogCargo = "<?php echo $routeParser->urlFor('cargos.list_all'); ?>";
    window.endpointValidateFieldForm = "<?php echo $routeParser->urlFor('clientes.validate_form'); ?>";
    window.endpointFormUpdateClient = "<?php echo $routeParser->urlFor('clientes.update', ['id' => 'null']); ?>";
    // // Función para filtrar las opciones del select mientras permanece abierto
    // function filterOptions(selectId, searchOptionId) {
    //     // Obtener el valor del campo de búsqueda
    //     const searchInput = document.getElementById(searchOptionId).value.toLowerCase();

    //     // Obtener el select y sus opciones
    //     const selectElement = document.getElementById(selectId);
    //     const options = selectElement.getElementsByTagName("option");

    //     // Mostrar todas las opciones, excepto la primera (la de búsqueda)
    //     for (let i = 1; i < options.length; i++) {
    //         const option = options[i];
    //         // Si el texto de la opción contiene el valor del campo de búsqueda, mostrar la opción
    //         if (option.text.toLowerCase().includes(searchInput)) {
    //             option.style.display = "";
    //         } else {
    //             option.style.display = "none"; // Ocultar la opción
    //         }
    //     }
    // }

    // Asignar el evento keyup al campo de búsqueda de cada select
    // document.getElementById("cat-departamento").addEventListener("keyup", function() {
    //     filterOptions('cat-departamento', 'cat-departamento');
    // });

    // document.getElementById("cat-cargo").addEventListener("keyup", function() {
    //     filterOptions('cat-cargo', 'cat-cargo');
    // });
</script>