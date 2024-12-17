<?php 

const USE_LAYOUT = 'layouts/main.php'; //Marks the use of a layout

?>


<div class="home_container item_types_container">
        <div class="container">
            <h2>Add new item type</h2>
            <?php $newItemTypeForm->generateForm(); ?>
        </div>
        
        <div class="container" style="grid-column: span 2;">
            <h2>Low inventory</h2>
        </div>
        
        <div class="container" style="grid-column: span 3;">
            <h2>Items</h2>
        </div>


</div>



