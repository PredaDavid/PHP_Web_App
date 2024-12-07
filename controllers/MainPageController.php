<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Request;
use core\Response;
use core\SqlModel;
use models\UserModel;

// controller classes and extend them from the Controller class
class MainPageController extends Controller
{
    public static function home()
    {

        if(!Application::isLoggedIn()) 
            Response::redirect('/login');

        // $users = array_map(function($email){
        //     return explode('@', $email)[0];
        // }, $users);

        $params = [
        ];

        return parent::render('home', $params);
    }

}