<?php const USE_LAYOUT = 'layouts/login.php'; //Marks the use of a layout ?>
<?php use core\FormField; ?>

<h1>Register</h1>

<form action="" method="POST">
    <?php echo new FormField($model, 'first_name');?>
    <?php echo new FormField($model, 'last_name');?>
    <?php echo new FormField($model, 'email');?>
    <?php echo new FormField($model, 'phone_number');?>
    <?php echo new FormField($model, 'password');?>
    <?php echo new FormField($model, 'password_confirm');?>
    <input type="submit" value="Login">
</form>
<br>

<p>Already have an account? Go to <a href="/login">login page...</a></p>