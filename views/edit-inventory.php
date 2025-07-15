<?php
include "../components/navbar.php";

require_once '../models/Inventario.php';
require_once "../controllers/HerramientaController.php";
require_once "../controllers/DepartamentoController.php";
require_once "../controllers/MarcaController.php";


$herramientas = HerramientaController::listar();
$departamentos = DepartamentoController::listar();
$departamentos = DepartamentoController::listar();
$marcas = MarcaController::listar();
$inventario = new Inventario()->getById($_GET['id']);
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


    <title>Editar inventario</title>

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

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <div class="row justify-content-center">
            <div class=" mb-3">
                <div class="input-group">
                    <a class="btn btn-primary" href="inventories.php" id="showForm"><i class="fas fa-arrow-left"> </i> Regresar</a>
                </div>
            </div>
            <div class="card col-sm-11 col-md-8 col-lg-6 shadow p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="mb-3">
                        <input type="text" class="form-control" id="ubicacion"  value="<?= $inventario->getUbicacion()?>">                     
                    </div>


                    <div id="tools-container"></div>
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
        <?php foreach($inventario->getHerramientas() as $herramienta):?>
            tool = {
                id: "<?= json_encode($herramienta['herramienta']->getId())?>",
                description: <?= json_encode($herramienta['herramienta']->getDescripcion())?>,
                img: <?= json_encode($herramienta['herramienta']->getImgRuta())?>,
                brand: <?= json_encode($herramienta['herramienta']->getMarca()->getNombre())?>,
                quantity: <?= json_encode($herramienta['cantidad'])?>
            }
            cart.push(tool);
        <?php endforeach?>
        

        document.addEventListener('DOMContentLoaded', function() {
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
                                            <img src="../${item.img}" class="img-fluid rounded position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;" alt="${item.description}">
                                        </div>
                                    </div>
                                    `}
                                    <div class=" ${item.img == null ? 'col-12 col-sm-8 col-md-6 col-lg-6' : 'col-10 col-sm-4 col-md-2 col-lg-4'}">
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

                    <button class="btn btn-success col-12 mb-3" id="updateInventory" onclick="updateInventory()">
                        Actualizar Inventario
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

            updateCartView();
        });


        function updateInventory() {
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
            formData.append('id', <?= $inventario->getId()?>);
            formData.append('ubicacion', ubicacion);
            formData.append('herramientas', JSON.stringify(tools));

            // Enviar datos al servidor
            fetch('../actions/inventario_editar.php', {
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
                    alert('inventario actualizado exitosamente');
                    window.location.href = 'inventories.php?status=success';
                } else {
                    throw new Error(data.message || 'Error al actualizar el inventario');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar el inventario: ' + error.message);
            });
        }
    </script>
    <!--   Bootstrap JS   -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq"
        crossorigin="anonymous"></script>
</body>
</html>