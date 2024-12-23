<?php 

const USE_LAYOUT = 'layouts/main.php'; //Marks the use of a layout

use core\Controller;
?>


<div class="user_container flex_row">
        
    <div class="container">
        <h2>User Details</h2>
        <?php $user_form->generateForm(); ?>
    </div>
    
    <?php if( Controller::isUserAdmin() and is_null($change_password_form) ):?>
        <div class="container user_actions">
            <h2>User Actions</h2>
            <form action="" method="POST" class="flex_column">
                <!-- For storing the id -->
                <input type="text" name="id" hidden value='<?php echo $user_form->id->value ?>'> 
                <?php if($user_form->status->value==1): ?>
                    <p>- By deleting the user his status will be set to 0. </p>
                    <input type="submit" value="Delete User" name="delete_user">
                <?php else: ?>
                    <p>- Set the status to 1 but the worker status will still be 0.</p>
                    <input type="submit" value="Reactivate User" name="reactivate_user">
                <?php endif; ?>
                <p>- Only use if the user can't remember password. The password will be reset to user's phone number. 
                    Then the user can login and reset his password.</p>
                <input type="submit" value="Reset Password" name="reset_password">
            </form>
        </div>
    <?php endif; ?>

    <?php if(!is_null($change_password_form)): ?>
        <div class="container user_change_password">
            <h2>Change Password</h2>
            <?php $change_password_form->generateForm() ?>
        </div>
    <?php endif; ?>

</div>



