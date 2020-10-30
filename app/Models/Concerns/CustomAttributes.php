<?php
namespace App\Models\Concerns;

trait CustomAttributes
{
    protected $editable = [];

    public function getEditable() {
        return $this->editable;
    }

    public function isEditable(String $attribute) {
        return empty($this->editable) || array_search($attribute, $this->editable) !== false;
    }
}
