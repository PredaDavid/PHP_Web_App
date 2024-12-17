<?php

namespace models\form;

use core\FormModel;
use core\Application;
use core\FormModelField;
use core\SqlModel;

use models\sql\User;
use models\sql\Worker;

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
            rules: [self::RULE_REQUIRED]
        );
        $this->name = new FormModelField(
            name: "name",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Name',
            rules: [self::RULE_REQUIRED]
        );
        $this->description = new FormModelField(
            name: "description",
            model: $this,
            type: FormModelField::TYPE_TEXT,
            label: 'Description',
            rules: [self::RULE_REQUIRED]
        );
        $this->rental_price = new FormModelField(
            name: "rental_price",
            model: $this,
            type: FormModelField::TYPE_NUMBER,
            label: 'Rental Price',
            rules: [self::RULE_REQUIRED]
        );
        $this->replacement_price = new FormModelField(
            name: "replacement_price",
            model: $this,
            type: FormModelField::TYPE_NUMBER,
            label: 'Replacement Price',
            rules: [self::RULE_REQUIRED]
        );
        $this->image = new FormModelField(
            name: "image",
            model: $this,
            type: FormModelField::TYPE_FILE,
            label: 'Image',
            rules: []
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
    }
}
