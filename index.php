<?php
/**
 * index.php
 * Displays the member registration form.
 * If register.php sent us back with errors, they are shown beside each field
 * and the previously typed values are restored (sticky form).
 */
session_start();
require_once __DIR__ . '/includes/validation.php';
require_once __DIR__ . '/includes/helpers.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Registration</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<main class="container">
    <header class="page-header">
        <h1>Member Registration</h1>
        <p>Fill in every field using the format shown. All fields are required.</p>
    </header>

    <?php if (!empty($_SESSION['errors'])): ?>
        <div class="alert alert-error">
            Please correct the <?= count($_SESSION['errors']) ?> highlighted field(s) below.
        </div>
    <?php endif; ?>

    <!--
        The form has NO validation of its own: every rule is checked in PHP (register.php).
        enctype="multipart/form-data" is REQUIRED for file uploads.
    -->
    <form action="register.php" method="post" enctype="multipart/form-data" class="card form">

        <fieldset>
            <legend>Personal Information</legend>

            <div class="row">
                <div class="field">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="<?= invalid_class('first_name') ?>"
                           value="<?= old('first_name') ?>" placeholder="Juan">
                    <?= error_for('first_name') ?>
                </div>
                <div class="field">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="<?= invalid_class('last_name') ?>"
                           value="<?= old('last_name') ?>" placeholder="Dela Cruz">
                    <?= error_for('last_name') ?>
                </div>
            </div>

            <div class="row">
                <div class="field">
                    <label for="birthdate">Birthdate <span class="format">YYYY-MM-DD</span></label>
                    <input type="text" id="birthdate" name="birthdate" class="<?= invalid_class('birthdate') ?>"
                           value="<?= old('birthdate') ?>" placeholder="2004-08-15">
                    <?= error_for('birthdate') ?>
                </div>
                <div class="field">
                    <label for="zip_code">ZIP Code <span class="format">4 digits</span></label>
                    <input type="text" id="zip_code" name="zip_code" class="<?= invalid_class('zip_code') ?>"
                           value="<?= old('zip_code') ?>" placeholder="6000">
                    <?= error_for('zip_code') ?>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Contact Details</legend>

            <div class="row">
                <div class="field">
                    <label for="email">Email Address</label>
                    <input type="text" id="email" name="email" class="<?= invalid_class('email') ?>"
                           value="<?= old('email') ?>" placeholder="juan.delacruz@usjr.edu.ph">
                    <?= error_for('email') ?>
                </div>
                <div class="field">
                    <label for="mobile">Mobile Number <span class="format">09XXXXXXXXX</span></label>
                    <input type="text" id="mobile" name="mobile" class="<?= invalid_class('mobile') ?>"
                           value="<?= old('mobile') ?>" placeholder="09171234567">
                    <?= error_for('mobile') ?>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Account</legend>

            <div class="field">
                <label for="username">Username <span class="format">6-15 chars, starts with a letter</span></label>
                <input type="text" id="username" name="username" class="<?= invalid_class('username') ?>"
                       value="<?= old('username') ?>" placeholder="juan_dc2004">
                <?= error_for('username') ?>
            </div>

            <div class="row">
                <div class="field">
                    <label for="password">Password <span class="format">8+ chars, Aa1!</span></label>
                    <!-- Passwords are never written back into the form. -->
                    <input type="password" id="password" name="password" class="<?= invalid_class('password') ?>">
                    <?= error_for('password') ?>
                </div>
                <div class="field">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           class="<?= invalid_class('confirm_password') ?>">
                    <?= error_for('confirm_password') ?>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Membership Photo</legend>
            <div class="field">
                <label for="photo">Upload Photo <span class="format">JPG or PNG, max 2 MB</span></label>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?= PHOTO_MAX_BYTES ?>">
                <input type="file" id="photo" name="photo"
                       class="<?= invalid_class('photo') ?>">
                <?= error_for('photo') ?>
                <p class="help-text">For security, you must choose the photo again if the form has errors.</p>
            </div>
        </fieldset>

        <div class="actions">
            <button type="reset" class="btn btn-secondary">Clear</button>
            <button type="submit" class="btn btn-primary">Register</button>
        </div>
    </form>
</main>
</body>
</html>
<?php
// Errors and old input are shown once only.
unset($_SESSION['errors'], $_SESSION['old']);
