<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Request;
use core\Response;
use core\SqlModel;
use models\UserRegisterModel;
use models\UserLoginModel;
use models\UserModel;

class UsersController extends Controller
{
    public static function user(Request $request) {
        // If we are not logged in, redirect to the login page
        if(!Application::isLoggedIn()){
            Response::redirect('login');
            return;
        }

        if($request->isPost()) {

        }
        else {
            $user = new UserModel();
            
            if(isset($request->getBody()['id'])) {
                if(!Application::current()->user->admin) { // If the user is not an admin
                    Response::redirect('/'); // Redirect to the home page
                    return;
                }
                // If the id is set return the user with the id
                $user->loadDataFromDb($request->getBody()['id']);
                if($user->id === SqlModel::INT_DEFAULT_VALUE) { // If the user was not found
                    Response::redirect('/'); // Redirect to the home page
                    return;
                }
            }            
            else {
                // If the id is not set return the current user
                $user->loadDataFromDb(Application::current()->user->id);
            }
            $params = [
                'user' => $user
            ];
            return parent::render('user', $params);
        }
    }

}