<?php 

const USE_LAYOUT = 'layouts/main.php'; //Marks the use of a layout

use core\Application;
use core\FormField;

?>


<div class="user_container">
    <div class="container">
            <?php echo $user->email; ?>
        </div>
        
        <div class="container main_grid_2">
            <h2>Details</h2>
            <form action="" method="POST">
                <?php echo new FormField($model, 'id');?>
                <?php echo new FormField($model, 'email');?>
                <?php echo new FormField($model, 'password');?>
                <?php echo new FormField($model, 'phone_number');?>
                <?php echo new FormField($model, 'password');?>
                <?php echo new FormField($model, 'password_confirm');?>
                <input type="submit" value="Login">
            </form>
        </div>
        
        <div class="container">
            <h2>Lastest items in inventory</h2>
        </div>
</div>



