var itemMenu = document.querySelector('sidebar-item');
const menuItems = document.querySelectorAll('.menu > .sidebar-item');
const submenuItems = document.querySelectorAll('.submenu > .sidebar-item');
const pathUrl = [
  {
    tagId: 'inicio',
    path: '/dashboard/',
  },
  {
    tagId: 'formulario',
    path: '/dashboard/formularios/',
  },
  {
    tagId: 'formulario-control-estadisticos',
    path: '/dashboard/formularios/control-estadisticos',
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

