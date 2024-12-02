<?php
ob_start();
$items = [
    ['icon' => 'fas fa-users', 'category' => 'Visitors', 'count' => '1,294'],
    ['icon' => 'fas fa-user-check', 'category' => 'Subscribers', 'count' => '1,303'],
    ['icon' => 'fas fa-users', 'category' => 'Visitors', 'count' => '1,294'],
    ['icon' => 'fas fa-user-check', 'category' => 'Subscribers', 'count' => '1,303'],
    ['icon' => 'fas fa-users', 'category' => 'Visitors', 'count' => '1,294']
];

// Divide los elementos en grupos de 3 para el carrusel
$chunkedItems = array_chunk($items, 3);
?>
<div class="d-flex row row-gap-3">
    <div class="title-dashboard">
        <h3>BIENVENIDO AL PANEL ADMINISTRADOR</h3>
    </div>

    <!-- Dropdown con estilos de Bootstrap y personalizados -->
    <div class="dropdown">
        <button class="dropdown-toggle custom-dropdown-btn" type="button" id="dropdownMenu" data-bs-toggle="dropdown" aria-expanded="false">
            Selecciona una opción
        </button>
        <ul id="dropdownList" class="dropdown-menu custom-dropdown-menu" aria-labelledby="dropdownMenu">
        </ul>
    </div>

    <div id="carouselExampleDark" class="carousel carousel-dark slide" style="width: 100%; height: 200px;">
        <!-- Indicadores del carrusel -->
        <div class="carousel-indicators">
            <?php foreach (array_keys($chunkedItems) as $index): ?>
                <button type="button" data-bs-target="#carouselExampleDark" data-bs-slide-to="<?= $index ?>" class="<?= $index === 0 ? 'active' : '' ?>" aria-label="Slide <?= $index + 1 ?>" <?= $index === 0 ? 'aria-current="true"' : '' ?>></button>
            <?php endforeach; ?>
        </div>

        <!-- Elementos del carrusel -->
        <div class="carousel-inner h-100 w-100" style="padding: 2rem;">
            <?php foreach ($chunkedItems as $index => $itemGroup): ?>
                <div class="carousel-item <?= $index === 0 ? 'active' : '' ?> h-100 w-100" data-bs-interval="10">
                    <div class="row h-100">
                        <?php foreach ($itemGroup as $item): ?>
                            <div class="col-12 col-md-6 col-lg-4 mb-3">
                                <div class="card-custom card-stats card-round h-100">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="col-icon">
                                                <div class="icon-big text-center icon-primary bubble-shadow-small">
                                                    <i class="<?= $item['icon'] ?>"></i>
                                                </div>
                                            </div>
                                            <div class="col col-stats ms-3 ms-sm-0">
                                                <div class="numbers">
                                                    <p class="card-category"><?= $item['category'] ?></p>
                                                    <h4 class="card-title"><?= $item['count'] ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Controles del carrusel -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="prev" style="width: 24px;">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleDark" data-bs-slide="next" style="width: 24px;">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <!-- Fila para las gráficas -->
    <div class="row mb-5">
        <!-- Gráfica de la izquierda que ocupa toda la columna en dispositivos pequeños -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="chart-container" style="position: relative; height: 300px;">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        <!-- Gráfica de la derecha con dos canvas anidados -->
        <div class="col-12 col-lg-6">
            <div class="chart-container" style="position: relative; height: 300px;">
                <canvas id="acquisitions"></canvas>
            </div>
            <div class="chart-container mt-3" style="position: relative; height: 300px;">
                <canvas id="acquisitions2"></canvas>
            </div>
        </div>

    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/../../layout/layout.php'; ?>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script type="module">
    import ApiService from "./../../assets/js/apiservice.js";
    var baseURL = "";
    var ListaDetallesZonas = [];

    const apiService = new ApiService(baseURL);

    const getZonasUsuariosById = async () => {
        const endpointListaZonaUsuarios = "<?php echo $routeParser->urlFor('zona_usuarios.list_relational_all', ['id' => $user_id]) ?>";
        try {
            return await apiService.get(endpointListaZonaUsuarios);
        } catch (error) {
            console.error("Error al obtener zonas de usuarios:", error);
            return null;
        }
    }

    document.addEventListener('DOMContentLoaded', async function() {
        const dropdownList = document.getElementById('dropdownList');

        const {
            DetallesZonas
        } = await getZonasUsuariosById();
        ListaDetallesZonas = DetallesZonas;

        DetallesZonas.forEach(opcion => {
            const li = document.createElement('li');
            const a = document.createElement('a');
            a.classList.add('dropdown-item');
            a.href = '#';
            a.textContent = opcion.zonas.nombre;
            a.value = opcion.codigoInternoZonaUsuario;
            a.onclick = function() {
                handleDropdownClick(opcion);
            };

            li.appendChild(a);
            dropdownList.appendChild(li);
        });
    });


    const handleDropdownClick = (option) => {
        document.getElementById('dropdownMenu').textContent = option.zonas.nombre;
        console.log("Opción seleccionada: " + option.zonas.nombre);
    }

    const ctx = document.getElementById('myChart');

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
            datasets: [{
                label: '# of Votes',
                data: [12, 19, 3, 5, 2, 3],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)',
                    'rgba(255, 159, 64, 0.6)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 2,
                borderRadius: 5,
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#333'
                    },
                    grid: {
                        color: 'rgba(200, 200, 200, 0.3)'
                    }
                },
                x: {
                    ticks: {
                        color: '#333'
                    }
                }
            },
            plugins: {
                legend: {
                    labels: {
                        color: '#333',
                        font: {
                            size: 14
                        }
                    }
                }
            }
        }
    });


    (async function() {
        const data = [{
                year: 2010,
                count: 10
            },
            {
                year: 2011,
                count: 20
            },
            {
                year: 2012,
                count: 15
            },
            {
                year: 2013,
                count: 25
            },
            {
                year: 2014,
                count: 22
            },
            {
                year: 2015,
                count: 30
            },
            {
                year: 2016,
                count: 28
            },
        ];

        new Chart(document.getElementById('acquisitions'), {
            type: 'bar',
            data: {
                labels: data.map(row => row.year),
                datasets: [{
                    label: 'Acquisitions by Year',
                    data: data.map(row => row.count),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 5,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#333'
                        },
                        grid: {
                            color: 'rgba(200, 200, 200, 0.3)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#333'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    })();


    (async function() {
        const data = [{
                year: 1,
                count: 10
            },
            {
                year: 2,
                count: 20
            },
            {
                year: 3,
                count: 15
            },
            {
                year: 4,
                count: 25
            },
            {
                year: 5,
                count: 22
            },
            {
                year: 6,
                count: 30
            },
            {
                year: 7,
                count: 28
            },
        ];

        new Chart(document.getElementById('acquisitions2'), {
            type: 'bar',
            data: {
                labels: data.map(row => row.year),
                datasets: [{
                    label: 'Acquisitions by Month',
                    data: data.map(row => row.count),
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 2,
                    borderRadius: 5,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            color: '#333'
                        },
                        grid: {
                            color: 'rgba(200, 200, 200, 0.3)'
                        }
                    },
                    x: {
                        ticks: {
                            color: '#333'
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: '#333',
                            font: {
                                size: 14
                            }
                        }
                    }
                }
            }
        });
    })();

    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById("myChart1").getContext("2d");

        new Chart(ctx, {
            type: "line",
            data: {
                labels: ["Lun", "Mar", "Mié"],
                datasets: [{
                    label: "Ventas Diarias",
                    data: [10000, 4200, 40000],
                    borderColor: "white",
                    backgroundColor: "#438eec",
                    tension: 0.3,
                    fill: true,
                    pointRadius: 3,
                    pointBackgroundColor: "green",
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        grid: {
                            display: false // Oculta la cuadrícula en el eje X
                        },
                        ticks: {
                            display: false // Muestra las etiquetas en el eje X
                        }
                    },
                    y: {
                        grid: {
                            display: false // Oculta la cuadrícula en el eje Y
                        },
                        beginAtZero: false,
                        ticks: {
                            display: false // Muestra las etiquetas en el eje Y
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false // Oculta la leyenda del gráfico
                    }
                }
            }
        });
    });
</script>