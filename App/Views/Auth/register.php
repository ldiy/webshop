<!DOCTYPE html>
<html lang="en">
<!-- HEAD -->
<?php template('head', ['title' => 'Register']); ?>
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
                <div class="col-md-6 col-xl-6">
                    <div class="card">
                        <div class="card-header py-3 text-center">
                            <p class="fw-bold text-primary mb-2">Register</p>
                            <h2 class="fw-bold">Welcome</h2>
                        </div>
                        <div class="card-body d-flex flex-column align-items-center">
                            <div class="round-icon my-4">
                                <i class="fa-regular fa-user fa-2xl"></i>
                            </div>
                            <form class="needs-validation" method="post" action="<?php echo url('/register'); ?>" novalidate>

                                <!-- First and lastname -->
                                <div class="row mb-3">
                                    <div class="col">
                                        <div class="form-floating">
                                            <input class="form-control <?php if(isset($errors['first_name'])) echo 'is-invalid';?>" type="text" name="first_name" id="first_name" placeholder="First Name" value="<?php echo old('first_name'); ?>" minlength="2" maxlength="45" required>
                                            <label class="form-label" for="first_name">First name</label>
                                            <div class="invalid-feedback">
                                                Enter your first name
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="form-floating">
                                            <input class="form-control <?php if(isset($errors['last_name'])) echo 'is-invalid';?>" type="text" name="last_name" id="last_name" placeholder="Last Name" value="<?php echo old('last_name'); ?>" minlength="2" maxlength="45" required>
                                            <label class="form-label" for="last_name">Last name</label>
                                            <div class="invalid-feedback">
                                                Enter your last name
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email address -->
                                <div class="mb-3 form-floating">
                                    <input class="form-control <?php if(isset($errors['email'])) echo 'is-invalid';?>" type="email" name="email" id="email" placeholder="Email" <?php if(isset($errors['email'])) echo 'oninput="removeInvalid(this)"'; ?> value="<?php echo old('email'); ?>" maxlength="319" required>
                                    <label class="form-label" for="email">Email address</label>
                                    <div class="invalid-feedback">
                                        <?php
                                        if(isset($errors['email']))
                                            echo $errors['email'];
                                        else
                                            echo 'Enter a valid email address';
                                        ?>
                                    </div>
                                </div>

                                <!-- Password -->
                                <div class="mb-3 form-floating">
                                    <input class="form-control <?php if(isset($errors['password'])) echo 'is-invalid';?>" type="password" name="password" id="password" oninput="checkPasswordsMatch()" placeholder="Password" value="<?php echo old('password'); ?>" minlength="8" required>
                                    <label for="password">Password</label>
                                    <div class="invalid-feedback">
                                        Password must contain at least 8 characters.
                                    </div>
                                </div>

                                <!-- Password confirm -->
                                <div class="mb-3 form-floating">
                                    <input class="form-control" type="password" name="password_confirm" id="password_confirm" oninput="checkPasswordsMatch()" placeholder="Password confirmation" value="<?php echo old('password'); ?>" required>
                                    <label for="password_confirm">Password confirmation</label>
                                    <div class="invalid-feedback">
                                        Passwords must match.
                                    </div>
                                </div>

                                <!-- Terms and conditions -->
                                <div class="mb-3 form-check">
                                    <input class="form-check-input <?php if(isset($errors['agreed'])) echo 'is-invalid';?>" type="checkbox" name="agreed" id="agreed" required>
                                    <label class="form-check-label" for="agreed">
                                        Agree to <a href="<?php echo url('/resources/terms.html'); ?>">terms and conditions</a>
                                    </label>
                                    <div class="invalid-feedback">
                                        You must agree before submitting.
                                    </div>
                                </div>

                                <!-- Submit button -->
                                <div class="mb-3">
                                    <button class="btn btn-primary shadow d-block w-100" type="submit">Register</button>
                                </div>

                                <div class="text-center">
                                    <a href="<?php echo url('/login'); ?>" class="text-muted">Login instead</a>
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
<script>
    function checkPasswordsMatch() {
        console.log('checkPasswordsMatch()');
        let password = document.getElementById('password');
        let passwordConfirm = document.getElementById('password_confirm');

        if (passwordConfirm.value !== '' && password.value !== passwordConfirm.value) {
            passwordConfirm.setCustomValidity('Passwords must match.');
        } else {
            passwordConfirm.setCustomValidity('');
        }
    }

    function removeInvalid(field) {
        field.classList.remove('is-invalid');
    }
</script>

</body>
</html>