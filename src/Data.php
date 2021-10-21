<?php

namespace StindCo\Rapido;

use ArrayAccess;

class Data implements ArrayAccess
{
    protected $asDataError = false;
    protected array $DataErrors = [];
    protected array $DataSafeInformations = [];

    public function setErrors(array $errors): self
    {
        $this->asDataError = true;
        foreach ($errors as $key => $value) {
            $this->DataErrors[$key] = $value;
        }
        return $this;
    }
    /**
     * this function returns a Data of Errors
     *
     * @return self
     */
    public function getErrors(): self
    {
        return (new $this)->setInformations($this->DataErrors);
    }
    /**
     * Cette fonction renvoie une data
     *
     * @param array $keys
     * @return self
     */
    public  function getData(array $keys): self
    {
        $error = false;
        foreach ($keys as $key => $value) {
            if (is_string($key)) {
                if (preg_match($value, $this->$key)) {
                    $tab[$key] = $this->$key;
                } else {
                    $error = true;
                    $errors[$key] = $this->$key;
                }
            } else $tab[$value] = $this->$value;
        }
        if ($error) {
            return (new $this)->setInformations($tab)->setErrors($errors);
        }
        return (new $this)->setInformations($tab);
    }
    public function object_to_Data(object $var1)
    {
    }
    public function setInformations(array $data): self
    {
        foreach ($data as $key => $value) {
            $this->DataSafeInformations[$key] = $value;
            $this->$key = $value;
        }
        return $this;
    }
    public function offsetExists($offset)
    {
        if (isset($this->$offset)) return true;
        return false;
    }
    public function offsetGet($offset)
    {
        return $this->$offset;
    }
    public function offsetSet($offset, $value)
    {
        $this->DataSafeInformations[$offset] = $value;
        $this->$offset = $value;
    }
    public function offsetUnset($offset)
    {
        if (!is_null($this->$offset)) unset($this->$offset);
    }
    public function get($key)
    {
        return $this->$key;
    }
    public function toArray()
    {
        return $this->DataSafeInformations;
    }
    public function toJson($options = null)
    {
        return json_encode($this->DataSafeInformations, $options);
    }
}
