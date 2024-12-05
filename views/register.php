<?php const USE_LAYOUT = 'layouts/login.php'; //Marks the use of a layout ?>


<h1>Register</h1>

<?php
use core\FormField;

echo '<form action="" method="POST">';
    echo new FormField($model, 'first_name');
    echo new FormField($model, 'last_name');
    echo new FormField($model, 'email');
    echo new FormField($model, 'phone_number');
    echo new FormField($model, 'password');
    echo new FormField($model, 'password_confirm');
    echo '<input type="submit" value="Login">';
echo '</form>';
?>
<br>

<p>Already have an account? Go to <a href="/login">login page...</a></p>