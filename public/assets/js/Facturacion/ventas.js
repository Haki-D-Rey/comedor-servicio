// Importa la clase ApiService
import ApiService from "./../apiservice.js";
import Utils from "./../utils/util.js";

// Variables globales
var ListaServiciosValidar = [];
var ListaServicios = [];
var ListaDetallesZonas = [];
var listaSistemas = [];
var listaTipoServicios = [];
var baseURL = "";

// Elementos DOM
var contentDropdownZona = document.getElementById("dropdownMenu");
var dropdownZonaUsuario = document.getElementById("dropdownList");
var dropdownSistema = document.getElementById("dropdown_sistema");
var inputVentas = document.getElementById("inputCodigo");
var inputCantidad = document.getElementById("inputCantidad");
var toggleButtonIdentificador = document.querySelector(
  "#toggleButtonIdentificador"
);

var inputSearch = document.getElementById("inputSearch");
const contentInputSearch = document.getElementById("content-input-search");
const contentInputCodigo = document.getElementById("content-input-codigo");

var loadingIcon = document.getElementById("loadingIcon");
var successIcon = document.getElementById("successIcon");
var resultList = document.getElementById("resultList");

var buttonLector = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layout-sidebar-inset-reverse" viewBox="0 0 16 16">
  <path d="M2 2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2z"/>
  <path d="M13 4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1z"/>
</svg><span id="iconLabel" style="margin-left: 14px;">Lector de Tarjeta</span>`;

var buttonFiltro = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layout-sidebar-inset" viewBox="0 0 16 16">
            <path d="M14 2a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zM2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2z" />
            <path d="M3 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z" />
        </svg><span id="iconLabel" style="margin-left: 14px;">Filtro de Busqueda</span>`;

var debounceTimer = 0;
var timerInterval;
var timer = 0;
var mealServices = [];

const apiService = new ApiService(baseURL);
const util = new Utils();

function init() {
  dropdownZonaUsuario.disabled = false;
  dropdownSistema.disabled = true;
  inputVentas.disabled = true;
  inputCantidad.disabled = true;
  inputSearch.disabled = true;
  contentInputSearch.style.display = "none";
}

document.addEventListener("DOMContentLoaded", () => {
  init();
  cargarZonasEnSelect();
  createDefaultCard();
});

// ==========================
// REGION: Servicios de API
// ==========================

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
 * Función para obtener las zonas de usuarios para servicios de validación desde la API.
 * @returns {Promise<Object>} Lista de zonas de usuarios para validar servicios.
 */
async function getZonasUsuariosServiciosValidar() {
  const endpointListaZonaUsuariosServiciosValidar =
    window.endpointListaZonaUsuariosServiciosValidar;

  try {
    return await apiService.get(endpointListaZonaUsuariosServiciosValidar);
  } catch (error) {
    console.error(
      "Error al obtener zonas de usuarios para validar servicios:",
      error
    );
    return null;
  }
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

async function ServicesSellDining(data) {
  //console.log("endpoint");
  const endpointFacturacion = window.endpointFacturacion;
  try {
    return await apiService.post(endpointFacturacion, data);
  } catch (error) {
    console.error(
      "Error al obtener zonas de usuarios para validar servicios:",
      error
    );
    return null;
  }
}
async function getSearchClientByName(query) {
  const endpointFilterClientByName = `${window.endpointFilterClientByName}?q=${query}`;
  try {
    return await apiService.get(endpointFilterClientByName);
  } catch (error) {
    console.error(
      "Error al obtener zonas de usuarios para validar servicios:",
      error
    );
    return null;
  }
}

async function getClientIdentificationRelational(query) {
  const endpointClientRelationalIdentification = `${window.endpointClientRelationalIdentification}?q=${query}`;
  try {
    return await apiService.get(endpointClientRelationalIdentification);
  } catch (error) {
    console.error(
      "Error al obtener zonas de usuarios para validar servicios:",
      error
    );
    return null;
  }
}

// ==========================
// Fin de los Servicios API
// ==========================

/**
 * Cargar las zonas en el elemento select al obtener los datos de la API.
 */
async function cargarZonasEnSelect() {
  //   const dropdownList = document.getElementById("dropdownList");

  const { DetallesZonas } = await getZonasUsuariosById();
  ListaServiciosValidar = await getZonasUsuariosServiciosValidar();
  [listaSistemas, listaTipoServicios] = await cargarSistemasYServicios();
  ListaDetallesZonas = DetallesZonas;
  renderZonasEnSelect(DetallesZonas);
}

/**
 * Renderiza las opciones de zonas dentro del elemento select.
 * @param {Array<Object>} detallesZonas - Lista de detalles de zonas.
 */
function renderZonasEnSelect(detallesZonas) {
  const resultado = [];

  detallesZonas.forEach((detalleZona) => {
    const li = document.createElement("li");
    const a = document.createElement("a");
    a.classList.add("dropdown-item");
    a.href = "#";
    a.textContent = detalleZona.zonas.nombre;
    a.value = detalleZona.codigoInternoZonaUsuario;
    a.onclick = function () {
      changeZonasEnSelect(detalleZona);
    };

    li.appendChild(a);
    dropdownZonaUsuario.appendChild(li);
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
function changeZonasEnSelect(opcion) {
  //   dropdownZonaUsuario.addEventListener("change", async (event) => {
  contentDropdownZona.textContent = opcion.zonas.nombre;
  contentDropdownZona.value = opcion.zonas.codigo_interno;
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
  //   });
}

dropdownSistema.addEventListener(
  "change",
  async function changeServiciosSelect(event) {
    //console.log("input");
    // Obtener el elemento que fue clickeado (el <option>)
    const selectedOption = event.target.selectedOptions[0];
    const detalles = JSON.parse(selectedOption.dataset.detalle);
    // if (dropdownSistema.value) {
    //   inputVentas.disabled = false;
    //   inputCantidad.disabled = false;
    // }
    const data = await cargarDetallesServiciosPorZona(detalles.code_zone);
    const serviciosFormateados = {};
    //console.log(data);
    // Filtrar y reestructurar los datos
    detalles.servicios.forEach((servicio) => {
      // Buscar el horario correspondiente para el servicio
      const servicioDetalles = data.find(
        (item) =>
          item.id_detalle_zona_servicio_horario ===
          servicio.id_detalle_zona_servicio_horario
      );

      if (servicioDetalles) {
        //console.log("horario");
        // Si se encuentra el horario, lo formateamos en el formato requerido
        const periodoInicio = servicioDetalles.horario.periodo_inicio;
        const periodoFinal = servicioDetalles.horario.periodo_final;
        const cod = servicioDetalles.horario.codigo_interno;

        // Añadimos el servicio al objeto con su rango de horas
        serviciosFormateados[servicio.name] = {
          start: periodoInicio,
          end: periodoFinal,
          periodo_inicial_servicio: servicioDetalles.periodo_inicial_servicio,
          periodo_final_servicio: servicioDetalles.periodo_final_servicio,
          cod: cod,
          id_detalle_zona_servicio_horario:
            servicioDetalles.id_detalle_zona_servicio_horario,
        };
      }
    });

    serviciosFormateados["Fuera de Servicio"] = [];
    mealServices = serviciosFormateados;

    renderMealServices();
  }
);

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

function addSelectItemClickListZona(lista, code_zone) {
  const uniqueCodes = new Set();
  //console.log("validar seleccin");
  var dropdown = document.getElementById("dropdown_sistema");
  dropdown.innerHTML = dropdown.options[0].outerHTML;

  lista
    .filter((detalle) => detalle.code_zone === code_zone) // Filtrar solo los servicios de la zona seleccionada
    .forEach((detalle) => {
      if (!uniqueCodes.has(detalle.codigo_sistema)) {
        uniqueCodes.add(detalle.codigo_sistema);

        const option = document.createElement("option");
        option.value = detalle.codigo_sistema;
        option.textContent = detalle.sistema;
        option.setAttribute("data-detalle", JSON.stringify(detalle));
        // option.onclick = function () {
        //   changeServiciosSelect(detalle);
        // };
        dropdownSistema.appendChild(option);
        dropdownSistema.disabled = false;
      }
    });
}

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

/**
 * Función para obtener la hora o la fecha actual con formato dinámico.
 * @param {string} format - El formato en el que se debe mostrar (por ejemplo, 'HH:mm', 'YYYY-MM-DD', 'HH:mm YYYY-MM-DD').
 * @returns {string} - La hora o la fecha actual en el formato especificado.
 */
function getCurrentTimeOrDate(format = "HH:mm") {
  // Obtener la fecha y hora actual
  const now = new Date();

  // Obtener las partes individuales de la fecha y la hora
  let hours = now.getHours(); // 0-23
  let minutes = now.getMinutes(); // 0-59
  let seconds = now.getSeconds(); // 0-59
  let day = now.getDate(); // 1-31
  let month = now.getMonth() + 1; // 0-11 (se le suma 1 para que sea 1-12)
  let year = now.getFullYear(); // 4 dígitos (ej. 2023)

  // Formatear a dos dígitos cuando sea necesario
  hours = hours < 10 ? "0" + hours : hours;
  minutes = minutes < 10 ? "0" + minutes : minutes;
  seconds = seconds < 10 ? "0" + seconds : seconds;
  day = day < 10 ? "0" + day : day;
  month = month < 10 ? "0" + month : month;

  // Construir el resultado dependiendo del formato deseado
  if (format === "HH:mm") {
    return `${hours}:${minutes}`;
  } else if (format === "HH:mm:ss") {
    return `${hours}:${minutes}:${seconds}`;
  } else if (format === "YYYY-MM-DD") {
    return `${year}-${month}-${day}`;
  } else if (format === "YYYY-MM-DD HH:mm:ss") {
    return `${year}-${month}-${day} ${hours}:${minutes}`;
  } else {
    return `${hours}:${minutes}`; // Por defecto 'HH:mm'
  }
}

/**
 * Función para verificar si la hora actual está dentro de un rango de tiempo.
 * @param {string} currentTime - Hora actual en formato 'HH:mm'.
 * @param {string} startTime - Hora de inicio en formato 'HH:mm'.
 * @param {string} endTime - Hora de fin en formato 'HH:mm'.
 * @returns {boolean} - Devuelve si el servicio está activo o no.
 */
function isCurrentMealActive(currentTime, startTime, endTime) {
  // Verificamos si los tiempos son válidos
  if (!startTime || !endTime) return true;

  // Convertimos las horas y minutos a minutos desde la medianoche
  const getMinutes = (time) => {
    const [hour, minute] = time.split(":").map(Number);
    return hour * 60 + minute;
  };

  const currentMinutes = getMinutes(getCurrentTimeOrDate("HH:mm"));
  const startMinutes = getMinutes(startTime);
  const endMinutes = getMinutes(endTime);

  // Caso cuando el rango no cruza medianoche
  if (startMinutes <= endMinutes) {
    return currentMinutes >= startMinutes && currentMinutes <= endMinutes;
  } else {
    // Caso cuando el rango cruza medianoche
    return currentMinutes >= startMinutes || currentMinutes <= endMinutes;
  }
}
/**
 * Crea un indicador del carrusel.
 * @param {string} service - El nombre del servicio.
 * @param {boolean} isActive - Si el servicio está activo.
 * @param {number} index - El índice del servicio en la lista.
 * @returns {HTMLElement} - El elemento de botón para el indicador.
 */
function createCarouselIndicator(service, isActive, index) {
  const indicator = document.createElement("button");
  indicator.type = "button";
  indicator.classList.add("btn", "btn-circle", "btn-sm");
  indicator.setAttribute("data-bs-target", "#carouselExampleDark");
  indicator.setAttribute("data-bs-slide-to", index);
  indicator.setAttribute("aria-label", "Slide " + (index + 1));

  if (isActive) {
    indicator.classList.add("active");
  }

  return indicator;
}

/**
 * Crea un item del carrusel.
 * @param {string} service - El nombre del servicio.
 * @param {Object} serviceData - Los datos del servicio (horarios).
 * @param {boolean} isActive - Si el servicio está activo.
 * @returns {HTMLElement} - El item del carrusel.
 */
function createCarouselItem(service, serviceData, isActive) {
  const carouselItem = document.createElement("div");
  carouselItem.classList.add("carousel-item");
  // Si el servicio está activo con HR-005, no se mueve
  //console.log("fuera");
  if (isActive) {
    carouselItem.classList.add("active");
    // carouselItem.setAttribute("data-bs-ride", "false"); // No se mueve automáticamente
  }
  carouselItem.setAttribute("data-bs-interval", "false");

  const card = document.createElement("div");
  const cardClasses = isActive
    ? service === "Fuera de Servicio"
      ? ["bg-danger", "text-white"]
      : ["bg-success", "text-white"]
    : ["bg-transparent", "text-dark"];

  // Agregar las clases
  card.classList.add("card", "h-100", "w-100", ...cardClasses);

  const cardBody = document.createElement("div");
  cardBody.classList.add("card-body", "text-center");

  const title = document.createElement("h3");
  title.textContent = service;

  const schedule = document.createElement("p");
  if (service !== "Fuera de Servicio") {
    schedule.textContent =
      serviceData.cod === "HR-005"
        ? "24 Hrs"
        : `Horario: ${serviceData.start} - ${serviceData.end}`;
    const status = document.createElement("p");
    status.innerHTML = isActive
      ? "<strong>¡Servicio actualmente activo!</strong>"
      : "<strong>¡Fuera de Servicio!</strong>";
    cardBody.appendChild(status);
  } else {
    schedule.textContent = "Sin servicio de alimentación disponible";
    const status = document.createElement("p");
    status.innerHTML = "<strong>Por favor, vuelva más tarde.</strong>";
    cardBody.appendChild(status);
  }

  // Agregar la información al cuerpo de la tarjeta
  cardBody.appendChild(title);
  cardBody.appendChild(schedule);
  card.appendChild(cardBody);
  carouselItem.appendChild(card);

  return carouselItem;
}

/**
 * Renderiza los servicios de comida en el carrusel.
 */
function renderMealServices() {
  const carouselIndicators = document.querySelector(".carousel-indicators");
  const carouselInner = document.querySelector(".carousel-inner");

  // Limpiar los indicadores y los items anteriores del carrusel
  carouselIndicators.innerHTML = "";
  carouselInner.innerHTML = "";

  let fechaString = util.formatDate(-6, "YYYY-MM-DD HH:mm:ss").split(" ")[0];
  let activeIndex = "Fuera de Servicio"; // Por defecto
  //console.log(mealServices);
  // Verificar si hay un servicio con el código 'HR-005'
  const hr005Services = Object.keys(mealServices).filter(
    (service) => mealServices[service].cod === "HR-005"
  );

  if (hr005Services.length > 0) {
    // Si existe al menos un servicio con cod 'HR-005', obtener el más temprano
    const earliestService = hr005Services.reduce((earliest, current) => {
      const currentStart = mealServices[current].start;
      const earliestStart = mealServices[earliest].start;

      return currentStart < earliestStart ? current : earliest;
    });

    activeIndex = earliestService;

    hr005Services.forEach((service) => {
      mealServices[service].isActive = true;
    });

    // Crear los indicadores y items para los servicios 'HR-005'
    hr005Services.forEach((service, index) => {
      const serviceData = mealServices[service];
      const isActive = serviceData.isActive;

      // Crear el indicador y el item del carrusel
      const indicator = createCarouselIndicator(service, isActive, index);
      const carouselItem = createCarouselItem(service, serviceData, isActive);

      // Agregar el indicador y el item al carrusel
      carouselIndicators.appendChild(indicator);
      carouselInner.appendChild(carouselItem);
    });
  } else {
    let fsIsActive = false;
    const servicesToDelete = [];

    Object.keys(mealServices).forEach((service, index) => {
      const serviceData = mealServices[service];
      let isActive = false;
      // Verificar si la fecha está dentro del rango
      let isDateInRange =
        fechaString >= serviceData.periodo_inicial_servicio &&
        fechaString <= serviceData.periodo_final_servicio;

      if (
        service !== "Fuera de Servicio" &&
        isCurrentMealActive("", serviceData.start, serviceData.end) &&
        serviceData.periodo_inicial_servicio &&
        serviceData.periodo_final_servicio &&
        isDateInRange
      ) {
        isActive = true;
        fsIsActive = false;
      } else if (service === "Fuera de Servicio") {
        isActive = fsIsActive ? true : false;
        if (!isActive) {
          servicesToDelete.push(service);
        }
      }

      if (service !== "Fuera de Servicio" && !isActive) {
        fsIsActive = !(activeIndex !== "Fuera de Servicio");
      }

      if (!servicesToDelete.includes(service)) {
        const indicator = createCarouselIndicator(service, isActive, index);
        const carouselItem = createCarouselItem(service, serviceData, isActive);
        carouselIndicators.appendChild(indicator);
        carouselInner.appendChild(carouselItem);
      }

      if (isActive) {
        activeIndex = service;
        if (service == "Fuera de Servicio") {
          inputCantidad.disabled = true;
          inputVentas.disabled = true;
          inputSearch.disabled = true;
        } else {
          inputVentas.disabled = false;
          inputCantidad.disabled = false;
          inputSearch.disabled = false;
          if (
            !inputSearch.value &&
            inputSearch &&
            (toggleButtonIdentificador.getAttribute("value") === "ITF-001" ||
              toggleButtonIdentificador.value === "ITF-001")
          ) {
            inputVentas.focus();
          } else {
            inputSearch.focus();
          }

          if (!inputCantidad.value) {
            inputCantidad.textContent = 1;
            inputCantidad.value = 1;
          }
        }
      }
    });

    servicesToDelete.forEach((service) => {
      delete mealServices[service];
    });
  }
}

/**
 * Crear una tarjeta por defecto en caso de que no haya servicios activos.
 * Esta tarjeta aparecerá si no hay ningún servicio activo al inicio.
 */
function createDefaultCard() {
  const carouselIndicators = document.querySelector(".carousel-indicators");
  const carouselInner = document.querySelector(".carousel-inner");

  carouselIndicators.innerHTML = "";
  carouselInner.innerHTML = "";

  const carouselItem = document.createElement("div");
  carouselItem.classList.add("carousel-item", "active");

  const card = document.createElement("div");
  card.classList.add(
    "card",
    "h-100",
    "w-100",
    "bg-transparent",
    "text-dark",
    "border-0"
  );

  const cardBody = document.createElement("div");
  cardBody.classList.add("card-body", "text-center");

  const title = document.createElement("h3");
  title.textContent = "No hay Servicios Disponibles";

  const schedule = document.createElement("p");
  schedule.textContent = "Por favor, vuelva más tarde.";

  cardBody.appendChild(title);
  cardBody.appendChild(schedule);
  card.appendChild(cardBody);
  carouselItem.appendChild(card);

  carouselInner.appendChild(carouselItem);
}
let debounceTimeout;

function postSellServices() {
  clearTimeout(debounceTimeout);

  debounceTimeout = setTimeout(async function () {
    const response = await crearArrayServiciosActivos();
    PopupSellDiningServices(response);
    return;
  }, 700);
}

inputVentas.addEventListener("input", function () {
  return postSellServices();
});

function crearArrayServiciosActivos() {
  let codIdentificacion = "";
  if (
    toggleButtonIdentificador.value === "ITF-001" ||
    !toggleButtonIdentificador.value
  ) {
    codIdentificacion = inputVentas.value ? inputVentas.value.trim() : null;
  } else {
    codIdentificacion =
      inputSearch.value && inputSearch.getAttribute("data-id")
        ? inputSearch.getAttribute("data-id")
        : 0;
  }

  // const codIdentificacion =
  //   inputVentas.value && toggleButtonIdentificador.value === "ITF-001"
  //     ? inputVentas.value.trim()
  //     : inputSearch.getAttribute("data-id") || "";

  const cantidadFacturada = inputCantidad
    ? parseInt(inputCantidad.value, 10)
    : 0;

  if (
    !codIdentificacion ||
    isNaN(cantidadFacturada) ||
    cantidadFacturada <= 0
  ) {
    console.warn("Código de identificación o cantidad no válidos.");
    return [];
  }

  const serviciosActivos = Object.keys(mealServices)
    .filter(
      (service) => service !== "Fuera de Servicio" && isServiceActive(service)
    )
    .map((service) => {
      const serviceData = mealServices[service];

      return {
        cod_identificacion: codIdentificacion,
        idDetalleZonaServicioHorario:
          serviceData.id_detalle_zona_servicio_horario,
        cantidadFacturada: cantidadFacturada,
        codigoInternoIF: toggleButtonIdentificador.value || "ITF-001",
      };
    });

  return ServicesSellDining(serviciosActivos);
}

function isServiceActive(service) {
  const serviceData = mealServices[service];
  // Verificar si la fecha está dentro del rango
  let fechaString = util.formatDate(-6, "YYYY-MM-DD HH:mm:ss").split(" ")[0];
  let isDateInRange =
    fechaString >= serviceData.periodo_inicial_servicio &&
    fechaString <= serviceData.periodo_final_servicio;

  if (serviceData.cod === "HR-005") {
    return true;
  }

  return (
    isCurrentMealActive("", serviceData.start, serviceData.end) && isDateInRange
  );
}

function PopupSellDiningServices(response) {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: "btn btn-success",
      cancelButton: "btn btn-danger",
      popup:
        "border-3 rounded-2 border-color col-6 col-sm-9 w-25 bg-success text-white",
    },
    buttonsStyling: true,
  });
  // Verifica si hay algún error en el objeto response
  const hasErrors = response.data.some(
    (item) =>
      item.error_punto_venta ||
      item.error_horarios ||
      item.error_validar_credencial ||
      item.error_creditos_disponibles ||
      item.error_limite_credito
  );

  if (hasErrors) {
    // Si hay errores, muestra una alerta de warning con fondo rojo
    swalWithBootstrapButtons
      .fire({
        title: "Error en la Venta",
        icon: "warning",
        background: "#f8d7da", // Fondo rojo claro
        html: generateErrorTable(response.data),
        timer: 5000,
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
          successIcon.style.display = "none";
          loadingIcon.style.display = "none";
          inputSearch.textContent = "";
          inputSearch.value = "";
          inputVentas.textContent = "";
          inputVentas.value = "";
          inputVentas.focus();
          inputSearch.focus();
        },
      })
      .then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
          inputVentas.textContent = "";
          inputVentas.value = "";
          inputVentas.focus();
          inputCantidad.textContent = 1;
          inputCantidad.value = 1;
          inputSearch.textContent = "";
          inputSearch.value = "";
          successIcon.style.display = "none";
          loadingIcon.style.display = "none";
        }
      });
  } else {
    // Si no hay errores, muestra la alerta de éxito
    swalWithBootstrapButtons
      .fire({
        title: "Venta Exitosa",
        html: "Se realizó la inserción con éxito. La página se recargará en <b></b> segundos.",
        icon: "success",
        timer: 1000,
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
          successIcon.style.display = "none";
          loadingIcon.style.display = "none";
          inputSearch.textContent = "";
          inputSearch.value = "";
          inputVentas.textContent = "";
          inputVentas.value = "";
          inputVentas.focus();
          inputSearch.focus();
        },
      })
      .then((result) => {
        if (result.dismiss === Swal.DismissReason.timer) {
          inputVentas.textContent = "";
          inputVentas.value = "";
          inputVentas.focus();
          inputSearch.textContent = "";
          inputSearch.value = "";
          successIcon.style.display = "none";
          loadingIcon.style.display = "none";
          inputCantidad.textContent = 1;
          inputCantidad.value = 1;
        }
      });
  }
}

// Función para generar la tabla con los errores
function generateErrorTable(data) {
  let tableContent =
    '<table class="table table-bordered"><thead><tr><th>Error</th><th>Detalle</th></tr></thead><tbody>';
  data.forEach((item) => {
    if (item.error_punto_venta) {
      tableContent += `<tr><td>Error Punto de Venta</td><td>${item.error_punto_venta}</td></tr>`;
    }
    if (item.error_horarios) {
      tableContent += `<tr><td>Error Horarios</td><td>${item.error_horarios}</td></tr>`;
    }
    if (item.error_validar_credencial) {
      tableContent += `<tr><td>Error Credencial</td><td>${item.error_validar_credencial}</td></tr>`;
    }
    if (item.error_creditos_disponibles) {
      tableContent += `<tr><td>Error Créditos Disponibles</td><td>${item.error_creditos_disponibles}</td></tr>`;
    }
    if (item.error_limite_credito) {
      tableContent += `<tr><td>Error Límite de Crédito</td><td>${item.error_limite_credito}</td></tr>`;
    }
  });
  tableContent += "</tbody></table>";
  return tableContent;
}

function triggerChangeEvent() {
  if (contentDropdownZona.value && dropdownSistema.value) {
    const event = new Event("change");
    dropdownSistema.dispatchEvent(event);
  }
}

setInterval(triggerChangeEvent, 10000);

//-------------------------------------------------------
// Eventos del Boton de Seleccion de Identificador Filtro
//-------------------------------------------------------

let isActive = true;

const toggleButton = () => {
  if (toggleButtonIdentificador.value === "ITF-002" && !isActive) {
    toggleButtonIdentificador.innerHTML = buttonLector;
    toggleButtonIdentificador.value = "ITF-001";
    toggleButtonIdentificador.setAttribute("value", "ITF-001");
  } else {
    toggleButtonIdentificador.innerHTML = buttonFiltro;
    toggleButtonIdentificador.value = "ITF-002";
    toggleButtonIdentificador.setAttribute("value", "ITF-002");
  }

  inputSearch.textContent = "";
  inputSearch.value = "";
  inputVentas.textContent = "";
  inputVentas.value = "";
  successIcon.style.display = "none";
  loadingIcon.style.display = "none";

  // Agregar animación
  toggleButtonIdentificador.classList.add("animate-icon");
  setTimeout(() => {
    toggleButtonIdentificador.classList.remove("animate-icon");
  }, 200);

  // Alternar estado
  isActive = !isActive;
};

function toggleInputs(value) {
  if (value === "ITF-001") {
    contentInputSearch.style.display = "none";
    contentInputCodigo.style.display = "block";
    inputVentas.focus();
  } else if (value === "ITF-002") {
    contentInputSearch.style.display = "block";
    contentInputCodigo.style.display = "none";
    inputSearch.focus();
  }
}

const onClickToggleIdentificador = () => {
  toggleButton();
  const currentValue = toggleButtonIdentificador.value;
  toggleInputs(currentValue);
};

// Evento de clic para alternar el botón
toggleButtonIdentificador.addEventListener("click", onClickToggleIdentificador);

async function buscarServicio(query) {
  if (!query.trim()) {
    resultList.style.display = "none";
    resultList.innerHTML = "";
    loadingIcon.style.display = "none"; // Ocultar ícono de carga
    return;
  }

  try {
    const response = await getSearchClientByName(encodeURIComponent(query));
    //console.log(response);
    // Crear la lista dinámica
    resultList.innerHTML = response
      .map(
        (item) => `
        <li class="list-group-item" data-id="${item.id}" data-nombre="${item.nombres} ${item.apellidos}">
            ${item.nombres} ${item.apellidos}
        </li>`
      )
      .join("");

    resultList.style.display = "block";
    loadingIcon.style.display = "none"; // Ocultar ícono de carga
  } catch (error) {
    console.error("Error al consultar el servicio:", error);
    resultList.innerHTML =
      "<li class='list-group-item'>Error al cargar resultados</li>";
    resultList.style.display = "block";
    loadingIcon.style.display = "none"; // Ocultar ícono de carga
  }
}

inputSearch.addEventListener("input", function (event) {
  const query = event.target.value.trim();

  if (query.length >= 4) {
    successIcon.style.display = "none";
    loadingIcon.style.display = "inline-block";

    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(async () => {
      await buscarServicio(query);
    }, 100);
  } else {
    resultList.style.display = "none";
    resultList.innerHTML = "";
    loadingIcon.style.display = "none";
  }
});

resultList.addEventListener("click", function (event) {
  const target = event.target;

  if (target.tagName === "LI") {
    const id = target.getAttribute("data-id");
    const nombre = target.getAttribute("data-nombre");
    inputSearch.setAttribute("data-id", id);

    inputSearch.value = nombre;

    resultList.style.display = "none";
    resultList.innerHTML = "";
    successIcon.style.display = "inline-block";
    loadingIcon.style.display = "none";
  }
  return postSellServices();
});
