// Importa la clase ApiService
import ApiService from "./apiservice.js";

// Variables globales
var ListaServicios = [];
var existingCodes = [];
var codesToAdd = new Set();
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
  detallesZonas.forEach((detalleZona) => {
    const option = document.createElement("option");
    option.value = detalleZona.codigoInternoZonaUsuario;
    option.textContent = detalleZona.zonas.nombre;
    selectZonaUsuario.appendChild(option);
  });
  return;
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
      const listaServiciosProductosDetalles =
        await cargarDetallesServiciosPorZona(idZonaUsuario);
      const [listaSistemas, listaTipoServicios] =
        await cargarSistemasYServicios();

      // Procesar y renderizar los servicios en base a la zona seleccionada
      const resultadoListaServicios = procesarServicios(
        listaServiciosProductosDetalles,
        listaSistemas,
        listaTipoServicios
      );

      ListaServicios = resultadoListaServicios;
      addSelectItemClickListZona(resultadoListaServicios);
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
  listaTipoServicios
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
          service.idTipoServicios === idTipoServicio
      );

      if (matchingServices.length > 0) {
        result.push({
          sistema: sistema.nombre,
          codigo_sistema: sistema.codigo_interno,
          tipo_servicio: tipoServicio.nombre,
          codigo_tiposervicio: tipoServicio.codigo_interno,
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
      result.push({
        sistema: "SISERVI",
        codigo_sistema: "Sin Definir",
        tipo_servicio: tipoServicio.nombre,
        codigo_tiposervicio: tipoServicio.codigo_interno,
        servicios: transformarServicios(serviciosSinSistema),
      });
    }
  });

  return result;
}

function addSelectItemClickListZona(lista) {
  const uniqueCodes = new Set();
  lista.forEach((detalle) => {
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
  var selectedOption =
    selectZonaUsuario?.options[selectZonaUsuario.selectedIndex];
  if (!selectedOption || !selectedOption.value) {
    console.error("No se ha seleccionado una zona o el valor es inválido.");
    return;
  }
  console.log(selectedOption);
  const container = document.getElementById("itemsContainer");

  let codesToAdd = new Set();

  // Filtrar los servicios seleccionados y agregar códigos
  selectedOptions.forEach((optionName) => {
    ListaServicios.filter(
      (servicio) => servicio.codigo_sistema === optionName
    ).forEach((sistemas) => {
      codesToAdd.add({
        codigo_sistema: sistemas.codigo_sistema,
        codigo_tiposervicio: sistemas.codigo_tiposervicio,
        code_zone: selectedOption.value,
      });
    });
  });

  // Filtrar los códigos duplicados
  const duplicateCodes = Array.from(codesToAdd).filter((newCode) =>
    existingCodes.some(
      (existingCode) =>
        existingCode.codigo_sistema === newCode.codigo_sistema &&
        existingCode.codigo_tiposervicio === newCode.codigo_tiposervicio &&
        existingCode.code_zone === newCode.code_zone
    )
  );

  // Eliminar códigos duplicados de la lista
  duplicateCodes.forEach((duplicate) => {
    codesToAdd.delete(duplicate);
  });

  const allCodesExist = Array.from(codesToAdd).every((newCode) =>
    existingCodes.some(
      (existingCode) =>
        existingCode.codigo_sistema === newCode.codigo_sistema &&
        existingCode.codigo_tiposervicio === newCode.codigo_tiposervicio &&
        existingCode.code_zone === newCode.code_zone
    )
  );

  console.log(allCodesExist);

  if (!allCodesExist && codesToAdd.size > 0) {
    selectedOptions.forEach((optionName) => {
      ListaServicios.filter(
        (servicio) => servicio.codigo_sistema === optionName
      ).forEach((sistemas) => {
        let zonaContainer = container.querySelector(
          `.zona-container[data-zona="${selectedOption.value}"]`
        );

        // Si no existe, crearlo
        if (!zonaContainer) {
          zonaContainer = document.createElement("div");
          zonaContainer.classList.add("zona-container", "mt-4", "w-100");
          zonaContainer.dataset.zona = selectedOption.value;

          const zonaTitle = document.createElement("h4");
          let selectedText = "";
          if (selectZonaUsuario.selectedIndex !== 0) {
            selectedText = selectedOption.textContent;
            console.log(selectedText);
          }
          zonaTitle.innerText = `Zona - ${selectedText}`;
          zonaContainer.appendChild(zonaTitle);
          container.appendChild(zonaContainer);
        }

        // Creación del div del servicio
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
        titleContent.innerHTML = `${sistemas.sistema} ${
          sistemas.codigo_sistema === "SIS-0001"
            ? ""
            : `- ${sistemas.tipo_servicio}`
        }`;

        const trashIcon = document.createElement("span");
        trashIcon.classList.add("trash-icon", "btn", "active");
        trashIcon.innerHTML = '<i class="fas fa-trash"></i>';

        headerDivContent.appendChild(titleContent);
        headerDivContent.appendChild(trashIcon);
        wrapperDiv.appendChild(headerDivContent);

        const rowDiv = document.createElement("div");
        rowDiv.classList.add("row", "mt-3");

        // Agregar los servicios si no están en existingCodes

        console.log(existingCodes);
        console.log(selectedOption.value);
        sistemas.servicios.forEach((option) => {
          if (
            !existingCodes.some(
              (existing) =>
                existing.code === option.code &&
                existing.codigo_sistema === sistemas.codigo_sistema &&
                existing.codigo_tiposervicio === sistemas.codigo_tiposervicio &&
                existing.code_zone === selectedOption.value
            )
          ) {
            const colDiv = document.createElement("div");
            colDiv.classList.add("col-12", "col-sm-4", "col-md-6");
            colDiv.innerHTML = `
                <div class="form-floating mb-3">
                  <input type="number" class="form-control" id="floatingInput" name="serviceCount[]" value="" placeholder="Ingrese la cantidad" required>
                  <label for="floatingInput">${option.name}</label>
                  <input type="hidden" class="form-control" value="${option.code}" name="serviceCode[]" data-tipo-servicio="${sistemas.codigo_tiposervicio}" data-tipo-sistema="${sistemas.codigo_sistema}" data-detalle-servicios="${option.id_detalle_zona_servicio_horario}" readonly>
                  <div class="invalid-feedback">Debe ingresar la cantidad</div>
                </div>
              `;
            rowDiv.appendChild(colDiv);
            console.log(selectedOption.value);

            if (selectedOption && selectedOption.value) {
              existingCodes.push({
                code: option.code,
                codigo_sistema: sistemas.codigo_sistema,
                codigo_tiposervicio: sistemas.codigo_tiposervicio,
                code_zone: selectedOption.value,
              });
            } else {
              console.error("Error: El valor de la zona no está definido.");
            }
          }
        });

        wrapperDiv.appendChild(rowDiv);
        parentDiv.appendChild(wrapperDiv);
        zonaContainer.appendChild(parentDiv);

        // Evento para eliminar el elemento del DOM
        trashIcon.addEventListener("click", function () {
          parentDiv.remove();
          const codesToRemove = Array.from(
            parentDiv.querySelectorAll('input[name="serviceCode[]"]')
          ).map((input) => ({
            code: input.value,
            codigo_sistema: input.dataset.tipoSistema,
            codigo_tiposervicio: input.dataset.tipoServicio,
            code_zone: selectedOption.value,
          }));
          console.log(existingCodes);
          codesToRemove.forEach((codeToRemove) => {
            existingCodes = existingCodes.filter(
              (existingCode) =>
                existingCode.code !== codeToRemove.code ||
                existingCode.codigo_sistema !== codeToRemove.codigo_sistema ||
                existingCode.codigo_tiposervicio !==
                  codeToRemove.codigo_tiposervicio ||
                existingCode.code_zone !== codeToRemove.code_zone
            );
          });
          console.log(existingCodes);
          console.log(codesToRemove);
        });
      });
    });

    // Actualizar los conteos de servicios
    container.addEventListener("input", function (event) {
      if (event.target.name === "serviceCount[]") {
        updateServiceCounts();
      }
    });
  }

  // Verificar si los códigos seleccionados ya existen
  selectedOptions.forEach((optionName) => {
    ListaServicios.filter((servicio) => servicio.name === optionName).forEach(
      (sistemas) => {
        sistemas.servicios.forEach((option) => {
          if (!existingCodes.includes(option.code)) {
            const listItem = document.createElement("li");
            listItem.classList.add("list-group-item");
            listItem.innerText = option.name;
            listGroup.appendChild(listItem);
          }
        });
      }
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

  ListaServicios.forEach((servicio) => {
    servicio.servicios.forEach((s) => {
      const serviceExists = existingCodes.some(
        (existing) =>
          existing.code === s.code &&
          existing.codigo_sistema === servicio.codigo_sistema &&
          existing.codigo_tiposervicio === servicio.codigo_tiposervicio &&
          existingCode.code_zone === servicio
      );

      // Si el servicio no existe en existingCodes, lo agregamos a la lista de faltantes
      if (!serviceExists) {
        missingServices.push({
          tipo_servicio: servicio.codigo_tiposervicio,
          tipo_sistema: servicio.codigo_sistema,
          servicio: s.name,
        });
      }
    });
  });

  // Si hay servicios faltantes, marcar isValid como false
  if (missingServices.length > 0) {
    const missingDetails = missingServices
      .map(
        ({ tipo_servicio, tipo_sistema, servicio }) =>
          `Tipo de Servicio: ${tipo_servicio}, Tipo de Sistema: ${tipo_sistema}, Servicio: ${servicio}`
      )
      .join("\n");

    alert(`Faltan los siguientes servicios:\n${missingDetails}`);
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
      alert(
        `Por favor, ingrese una cantidad válida para el servicio correspondiente a ${code}.`
      );
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
    alert("Por favor, complete todos los campos requeridos correctamente.");
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

    for (const key in data) {
      if (data.hasOwnProperty(key)) {
        console.log(`Key: ${key}`);
        data[key].forEach((value, index) => {
          console.log(`  Value ${index + 1}: ${value}`);
        });
      }
    }
    console.log(data);
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
      popup: "border border-2 rounded-2 border-success border-opacity-75",
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
      popup: "border border-2 rounded-2 border-success border-opacity-75",
    },
    buttonsStyling: true,
  });
  swalWithBootstrapButtons
    .fire({
      title: "Validando Fechas existentes",
      text: `Se ha revisado la informacion del control estadisticos de los servicios y se ha verificado que ya existe un registro de la fecha ${message}, por favor elegir otra fecha`,
      icon: "warning",
      showCancelButton: false,
      showConfirmButton: true,
      confirmButtonText: "Salir",
      reverseButtons: true,
    })
    .then(async (result) => {
      if (result.isConfirmed) {
        if (resultado.estado) {
          return;
        }
      }
    });
}
