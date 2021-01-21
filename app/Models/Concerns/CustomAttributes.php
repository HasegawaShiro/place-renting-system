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

    protected static $hasBody = false;
    public static function hasBody() {
        return self::$hasBody;
    }
    public static function getBodyModel() {
        $result = null;
        $class = "App\\Models\\".self::getModelName()."_body";

        if(self::$hasBody && class_exists($class)) {
            $result = new $class();
        }

        return $result;
    }

    public static function getModelName() {
        $classSplit = explode('\\', get_called_class());
        return $classSplit[sizeof($classSplit)-1];
    }

    public static function getPage() {
        $result = null;
        $class = "App\\Pages\\".self::getModelName();

        if(class_exists($class)) {
            $result = new $class();
        }

        return $result;
    }
}
