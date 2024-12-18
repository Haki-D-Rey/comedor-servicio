// Importa la clase ApiService
import ApiService from "./apiservice.js";

var buttonLogout = document.getElementById("button-logout");
var buttonSiderbarClose = document.getElementById("toggleButton");
var layout = document.querySelector("#layout");
var sidebar = document.querySelector(".sidebar");
var main = document.querySelector("main");
var baseURL = "";
var buttonCloseSidebar = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layout-sidebar-inset-reverse" viewBox="0 0 16 16">
  <path d="M2 2a1 1 0 0 0-1 1v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V3a1 1 0 0 0-1-1zm12-1a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2z"/>
  <path d="M13 4a1 1 0 0 0-1-1h-2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1z"/>
</svg>`;

var buttonOpenSidebar = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-layout-sidebar-inset" viewBox="0 0 16 16">
            <path d="M14 2a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zM2 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2z" />
            <path d="M3 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z" />
        </svg>`;

const apiService = new ApiService(baseURL);

document.addEventListener("DOMContentLoaded", () => {
  init();
  buttonLogout.disabled = true;
  buttonLogout.style.visibility = "hidden";
  buttonSiderbarClose.disabled = true;
  buttonSiderbarClose.style.visibility = "hidden";
});

function init() {
  validateButtonLogout();
  clickActionLogout();
  // toggleSubMenu();
  return;
}

/**
 * Servicio Cerrar Sesion
 */
async function postLogoutSesion() {
  const endpointLogoutSesion = "/auth/logout";
  const resultado = await apiService.post(endpointLogoutSesion);
  return resultado;
}

/**
 * Servicio Consulta Si existe una Sesion
 */
async function postVerifyAccessToken() {
  const endpointLogoutSesion = "/token";
  const bodyTokenAcess = {
    token: window.tokenAccess,
  };
  const resultado = await apiService.post(endpointLogoutSesion, bodyTokenAcess);
  return resultado;
}

async function validateButtonLogout() {
  const tokenAccess = await postVerifyAccessToken();
  const isValid = tokenAccess.valido;

  if (isValid) {
    // layout.classList.toggle("no-sidebar");
    //console.log("aqui");
  }

  buttonLogout.disabled = !isValid;
  buttonLogout.style.visibility = isValid ? "visible" : "hidden";
  buttonSiderbarClose.disabled = !isValid;
  buttonSiderbarClose.style.visibility = isValid ? "visible" : "hidden";
}

function clickActionLogout() {
  buttonLogout.addEventListener("click", async () => {
    const response = await postLogoutSesion();
    //console.log(response);

    if (response.status) {
      // Borrar todas las cookies
      document.cookie.split(";").forEach((cookie) => {
        const cookieName = cookie.split("=")[0].trim();
        document.cookie = `${cookieName}=;expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/`;
      });

      // Redireccionar a la página de inicio de sesión
      window.location.href = "/auth/login";
    }
  });
}

const toggleSidebar = () => {
  const toggleButton = document.querySelector("#toggleButton");

  // Cambiar el estado del sidebar
  if (sidebar.classList.contains("nocollapsed")) {
    sidebar.classList.remove("nocollapsed");
    sidebar.classList.add("collapsed");
    // layout.classList.add("no-sidebar");
    main.classList.add("collapsed");

    // Cambiar el icono al cerrar el sidebar
    toggleButton.innerHTML = buttonOpenSidebar;
  } else {
    sidebar.classList.remove("collapsed");
    sidebar.classList.add("nocollapsed");
    layout.classList.remove("no-sidebar");
    main.classList.remove("collapsed");

    // Cambiar el icono al abrir el sidebar
    toggleButton.innerHTML = buttonCloseSidebar;
  }

  // Añadir la clase de animación
  toggleButton.classList.add("animate-icon");

  // Remover la clase de animación después de un corto tiempo
  setTimeout(() => {
    toggleButton.classList.remove("animate-icon");
  }, 100); // Ajusta el tiempo según sea necesario
};

document
  .querySelector("#toggleButton")
  .addEventListener("click", toggleSidebar);
