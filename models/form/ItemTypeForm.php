<?php

namespace models\form;

use core\FormModel;
use core\Application;
use core\Controller;
use core\FormModelField;
use core\Request;
use core\Session;
use core\SqlModel;

use models\sql\ItemType;

class ItemTypeForm extends FormModel
{
    const DB_TABLE = 'item_type';
    const FORM_SUBMIT_VALUE = 'Save';
    const FILES_UPLOAD_PATH = 'images/uploads/items/';

    public FormModelField $id;
    public FormModelField $original_barcode;
    public FormModelField $name;
    public FormModelField $description;
    public FormModelField $rental_price;
    public FormModelField $replacement_price;
    public FormModelField $image;
    public FormModelField $image_name;
    public FormModelField $need_cleaning_after_use;
    public FormModelField $one_time_use;
    public FormModelField $status;

    public function __construct()
    {
        $this->id = new FormModelField(
            name: "id",
            model: $this,
            type: FormModelField::TYPE_NUMBER,
            label: "ID",
            rules: [self::RULE_READONLY]
        );
        $this->original_barcode = new FormModelField(
            name: "original_barcode",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Original Barcode',
            rules: [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 50]]
        );
        $this->name = new FormModelField(
            name: "name",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Name',
            rules: [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 100]]
        );
        $this->description = new FormModelField(
            name: "description",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Description',
            rules: [self::RULE_REQUIRED, [self::RULE_MAX, 'max' => 500]]
        );
        $this->rental_price = new FormModelField(
            name: "rental_price",
            model: $this,
            type: FormModelField::TYPE_NUMBER,
            label: 'Rental Price',
            rules: [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 0]]
        );
        $this->replacement_price = new FormModelField(
            name: "replacement_price",
            model: $this,
            type: FormModelField::TYPE_NUMBER,
            label: 'Replacement Price',
            rules: [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 0]]
        );
        $this->image = new FormModelField(
            name: "image",
            model: $this,
            type: FormModelField::TYPE_FILE,
            label: 'Image',
            rules: []
        );
        $this->image_name = new FormModelField(
            name: "image_name",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Image Name',
        );
        $this->need_cleaning_after_use = new FormModelField(
            name: "need_cleaning_after_use",
            model: $this,
            type: FormModelField::TYPE_CHECKBOX,
            label: 'Need Cleaning After Use',
            rules: []
        );
        $this->one_time_use = new FormModelField(
            name: "one_time_use",
            model: $this,
            type: FormModelField::TYPE_CHECKBOX,
            label: 'One Time Use',
            rules: []
        );
        $this->status = new FormModelField(
            name: "status",
            model: $this,
            type: FormModelField::TYPE_NUMBER,
            label: 'Status',
            rules: [self::RULE_READONLY]
        );

        $this->image_name->options = array_diff(scandir(self::FILES_UPLOAD_PATH), ['.', '..']);  
    }

    public function createNewItemType() 
    {
        $newItemType = new ItemType();

        $this->image->value = '';
        if(Request::wasFileSent('image')) { // If an image was sent
            $filename = $this->image_name->value; // Get the filename from the input field
            if(empty($filename)) { // If the filename is empty
                $filename = ItemType::getAutoIncrement() . '_' . $this->name->value; // Generate a filename
            }

            $errors = Request::saveFile('image', self::FILES_UPLOAD_PATH, $filename);
            $filename .= '.' . Request::getSendedFileExtension('image');

            if(!empty($errors)) { // If there are errors
                foreach($errors as $error) {
                    Session::setFlashError($error);
                }
                Session::setFlashInfo('Image was not saved.');
            }
            else {
                Session::setFlashInfo('Image was saved.');
                $this->image->value = $filename;
            }
        }
        else {
            if(in_array($this->image_name->value, $this->image_name->options)) {
                $this->image->value = $this->image_name->value;
            }
            else {
                $this->image->value = '';
                Session::setFlashWarning('Image was not found.');
            }
        }

        $this->loadDataToSqlModel($newItemType, ['id', 'status']);
        $newItemType->status = 1;
        $newItemType->save();

    }
}
