<?php

class Jambura
{
    private $router;

    public static function app()
    {
        static $instance = null;
        if ($instance === null) {
            $instance = new self();
        }
        return $instance;
    }

    public function setConfig($config)
    {
        define('ROOT', '');
        define('DB_SERVER', $config['database']['host']);
        define('DB_SERVER_USERNAME', $config['database']['user']);
        define('DB_SERVER_PASSWORD', $config['database']['pass']);
        define('DB_NAME', $config['database']['db']);
        define('JAMBURA_TEMPLATES', $config['directories']['templates']);
        define('JAMBURA_MODS', $config['directories']['models']);
        define('JAMBURA_CONTROLLERS', $config['directories']['controllers']);
        define('JAMBURA_VIEWS', $config['directories']['views']);
        define('JAMBURA_CLASSES', $config['directories']['classes']);
        define('DEFAULT_LAYOUT', $config['view']['default_layout']);
        define('DEFAULT_PAGE', $config['view']['default_page']);
        define('DEFAULT_TEMPLATE', $config['view']['default_template']);

        // Environment
        define('EXT', '.php');

        return $this;
    }

    public function routeRequest()
    {
        if($this->router === null) {
            $this->router = new jRouter();
        }

        $this->router->route();

        return $this;
    }

    public function respond()
    {
        $this->router->display();

        return $this;
    }
}