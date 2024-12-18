// En reportes.js
import ApiService from "./../apiservice.js";

var timer = 0;
var timerInterval = 0;
var baseURL = "";
var detalleRelacionZonaUsuario = [];

var inputStartDate = document.getElementById("startDate");
var inputEndDate = document.getElementById("endDate");
var inputReportType = document.getElementById("reportType");

const apiService = new ApiService(baseURL);

document.addEventListener("DOMContentLoaded", async function () {
  detalleRelacionZonaUsuario = await getRelacionZonaUsuario();
});

//-------------------------------------------------------------------//
// SERVICIOS WEB API
//-------------------------------------------------------------------//

/**
 * Función para obtener las zonas de usuarios por ID desde la API.
 * @returns {Promise<Object>} Obtener Reporte de Ventas Tipo Consolidado o Detallado.
 */
async function getRelacionZonaUsuario() {
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
 * @returns {Promise<Object>} Obtener Reporte de Ventas Tipo Consolidado o Detallado.
 */
async function getReportesVentas(data) {
  const jsonString = encodeURIComponent(JSON.stringify(data));
  const endpointGetReportesVentas = `${window.endpointGetReportesVentas}?q=${jsonString}`;

  try {
    const response = await apiService.get(endpointGetReportesVentas);
    if (response && response.size > 0) {
      return response;
    } else {
      return null;
    }
  } catch (error) {
    console.error("Error al obtener reportes de ventas:", error);
    return null;
  }
}

const swalWithBootstrapButtons = Swal.mixin({
  customClass: {
    confirmButton: "btn btn-success",
    cancelButton: "btn btn-danger",
    popup: "border-3 rounded-2 border-color col-6 col-sm-9 w-25 text-white",
  },
  buttonsStyling: true,
});

function openModal() {
  document.getElementById("reportModal").style.display = "flex";
}

function closeModal() {
  inputReportType.value = "";
  inputStartDate.value = "";
  inputEndDate.value = "";
  document.getElementById("reportModal").style.display = "none";
}

async function downloadReport(data) {
  try {
    const blob = await getReportesVentas(data);

    if (blob && blob.size > 0) {
      swalWithBootstrapButtons.fire({
        title: "El archivo se descargará en",
        icon: "success",
        background: "#014a2c",
        html: "<b>3</b>",
        timer: 3000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();

          let counter = 3;
          let timerInterval = setInterval(() => {
            const counterElement = Swal.getPopup()
              ? Swal.getPopup().querySelector("b")
              : 0;
            if (counterElement) {
              counterElement.textContent = `${counter--}`;
            }
            if (counter < 0) {
              clearInterval(timerInterval);
            }
          }, 1000);
        },
        willClose: () => {
          clearInterval(timerInterval);
          const downloadLink = document.createElement("a");
          const url = window.URL.createObjectURL(blob);
          downloadLink.href = url;
          const reportType = inputReportType.value;
          const startDate = inputStartDate.value;
          const endDate = inputEndDate.value;
          const fileName = `Reporte de Ventas - ${startDate} a ${endDate} - ${reportType}.xlsx`;

          downloadLink.download = fileName;

          downloadLink.click();
          window.URL.revokeObjectURL(url);
          closeModal();
        },
      });
    } else {
      swalWithBootstrapButtons.fire({
        title: "Error al generar el archivo",
        icon: "error",
        background: "#f8d7da",
        text: "El archivo no fue generado o está vacío.",
        timer: 3000,
        timerProgressBar: true,
        didOpen: () => {
          Swal.showLoading();
          let timer = Swal.getPopup() ? Swal.getPopup().querySelector("b") : 0;
          let counter = 3;

          let timerInterval = setInterval(() => {
            if (timer) {
              timer.textContent = `${counter--}`;
            }
            if (counter < 0) {
              clearInterval(timerInterval);
              closeModal();
            }
          }, 1000);
        },
        willClose: () => {
          clearInterval(timerInterval);
        },
      });

      return false;
    }
  } catch (error) {
    console.error("Error al intentar descargar el reporte:", error);
  }
}

document
  .getElementById("reportForm")
  .addEventListener("submit", async function (e) {
    e.preventDefault();

    const startDate = inputStartDate.value;
    const endDate = inputEndDate.value;
    const reportType = inputReportType.value;
    console.log(detalleRelacionZonaUsuario.DetallesZonas);
    const areFieldsIncomplete = !startDate || !endDate || !reportType;
    if (areFieldsIncomplete) {
      swalWithBootstrapButtons.fire({
        title: "Campos Requeridos",
        icon: "warning",
        background: "#f8d7da",
        text: "Por favor, complete todos los campos requeridos.",
        timer: 3000,
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
      });
      return;
    }

    let data = {
      id_zone: detalleRelacionZonaUsuario.DetallesZonas.map(item => item.zonas.id),
      dateStart: startDate,
      dateEnd: endDate,
      reportType: reportType,
    };

    const blob = await downloadReport(data);

    if (!blob) {
      return;
    }
    return closeModal();
  });

document.querySelector(".icon-button").addEventListener("click", openModal);
document.querySelector(".cancel-button").addEventListener("click", closeModal);
