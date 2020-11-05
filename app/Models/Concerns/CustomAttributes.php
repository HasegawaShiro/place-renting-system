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

    protected $fail = false;
    public function fails() {$this->fail = true;}
    public function isFailed() {return $this->fail;}

    protected $error = null;
    public function setError($data) {$this->error = $data;}
    public function getError() {return $this->error;}
}
