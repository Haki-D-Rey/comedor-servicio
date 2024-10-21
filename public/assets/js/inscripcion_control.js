// Importa la clase ApiService
import ApiService from "./apiservice.js";

// Variables globales
var ListaServiciosValidar = [];
var ListaServicios = [];
var listaSistemas = [];
var listaTipoServicios = [];
var existingCodes = [];
var codesToAdd = new Set();
var selectedOption = "";
var baseURL = "";
var selectZonaUsuario = document.getElementById("select_zona_usuario");
var selectClickZonaUsuario = document.getElementById("inputSelect");
var inputDateDOM = document.getElementById("inputDate");
var zonasUsuarios = [];
var existingCodes = [];

const apiService = new ApiService(baseURL);

function init() {
  inputDateDOM.value = "";
  inputDateDOM.textContent = "";

  selectZonaUsuario.disabled = true;
  selectClickZonaUsuario.disabled = true;
}

document.addEventListener("DOMContentLoaded", () => {
  init();
  cargarZonasEnSelect();
  changeZonasEnSelect();
});

inputDateDOM.addEventListener("change", async function () {
  const inputFecha = this;
  const fechaSeleccionada = inputFecha.value;

  const fechasBloqueadas = await getValidateInsertControlStadistics(
    fechaSeleccionada
  );

  if (!fechasBloqueadas) {
    return;
  }

  // Validar si la fecha seleccionada está ocupada
  const fechaOcupada = fechasBloqueadas.some(
    (bloqueo) => bloqueo.fecha === fechaSeleccionada
  );

  if (fechaOcupada) {
    inputFecha.value = "";
    return alertValidateDate(fechaSeleccionada);
  }
  selectZonaUsuario.disabled = false;
});

/**
 * Función para obtener las zonas de usuarios por ID desde la API.
 * @returns {Promise<Object>} Lista de zonas de usuarios.
 */
async function getZonasUsuariosById() {
  const endpointListaZonaUsuarios = window.endpointListaZonasUsuario;

  try {
    return await apiService.get(endpointListaZonaUsuarios);
  } catch (error) {
    console.error("Error al obtener zonas de usuarios:", error);
    return null;
  }
}

/**
 * Función para obtener las zonas de usuarios por ID desde la API.
 * @returns {Promise<Object>} Lista de zonas de usuarios.
 */
async function getZonasUsuariosServiciosValidar() {
  const endpointListaZonaUsuariosServiciosValidar =
    window.endpointListaZonaUsuariosServiciosValidar;

  try {
    return await apiService.get(endpointListaZonaUsuariosServiciosValidar);
  } catch (error) {
    console.error("Error al obtener zonas de usuarios:", error);
    return null;
  }
}

/**
 * Función para obtener las zonas de usuarios por ID desde la API.
 * @returns {Promise<Object>} Lista de zonas de usuarios.
 */
async function getValidateInsertControlStadistics(date) {
  const endpointListaZonaUsuarios = `/control-estadisticos-servicios/filter-date-search/${date}`;

  try {
    return await apiService.get(endpointListaZonaUsuarios);
  } catch (error) {
    console.error("Error al obtener zonas de usuarios:", error);
    return null;
  }
}

/**
 * Función para obtener las zonas de usuarios por ID desde la API.
 * @returns {Promise<Object>} Lista de zonas de usuarios.
 */
async function validateInsertControlStadistics(data) {
  const endpointListaZonaUsuarios = window.endpointEnviarFormulario;
  try {
    return await apiService.post(endpointListaZonaUsuarios, data);
  } catch (error) {
    console.error("Error al obtener zonas de usuarios:", error);
    return null;
  }
}

/**
 * Cargar las zonas en el elemento select al obtener los datos de la API.
 */
async function cargarZonasEnSelect() {
  zonasUsuarios = await getZonasUsuariosById();
  if (!zonasUsuarios) return;

  const detallesZonas = zonasUsuarios.DetallesZonas || [];

  ListaServiciosValidar = await getZonasUsuariosServiciosValidar();
  [listaSistemas, listaTipoServicios] = await cargarSistemasYServicios();

  renderZonasEnSelect(detallesZonas);
}

/**
 * Renderiza las opciones de zonas dentro del elemento select.
 * @param {Array<Object>} detallesZonas - Lista de detalles de zonas.
 */
function renderZonasEnSelect(detallesZonas) {
  // Limpiamos el select y añadimos una opción por defecto
  selectZonaUsuario.innerHTML =
    '<option value="" selected disabled>Seleccione una zona</option>';

  // Iteramos sobre las zonas y las añadimos al select
  const resultado = [];
  detallesZonas.forEach((detalleZona) => {
    const option = document.createElement("option");
    option.value = detalleZona.codigoInternoZonaUsuario;
    option.textContent = detalleZona.zonas.nombre;
    selectZonaUsuario.appendChild(option);

    resultado.push(
      ...procesarServicios(
        ListaServiciosValidar,
        listaSistemas,
        listaTipoServicios,
        detalleZona.idZonaUsuario,
        detalleZona.codigoInternoZonaUsuario
      )
    );
  });

  return (ListaServiciosValidar = resultado);
}

/**
 * Configura el evento para manejar el cambio de selección de zona.
 * Al seleccionar una zona, se cargan los detalles de servicios y productos.
 */
function changeZonasEnSelect() {
  selectZonaUsuario.addEventListener("change", async (event) => {
    selectClickZonaUsuario.textContent = "";
    const idZonaUsuario = event.target.value;
    if (!idZonaUsuario) return;

    // Cargar servicios y productos correspondientes a la zona seleccionada
    try {
      // Procesar los servicios para la zona seleccionada
      const resultadoListaServicios = ListaServiciosValidar.filter(
        (lista) => lista.code_zone === idZonaUsuario
      );

      // Verificar si ya existen servicios de esta zona para evitar duplicados
      resultadoListaServicios.forEach((nuevoServicio) => {
        const servicioDuplicado = ListaServicios.some(
          (servicioExistente) =>
            servicioExistente.codigo_sistema === nuevoServicio.codigo_sistema &&
            servicioExistente.codigo_tiposervicio ===
              nuevoServicio.codigo_tiposervicio &&
            servicioExistente.code_zone === idZonaUsuario
        );

        // Si no es duplicado, lo agregamos
        if (!servicioDuplicado) {
          ListaServicios.push({
            ...nuevoServicio,
            code_zone: idZonaUsuario, // Asegurarse de asociar con la zona correcta
          });
        }
      });

      // Actualizar el DOM con los nuevos servicios sin duplicados
      addSelectItemClickListZona(ListaServicios, idZonaUsuario);
    } catch (error) {
      console.error("Error al procesar las solicitudes:", error);
    }
  });
}

/**
 * Carga los detalles de servicios y productos de una zona por su ID.
 * @param {string} idZonaUsuario - ID de la zona seleccionada.
 * @returns {Promise<Array>} Lista de detalles de servicios y productos.
 */
async function cargarDetallesServiciosPorZona(idZonaUsuario) {
  const endpoint = `/detalle-zona-servicio-horario/getByIdZonaUsuarioDetalleServicio/${idZonaUsuario}`;
  return apiService.get(endpoint);
}

/**
 * Carga la lista de sistemas y tipos de servicios desde la API.
 * @returns {Promise<Array>} Una tupla con la lista de sistemas y tipos de servicios.
 */
async function cargarSistemasYServicios() {
  const [listaSistemas, listaTipoServicios] = await Promise.all([
    apiService.get(window.endpointListaSistemas),
    apiService.get(window.endpointListaTipoServicios),
  ]);
  return [listaSistemas, listaTipoServicios];
}

/**
 * Procesa y organiza los servicios en base a los tipos de servicios y sistemas.
 * @param {Array} listaServiciosProductosDetalles - Lista de detalles de servicios/productos.
 * @param {Array} listaSistemas - Lista de sistemas.
 * @param {Array} listaTipoServicios - Lista de tipos de servicios.
 * @returns {Array} Estructura organizada de servicios por sistemas y tipos.
 */
function procesarServicios(
  listaServiciosProductosDetalles,
  listaSistemas,
  listaTipoServicios,
  idZona,
  codigoInternoZonaUsuario
) {
  const result = [];

  // Iteramos sobre los tipos de servicios
  listaTipoServicios.forEach((tipoServicio) => {
    const idTipoServicio = tipoServicio.id;

    // Procesar servicios por cada sistema
    listaSistemas.forEach((sistema) => {
      const idSistema = sistema.id;

      // Filtramos los servicios que coinciden con el tipo y sistema actual
      const matchingServices = listaServiciosProductosDetalles.filter(
        (service) =>
          service.idSistemas === idSistema &&
          service.idTipoServicios === idTipoServicio &&
          service.idZonaUsuario === idZona
      );

      if (matchingServices.length > 0) {
        result.push({
          sistema: sistema.nombre,
          codigo_sistema: sistema.codigo_interno,
          tipo_servicio: tipoServicio.nombre,
          codigo_tiposervicio: tipoServicio.codigo_interno,
          code_zone: codigoInternoZonaUsuario,
          servicios: transformarServicios(matchingServices),
        });
      }
    });

    // Procesar los servicios que no tienen sistemas definidos
    const serviciosSinSistema = listaServiciosProductosDetalles.filter(
      (service) =>
        !service.idSistemas && service.idTipoServicios === idTipoServicio
    );

    if (serviciosSinSistema.length > 0) {
      return;
    }
  });

  return result;
}

function addSelectItemClickListZona(lista, code_zone) {
  const uniqueCodes = new Set();

  lista
    .filter((detalle) => detalle.code_zone === code_zone) // Filtrar solo los servicios de la zona seleccionada
    .forEach((detalle) => {
      if (!uniqueCodes.has(detalle.codigo_sistema)) {
        uniqueCodes.add(detalle.codigo_sistema);

        const option = document.createElement("option");
        option.value = detalle.codigo_sistema;
        option.textContent = detalle.sistema;
        selectClickZonaUsuario.appendChild(option);
        selectClickZonaUsuario.disabled = false;
      }
    });
}

/**
 * Transforma la lista de servicios a un formato más simple.
 * @param {Array} services - Lista de servicios a transformar.
 * @returns {Array} Servicios transformados con nombre, código y otros datos.
 */
function transformarServicios(services) {
  return services.map((service) => ({
    name: service.nombre,
    code: service.codigo_interno,
    id_detalle_zona_servicio_horario: service.id_detalle_zona_servicio_horario,
    id_servicio_detalle_producto: service.id_servicio_detalle_producto,
  }));
}

document
  .getElementById("inputSelect")
  .addEventListener("change", async function () {
    validateCreateServices(this);
  });
function validateCreateServices(event) {
  const selectedOptions = Array.from(event.selectedOptions).map(
    (option) => option.value
  );

  selectedOption = selectZonaUsuario?.options[selectZonaUsuario.selectedIndex];
  if (!selectedOption || !selectedOption.value) {
    console.error("No se ha seleccionado una zona o el valor es inválido.");
    return;
  }

  const container = document.getElementById("itemsContainer");
  const codesToAdd = new Set();

  // Filtrar y agregar servicios seleccionados
  selectedOptions.forEach((optionName) => {
    ListaServicios.filter(
      (servicio) => servicio.codigo_sistema === optionName
    ).forEach((sistemas) => {
      codesToAdd.add({
        codigo_sistema: sistemas.codigo_sistema,
        codigo_tiposervicio: sistemas.codigo_tiposervicio,
        code_zone: selectedOption.value,
        servicios: sistemas.servicios,
        sistema: sistemas.sistema,
        tipo_servicio: sistemas.tipo_servicio,
      });
    });
  });

  // Filtrar códigos duplicados
  const uniqueCodesToAdd = Array.from(codesToAdd).filter(
    (newCode) =>
      !existingCodes.some(
        (existingCode) =>
          existingCode.codigo_sistema === newCode.codigo_sistema &&
          existingCode.codigo_tiposervicio === newCode.codigo_tiposervicio &&
          existingCode.code_zone === newCode.code_zone
      )
  );

  if (uniqueCodesToAdd.length > 0) {
    uniqueCodesToAdd.forEach(
      ({
        tipo_servicio,
        sistema,
        codigo_sistema,
        codigo_tiposervicio,
        code_zone,
        servicios,
      }) => {
        let zonaContainer = container.querySelector(
          `.zona-container[data-zona="${code_zone}"]`
        );

        // Crear contenedor de zona si no existe
        if (!zonaContainer) {
          zonaContainer = createZonaContainer(
            selectedOption.textContent,
            code_zone
          );
          container.appendChild(zonaContainer);
        }

        // Agregar servicios a la zona
        addServicesToZona(
          zonaContainer,
          servicios,
          codigo_sistema,
          codigo_tiposervicio,
          code_zone,
          tipo_servicio,
          sistema
        );
      }
    );
  }

  // Actualizar conteos de servicios
  container.addEventListener("input", updateServiceCounts);
}

/**
 * Crea el contenedor de zona.
 */
function createZonaContainer(selectedText, code_zone) {
  const zonaContainer = document.createElement("div");
  zonaContainer.classList.add("zona-container", "mt-4", "w-100");
  zonaContainer.dataset.zona = code_zone;

  const zonaTitle = document.createElement("h4");
  zonaTitle.innerText = `Zona - ${selectedText}`;
  zonaContainer.appendChild(zonaTitle);

  return zonaContainer;
}

/**
 * Agrega los servicios al contenedor de zona.
 */
function addServicesToZona(
  zonaContainer,
  servicios,
  codigo_sistema,
  codigo_tiposervicio,
  code_zone,
  tipo_servicio,
  sistema
) {
  const parentDiv = document.createElement("div");
  parentDiv.classList.add("mb-3", "w-100");

  const wrapperDiv = document.createElement("div");
  wrapperDiv.classList.add(
    "border",
    "border-2",
    "border-success",
    "border-opacity-75",
    "rounded-3",
    "p-3",
    "mb-2"
  );

  const headerDivContent = document.createElement("div");
  headerDivContent.classList.add(
    "d-flex",
    "flex-row",
    "align-items-center",
    "justify-content-between"
  );

  const titleContent = document.createElement("h4");
  titleContent.classList.add("flex-grow-1", "m-2", "titleServiceFilter");
  titleContent.innerHTML = `${sistema} ${
    codigo_sistema === "SIS-0001" ? "" : `- ${tipo_servicio}`
  }`;

  const trashIcon = document.createElement("span");
  trashIcon.classList.add("trash-icon", "btn", "active");
  trashIcon.innerHTML = '<i class="fas fa-trash"></i>';

  headerDivContent.appendChild(titleContent);
  headerDivContent.appendChild(trashIcon);
  wrapperDiv.appendChild(headerDivContent);

  const rowDiv = document.createElement("div");
  rowDiv.classList.add("row", "mt-3");

  servicios.forEach((option) => {
    if (
      !existingCodes.some(
        (existing) =>
          existing.code === option.code &&
          existing.codigo_sistema === codigo_sistema &&
          existing.codigo_tiposervicio === codigo_tiposervicio &&
          existing.code_zone === code_zone
      )
    ) {
      const colDiv = document.createElement("div");
      colDiv.classList.add("col-12", "col-sm-4", "col-md-6");
      colDiv.innerHTML = `
          <div class="form-floating mb-3">
            <input type="number" class="form-control" id="floatingInput" name="serviceCount[]" value="" placeholder="Ingrese la cantidad" required>
            <label for="floatingInput">${option.name}</label>
            <input type="hidden" class="form-control" value="${option.code}" name="serviceCode[]" data-tipo-servicio="${codigo_tiposervicio}" data-tipo-sistema="${codigo_sistema}" data-detalle-servicios="${option.id_detalle_zona_servicio_horario}" data-code-zone="${code_zone}" readonly>
            <div class="invalid-feedback">Debe ingresar la cantidad</div>
          </div>
        `;
      rowDiv.appendChild(colDiv);
      existingCodes.push({
        code: option.code,
        codigo_sistema: codigo_sistema,
        codigo_tiposervicio: codigo_tiposervicio,
        code_zone: code_zone,
      });
      wrapperDiv.appendChild(rowDiv);
      parentDiv.appendChild(wrapperDiv);
    }
  });
  zonaContainer.appendChild(parentDiv);

  // Evento para eliminar el elemento del DOM
  trashIcon.addEventListener("click", () => {
    parentDiv.remove();
    removeCodesFromExisting(parentDiv, selectedOption.value);
  });
}

/**
 * Elimina los códigos de la lista existente.
 */
function removeCodesFromExisting(parentDiv, code_zone) {
  const codesToRemove = Array.from(
    parentDiv.querySelectorAll('input[name="serviceCode[]"]')
  ).map((input) => ({
    code: input.value,
    codigo_sistema: input.dataset.tipoSistema,
    codigo_tiposervicio: input.dataset.tipoServicio,
    code_zone: code_zone,
  }));

  codesToRemove.forEach((codeToRemove) => {
    existingCodes = existingCodes.filter(
      (existingCode) =>
        existingCode.code !== codeToRemove.code ||
        existingCode.codigo_sistema !== codeToRemove.codigo_sistema ||
        existingCode.codigo_tiposervicio !== codeToRemove.codigo_tiposervicio ||
        existingCode.code_zone !== codeToRemove.code_zone
    );
  });
}

function updateServiceCounts() {
  const serviceCounts = Array.from(
    document.querySelectorAll('input[name="serviceCount[]"]')
  ).map((input) => {
    const codeInput = input.parentElement.querySelector(
      'input[name="serviceCode[]"]'
    );
    return {
      code: codeInput.value,
      value: input.value,
      codigo_tipo_servicio: codeInput.dataset.tipoServicio,
      codigo_tipo_sistema: codeInput.dataset.tipoSistema,
      id_detalle_zona_servicio_horario: codeInput.dataset.detalleServicios,
      code_zone: codeInput.dataset.codeZone,
    };
  });
  document.getElementById("serviceCounts").value =
    JSON.stringify(serviceCounts);
}

function validateInput(input) {
  if (input.name === "serviceCount[]") {
    if (
      input.value === "" ||
      isNaN(input.value) ||
      parseInt(input.value) <= 0
    ) {
      input.classList.add("is-invalid");
    } else {
      input.classList.remove("is-invalid");
    }
  } else if (input.name === "inputDate") {
    // Check if date input is valid (not empty)
    if (input.value === "") {
      input.classList.add("is-invalid");
    } else {
      input.classList.remove("is-invalid");
    }
  }
}

function validateForm(existingCodes) {
  let isValid = true;

  // Validate date input
  const dateInput = document.getElementById("inputDate");
  validateInput(dateInput);
  if (dateInput.classList.contains("is-invalid")) {
    isValid = false;
  }

  // Obtener los inputs de serviceCount[]
  const serviceCountInputs = document.querySelectorAll(
    'input[name="serviceCount[]"]'
  );

  // Validar que cada servicio en ListaServicios tenga una entrada correspondiente en existingCodes
  const missingServices = [];

  // Crear un mapa para un acceso rápido a los servicios existentes
  const existingCodesMap = new Map();
  existingCodes.forEach((existing) => {
    const key = `${existing.code_zone}-${existing.codigo_sistema}-${existing.codigo_tiposervicio}-${existing.code}`;
    existingCodesMap.set(key, existing);
  });

  ListaServiciosValidar.forEach((servicioZona) => {
    const { code_zone, servicios, codigo_sistema, codigo_tiposervicio } =
      servicioZona;

    // Validar si cada servicio de esta code_zone está en existingCodes
    servicios.forEach((servicio) => {
      const key = `${code_zone}-${codigo_sistema}-${codigo_tiposervicio}-${servicio.code}`;

      if (!existingCodesMap.has(key)) {
        // Comprobar si el servicio ya está en missingServices antes de agregarlo
        const isServiceMissing = missingServices.some(
          (missing) =>
            missing.servicio === servicio.name &&
            missing.tipo_servicio === codigo_tiposervicio &&
            missing.tipo_sistema === codigo_sistema &&
            missing.code_zone === code_zone
        );

        // Solo agregar si no está en missingServices
        if (!isServiceMissing) {
          missingServices.push({
            tipo_servicio: codigo_tiposervicio,
            tipo_sistema: codigo_sistema,
            servicio: servicio.name,
            code_zone: code_zone,
          });
        }
      }
    });
  });

  // Si hay servicios faltantes, marcar isValid como false
  if (missingServices.length > 0) {
    let messageAlert = [];
    missingServices.forEach(
      ({ tipo_servicio, tipo_sistema, servicio, code_zone }) => {
        let nombreSistema = listaSistemas.find(
          (lista) => lista.codigo_interno === tipo_sistema
        ).nombre;
        let nombreTipoServicio =
          tipo_sistema === "SIS-0001"
            ? "Tipo Servicio General"
            : listaTipoServicios.find(
                (lista) => lista.codigo_interno === tipo_servicio
              ).descripcion;
        let nombreZona = zonasUsuarios.DetallesZonas.find(
          (zona) => zona.codigoInternoZonaUsuario === code_zone
        ).zonas.nombre;

        // Push the row data for the table
        messageAlert.push(
          `<tr>
           <td>${nombreTipoServicio}</td>
           <td>${nombreSistema}</td>
           <td>${servicio}</td>
           <td>${nombreZona}</td>
         </tr>`
        );
      }
    );
    const missingDetailsTable = `
    <div class="table-responsive-container">
      <table class="table table-bordered table-responsive">
        <thead class="table-dark">
          <tr>
            <th>Tipo de Servicio</th>
            <th>Tipo de Sistema</th>
            <th>Servicio</th>
            <th>Zona</th>
          </tr>
        </thead>
        <tbody>
          ${messageAlert.join("")}
        </tbody>
      </table>
    </div>
  `;

    // Alert with SweetAlert and insert the table
    const swalWithBootstrapButtons = Swal.mixin({
      customClass: {
        confirmButton: "btn btn-success",
        cancelButton: "btn btn-danger",
        popup:
          "border border-2 rounded-2 border-success border-opacity-75 col-12 col-sm-9 swal-custom-popup",
      },
      buttonsStyling: true,
    });

    swalWithBootstrapButtons.fire({
      title: "Faltan los siguientes servicios",
      html: missingDetailsTable, // Insert table here
      icon: "warning",
      showCancelButton: false,
      confirmButtonText: "Revisar",
      cancelButtonText: "",
      reverseButtons: true,
    });
    isValid = false;
  }

  // Validar que cada código en existingCodes tenga un valor válido
  existingCodes.forEach(({ code, codigo_tiposistema, codigo_tiposervicio }) => {
    const hasValidInput = Array.from(serviceCountInputs).some((input) => {
      const inputCode = input.parentElement.querySelector(
        'input[name="serviceCode[]"]'
      );

      // Verifica que el servicio tenga una cantidad válida
      return inputCode.value === code && input.value > 0;
    });

    // Si no hay un input válido para el código existente, marcar isValid como false
    if (!hasValidInput) {
      // alert(
      //   `Por favor, ingrese una cantidad válida para el servicio correspondiente a ${code}.`
      // );
      isValid = false;
    }
  });

  serviceCountInputs.forEach((input) => {
    validateInput(input);
    if (input.classList.contains("is-invalid")) {
      isValid = false;
    }
  });

  if (!isValid) {
    // alert("Por favor, complete todos los campos requeridos correctamente.");
  }

  return isValid;
}

// Add focus and blur event listeners to all serviceCount[] inputs
document.addEventListener("focusin", function (event) {
  if (
    event.target.name === "serviceCount[]" ||
    event.target.name === "inputDate"
  ) {
    event.target.classList.remove("is-invalid");
  }
});

document.addEventListener("focusout", function (event) {
  if (
    event.target.name === "serviceCount[]" ||
    event.target.name === "inputDate"
  ) {
    validateInput(event.target);
  }
});

document.getElementById("myForm").addEventListener("submit", function (event) {
  event.preventDefault();
  if (validateForm(existingCodes)) {
    updateServiceCounts();

    const formData = new FormData(this);

    const data = {};
    formData.forEach((value, key) => {
      if (!data[key]) {
        data[key] = [];
      }
      data[key].push(value);
    });

    // for (const key in data) {
    //   if (data.hasOwnProperty(key)) {
    //     console.log(`Key: ${key}`);
    //     data[key].forEach((value, index) => {
    //       console.log(`  Value ${index + 1}: ${value}`);
    //     });
    //   }
    // }
    const fecha_corte = data.inputDate[0];
    const parsedServiceCounts = JSON.parse(data.serviceCounts[0]);
    const bodyData = {
      fecha_corte: fecha_corte,
      json_configuracion: parsedServiceCounts, // ya está estructurado, no es necesario mapear
    };

    alertConfirmForm(bodyData);
  }
});

//alert custom
function alertConfirmForm(bodyData) {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
      popup: "border border-2 rounded-2 border-success border-opacity-75 col-12 col-sm-9",
    },
    buttonsStyling: true,
  });
  swalWithBootstrapButtons
    .fire({
      title: "¿Desea Enviar el Formulario?",
      text: "Recordar haber revisado los datos ingresado por cada Servicios de Alimentacion",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Enviar Formulario",
      cancelButtonText: "Cancelar",
      reverseButtons: true,
    })
    .then(async (result) => {
      if (result.isConfirmed) {
        const resultado = await validateInsertControlStadistics(bodyData);
        if (resultado.estado) {
          let timerInterval;
          return swalWithBootstrapButtons
            .fire({
              title: "Inserción completada!",
              html: "Se realizo la inserción con exito. la pagina se recargara en <b></b> segundos.",
              icon: "success",
              timer: 3000,
              timerProgressBar: true,
              didOpen: () => {
                Swal.showLoading();
                const timer = Swal.getPopup().querySelector("b");
                timerInterval = setInterval(() => {
                  timer.textContent = `${Swal.getTimerLeft() / 1000}`;
                }, 100);
              },
              willClose: () => {
                clearInterval(timerInterval);
              },
            })
            .then((result) => {
              if (result.dismiss === Swal.DismissReason.timer) {
                // Redirigir o recargar la página aquí
                window.location.reload();
              }
            });
        }
        swalWithBootstrapButtons.fire({
          title: "Inserción Falló!",
          text: `Se encontro un error en la inserccion ${resultado.message}`,
          icon: "error",
        });
      } else if (
        /* Read more about handling dismissals below */
        result.dismiss === Swal.DismissReason.cancel
      ) {
        swalWithBootstrapButtons.fire({
          title: "Revisar Formulario",
          text: "Antes de mandar revisar a detalle los campos del conteo de los servicios de alimenacion",
          icon: "warning",
        });
      }
    });
}
function alertValidateDate(message) {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
      popup: "border border-2 rounded-2 border-success border-opacity-75 col-12 col-sm-9",
    },
    buttonsStyling: true,
  });
  swalWithBootstrapButtons.fire({
    title: "Validando Fechas existentes",
    text: `Se ha revisado la informacion del control estadisticos de los servicios y se ha verificado que ya existe un registro de la fecha ${message}, por favor elegir otra fecha`,
    icon: "warning",
    showCancelButton: false,
    showConfirmButton: true,
    confirmButtonText: "Salir",
    reverseButtons: true,
  });
}
