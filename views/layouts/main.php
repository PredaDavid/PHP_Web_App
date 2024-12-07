<?php

use core\Application;
use models\UserModel;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/styles/styles.css">
    <title>Wedding Company</title>
</head>
<body>

    <nav class="flex_row" id="nav">
        <div class="nav_left_container flex_row">
           <button onclick="sidebarToggle()">
                <img src="/images/icons/icon_menu_sidebar.svg" alt="">
           </button>
           <div class="search_container">
                <input type="text" placeholder="Search...">
                <img src="/images/icons/icon_search.svg" alt="">
           </div>
        </div>

        <div class="nav_right_container">
            <a href="">Find new transport</a>
            <a href="">Hello </a>
            <a href="/">Home</a>
            <button>
                <img src="/images/icons/icon_settings.svg" alt="">
            </button>
            <button onclick="toggleByOpacity('notifications_container');toggleActiveClass(this)">
                <img src="/images/icons/icon_notification.svg" alt="">
            </button>
            <button class="nav_profile" onclick="toggleByOpacity('account_container');">
                <img src="/images/placeholders/user_placeholder.svg" alt="">
            </button>

            <div id="notifications_container" class="notifications_container flex_column opacity_0">
                <h4>Notifications</h4>
                <div class="notification flex_row" >
                    <img src="/images/placeholders/user_placeholder.svg" alt="">
                    <div class="flex_column">
                        <p><strong>Notification 1</strong></p>
                        <p>Lorem ipsum dolor sit  fuga repellendus. Praesentium impedit necessitatibus quo</p>
                    </div>
                </div>
                <div class="notification flex_row">
                    <img src="/images/placeholders/user_placeholder.svg" alt="">
                    <div class="flex_column">
                        <p><strong>Notification 1</strong></p>
                        <p>Lorem ipsum dolor sit  fuga repellendus. Praesentium impedit necessitatibus quo</p>
                    </div>
                </div>
                <div class="notification flex_row">
                    <img src="/images/placeholders/user_placeholder.svg" alt="">
                    <div class="flex_column">
                        <p><strong>Notification 1</strong></p>
                        <p>Lorem ipsum dolor sit  fuga repellendus. Praesentium impedit necessitatibus quo</p>
                    </div>
                </div>
                <div class="notification flex_row">
                    <img src="/images/placeholders/user_placeholder.svg" alt="">
                    <div class="flex_column">
                        <p><strong>Notification 1</strong></p>
                        <p>Lorem ipsum dolor sit  fuga repellendus. Praesentium impedit necessitatibus quo</p>
                    </div>
                </div>
            </div>

            <div id="account_container" class="account_container flex_column opacity_0">
                <h2>Hello <?php echo Application::current()->user->first_name ?>!</h2>

                <div class="flex_row">
                    <?php if(Application::isLoggedIn()): ?>
                        <!-- <a href="/logout"><h2>Logout</h2></a> -->
                        <a href="logout">Logout</a>
                    <?php else: ?>
                        <a href="login">Login</a>
                        <a href="register">Register</a>
                    <?php endif; ?>
                </div>
                
            </div>

        </div>
    </nav>

    <div class="sidebar" id="sidebar" toggled="true">
        <img class="logo" src="/images/logo.png" alt="">


        <div class="sidebar_buttons_container flex_column">
            
            <button class="sidebar_button flex_row active">
                <img src="/images/icons/icon_clip.svg"> 
                <div class="sidebar_button_text">Dashboard</div>  
            </button>

            <button class="sidebar_button flex_row" onclick="sidebarDropdown(this)">
                <img src="/images/icons/icon_clip.svg"> 
                <div class="sidebar_button_text">Events</div>  
                <img src="/images/icons/icon_arrow_down.svg">
            </button>
            <div class="sidebar_dropdown flex_column sidebar_buttons_container ">
                <button class="sidebar_button flex_row sidebar_dropdown_element">
                    <img src="/images/icons/icon_list.svg"> 
                    <div class="sidebar_button_text">Button 1</div>  
                </button>
            </div>

            <?php if(Application::current()->user->worker or Application::current()->user->admin): ?>
                <button class="sidebar_button flex_row" onclick="sidebarDropdown(this)">
                    <img src="/images/icons/icon_clip.svg"> 
                    <div class="sidebar_button_text">Users</div>  
                    <img src="/images/icons/icon_arrow_down.svg">
                </button>
                <div class="sidebar_dropdown flex_column sidebar_buttons_container ">
                    <?php $users = UserModel::getColumnWithId('email'); ?>
                    <?php foreach($users as $user): ?>
                        <button class="sidebar_button flex_row sidebar_dropdown_element" onclick='goToLink(" <?php echo "/user?id=".$user["id"].""  ?> ")';>
                            <img src="/images/icons/icon_list.svg"> 
                            <div class="sidebar_button_text"><?php echo explode('@', $user['email'])[0] ?></div>  
                        </button>
                    <?php endforeach; ?>
                </div>

                <button class="sidebar_button flex_row" onclick="sidebarDropdown(this)">
                    <img src="/images/icons/icon_clip.svg"> 
                    <div class="sidebar_button_text">Inventory</div>  
                    <img src="/images/icons/icon_arrow_down.svg">
                </button>
                <div class="sidebar_dropdown flex_column sidebar_buttons_container ">
                    <button class="sidebar_button flex_row sidebar_dropdown_element">
                        <img src="/images/icons/icon_list.svg"> 
                        <div class="sidebar_button_text">Storage 1</div>  
                    </button>
                    <button class="sidebar_button flex_row sidebar_dropdown_element">
                        <img src="/images/icons/icon_list.svg"> 
                        <div class="sidebar_button_text">Storage 2</div>  
                    </button>
                    <button class="sidebar_button flex_row sidebar_dropdown_element">
                        <img src="/images/icons/icon_list.svg"> 
                        <div class="sidebar_button_text">Storage 3</div>  
                    </button>
                    <button class="sidebar_button flex_row sidebar_dropdown_element">
                        <img src="/images/icons/icon_list.svg"> 
                        <div class="sidebar_button_text">Add new item</div>  
                    </button>
                </div>

                <button class="sidebar_button flex_row">
                    <img src="/images/icons/icon_clip.svg"> 
                    <div class="sidebar_button_text">Organize new event</div>  
                </button>

                <button class="sidebar_button flex_row" onclick="sidebarDropdown(this)">
                    <img src="/images/icons/icon_clip.svg"> 
                    <div class="sidebar_button_text">Items and Services</div>  
                    <img src="/images/icons/icon_arrow_down.svg">
                </button>
                <div class="sidebar_dropdown flex_column sidebar_buttons_container ">
                    <button class="sidebar_button flex_row sidebar_dropdown_element" onclick="goToLink('/item-types')">
                        <img src="/images/icons/icon_list.svg"> 
                        <div class="sidebar_button_text">Item Types</div>  
                    </button>
                    <button class="sidebar_button flex_row sidebar_dropdown_element" onclick="goToLink('/services')">
                        <img src="/images/icons/icon_list.svg"> 
                        <div class="sidebar_button_text">Services</div>  
                    </button>
                    <button class="sidebar_button flex_row sidebar_dropdown_element" onclick="goToLink('/add-item-type')">
                        <img src="/images/icons/icon_list.svg"> 
                        <div class="sidebar_button_text">Add new item type</div>  
                    </button>
                    <button class="sidebar_button flex_row sidebar_dropdown_element" onclick="goToLink('/add-service')">
                        <img src="/images/icons/icon_list.svg"> 
                        <div class="sidebar_button_text">Add new services</div>  
                    </button>
                </div>
            <?php endif ?>
        </div>



        <p>Portfolio Project</p>
        <p>© 2024 Preda Ștefan David</p>
    </div>

    
    
    <div class="main_container" id="main">
        <?php echo $content; //A valid layout needs to ouput the variable content ?>
    </div>



    <div class="flash_messages_container flex_column">
        <?php foreach ($_SESSION['flash_messages'] as $message):?>
            <div class="flash_message" >
                <p><?php echo $message ?></p>
                <div class="flash_message_line"></div>
            </div>
        <?php endforeach; ?>
    </div>

</body>
</html>
<script src="/scripts/script.js"></script>
