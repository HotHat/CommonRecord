<?php declare(strict_types=1);


namespace App\Services;

use ArrayAccess;

class ParamBuilder implements ArrayAccess
{
    protected $attributes;
    protected $attributeMessage;
    protected $defaultValue = '';

    public function getAttribute($key) {

        $this->emptyVerify($key);

        return $this->attributes[$key] ?? $this->defaultValue;
    }

    protected function emptyVerify($key) {

    }

    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
        return $this;
    }


    public function offsetExists($offset)
    {
        return ! is_null($this->getAttribute($offset));
    }

    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }


    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }


    public function offsetUnset($offset)
    {
        unset($this->attributes[$offset]);
    }

    public function __get($key)
    {
        return $this->getAttribute($key);
    }

    public function __set($key, $value)
    {
        $this->setAttribute($key, $value);
    }
}