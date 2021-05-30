<?php

namespace StindCo\Rapido;

class Response extends Http
{
    public Data $locals;
    public bool $headerSent = false;
    public array $headers = [];
    public $configs = [
        "route_mode" => "",
        "views_folder" => "",
        "layouts_folder" => ""
    ];

    public function __construct()
    {
        parent::__construct();
        $this->locals = new Data;
    }
    private function get_conf($key)
    {
        return  $this->configs[$key];
    }
    public function append($key, $content)
    {
        $this->headers[$key] = $content;

        return $this;
    }
    public function set($text)
    {
    }
    public function attachement($filename): self
    {
        $this->append('Content-Disposition', 'attachment');

        return $this;
    }

    public function status($code): self
    {
        http_response_code($code);

        return $this;
    }
    public function type($type): self
    {
        if ($type == "pdf" or $type == "json") {
            $this->append("Content-type", "application/{$type}");
        } elseif ($type == "png" or $type == "jpg" or $type == "jpeg" or $type == "gif") {
            $this->append("Content-type", "image/{$type}");
        } else {
            $this->append("Content-type", "text/{$type}");
        }
        return $this;
    }

    public function send($message = null)
    {
        $this->unzip_headers();
        if (is_string($message)) {
            echo $message;
        } else if (is_array($message)) {
            echo json_encode($message);
        }
    }

    public function sendJson($message, $options = null)
    {
        $this->type('json');
        $this->unzip_headers();
        $this->locals->setInformations($message);
        echo $this->locals->toJson($options);
    }

    public function sendFile($path, $type, $name = null)
    {
        $this->type($type);
        if (!is_null($name)) {
            $this->append('Content-Disposition', 'attachment; filename="' . $name . '.' . $type . '"');
        } else {
            $this->append('Content-Disposition', 'attachment');
        }

        $this->unzip_headers();

        readfile($path);
    }

    private function unzip_headers()
    {
        $this->headerSent = true;
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }
    }

    public function redirect($url)
    {
        $url =  $this->formate_url($url);
        header("Location: {$url}");
    }

    private function formate_url($url)
    {
        if ($this->configs['route_mode'] == '/') return $url;
        else {
            $a = explode('/', $url);
            $controller = $a[1];
            $action = $a[2];
            $params = $a[3];
            return '/?ref_component=' . $controller . '&ref_action=' . $action . '&ref_params=' . $params;
        }
    }

    public function end()
    {
    }

    public function render($view, array $params, $layout = null)
    {
        $vuePath = $this->get_conf('views_folder');
        $layoutPath = $this->get_conf('layouts_folder');
        extract($params);
        extract($this->locals->toArray());
        if (is_null($layout)) {
            require $vuePath . '/' . $view . '.php';
        } else {
            ob_start();
            require $vuePath . '/' . $view . '.php';
            $view_content = ob_get_clean();
            require $layoutPath . '/' . $layout . '.php';
        }
    }
}
