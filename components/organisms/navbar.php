<?php
function navBar($activeLink)
{
    echo "
        <nav class='navbar navbar-expand-lg shadow p-3 mb-3 ' style='background-color: #55BCD1;'>
            <div class='container-fluid'>
                <a class='navbar-brand' href='#'>
                    <img src='../images/logo.png' alt='Logo' width='25' height='25' />
                </a>
                <button class='navbar-toggler' type='button' data-bs-toggle='collapse'
                    data-bs-target='#navbarNavDropdown' aria-controls='navbarNavDropdown' aria-expanded='false'
                    aria-label='Toggle navigation'>
                    <span class='navbar-toggler-icon'></span>
                </button>

                <div class='collapse navbar-collapse justify-content-center' id='navbarNavDropdown'>
                    <ul class='navbar-nav '>
                        <li class='nav-item'>
                            <a class='nav-link " . ($activeLink === 'inicio' ? 'active' : '') . "' href='index.php'>Inicio</a>
                        </li>

                        <li class='nav-item'>
                            <a class='nav-link " . ($activeLink === 'productos' ? 'active' : '') . "' href='products.php'>Productos</a>
                        </li>

                        <li class='nav-item'>
                            <a class='nav-link " . ($activeLink === 'herramientas' ? 'active' : '') . "' href='tools.php'>Herramientas</a>
                        </li>

                        <li class='nav-item'>
                            <a class='nav-link " . ($activeLink === 'cotizaciones' ? 'active' : '') . "' href='prices.php'>Cotizaciones</a>
                        </li>

                        <li class='nav-item'>
                            <a class='nav-link " . ($activeLink === 'inventarios' ? 'active' : '') . "' href='inventories.php'>Inventarios</a>
                        </li>

                        <li>
                            <a href='../bd/logout.php' class='nav-link logout-icon'>
                                <i class='fas fa-sign-out-alt'></i></a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    ";
}
