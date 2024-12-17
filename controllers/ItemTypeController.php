<?php

namespace controllers;

use core\Session;
use core\Controller;
use core\Request;
use core\Response;

use models\form\ItemTypeForm;
use models\sql\ItemType;

class ItemTypeController extends Controller
{
    public static function item_types(Request $request)
    {
        if (!Controller::isUserLoggedIn()) {
            Response::redirect('login');
            return;
        }
        if (!Controller::isUserAdmin() && !Controller::isUserSupervisor()) {
            Response::redirect('/');
            return;
        }

        if($request->isGet()) {
            $newItemTypeForm = new ItemTypeForm();
            $params = [
                'newItemTypeForm' => $newItemTypeForm
            ];
            return parent::render('item_types', $params);
        }
        else { // POST
            $newItemTypeForm = new ItemTypeForm();
            $newItemTypeForm->loadDataFromArray($request->getBody());

            if($newItemTypeForm->validate()){ 

                $newItemType = new ItemType();
                $newItemTypeForm->loadDataToSqlModel($newItemType);

                Session::setFlash('success', 'You have successfully registered'); // Add a flash message
                Response::redirect('/'); // Redirect to the home page
            }
            else { // If the data is not valid
                // return parent::render('register', ['model' => $registerModel]);
                die('Data is not valid');
            }
        }
    }

    public static function add_item_type(Request $request) 
    {

    }

}