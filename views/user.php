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
            <?php $user_form->generateForm(); ?>
        </div>
        
        <div class="container">
            <h2>Lastest items in inventory</h2>
        </div>
</div>



