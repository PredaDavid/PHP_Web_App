<?php const USE_LAYOUT = 'layouts/login.php'; //Marks the use of a layout ?>
<?php use core\FormField; ?>

<h1>Register</h1>

<?php $model->generateForm(); ?>
<br>

<p>Already have an account? Go to <a href="/login">login page...</a></p>