<?php

namespace StindCo\Rapido;

class Http extends Data{
    public function __construct() {
        $this->setInformations($_SERVER);
    }
}