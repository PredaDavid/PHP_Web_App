<?php 
use core\FormField;
const USE_LAYOUT = 'layouts/login.php'; //Marks the use of a layout ?>


<h1>Login</h1>

<form action="" method="POST">
    <?php echo new FormField($model, 'email');?>
    <?php echo new FormField($model, 'password');?>
    <input type="submit" value="Login">
</form>

<br>
<p>Don't have an account?  <a href="register">Register now...</a></p>