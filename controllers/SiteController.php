<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Request;
use core\Response;
use core\SqlModel;
use models\UserModel;

// controller classes and extend them from the Controller class
class SiteController extends Controller
{
    public static function home()
    {

        if(!Application::isLoggedIn()) 
            Response::redirect('/login');

        $params = [
            'user' => UserModel::getById($_SESSION['user']),
        ];

        return parent::render('home', $params);
    }

}