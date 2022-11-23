<!-- Stylesheet links are body-ok see: https://html.spec.whatwg.org/multipage/links.html#body-ok -->
<link rel="stylesheet" href="<?php echo url('/resources/css/navbar.css'); ?>">

<!-- Navbar -->
<nav class="navbar navbar-light navbar-expand-md sticky-top navbar-shrink py-3" id="mainNav">
    <div class="container">
        <a class="navbar-brand" href="<?php echo url('/') ?>">L-shop</a>
        <form class="d-flex search-bar" method="get" id="search-form" action="<?php echo url('/product') ?>">
            <input class="form-control me-2 input-group-lg" type="search" name="search" id="search" placeholder="Search..." aria-label="Search">
            <button class="search-icon btn btn-outline-secondary border-start-0 border-bottom-0 border" type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>
        <?php if (auth()->check()): ?>
            <div class="d-flex">
                    <div class="nav-item dropdown me-4">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo auth()->user()->getAttribute('first_name') ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
                            <li><a class="dropdown-item" href="<?php echo url('/order'); ?>">Order history</a></li>
                            <li><a class="dropdown-item" href="<?php echo url('/logout'); ?>">Logout</a></li>
                        </ul>
                    </div>
                <a href="<?php echo url('/cart'); ?>"><i class="cart-icon fa-solid fa-cart-shopping fa-xl align-middle"></i></a>
            </div>
        <?php else: ?>
            <a class="btn btn-primary shadow" role="button" href="<?php echo url('/login'); ?>">Sign in</a>
        <?php endif; ?>
    </div>
</nav>