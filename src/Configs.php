<?php

namespace StindCo\Rapido;

class Configs
{
    protected array $configs = [];

    public function get(String $key)
    {
        if (isset($this->configs[$key])) return $this->configs[$key];
        else {
            return null;
        }
    }
    public function set(String $key, $value)
    {
        $this->configs[$key] = $value;
        return $this;
    }

    public function rm(string $key)
    {
        unset($this->configs[$key]);
        return $this;
    }
    public function getAll() {
        return $this->configs;
    }
}
