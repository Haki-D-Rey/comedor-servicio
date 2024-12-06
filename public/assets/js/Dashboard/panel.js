import ApiService from "./../../js/apiservice.js";

var baseURL = "";
var ListaDetallesZonas = [];
var ListaDetalleZonasServicios = [];
const dropdownList = document.getElementById("dropdownList");
const dropdownList2 = document.getElementById("dropdownList2");

const apiService = new ApiService(baseURL);

const getZonasUsuariosById = async () => {
  const endpointListaZonaUsuarios = window.endpointListaRelacionZonaUsuario;
  try {
    return await apiService.get(endpointListaZonaUsuarios);
  } catch (error) {
    console.error("Error al obtener zonas de usuarios:", error);
    return null;
  }
};

const getZonasServiciosByZonaUsuario = async (code) => {
  try {
    return await apiService.get(
      `/detalle-zona-servicio-horario/getByIdZonaUsuarioDetalleServicio/${code}`
    );
  } catch (error) {
    console.error("Error al obtener servicios por zona de usuario:", error);
    return [];
  }
};

const getStadisticZone = async (code) => {
  try {
    return await apiService.post(
      window.endpointListaServicioRelacionZonaUsuario,
      code
    );
  } catch (error) {
    console.error("Error al obtener servicios por zona de usuario:", error);
    return [];
  }
};

document.addEventListener("DOMContentLoaded", async function () {
  const { DetallesZonas } = await getZonasUsuariosById();
  ListaDetallesZonas = DetallesZonas;

  DetallesZonas.forEach((opcion) => {
    const li = document.createElement("li");
    const a = document.createElement("a");
    a.classList.add("dropdown-item");
    a.href = "#";
    a.textContent = opcion.zonas.nombre;
    a.value = opcion.codigoInternoZonaUsuario;
    a.onclick = function () {
      handleDropdownClick(opcion);
    };

    li.appendChild(a);
    dropdownList.appendChild(li);
  });
});

const handleDropdownClick = async (option) => {
  // Actualiza el texto del botón del dropdown
  document.getElementById("dropdownMenu").textContent = option.zonas.nombre;

  // Limpia el dropdownList2 para evitar duplicidad
  dropdownList2.innerHTML = "";

  const code = option.codigoInternoZonaUsuario;
  const response = await getZonasServiciosByZonaUsuario(code);

  ListaDetalleZonasServicios = response.map((data) => ({
    nombre_servicio: data.nombre,
    idzona_usuario: data.idZonaUsuario,
    zonacode_usuario: code,
    id_detalle_zona_servicio_horario: data.id_detalle_zona_servicio_horario,
  }));

  ListaDetalleZonasServicios.forEach((opcion) => {
    const li = document.createElement("li");
    const a = document.createElement("a");
    a.classList.add("dropdown-item");
    a.href = "#";
    a.textContent = opcion.nombre_servicio;
    a.value = opcion.id_detalle_zona_servicio_horario;

    // Agregar el evento click con un console.log
    a.addEventListener("click", (event) => {
      handleDropdownChangeList(opcion);
    });

    li.appendChild(a);
    dropdownList2.appendChild(li);
  });
};const handleDropdownChangeList = async (opcion) => {
    document.getElementById("dropdownMenu2").textContent = opcion.nombre_servicio;
  
    // Llama al servicio para obtener datos
    const resultado = await getStadisticZone({
      zonecode: opcion.zonacode_usuario,
      id_detalle_zona_servicio_horario: opcion.id_detalle_zona_servicio_horario,
    });
  
    // Transforma los datos del servicio al formato esperado
    const transformedItems = [
      {
        icon: "fas fa-users",
        category: "Personal Inscritos",
        count: resultado[0]?.ClienteCount || 0,
      },
      {
        icon: "fas fa-user-check",
        category: "Ventas",
        count: resultado[0]?.VentaCount || 0,
      },
      {
        icon: "fas fa-users",
        category: "Servicio",
        count: opcion.nombre_servicio,
      },
      {
        icon: "fas fa-user-check",
        category: "Eventos",
        count: 1,
      },
    ];
  
    // Actualiza el carrusel
    const carouselInner = document.querySelector(".carousel-inner");
    carouselInner.innerHTML = "";
  
    // Divide los elementos en chunks para el carrusel (3 por slide)
    const chunkSize = 3;
    const chunks = [];
    for (let i = 0; i < transformedItems.length; i += chunkSize) {
      chunks.push(transformedItems.slice(i, i + chunkSize));
    }
  
    // Crear elementos del carrusel
    chunks.forEach((chunk, index) => {
      const carouselItem = document.createElement("div");
      carouselItem.className = `carousel-item ${
        index === 0 ? "active" : ""
      } h-100 w-100`;
  
      const row = document.createElement("div");
      row.className = "row h-100";
  
      chunk.forEach((item) => {
        const col = document.createElement("div");
        col.className = "col-12 col-md-6 col-lg-4 mb-3";
  
        const card = `
          <div class="card-custom card-stats card-round h-100">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="col-icon">
                  <div class="icon-big text-center icon-primary bubble-shadow-small">
                    <i class="${item.icon}"></i>
                  </div>
                </div>
                <div class="col col-stats ms-3 ms-sm-0">
                  <div class="numbers">
                    <p class="card-category">${item.category}</p>
                    <h4 class="card-title">${item.count}</h4>
                  </div>
                </div>
              </div>
            </div>
          </div>
        `;
  
        col.innerHTML = card;
        row.appendChild(col);
      });
  
      carouselItem.appendChild(row);
      carouselInner.appendChild(carouselItem);
    });
  
    // Reinicia el carrusel al primer elemento
    const carousel = new bootstrap.Carousel(
      document.getElementById("carouselExampleDark")
    );
    carousel.to(0);
  };
  
