<!-- Stylesheet -->
<link rel="stylesheet" href="<?php echo url('/resources/css/breadcrumb.css'); ?>">

<!-- Breadcrumb -->
<div class="container mt-2">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb" id="breadcrumb-container">
            <?php for($i = 0; $i < count($items); $i++): ?>
                <?php if($i === count($items) - 1): ?>
                    <li class="breadcrumb-item active"><?php echo htmlspecialchars($items[$i]['name']); ?></li>
                <?php else: ?>
                    <li class="breadcrumb-item"><a href="<?php echo htmlspecialchars($items[$i]['url']); ?>"><?php echo htmlspecialchars($items[$i]['name']); ?></a></li>
                <?php endif; ?>
            <?php endfor; ?>
        </ol>
    </nav>
    <hr class="breadcrumb-hr mt-2">
</div>

<!--  Templates that can be used to fill the breadcrumb with from JS  -->
<template id="breadcrumb-item">
    <li class="breadcrumb-item">
        <a href="#">Item name</a>
    </li>
</template>

<template id="breadcrumb-item-active">
    <li class="breadcrumb-item active">Item name</li>
</template>