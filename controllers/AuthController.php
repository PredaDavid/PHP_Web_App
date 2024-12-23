<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Request;
use core\Response;
use core\Session;

use models\form\UserRegisterForm;
use models\form\UserLoginForm;
use models\sql\User;

// The AuthController supports login, register and logout
class AuthController extends Controller
{
    public static function login(Request $request)
    {
        // If we are already logged in, redirect to the home page
        if(Controller::isUserLoggedIn()){
            Response::redirect('/');
            return;
        }

        $loginModel = new UserLoginForm();
        if($request->isGet()){ // If the request is a GET request
            // Render the login view; We still need to pass the model to the view so that we can display the form
            return parent::render('login', ['model' => $loginModel]); 
        }
        else { // If the request is a POST request
            $loginModel->loadDataFromArray($request->getBody()); // Load data into the model

            if($loginModel->validate()){ // If the data is valid
                $user_id = $loginModel->login(); // Try to login
                if($user_id){ // If login was successful
                    Session::set('user_id', $user_id); // Set the user id in the session
                    Session::setFlashSuccess('You have successfully login'); // Add a flash message
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
        if(Controller::isUserLoggedIn()){
            Response::redirect('/');
            return;
        }

        $registerModel = new UserRegisterForm(); // Create a new instance of the model
        if($request->isPost()){ // If the request is a POST request
            $registerModel->loadDataFromArray($request->getBody()); // Load data into the model
            
            if($registerModel->validate()){ // If the data is valid
                $registerModel->register(); // Register the user - insert the data into the database

                // Login the user
                $user = User::getByWhere(' email = ?', [$registerModel->email->value])[0];
                Session::set('user_id', $user->id); // Set the user id in the session

                Session::setFlashSuccess('You have successfully registered'); // Add a flash message
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
            Session::remove('user_id'); // Remove the user id from the session 
            Session::setFlashSuccess('You logout'); // Add a flash message
        }
        Response::redirect('/'); // Redirect to the home page
    }
}