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
        <div class="container py-5">
            <div class="row d-flex justify-content-center">
                <div class="col-md-6 col-xl-4">
                    <div class="card">
                        <div class="card-header py-3 text-center">
                            <p class="fw-bold text-primary mb-2">Login</p>
                            <h2 class="fw-bold">Welcome back</h2>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="round-icon my-4">
                                <i class="fa-regular fa-user fa-2xl"></i>
                            </div>
                            <form class="needs-validation" method="post" action="<?php echo url('/login'); ?>">
                                <?php
                                if (isset($errors['email']) || isset($errors['password'])): ?>
                                    <div class="alert alert-danger" role="alert">
                                        Email or password is incorrect.
                                    </div>
                                <?php endif; ?>

                                <!-- Email -->
                                <div class="mb-3 form-floating">
                                    <input class="form-control" type="text" name="email" id="email" placeholder="Email" value="<?php echo old('email'); ?>" maxlength="319" required>
                                    <label class="form-label" for="email">Email address</label>
                                    <div class="invalid-feedback">
                                        Enter a valid email address
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-3 form-floating">
                                    <input class="form-control" type="password" name="password" id="password" placeholder="Password" required>
                                    <label for="password">Password</label>
                                </div>

                                <!-- Submit -->
                                <div class="mb-3">
                                    <button class="btn btn-primary shadow d-block w-100" type="submit">Log in</button>
                                </div>

                                <div class="text-center">
                                    <a href="<?php echo url('/register'); ?>" class="text-muted">Register instead</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!--  Footer  -->
    <?php template('footer'); ?>
</div>

<!-- Scripts -->
<?php template('bottomScripts'); ?>
<script src="<?php echo url('/resources/js/form-validation.js'); ?>"></script>

</body>
</html>