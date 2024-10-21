// Importa la clase ApiService
import ApiService from "./apiservice.js";

var buttonLogout = document.getElementById("button-logout");
// var buttonSiderbarClose = document.getElementById("button-sidebar");
var baseURL = "";
const apiService = new ApiService(baseURL);

document.addEventListener("DOMContentLoaded", () => {
  init();
});

function init() {
  validateButtonLogout();
  clickActionLogout();
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

  buttonLogout.disabled = !isValid;
  buttonLogout.style.visibility = isValid ? "visible" : "hidden";
//   buttonSiderbarClose.disabled = !isValid;
//   buttonSiderbarClose.style.visibility = isValid ? "visible" : "hidden";
}

function clickActionLogout() {
  buttonLogout.addEventListener("click", async () => {
    const response = await postLogoutSesion();
    console.log(response);
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
