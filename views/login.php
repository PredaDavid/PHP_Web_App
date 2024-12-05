<?php const USE_LAYOUT = 'layouts/login.php'; //Marks the use of a layout ?>


<h1>Login</h1>

<?php
use core\FormField;

echo '<form action="" method="POST">';
    echo new FormField($model, 'email');
    echo new FormField($model, 'password');
    echo '<input type="submit" value="Login">';
echo '</form>';
?>

<br>
<p>Don't have an account?  <a href="register">Register now...</a></p>