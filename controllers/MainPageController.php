<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Response;

// controller classes and extend them from the Controller class
class MainPageController extends Controller
{
    public static function home()
    {

        if(!Controller::isUserLoggedIn()) 
            Response::redirect('/login');

        $params = [
        ];

        return parent::render('home', $params);
    }

}