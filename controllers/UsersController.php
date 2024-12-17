<?php

namespace controllers;

use core\Application;
use core\Controller;
use core\Request;
use core\Response;
use core\Session;
use core\SqlModel;

use models\sql\User;
use models\form\UserForm;
use models\form\UserChangePasswordForm;

class UsersController extends Controller
{
    public static function user(Request $request) {
        // If we are not logged in, redirect to the login page
        if(!Controller::isUserLoggedIn()){
            Response::redirect('login');
            return;
        }

        if($request->isGet()) {
            $user = new User();
            $change_password_form = null;
            
            if(isset($request->getBody()['id'])) {
                if(!Controller::isUserAdmin()) { // If the user is not an admin
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
                $change_password_form = new UserChangePasswordForm();
            }
            $user_form = new UserForm();
            $user_form->loadDataFromSqlModel($user);
            $params = [
                'user_form' => $user_form,
                'change_password_form' => $change_password_form,
            ];
            return parent::render('user', $params);

        }
        else { // POST
            $change_password_form = new UserChangePasswordForm();
            // We check which submit button was pressed
            if( in_array(UserForm::FORM_SUBMIT_VALUE, $request->getBody()) ) {
                $user_form = new UserForm();
                $user_form->loadDataFromArray($request->getBody());

                // Update user values
                if($user_form->validate()) {
                    $user = new User();
    
                    $user->loadDataFromDb($user_form->id->value);
                    $user_form->loadDataToSqlModel($user);
                    $user->save();

                    Session::setFlash('success', 'User updated successfully');
                    Response::redirect($request->getUrl());
                }
                else {
                    $params = [
                        'user_form' => $user_form,
                        'change_password_form' => $change_password_form,
                    ];
                    return parent::render('user', $params);
                }
            }
            else if( in_array(UserChangePasswordForm::FORM_SUBMIT_VALUE, $request->getBody()) ) {
                // Change password
                $change_password_form = new UserChangePasswordForm();
                $change_password_form->loadDataFromArray($request->getBody());
                if($change_password_form->validate() && $change_password_form->changePassword()) {
                    Session::setFlash('success', 'Password changed successfully');
                    Response::redirect('/');
                }
                else {
                    $user = new User();
                    $user->loadDataFromDb(Application::current()->user->id);
                    $user_form = new UserForm();
                    $user_form->loadDataFromSqlModel($user);
                    $params = [
                        'user_form' => $user_form,
                        'change_password_form' => $change_password_form,
                    ];
                    return parent::render('user', $params);
                }
            }
            else if( in_array('Delete User', $request->getBody()) ) {
                // Delete user
                $user = new User();
                $user->loadDataFromDb($request->getBody()['id']);
                $user->delete();
                Session::setFlash('success', 'User deleted successfully');
                Response::reload();
            }
            else if (in_array('Reset Password', $request->getBody())) {
                // Reset password
                $user = new User();
                $user->loadDataFromDb($request->getBody()['id']);
                $user->password = password_hash($user->phone_number, PASSWORD_DEFAULT);
                $user->save();
                Session::setFlash('success', 'Password reset successfully');
                Response::reload();
            }
            else if (in_array('Reactivate User', $request->getBody())) {
                // Reset password
                $user = new User();
                $user->loadDataFromDb($request->getBody()['id']);
                $user->status = 1;
                $user->save();
                Session::setFlash('success', 'User reactivated successfully');
                Response::reload();
            }
        }
    }

}