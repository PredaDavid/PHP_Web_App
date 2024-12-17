<?php 
const USE_LAYOUT = 'layouts/login.php'; //Marks the use of a layout ?>

<h1>Login</h1>

<?php $model->generateForm(); ?>

<br>
<p>Don't have an account?  <a href="register">Register now...</a></p>