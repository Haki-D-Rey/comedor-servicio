import ApiService from "./../../../js/apiservice.js";

const apiService = new ApiService("");
const urlApiGetAjaxServerSide = window.endpointAjaxServerSideList;
var table = [];
var rowData = [];
var serviceCatalogoDepartamento = [];
var serviceCatalogoCargo = [];
let timerInterval = 0;

var ModalEditarClientes = document.getElementById("exampleModal");
var formDocument = document.getElementById("formularioEditarClientes");
var ButtonAddClient = document.getElementById("btn-agregar");

//FUNCIONES
async function init() {
  try {
    // Obtener los catálogos de departamento y cargo
    serviceCatalogoDepartamento = (
      await listEventServiceCatalogDepartamento()
    ).filter((item) => item.estado);

    // Ordenar por la clave 'nombre'
    serviceCatalogoDepartamento.sort((a, b) => {
      if (a.nombre.toLowerCase() < b.nombre.toLowerCase()) {
        return -1;
      }
      if (a.nombre.toLowerCase() > b.nombre.toLowerCase()) {
        return 1;
      }
      return 0;
    });

    serviceCatalogoCargo = (await listEventServiceCatalogCargo()).filter(
      (item) => item.estado
    );

    // Ordenar por la clave 'nombre' ascedentemente
    serviceCatalogoCargo.sort((a, b) => {
      if (a.nombre.toLowerCase() < b.nombre.toLowerCase()) {
        return -1;
      }
      if (a.nombre.toLowerCase() > b.nombre.toLowerCase()) {
        return 1;
      }
      return 0;
    });

    // Llenar el catálogo de departamentos
    const departmentSelect = document.getElementById("cat-departamento");
    serviceCatalogoDepartamento.forEach((item) => {
      const option = document.createElement("option");
      option.value = item.id;
      option.textContent = item.nombre;
      departmentSelect.appendChild(option);
    });

    // Llenar el catálogo de cargos
    const positionSelect = document.getElementById("cat-cargo");
    serviceCatalogoCargo.forEach((item) => {
      const option = document.createElement("option");
      option.value = item.id;
      option.textContent = item.nombre;
      positionSelect.appendChild(option);
    });
  } catch (error) {
    console.error("Error al obtener los catálogos", error);
  }
}

// Llamar a la función init para inicializar los datos al cargar la página
window.onload = init;

async function ClickUpdateInformationClients(row) {
  const validate = await validateFieldsFormEventService(row);
  if (validate.estado) {
    PopupEditClientError(validate);
  }
  const responseForm = await FormEventServiceUpdateClient(row);
  PopupUpdateClient(responseForm);
  return;
}

function ShowModalEditarClientes(rowData) {
  if (ModalEditarClientes) {
    // Obtener los campos del modal
    const departamentSelect = exampleModal.querySelector("#cat-departamento");
    const positionSelect = exampleModal.querySelector("#cat-cargo");
    const firstNameInput = exampleModal.querySelector("#nombres");
    const lastNameInput = exampleModal.querySelector("#apellidos");
    const emailInput = exampleModal.querySelector("#correo");
    const employeeCodeInput = exampleModal.querySelector("#codigo");
    const statusCheckbox = exampleModal.querySelector("#estado");

    firstNameInput.value = rowData.nombres;
    lastNameInput.value = rowData.apellidos;
    emailInput.value = rowData.correo;
    employeeCodeInput.value = rowData.clie_docnum;

    statusCheckbox.checked = rowData.estado;

    const departmentOptions = departamentSelect.querySelectorAll("option");
    departmentOptions.forEach((option) => {
      if (option.value == rowData.id_departamento) {
        option.selected = true;
      }
    });

    // Establecer los valores del select de "Cargo"
    const positionOptions = positionSelect.querySelectorAll("option");
    positionOptions.forEach((option) => {
      if (option.value == rowData.id_cargo) {
        option.selected = true;
      }
    });

    // Mostrar el modal después de haber llenado los datos
    $(ModalEditarClientes).modal("show");
  }
}

//SERVICES
const deleteEventServicesCell = async (row) => {
  let urlDeleteService = `/admin/clientes/${row.id}`;
  try {
    return await apiService.delete(urlDeleteService);
  } catch (error) {
    console.error("Error al obtener zonas de usuarios:", error);
    return false;
  }
};

const listEventServiceCatalogDepartamento = async () => {
  let url = window.endpointListCatalogDepartamento;
  try {
    return await apiService.get(url);
  } catch (error) {
    console.error("Error al obtener zonas de usuarios:", error);
    return false;
  }
};

const listEventServiceCatalogCargo = async () => {
  let url = window.endpointListCatalogCargo;
  try {
    return await apiService.get(url);
  } catch (error) {
    console.error("Error al obtener zonas de usuarios:", error);
    return false;
  }
};

const validateFieldsFormEventService = async (rowData) => {
  const json = [
    {
      id: rowData["id"],
      fields: [
        {
          correo: rowData["correo"],
          clie_docnum: rowData["clie_docnum"],
        },
      ],
      table: "public.cliente",
      Alias: "TB",
    },
  ];

  const query = encodeURIComponent(JSON.stringify(json));
  const url = `${window.endpointValidateFieldForm}?q=${query}`;
  console.log(url);

  try {
    const response = await apiService.get(url);
    return response;
  } catch (error) {
    console.error("Error al obtener reportes de ventas:", error);
    return null;
  }
};

const FormEventServiceUpdateClient = async (rowEdit) => {
  rowEdit = rowEdit[0];
  let row = {
    nombres: rowEdit["nombres"],
    apellidos: rowEdit["apellidos"],
    id_departamento: parseInt(rowEdit["id_departamento"]),
    id_cargo: parseInt(rowEdit["id_cargo"]),
    correo: rowEdit["correo"],
    clie_docnum: rowEdit["clie_docnum"],
    estado: Boolean(rowEdit["estado"]),
  };
  const json = [
    {
      ...row,
      table: "public.cliente",
      Alias: "TB",
    },
  ];

  const bodyData = json;
  const url = window.endpointFormUpdateClient.replace(
    "/null",
    `/${rowData["id"]}`
  );

  try {
    const response = await apiService.put(url, bodyData);
    console.log(response);
    return response;
  } catch (error) {
    console.error("Error al obtener reportes de ventas:", error);
    return null;
  }
};

//ALERTAS
function PopupDeleteClient(response) {
  timerInterval = 0;
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
      popup:
        "border-3 rounded-2 border-color col-6 col-sm-9 w-25 bg-success text-white",
    },
    buttonsStyling: true,
  });

  if (!response.estado) {
    // Si hay errores, muestra una alerta de warning con fondo rojo
    swalWithBootstrapButtons
      .fire({
        title: "Error al Eliminar Cliente",
        icon: "warning",
        background: "#f8d7da", // Fondo rojo claro
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
          timer = Swal.getPopup().querySelector("b") || 0;
          timerInterval = setInterval(() => {
            if (timer) {
              timer.textContent = `${Swal.getTimerLeft() / 1000}`;
            }
          }, 250);
        },
        willClose: () => {
          clearInterval(timerInterval);
        },
      })
      .then((result) => {
        table.ajax.reload();
      });
  } else {
    // Si no hay errores, muestra la alerta de éxito
    swalWithBootstrapButtons
      .fire({
        title: "Cliente Eliminado con Éxito",
        html: "El cliente ha sido eliminado correctamente. La página se recargará en <b></b> segundos.",
        icon: "success",
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
          const timer = Swal.getPopup().querySelector("b") || 0;
          timerInterval = setInterval(() => {
            if (timer) {
              timer.textContent = `${Swal.getTimerLeft() / 1000}`;
            }
          }, 250);
        },
        willClose: () => {
          clearInterval(timerInterval);
        },
      })
      .then((result) => {
        table.ajax.reload();
      });
  }
}

function PopupEditClientError(response) {
  timerInterval = 0;
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
      popup:
        "border-3 rounded-2 border-color col-6 col-sm-9 w-25 bg-success text-white",
    },
    buttonsStyling: true,
  });

  if (!response.estado) {
    // Si hay errores, muestra una alerta de warning con fondo rojo
    swalWithBootstrapButtons
      .fire({
        title: "Error al Editar el Cliente",
        icon: "warning",
        html: `<p>${response.message}</p>`,
        background: "#f8d7da", // Fondo rojo claro
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
          timer = Swal.getPopup().querySelector("b") || 0;
          timerInterval = setInterval(() => {
            if (timer) {
              timer.textContent = `${Swal.getTimerLeft() / 1000}`;
            }
          }, 250);
        },
        willClose: () => {
          clearInterval(timerInterval);
        },
      })
      .then((result) => {
        table.ajax.reload();
      });
  }
}

function PopupUpdateClient(response) {
  timerInterval = 0;
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
      popup:
        "border-3 rounded-2 border-color col-6 col-sm-9 w-25 bg-success text-white",
    },
    buttonsStyling: true,
  });

  if (!response.estado) {
    // Si hay errores, muestra una alerta de warning con fondo rojo
    swalWithBootstrapButtons
      .fire({
        title: "Error al Actualizar los Datos del Cliente",
        icon: "warning",
        html: `<p>${response.message}</p>`,
        background: "#f8d7da", // Fondo rojo claro
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
          timer = Swal.getPopup().querySelector("b") || 0;
          timerInterval = setInterval(() => {
            if (timer) {
              timer.textContent = `${Swal.getTimerLeft() / 1000}`;
            }
          }, 250);
        },
        willClose: () => {
          clearInterval(timerInterval);
        },
      })
      .then((result) => {
        table.ajax.reload();
      });
  } else {
    $(ModalEditarClientes).modal("hide");
    rowData = [];
    // Si no hay errores, muestra la alerta de éxito
    swalWithBootstrapButtons
      .fire({
        title: "Se ha Actualizado los Datos del Cliente con Éxito",
        html: "La página se recargará en <b></b> segundos.",
        icon: "success",
        timer: 2000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
          const timer = Swal.getPopup().querySelector("b") || 0;
          timerInterval = setInterval(() => {
            if (timer) {
              timer.textContent = `${Swal.getTimerLeft() / 1000}`;
            }
          }, 250);
        },
        willClose: () => {
          clearInterval(timerInterval);
        },
      })
      .then((result) => {
        table.ajax.reload();
      });
  }
}

// buttonEdit.addEventListener("submit", ClickUpdateInformationClients);

// Agregar un event listener para manejar el evento "submit" del formulario
formDocument.addEventListener("submit", function (event) {
  event.preventDefault();

  var formData = new FormData(formDocument);
  var formValues = {};
  var isValid = true;

  formData.forEach(function (value, key) {
    var checkboxElement = document.querySelector(`#${key}`);
    if (
      checkboxElement &&
      checkboxElement.classList.contains("form-check-input")
    ) {
      formValues[key] = checkboxElement.checked ? true : false;
    } else {
      if (value === "on") {
        formValues[key] = true;
      } else if (value === "off") {
        formValues[key] = false;
      } else {
        formValues[key] = value;
      }
    }
  });

  formValues["id"] = rowData["id"];

  // Mostrar los valores capturados en la consola
  for (const [key, value] of Object.entries(formValues)) {
    console.log(key + ": " + value);
  }

  if (isValid) {
    ClickUpdateInformationClients([formValues]);
  } else {
    console.log("Formulario no válido. Corrige los campos requeridos.");
  }
});

document.addEventListener("DOMContentLoaded", function () {
  ModalEditarClientes.addEventListener("hidden.bs.modal", function () {
    return (rowData = []);
  });

  table = new DataTable("#tablaListaClientes", {
    serverSide: true,
    processing: true,
    ajax: {
      url: `${urlApiGetAjaxServerSide}`,
      type: "POST",
      contentType: "application/json",
      data: function (d) {
        return JSON.stringify({
          tabla: "public.cliente",
          column_dtrow: "clie_docnum",
          columns_table: [
            {
              name: "id",
              type: "int",
              relational: [],
            },
            {
              name: "nombres",
              type: "text",
              relational: [],
            },
            {
              name: "apellidos",
              type: "text",
              relational: [],
            },
            {
              name: "id_departamento",
              type: "int",
              relational: [
                {
                  name: "catalogo.departamento",
                  column_table: "nombre",
                  type: "INNER JOIN",
                  filters: [
                    {
                      name: "estado",
                      value: true,
                    },
                  ],
                },
              ],
            },
            {
              name: "id_cargo",
              type: "int",
              relational: [
                {
                  name: "catalogo.cargo",
                  column_table: "nombre",
                  type: "INNER JOIN",
                  filters: [
                    {
                      name: "estado",
                      value: true,
                    },
                  ],
                },
              ],
            },
            {
              name: "correo",
              type: "text",
              relational: [],
            },
            {
              name: "clie_docnum",
              type: "text",
              relational: [],
            },
            {
              name: "fecha_creacion",
              type: "text",
              relational: [],
            },
            {
              name: "estado",
              type: "bool",
              relational: [],
            },
          ].map(function (column) {
            var colData = d.columns.find(function (col) {
              if (column.relational && column.relational.length > 0) {
                let columname =
                  column.name + "_" + column.relational[0].column_table;
                return columname === col.data;
              }

              return col.data === column.name;
            });

            if (colData) {
              column.searchable = colData.searchable || false;
              column.orderable = colData.orderable || false;
              column.search = {
                value: colData.search.value || "",
                regex: colData.search.regex || false,
              };
            }

            return column;
          }),
          filters: [
            {
              name: "estado",
              value: true,
            },
          ],
          searchValue: d.search.value, // Valor de búsqueda global
          start: d.start, // Índice de inicio para la paginación
          length: d.length, // Número de registros por página
          orderColumn: d.order[0].column, // Índice de la columna que está ordenada
          orderDirection: d.order[0].dir, // Dirección del orden (ascendente o descendente)
          draw: d.draw, // Número de "draw" para la solicitud
          columns: d.columns.map(function (col, index) {
            return {
              data: col.data,
              name: col.name || "", // Si no existe 'name', usar una cadena vacía
              searchable: col.searchable || false, // Si no existe 'searchable', usar 'false'
              orderable: col.orderable || false,
              search: {
                value: col.search.value || "", // Valor de búsqueda para la columna
                regex: col.search.regex || false, // Si no existe 'regex', usar 'false'
              },
            };
          }),
        });
      },
      dataSrc: function (response) {
        return response.data;
      },
      dataType: "json",
      error: function (xhr, error, thrown) {
        console.log("Error:", error);
      },
    },
    paging: true,
    pageLength: 25,
    lengthMenu: [5, 10, 25, 50, 100],
    searching: true,
    ordering: true,
    info: true,
    columns: [
      {
        data: "dt_rowid",
        title: "N°",
        searchable: false,
        orderable: false,
      },
      {
        data: "id",
        title: "id",
        searchable: false,
        orderable: false,
        visible: false,
      },
      {
        data: "nombres",
        title: "Nombre",
        searchable: true,
        orderable: true,
      },
      {
        data: "apellidos",
        title: "Apellido",
        searchable: true,
        orderable: true,
      },
      {
        data: "id_departamento_nombre",
        title: "Departamento",
        searchable: true,
        orderable: true,
      },
      {
        data: "id_cargo_nombre",
        title: "Cargo",
        searchable: true,
        orderable: true,
      },
      {
        data: "correo",
        title: "Correo",
        searchable: true,
        orderable: false,
      },
      {
        data: "clie_docnum",
        title: "Codigo Empleado",
        searchable: true,
        orderable: true,
      },
      {
        data: "fecha_creacion",
        title: "Fecha",
        searchable: true,
        orderable: true,
      },
      {
        data: "estado",
        title: "Estado",
        searchable: true,
        orderable: true,
        visible: false,
      },
      {
        data: null,
        title: "Operaciones",
        orderable: false,
        searchable: false,
        render: function (data, type, row) {
          return `<div class="d-flex flex-row justify-content-start column-gap-3">
                        <button class="btn btn-success edit-btn" data-id="${row.id}"  data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@getbootstrap">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger delete-btn" data-id="${row.id}">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                        </div>`;
        },
      },
    ],
    order: [[7, "asc"]],
    scrollY: "50vh",
    scrollX: "70vw",
    scrollCollapse: true,
    responsive: true,
    layout: {
      top2Start: {
        buttons: ["excel", "pdf"],
      },
      topStart: "pageLength",
      top2End: {
        div: {
          id: "btn-agregar",
          class: "layout-full",
          text: "agregar",
        },
      },
      topEnd: "search",
      bottomStart: "info",
      bottomEnd: "paging",
    },
    language: {
      paginate: {
        previous: "Anterior",
        next: "Siguiente",
      },
      search: "Buscar:",
      lengthMenu: "Mostrar _MENU_ registros por página",
      info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
    },
    preXhr: function (e) {
      $("#DataTables_Table_0_processing").css("display", "block");
    },
    drawCallback: function () {
      $("#DataTables_Table_0_processing").css("display", "none");
    },
    infoCallback: function (settings, start, end, max, total, pre) {
      return total + " Clientes registrados.";
    },
  });

  ButtonAddClient.style;

  // Evento para manejar el clic en el botón de editar
  table.on("click", ".edit-btn", function () {
    rowData = table.row($(this).closest("tr")).data();
    console.log(rowData);
    ShowModalEditarClientes(rowData);
    return;
  });

  table.on("click", ".delete-btn", async function () {
    rowData = table.row($(this).closest("tr")).data();
    const response = await deleteEventServicesCell(rowData);
    PopupDeleteClient(response);
    return (rowData = []);
  });

  // Agregar evento de doble clic en las celdas para activar el modo de edición
  table.on("dblclick", "td", function () {
    rowData = table.row($(this).closest("tr")).data();
    ShowModalEditarClientes(rowData);
    return;
  });
});
