<!DOCTYPE html>
<html lang="en">
<?php template('head', ['title' => 'Log in']); ?>
<body>

<!-- Stylesheets -->
<link rel="stylesheet" href="<?php echo url('/resources/css/login.css'); ?>">

<div class="d-flex flex-column min-vh-100">

    <!--  Header  -->
    <?php template('header'); ?>

    <!--  Main  -->
    <main class="flex-fill">

    </main>

    <!--  Footer  -->
    <?php template('footer'); ?>
</div>

<!-- Scripts -->
<?php template('bottomScripts'); ?>
<script src="<?php echo url('/resources/js/form-validation.js'); ?>"></script>

</body>
</html>