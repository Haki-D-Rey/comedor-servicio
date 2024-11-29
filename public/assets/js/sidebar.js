import ApiService from "./apiservice.js";

var baseURL = "";
const apiService = new ApiService(baseURL);

var itemMenu = document.querySelector('sidebar-item');
const menuItems = document.querySelectorAll('.menu .sidebar-item, .menu-footer .sidebar-item'); 
const submenuItems = document.querySelectorAll('.submenu > .sidebar-item');
const pathUrl = [
  {
    tagId: 'inicio',
    path: '/dashboard/',
    permission: 'view_sidebar_inicio'
  },
  {
    tagId: 'formulario',
    path: '/dashboard/formularios/',
    permission: 'view_sidebar.levelone_formulario'
  },
  {
    tagId: 'formulario-control-estadisticos',
    path: '/dashboard/formularios/control-estadisticos',
    permission: 'view_sidebar.levelone_formulario_controlestadisticos'
  },
  {
    tagId: 'facturacion',
    path: '/dashboard/facturacion/',
    permission: 'view_sidebar.levelone_facturacion'
  },
];

document.addEventListener('DOMContentLoaded', () => {
  init();
});

function init() {
 menuItems.forEach((item) => {
  item.addEventListener('click', () => {
    const tagId = item.getAttribute('data-id');
    console.log(tagId);
    if (tagId) {
      redirectUrlValid(tagId);
    }
  });
});

submenuItems.forEach((item) => {
  item.addEventListener('click', () => {
    const tagId = item.getAttribute('data-id');
    if (tagId) {
      redirectUrlValid(tagId);
    }
  });
});
  return;
}


const redirectUrlValid = (tagId) => {
  const route = pathUrl.find((e) => e.tagId === tagId);
  if (route) {
    window.location.href = route.path;
  } else {
    console.warn(`Ruta no encontrada para tagId: ${tagId}`);
  }
};


document.querySelectorAll('.menu .sidebar-item, .submenu .sidebar-item').forEach(item => {
  item.addEventListener('click', function(event) {
      event.stopPropagation();

      const parentMenu = this.closest('.menu, .submenu');
      const hasActiveParentChild = parentMenu.querySelector('.sidebar-item.active');
      if (hasActiveParentChild) {

          if (hasActiveParentChild !== this) {
              hasActiveParentChild.classList.remove('active');
          } else {
              return;
          }
      }
      document.querySelectorAll('.menu .sidebar-item.active, .submenu .sidebar-item.active').forEach(el => {
          if (el !== this) el.classList.remove('active');
      });

      this.classList.add('active');

      if (parentMenu && !parentMenu.classList.contains('active')) {
          parentMenu.classList.add('active');
      }
      const grandParentMenu = parentMenu.closest('.sidebar-item');

      if (grandParentMenu) {
          grandParentMenu.classList.add('active');
      }

      sidebar.classList.add("nocollapsed");
      sidebar.classList.remove("collapsed");
  });
});
