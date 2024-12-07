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
use models\UserWorkerFormModel;

class UsersController extends Controller
{
    public static function user(Request $request) {
        // If we are not logged in, redirect to the login page
        if(!Application::isLoggedIn()){
            Response::redirect('login');
            return;
        }

        if($request->isPost()) {
            $user_form = new UserWorkerFormModel();
            $user_form->loadDataFromBody($request->getBody());

            
            if($user_form->validate()) {
                $user = new UserModel();

                $user->loadDataFromDb($user_form->id->value);
                $user_form->sendDataToSqlModel($user);

                $user->save();
                Response::redirect('/user?id=' . $user->id);
            }
            else {
                $params = [
                    'user' => new UserModel(),
                    'user_form' => $user_form,
                ];
                return parent::render('user', $params);
            }
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
            $user_form = new UserWorkerFormModel();
            $user_form->loadDataFromSqlModel($user);
            $params = [
                'user' => $user,
                'user_form' => $user_form,
            ];
            return parent::render('user', $params);
        }
    }

}