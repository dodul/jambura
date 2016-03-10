<?php
class jRouter
{
    private $controller;
    private $action;

    public function route(){
        if($controller = $_GET['controller']) {
            if( file_exists(JAMBURA_CONTROLLERS.$controller.'.php') ){
                include(JAMBURA_CONTROLLERS.$controller.'.php');
            } else {
		// FIXME rather than throwing exception this should display a 
		// default 404 page.
                throw new Exception('Invalid controller file : '.$controller);
            }
	    $this->action = isset($_GET['action']) ? 'action_'.$_GET['action'] : 'action_index';
            $class = 'Controller_'.$controller;
            $this->controller = new $class();
	    return $this;
        } else {
            header( 'location:index.php?controller='.DEFAULT_PAGE );
        }
    }

    public function display(){
        if( $this->controller ){
            $action = $this->action;
            $this->controller->$action();
            $this->controller->end();
        }
    }
}


