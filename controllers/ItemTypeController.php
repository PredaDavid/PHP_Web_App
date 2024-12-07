<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Request;
use core\Response;
use models\UserRegisterModel;
use models\UserLoginModel;
use models\UserModel;

class ItemTypeController extends Controller
{
    public static function register(Request $request)
    {
        // If we are already logged in, redirect to the home page
        if(!Application::isLoggedIn()){
            Response::redirect('login');
            return;
        }
        // else if

        $registerModel = new UserRegisterModel(); // Create a new instance of the model
        if($request->isPost()){ // If the request is a POST request
            $registerModel->loadData($request->getBody()); // Load data into the model
            
            if($registerModel->validate()){ // If the data is valid
                $registerModel->register(); // Register the user - insert the data into the database

                // Login the user
                $user = UserModel::getByWhere(' email = ?', [$registerModel->email->value])[0];
                $_SESSION['user'] = $user->id; // Set the user id in the session

                Application::current()->session->setFlash('success', 'You have successfully registered'); // Add a flash message
                Response::redirect('/'); // Redirect to the home page
            }
            else { // If the data is not valid
                return parent::render('register', ['model' => $registerModel]);
            }
        }
        else { // If the request is GET 
            return parent::render('register', ['model' => $registerModel]); 
        }
    }

    public static function add_item_type(Request $request) {

    }

}