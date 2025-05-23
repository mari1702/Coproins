<?php
require_once "../controllers/ProductoController.php";
require_once "../controllers/CategoriaController.php";
require_once "../controllers/CotizacionController.php";

$productos = ProductoController::listar();
$categorias = CategoriaController::listar();
$cotizaciones = CotizacionController::listar();
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

    <title>Cotizaciones</title>

</head>
<body>
    <header>
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg shadow p-3 mb-3" style="background-color: #55BCD1;">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">
                    <img src="../images/logo.png" alt="Logo" width="25" height="25" />
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
                    aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                    <ul class="navbar-nav ">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="#">Inicio</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">Productos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="prices.php">Cotizaciones</a>
                        </li>
                        
                        <li>
                            <a href="../bd/logout.php" class="nav-link logout-icon">
                                <i class="fas fa-sign-out-alt"></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>



    <section class="container-fluid">
        <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
            <div class="alert alert-success" role="alert"> Cambios realizados exitosamente!</div>
        <?php endif; ?>
        <div class=" new row justify-content-center d-none">
            <div class="card col-sm-11 col-md-8 col-lg-6 shadow p-3 mb-5 bg-white rounded border-0">
                <div class="card-header bg-white">
                    <h3 class="text-center">Nueva Cotización</h3>
                </div>
                <div class="card-body">
                    
                    <div class="mb-3">
                        <select class="form-select" id="client" name="client">
                            <option value="">Selecciona un cliente</option>
                            <option value="Cliente 1">Cliente 1 </option>
                            <option value="Cliente 2">Cliente 2</option>
                            <option value="Cliente 3">Cliente 3</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <input type="text" class="form-control" id="projectName" placeholder="Nombre del proyecto">                     
                    </div>

                    <div id="notes-container">

                    </div>


                    <div id="products-container">

                        <div class="row">
                            <div class="col-6">
                                <button class="btn btn-primary w-100" 
                                data-bs-toggle="modal"
                                data-bs-target="#ModalProductList">
                                    Añadir Producto
                                <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <div class="col-6">
                                <button class="btn btn-primary w-100" id="newNote">
                                    Añadir Nota
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


                        <div class="col-lg-4 col-sm-9 mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-filter"></i>
                                </span>
                                <select class="form-select" id="clientFilter">
                                    <option value="">Todos los clientes</option>
                                    <option value="Cliente 1">Cliente 1</option>
                                    <option value="Cliente 2">Cliente 2</option>
                                    <option value="Cliente 3">Cliente 3</option>    
                                </select>
                            </div>
                        </div>

                        <div class="col-lg-6 col-sm-12 mb-3">
                            <div class="input-group">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" id="searchInput" class="form-control"
                                    placeholder="Buscar proyectos...">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                    <table class="table" id="pricesTable">
                        <thead class="table-primary">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Proyecto</th>
                                <th scope="col">Fecha</th>
                                <th scope="col">Total</th>
                                <th scope="col" class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($cotizaciones as $cotizacion):?>
                            <tr data-client="<?= $cotizacion->getCliente()?>">
                                <th scope="row"><?= $cotizacion->getId()?></th>
                                <td><?= $cotizacion->getNombreProyecto()?></td>
                                <td><?= $cotizacion->getFecha()?></td>
                                <td class="price-format"><?= $cotizacion->getTotal()?></td>
                                <td>
                                    <div class="d-flex justify-content-center gap-2">
                                        <!-- Ver -->
                                        <a class="btn btn-info btn-sm" href="price-pdf.php?id=<?= $cotizacion->getId()?>" target="_blank">
                                            <i class="fas fa-download"></i>
                                        </a>

                                        <!-- Editar -->
                                        <a href="edit-price.php?id=<?= $cotizacion->getId()?>" class="btn btn-primary btn-sm" title="Editar"
                                            aria-label="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <!-- Eliminar -->
                                        <form method="POST" action="../actions/cotizacion_borrar.php" class="d-inline">
                                            <input type="hidden" name="id" value="<?= $cotizacion->getId(); ?>">
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
                            <h5 class="text-muted">No se encontraron cotizaciones</h5>
                            <p class="text-muted">Intenta con otros términos de búsqueda</p>
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </section>

    
    <section>
        <div class="modal fade" id="ModalProductList" tabindex="-1" aria-labelledby="modalProductListLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="modalProductListLabel">Seleccionar producto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
    
                    <div class="modal-body">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <div class="flex-grow-1">
                                <div class="input-group">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="productSearch" placeholder="Buscar productos...">
                                </div>
                            </div>
                            
                            <div class="flex-shrink-0">
                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#categoryCollapse">
                                    <i class="fas fa-filter"></i> Categorías
                                </button>
                            </div>
                        </div>
                        
                        <div class="collapse mb-3" id="categoryCollapse">
                            <div class="d-flex flex-wrap gap-2"> 
                                <button class="btn btn-sm btn-outline-secondary category-filter active" data-category="all">
                                    Todos
                                </button>
                                <?php foreach($categorias as $categoria):?>
                                <button class="btn btn-sm btn-outline-secondary category-filter" data-category="<?= $categoria->getId();?>">
                                    <?= $categoria->getNombre(); ?>
                                </button>
                                <?php endforeach ?>
                            </div>
                        </div>
                        
                        <div class="product-list" style="max-height: 400px; overflow-y: auto;">

                                    
                            <?php foreach($productos as $producto):?>
                            <div class="card mb-2 product-item" id="product-item" data-category="<?= $producto->getCategoria()->getId()?>">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">

                                        <?php if ($producto->getImgRuta()): ?>
                                        <div class="col-2">
                                            <div style="position: relative; width: 100%; padding-bottom: 100%;">
                                                <img src="../<?= $producto->getImgRuta()?>" class="img-fluid rounded position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;"  id="img" alt="<?= $producto->getNombre()?>">
                                            </div>
                                        </div>
                                        <?php endif?>

                                        <div class=" <?= ($producto->getImgRuta()) ? 'col-7' : 'col-9' ?>">
                                            <h6 class="mb-1 text-truncate" id="description"  ><?= $producto->getDescripcion()?></h6>
                                            <p class="small text-muted mb-1"><?= $producto->getDescripcion()?></p>
                                            <p class="d-none" id="price"><?= $producto->getPrecio()?></p>
                                            <span class="badge bg-light text-dark" id="id" ><strong><?= $producto->getId()?></strong></span>
                                            <span class="badge bg-primary" id="category-name" ><?= $producto->getCategoria()->getNombre()?></span>
                                        </div>
                                        <div class="col-3  text-end">
                                            <button class="btn btn-sm btn-primary select-product">
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
        var note_list = [];

        document.addEventListener('DOMContentLoaded', function() {
            // Elementos del DOM
            const searchInput = document.getElementById('searchInput');
            const clientFilter = document.getElementById('clientFilter');
            const clearSearch = document.getElementById('clearSearch');
            const projectsTable = document.getElementById('pricesTable');
            const noResults = document.getElementById('noResults');
            const rows = projectsTable.querySelectorAll('tbody tr');

            // Función para filtrar
            function filterProjects() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedClient = clientFilter.value;
                let visibleCount = 0;

                rows.forEach(row => {
                    const name = row.cells[1].textContent.toLowerCase();
                    const client = row.getAttribute('data-client');
                    const matchesSearch = name.includes(searchTerm);
                    const matchesClient = !selectedClient || client === selectedClient;

                    if (matchesSearch && matchesClient) {
                        row.classList.remove('d-none');
                        visibleCount++;
                    } else {
                        row.classList.add('d-none');
                    }
                });

                noResults.classList.toggle('d-none', visibleCount > 0);
            }

            searchInput.addEventListener('input', filterProjects);
            clientFilter.addEventListener('change', filterProjects);

            clearSearch.addEventListener('click', function () {
                searchInput.value = '';
                filterProjects();
                searchInput.focus();
            });




            // Filtrado por búsqueda
            const productSearch = document.getElementById('productSearch');
            productSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                filterProducts();
            });
        
            // Filtrado por categoría
            document.querySelectorAll('.category-filter').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.category-filter').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    filterProducts();
                });
            });
        
            // Función combinada de filtrado
            function filterProducts() {
                const searchTerm = productSearch.value.toLowerCase();
                const activeCategory = document.querySelector('.category-filter.active').dataset.category;
                
                document.querySelectorAll('.product-item').forEach(item => {
                    const productName = item.querySelector('h6').textContent.toLowerCase();
                    const productCategory = item.dataset.category;
                    
                    const matchesSearch = productName.includes(searchTerm);
                    const matchesCategory = activeCategory === 'all' || productCategory === activeCategory;
                    
                    item.style.display = (matchesSearch && matchesCategory) ? 'block' : 'none';
                });
            }

            // Añadir nota
            document.getElementById('products-container').addEventListener('click', function(e) {
                if (e.target.id === 'newNote' || e.target.closest('#newNote')) {
                    const noteContainer = document.getElementById('notes-container');
                    const noteInput = document.createElement('textarea');
                    noteInput.classList.add('form-control');
                    noteInput.classList.add('mb-3');
                    noteInput.placeholder = 'Añadir nota';
                    noteContainer.appendChild(noteInput);
                }
            });
        
            // Selección de producto
            document.querySelectorAll('.select-product').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productCard = this.closest('#product-item');
                    const productId = productCard.querySelector('#id').textContent.trim();
                    const productDescription = productCard.querySelector('#description').textContent.trim();
                    const productPrice = productCard.querySelector('#price').textContent.trim();
                    const productImg = productCard.querySelector('#img') ? productCard.querySelector('#img').src : null;
                    
                    // Agregar producto al carrito
                    addToCart({
                        id: productId,
                        description: productDescription,
                        price: productPrice,
                        img: productImg,
                        quantity: 1
                    });
                    
                    // Actualizar la vista del carrito
                    updateCartView();
                });
            });

            // Función para agregar producto al carrito
            function addToCart(product) {
                const existingProduct = cart.find(item => item.id === product.id);
                if (existingProduct) {
                    existingProduct.quantity += 1;
                } else {
                    cart.push(product);
                }
            }

            // Función para actualizar la vista del carrito
            function updateCartView() {
                const cartContainer = document.querySelector('#products-container');
                let cartHTML = ``;

                let cartTotal = 0;

                cart.forEach(item => {
                    const totalPrice = item.price * item.quantity;
                    cartTotal += totalPrice;
                    cartHTML += `
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-body p-3">
                                <div class="row align-items-center g-3 mb-3">
                                    ${item.img == null ? " " : `    
                                    <div class="col-md-2">
                                        <div style="position: relative; width: 100%; padding-bottom: 100%;">
                                            <img src="${item.img}" class="img-fluid rounded position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;" alt="${item.description}">
                                        </div>
                                    </div>
                                    `}
                                    <div class=" ${item.img == null ? 'col-md-6' : 'col-md-4'} col-12">
                                        <h6 class="card-title mb-1 fw-bold text-truncate">${item.description}</h6>
                                        <p class="card-text text-muted small mb-1">${item.description}</p>
                                        <span class="badge bg-light text-dark">ID: ${item.id}</span>
                                    </div>
                                    <div class="col-md-2 col-4">
                                        <div class="input-group input-group-sm">
                                            <input type="number" class="form-control text-center quantity-input" 
                                                value="${item.quantity}" min="1" 
                                                data-product-id="${item.id}"
                                                onchange="updateQuantity(this)">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-4 text-center">
                                        <p class="mb-0 fw-bold"><span class="price-format total-price" data-unit-price="${item.price}">${totalPrice}</span> 
                                        <span class="text-muted small d-block"><span class="price-format unit-price">${item.price}</span> c/u</span></p>
                                    </div>
                                    <div class="col-md-2 col-4 text-end">
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="removeFromCart('${item.id}')"
                                                aria-label="Eliminar producto"
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
                        <div class="col-6">
                            <button class="btn btn-primary w-100" 
                            data-bs-toggle="modal"
                            data-bs-target="#ModalProductList">
                                Añadir Producto
                            <i class="fa fa-plus"></i>
                            </button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-primary w-100" id="newNote">
                                Añadir Nota
                            <i class="fa fa-plus"></i>
                            </button>
                        </div>

                    </div>

                    <button class="btn btn-success col-12 mb-3" id="generateQuote" onclick="generateQuote()">
                        Generar Cotización
                    </button>
                   
                    <div class="row justify-content-end">
                        <div class="col-md-4 text-end">
                            <h5 class="mb-0">Total: <span class="price-format cart-total">${cartTotal}</span></h5>
                        </div>
                    </div>        
                `;

                cartContainer.innerHTML = cartHTML;
                formatPrices();
            }

            // Función para actualizar cantidad
            window.updateQuantity = function(input) {
                const productId = input.dataset.productId;
                const quantity = parseInt(input.value);
                
                const product = cart.find(item => item.id === productId);
                if (product) {
                    product.quantity = quantity;
                    const totalPriceElement = input.closest('.row').querySelector('.total-price');
                    const unitPrice = parseFloat(totalPriceElement.dataset.unitPrice);
                    totalPriceElement.textContent = (unitPrice * quantity).toString();
                    
                    // Actualizar el total del carrito
                    let cartTotal = 0;
                    cart.forEach(item => {
                        cartTotal += item.price * item.quantity;
                    });
                    document.querySelector('.cart-total').textContent = cartTotal.toString();
                    
                    formatPrices();
                }
            };

            window.removeFromCart = function(productId) {
                cart = cart.filter(item => item.id !== productId);
                updateCartView();
            };

            formatPrices();
        });

        function formatPrices() {
            document.querySelectorAll('.price-format').forEach(element => {
                const price = parseFloat(element.textContent.replace(/[^0-9.-]+/g, ""));
                if (!isNaN(price)) {
                    element.textContent = new Intl.NumberFormat('es-MX', {
                        style: 'currency',
                        currency: 'MXN',
                        minimumFractionDigits: 0
                    }).format(price);
                }
            });
        }

        function generateQuote() {
            const projectName = document.getElementById('projectName').value;
            const client = document.getElementById('client').value;
            const products = cart;
            const total = document.querySelector('.cart-total').textContent.replace(/[^0-9.-]+/g, "");

            const notes = document.querySelectorAll('#notes-container textarea');

            note_list = [];
            notes.forEach(note => {
                if (note.value) {
                    note_list.push({
                        note: note.value
                    });
                }
            
            });

            console.log(note_list);

            // Validar que los campos no estén vacíos
            if (client == '') {
                alert('Por favor, seleccione un cliente');
                return;
            }

            if (!projectName) {
                alert('Por favor, ingrese el nombre del proyecto');
                return;
            }
            
            if (products.length === 0) {
                alert('Por favor, seleccione al menos un producto');
                return;
            }

            // Crear FormData para enviar los datos
            const formData = new FormData();
            formData.append('nombre_proyecto', projectName);
            formData.append('cliente', client);
            formData.append('total', total);
            formData.append('productos', JSON.stringify(products));
            formData.append('notas', JSON.stringify(note_list));

            // Enviar datos al servidor
            fetch('../actions/cotizacion_crear.php', {
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
                    alert('Cotización generada exitosamente');
                    window.location.href = 'prices.php?status=success';
                } else {
                    throw new Error(data.message || 'Error al generar la cotización');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al generar la cotización: ' + error.message);
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