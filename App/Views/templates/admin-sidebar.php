<link rel="stylesheet" href="<?php echo url('/resources/css/sidebar.css'); ?>">

<nav class="flex-fill sidebar">
    <a href="<?php echo url('/admin'); ?>" class="sidebar-link"><i class="fa-solid fa-chart-line icon"></i> Dashboard</a>
    <hr class="delimiter">
    <a href="<?php echo url('/admin/user'); ?>" class="sidebar-link"><i class="fa-solid fa-user icon"></i> Users</a>
    <a href="<?php echo url('/admin/order'); ?>" class="sidebar-link"><i class="fa-solid fa-basket-shopping icon"></i> Orders</a>
    <a href="<?php echo url('/admin/product'); ?>" class="sidebar-link"><i class="fa-solid fa-barcode icon"></i> Products</a>
</nav>