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
            $newItemTypeForm->fieldsToIgnore = ['id', 'status'];
            $params = [
                'newItemTypeForm' => $newItemTypeForm
            ];
            return parent::render('item_types', $params);
        }
        else { // POST
            $newItemTypeForm = new ItemTypeForm();
            $newItemTypeForm->fieldsToIgnore = ['id', 'status'];
            $newItemTypeForm->loadDataFromArray($request->getBody());

            if($newItemTypeForm->validate()){ 

                $newItemTypeForm->createNewItemType();

                Session::setFlashSuccess('Item added'); // Add a flash message
                Response::reload();
            }
            else { // If the data is not valid
                $params = [
                    'newItemTypeForm' => $newItemTypeForm
                ];
                return parent::render('item_types', ['newItemTypeForm' => $newItemTypeForm]);
            }
        }
    }

    public static function add_item_type(Request $request) 
    {

    }

}