<?php

namespace models\sql;

use core\SqlModel;

class ItemType extends SqlModel
{

    const TABLE_NAME = 'item_type';
    const EXTRA_ATTRIBUTES = ['worker'];

    // Columns in table
    public int $id;
    public string $original_barcode;
    public string $name;
    public string $description;
    public float $rental_price;
    public float $replacement_price;
    public string $image;
    public bool $need_cleaning_after_use;
    public bool $one_time_use;
    public int $status;

    public function delete()
    {
        $this->status = 0;
        $this->save();
    }
}
