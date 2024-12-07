<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Request;
use core\Response;
use models\UserRegisterModel;
use models\UserLoginModel;
use models\UserModel;

// The AuthController supports login, register and logout
class AuthController extends Controller
{
    public static function login(Request $request)
    {
        // If we are already logged in, redirect to the home page
        if(Application::isLoggedIn()){
            Response::redirect('/');
            return;
        }

        $loginModel = new UserLoginModel();
        if($request->isGet()){ // If the request is a GET request
            // Render the login view; We still need to pass the model to the view so that we can display the form
            return parent::render('login', ['model' => $loginModel]); 
        }
        else { // If the request is a POST request
            $loginModel->loadDataFromBody($request->getBody()); // Load data into the model

            if($loginModel->validate()){ // If the data is valid
                $user_id = $loginModel->login(); // Try to login
                if($user_id){ // If login was successful
                    $_SESSION['user'] = $user_id; // Set the user id in the session
                    Application::current()->session->setFlash('success', 'You have successfully login'); // Add a flash message
                    Response::redirect('/');
                    return;
                }
            }

            // If the data is not valid or the login was not successful
            return parent::render('login', ['model' => $loginModel]); // Render the login view with the model 
        }
    }


    public static function register(Request $request)
    {
        // If we are already logged in, redirect to the home page
        if(Application::isLoggedIn()){
            Response::redirect('/');
            return;
        }

        $registerModel = new UserRegisterModel(); // Create a new instance of the model
        if($request->isPost()){ // If the request is a POST request
            $registerModel->loadDataFromBody($request->getBody()); // Load data into the model
            
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

    public static function logout()
    {
        if(isset(Application::current()->user)){ // If the user is logged in
            unset($_SESSION['user']); // Remove the user from the session
            Application::current()->session->setFlash('success', 'You logout'); // Add a flash message
        }
        Response::redirect('/'); // Redirect to the home page
    }
}