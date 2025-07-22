<?php
include "../components/templates/template.php";
include "../components/organisms/navbar.php";

require_once "../core/RoleHandler.php";
require_once '../models/Cotizacion.php';
require_once "../controllers/ProductoController.php";
require_once "../controllers/CategoriaController.php";
require_once "../controllers/ClienteController.php";

RoleHandler::OnlyAdmin();
RoleHandler::checkSession();


$productos = ProductoController::listar();
$categorias = CategoriaController::listar();
$clientes = ClienteController::listar();
$cotizacion = new Cotizacion()->getById($_GET['id']);

startTemplate("Editar Cotización");
?>

<header>
    <!-- Navbar -->
    <?php
    navBar('cotizaciones');
    ?>
</header>

<section class="container-fluid">

    <div class="row justify-content-center">
        <div class=" mb-3">
            <div class="input-group">
                <a class="btn btn-primary" href="prices.php" id="showForm"><i class="fas fa-arrow-left"> </i> Regresar</a>
            </div>
        </div>
        <div class="card col-sm-11 col-md-8 col-lg-6 shadow p-3 mb-5 bg-white rounded">
            <div class="card-body">

                <div class="mb-3">
                    <div class="mb-3">
                        <select class="form-select" id="client" name="client">
                            <option value="" <?= ($cotizacion->getCliente() == '') ? 'selected' : '' ?>>Selecciona un cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= $cliente->getId(); ?>" <?= ($cotizacion->getCliente()->getId() == $cliente->getId()) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cliente->getCliente()); ?>
                                </option>
                            <?php endforeach; ?>

                        </select>
                    </div>

                    <div class="mb-3">
                        <input type="text" class="form-control" id="projectName" value="<?= $cotizacion->getNombreProyecto() ?>">
                    </div>

                    <div id="notes-container"></div>

                    <div id="products-container"></div>
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
                            <?php foreach ($categorias as $categoria): ?>
                                <button class="btn btn-sm btn-outline-secondary category-filter" data-category="<?= $categoria->getId(); ?>">
                                    <?= $categoria->getNombre(); ?>
                                </button>
                            <?php endforeach ?>
                        </div>
                    </div>

                    <div class="product-list" style="max-height: 400px; overflow-y: auto;">


                        <?php foreach ($productos as $producto): ?>
                            <div class="card mb-2 product-item" id="product-item" data-category="<?= $producto->getCategoria()->getId() ?>">
                                <div class="card-body py-2">
                                    <div class="row align-items-center">

                                        <?php if ($producto->getImgRuta()): ?>
                                            <div class="col-2">
                                                <div style="position: relative; width: 100%; padding-bottom: 100%;">
                                                    <img src="../<?= $producto->getImgRuta() ?>" class="img-fluid rounded position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;" id="img" alt="<?= $producto->getNombre() ?>">
                                                </div>
                                            </div>
                                        <?php endif ?>

                                        <div class=" <?= ($producto->getImgRuta()) ? 'col-7' : 'col-9' ?>">
                                            <h6 class="mb-1 text-truncate" id="description"><?= $producto->getDescripcion() ?></h6>
                                            <p class="small text-muted mb-1"><?= $producto->getDescripcion() ?></p>
                                            <p class="d-none" id="price"><?= $producto->getPrecio() ?></p>
                                            <span class="badge bg-light text-dark" id="id"><strong><?= $producto->getId() ?></strong></span>
                                            <span class="badge bg-primary" id="category-name"><?= $producto->getCategoria()->getNombre() ?></span>
                                        </div>
                                        <div class="col-3  text-end">
                                            <button class="btn btn-sm btn-primary select-product">
                                                Seleccionar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach ?>

                    </div>
                </div>


            </div>
        </div>
    </div>
</section>
<script>
    var cart = [];
    var note_list = [];
    <?php foreach ($cotizacion->getProductos() as $producto): ?>
        product = {
            id: "<?= json_encode($producto['producto']->getId()) ?>",
            description: <?= json_encode($producto['producto']->getDescripcion()) ?>,
            price: "<?= json_encode($producto['producto']->getPrecio()) ?>",
            img: "../"+<?= json_encode($producto['producto']->getImgRuta()) ?>,
            quantity: <?= json_encode($producto['cantidad']) ?>
        }
        cart.push(product);
    <?php endforeach ?>

    <?php foreach ($cotizacion->getNotas() as $nota): ?>
        note_list.push({
            id: <?= json_encode($nota->getId()) ?>,
            note: <?= json_encode($nota->getNota()) ?>
        });
    <?php endforeach ?>






    document.addEventListener('DOMContentLoaded', function() {
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
                const imgElement = productCard.querySelector('#img');
                const productImg = imgElement ? imgElement.getAttribute('src') : null;

                console.log(productImg);
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
                                    ${item.img == null ? " "  : 
                                    `
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">
                                        <div style="position: relative; width: 100%; padding-bottom: 100%;">
                                            <img src="${item.img}" class="img-fluid rounded position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;" alt="${item.description}">
                                        </div>
                                    </div>
                                    `
                                    }

                                    <div class=" ${item.img == null ? 'col-12 col-sm-6 col-md-4 col-lg-4' : 'col-10 col-sm-4 col-md-2 col-lg-4'}">
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

                    <button class="btn btn-success col-12 mb-3" id="updateQuote" onclick="updateQuote()">
                        Actualizar Cotización
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

        //Mostrar notas     
        note_list.forEach(note => {
            const noteContainer = document.getElementById('notes-container');
            const noteInput = document.createElement('textarea');
            noteInput.classList.add('form-control');
            noteInput.classList.add('mb-3');
            noteInput.id = note.id;
            noteInput.value = note.note;
            noteContainer.appendChild(noteInput);
        });

        window.removeFromCart = function(productId) {
            cart = cart.filter(item => item.id !== productId);
            updateCartView();
        };

        formatPrices();
        updateCartView();
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

    function updateQuote() {
        const projectName = document.getElementById('projectName').value;
        const client = document.getElementById('client').value;
        const products = cart;
        const total = document.querySelector('.cart-total').textContent.replace(/[^0-9.-]+/g, "");
        const notes = document.querySelectorAll('#notes-container textarea');

        note_list = [];
        notes.forEach(note => {
            if (note.value) {
                note_list.push({
                    id: parseInt(note.id),
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
        formData.append('id', <?= $cotizacion->getId() ?>);
        formData.append('nombre_proyecto', projectName);
        formData.append('cliente', client);
        formData.append('total', total);
        formData.append('productos', JSON.stringify(products));
        formData.append('notas', JSON.stringify(note_list));
        // Enviar datos al servidor
        fetch('../actions/cotizacion_editar.php', {
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
                    alert('Cotización actualizada exitosamente');
                    window.location.href = 'prices.php?status=success';
                } else {
                    throw new Error(data.message || 'Error al actualizar la cotización');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al actualizar la cotización: ' + error.message);
            });
    }
</script>
<?php
endTemplate();
