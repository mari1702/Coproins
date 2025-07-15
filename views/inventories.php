<?php
include "../components/navbar.php";

require_once "../controllers/HerramientaController.php";
require_once "../controllers/DepartamentoController.php";
require_once "../controllers/MarcaController.php";
require_once "../controllers/InventarioController.php";

$herramientas = HerramientaController::listar();
$departamentos = DepartamentoController::listar();
$marcas = MarcaController::listar();
$inventarios = InventarioController::listar();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--  Bootstrap CSS    -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <!--  Navbar CSS    -->
    <link rel="stylesheet" href="../css/navbar.css">
    <!-- Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <title>Inventarios</title>

</head>
<body>
    <header>
        <!-- Navbar -->
        <?php
        navBar('inventarios');
        ?>
    </header>



    <section class="container-fluid">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success" role="alert"> Cambios realizados exitosamente!</div>
        <?php endif; ?>
        <div class=" new row justify-content-center d-none">
            <div class="card col-sm-11 col-md-8 col-lg-6 shadow p-3 mb-5 bg-white rounded border-0">
                <div class="card-header bg-white">
                    <h3 class="text-center">Nuevo Inventario</h3>
                </div>
                <div class="card-body">

                    <div class="mb-3">
                        <input type="text" class="form-control" id="ubicacion" placeholder="Ubicación">                     
                    </div>



                    <div id="tools-container">

                        <div class="row">
                            <div class="col-12">
                                <button class="btn btn-primary w-100" 
                                data-bs-toggle="modal"
                                data-bs-target="#ModalToolList">
                                    Añadir Herramienta
                                <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            
                        </div>

                    </div>

                    
                    
                    
                                          
                    
                </div>
            </div>
        </div>
    </section>

    <section class="container-fluid">

        

        <div class="row mb-4">
            <div class="card col-12 shadow mb-5 rounded border-0">
                <div class="card-body">

                    <div class="row mb-4">

                    <div class="col-lg-2 col-sm-3 mb-3">
                            <div class="input-group">
                                <button class="btn btn-primary" id="showForm"><i class="fas fa-plus"> </i>
                                    Nuevo</button>
                            </div>
                        </div>

                        <div class="col-lg-10 col-sm-9 mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Buscar ubicaciones...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                    <table class="table" id="inventoriesTable">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Ubicación</th>
                                <th scope="col">Fecha</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($inventarios as $inventario):?>
                            <tr>
                                <th scope="row"><?= $inventario->getId()?></th>
                                <td><?= $inventario->getUbicacion()?></td>
                                <td><?= $inventario->getFecha()?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Ver -->
                                        <a class="btn btn-info btn-sm" href="inventory-pdf.php?id=<?= $inventario->getId()?>" target="_blank">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <!-- Editar -->
                                        <a href="edit-inventory.php?id=<?= $inventario->getId()?>" class="btn btn-primary btn-sm" title="Editar"
                                            aria-label="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Eliminar -->
                                        <form method="POST" action="../actions/inventario_borrar.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $inventario->getId(); ?>">
                                            <button type="button" class="btn btn-danger btn-sm" title="Eliminar"
                                                aria-label="Eliminar" onclick="confirmarEliminacion(this)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach?>
                        </tbody>
                        <div id="noResults" class="text-center py-4 d-none">
                            <i class="fas fa-search fa-3x mb-3 text-muted"></i>
                            <h5 class="text-muted">No se encontraron inventarios</h5>
                            <p class="text-muted">Intenta con otros términos de búsqueda</p>
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </section>

    
    <section>
        <div class="modal fade" id="ModalToolList" tabindex="-1" aria-labelledby="modalToolListLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalToolListLabel">Seleccionar herramienta</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
    
                    <div class="modal-body">
                        <div class="d-flex flex-column align-items-center gap-2 mb-3">
    
                        <div class="w-100">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" class="form-control" id="toolSearch" placeholder="Buscar herramientas...">
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-outline-primary flex-shrink-0" type="button" data-bs-toggle="collapse" data-bs-target="#departmentCollapse">
                                <i class="fas fa-filter"></i> Departamentos
                            </button>

                            <button class="btn btn-outline-primary flex-shrink-0" type="button" data-bs-toggle="collapse" data-bs-target="#brandCollapse">
                                <i class="fas fa-filter"></i> Marcas
                            </button>
                        </div>

                    </div>

                        
                        <div class="collapse mb-3" id="departmentCollapse">
                            <div class="d-flex flex-wrap gap-2"> 
                                <button class="btn btn-sm btn-outline-secondary department-filter active" data-department="all">
                                    Todos
                                </button>
                                <?php foreach($departamentos as $departamento):?>
                                <button class="btn btn-sm btn-outline-secondary department-filter" data-department="<?= $departamento->getId();?>">
                                    <?= $departamento->getNombre(); ?>
                                </button>
                                <?php endforeach ?>
                            </div>
                        </div>

                        <div class="collapse mb-3" id="brandCollapse">
                            <div class="d-flex flex-wrap gap-2"> 
                                <button class="btn btn-sm btn-outline-secondary brand-filter active" data-brand="all">
                                    Todos
                                </button>
                                <?php foreach($marcas as $marca):?>
                                <button class="btn btn-sm btn-outline-secondary brand-filter" data-brand="<?= $marca->getId();?>">
                                    <?= $marca->getNombre(); ?>
                                </button>
                                <?php endforeach ?>
                            </div>
                        </div>
                        
                        <div class="tool-list" style="max-height: 400px; overflow-y: auto;">

                                    
                            <?php foreach($herramientas as $herramienta):?>
                            <div class="card mb-2 tool-item" id="tool-item" data-department="<?= $herramienta->getDepartamento()->getId()?>" data-brand="<?= $herramienta->getMarca()->getId()?>">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">

                                        <?php if ($herramienta->getImgRuta()): ?>
                                        <div class="col-2">
                                            <div style="position: relative; width: 100%; padding-bottom: 100%;">
                                                <img src="../<?= $herramienta->getImgRuta()?>" class="img-fluid rounded position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;"  id="img" alt="<?= $herramienta->getDescripcion()?>">
                                            </div>
                                        </div>
                                        <?php endif?>

                                        <div class=" <?= ($herramienta->getImgRuta()) ? 'col-7' : 'col-9' ?>">
                                            <h6 class="mb-1 text-truncate" id="description"  ><?= $herramienta->getDescripcion()?></h6>
                                            <p class="small text-muted mb-1"><?= $herramienta->getDescripcion()?></p>
                                            <span class="badge bg-light text-dark" id="id" ><strong><?= $herramienta->getId()?></strong></span>
                                            <span class="badge bg-primary" id="department-name" ><?= $herramienta->getDepartamento()->getNombre()?></span>
                                            <span class="badge bg-secondary" id="brand-name" ><?= $herramienta->getMarca()->getNombre()?></span>

                                        </div>
                                        <div class="col-3  text-end">
                                            <button class="btn btn-sm btn-primary select-tool">
                                                Seleccionar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach?>
                            
                        </div>
                    </div>
    
                    
                </div>
            </div>
        </div>
    </section>
    
    <script>
        var cart = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Elementos del DOM
            const searchInput = document.getElementById('searchInput');
            const clearSearch = document.getElementById('clearSearch');
            const inventoriesTable = document.getElementById('inventoriesTable');
            const noResults = document.getElementById('noResults');
            const rows = inventoriesTable.querySelectorAll('tbody tr');

            // Función para filtrar
            function filterInventories() {
                const searchTerm = searchInput.value.toLowerCase();
                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const matchesSearch = name.includes(searchTerm);

                    if (matchesSearch ) {
                        row.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        row.classList.add('d-none');
                    }
                });

                noResults.classList.toggle('d-none', visibleCount > 0);
            }

            searchInput.addEventListener('input', filterInventories);

            clearSearch.addEventListener('click', function () {
                searchInput.value = '';
                filterInventories();
                searchInput.focus();
            });




            // Filtrado por búsqueda
            const toolSearch = document.getElementById('toolSearch');
            toolSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                filterTools();
            });
        
            // Filtrado por departamentos
            document.querySelectorAll('.department-filter').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.department-filter').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterTools();
                });
            });

            // Filtrado por marcas
            document.querySelectorAll('.brand-filter').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.brand-filter').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterTools();
                });
            });
        
            // Función combinada de filtrado
            function filterTools() {
                const searchTerm = toolSearch.value.toLowerCase();
                const activeDepartment = document.querySelector('.department-filter.active').dataset.department;
                const activeBrand = document.querySelector('.brand-filter.active').dataset.brand;

                
                document.querySelectorAll('.tool-item').forEach(item => {
                    const toolName = item.querySelector('h6').textContent.toLowerCase();
                    const toolDepartment = item.dataset.department;
                    const toolBrand = item.dataset.brand;

                    
                    const matchesSearch = toolName.includes(searchTerm);
                    const matchesDepartment = activeDepartment === 'all' || toolDepartment === activeDepartment;
                    const matchesBrand = activeBrand === 'all' || toolBrand === activeBrand;

                    
                    item.style.display = (matchesSearch && matchesDepartment && matchesBrand) ? 'block' : 'none';
                });
            }

        
            // Selección de herramienta
            document.querySelectorAll('.select-tool').forEach(btn => {
                btn.addEventListener('click', function() {
                    const toolCard = this.closest('#tool-item');
                    const toolId = toolCard.querySelector('#id').textContent.trim();
                    const toolDescription = toolCard.querySelector('#description').textContent.trim();
                    const toolImg = toolCard.querySelector('#img') ? toolCard.querySelector('#img').src : null;
                    const toolBrand =toolCard.querySelector('#brand-name').textContent;
                    
                    // Agregar toolo al carrito
                    addToCart({
                        id: toolId,
                        description: toolDescription,
                        img: toolImg,
                        brand: toolBrand,
                        quantity: 1
                    });
                    
                    // Actualizar la vista del carrito
                    updateCartView();
                });
            });

            // Función para agregar toolo al carrito
            function addToCart(tool) {
                const existingTool = cart.find(item => item.id === tool.id);
                if (existingTool) {
                    existingTool.quantity += 1;
                } else {
                    cart.push(tool);
                }
            }

            // Función para actualizar la vista del carrito
            function updateCartView() {
                const cartContainer = document.querySelector('#tools-container');
                let cartHTML = ``;

                cart.forEach(item => {
                    cartHTML += `
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-body p-3">
                                <div class="row align-items-center g-3 mb-3">
                                    ${item.img == null ? " " : `    
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">
                                        <div style="position: relative; width: 100%; padding-bottom: 100%;">
                                            <img src="${item.img}" class="img-fluid rounded position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;" alt="${item.description}">
                                        </div>
                                    </div>
                                    `}
                                    <div class=" ${item.img == null ? 'col-12 col-sm-8 col-md-6 col-lg-6' : 'col-10 col-sm-4 col-md-4 col-lg-4'}">
                                        <h6 class="card-title mb-1 fw-bold text-truncate">${item.description}</h6>
                                        <p class="card-text text-muted small mb-1">${item.description}</p>
                                        <span class="badge bg-primary text-white">ID: ${item.id}</span>
                                        <span class="badge bg-secondary text-white">${item.brand}</span>

                                    </div>
                                    <div class="col-md-2 col-4">
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control text-center quantity-input" 
                                                value="${item.quantity}" min="1" 
                                                data-tool-id="${item.id}"
                                                onchange="updateQuantity(this)">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 col-4 text-end">
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="removeFromCart('${item.id}')"
                                                aria-label="Eliminar toolo"
                                                title="Eliminar">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });

                cartHTML += `
                    <div class="row mb-3">
                        <div class="col-12">
                            <button class="btn btn-primary w-100" 
                            data-bs-toggle="modal"
                            data-bs-target="#ModalToolList">
                                Añadir Herramienta
                            <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        

                    </div>

                    <button class="btn btn-success col-12 mb-3" id="generateInventory" onclick="generateInventory()">
                        Generar Inventario
                    </button>
          
                `;

                cartContainer.innerHTML = cartHTML;
            }

            // Función para actualizar cantidad
            window.updateQuantity = function(input) {
                const toolId = input.dataset.toolId;
                const quantity = parseInt(input.value);
                const tool = cart.find(item => item.id === toolId);
                if (tool) {
                    tool.quantity = quantity;
                }
            };

            
            window.removeFromCart = function(toolId) {
                cart = cart.filter(item => item.id !== toolId);
                updateCartView();
            };

        });

        

        function generateInventory() {
            const ubicacion = document.getElementById('ubicacion').value;
            const tools = cart;



            // Validar que los campos no estén vacíos
            

            if (!ubicacion) {
                alert('Por favor, ingrese la ubicación');
                return;
            }
            
            if (tools.length === 0) {
                alert('Por favor, seleccione al menos un toolo');
                return;
            }

            // Crear FormData para enviar los datos
            const formData = new FormData();
            formData.append('ubicacion', ubicacion);
            formData.append('herramientas', JSON.stringify(tools));

            // Enviar datos al servidor
            fetch('../actions/inventario_crear.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    alert('Inventario generado exitosamente');
                    window.location.href = 'inventories.php?status=success';
                } else {
                    throw new Error(data.message || 'Error al generar el inventario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar el inventario: ' + error.message);
            });
        }
    </script>
    
    
    <script src="../js/toggleForm.js"></script>
    <script src="../js/confirmations.js"></script>

    <!--   Bootstrap JS   -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
</body>
</html>