<?php
class jRouter
{
    private $controller;
    private $action;

    public function route($namespace = ''){
        if ($controller = $_GET['controller']) { 
            if( file_exists(JAMBURA_CONTROLLERS.$controller.'.php') ){
                include(JAMBURA_CONTROLLERS.$controller.'.php');
            } else {
		// FIXME rather than throwing exception this should display a 
		// default 404 page.
                throw new jamexBadController('Invalid controller file : '.$controller);
            }
            
	    $this->action = isset($_GET['action']) ? 'action_'.$_GET['action'] : 'action_index';
            $class = $namespace.'Controller_'.$controller;
            $this->controller = new $class();
	    return $this;
        } else {
            header( 'location:/'.DEFAULT_PAGE );
        }
    }

    public function display(){
        if( $this->controller ){
            if (!method_exists($this->controller, $this->action)) {
                throw new jamexBadAction("Action: $this->action does not exist");
                
            }
            $action = $this->action;
            $this->controller->$action();
            $this->controller->end();
        }
    }

    public static function showErrorPage($page, \Exception $e, $forceErrorReport = false) {
        Logger::error($e->getMessage());
	if (JAMBURA_MOD == 'DEV' || $forceErrorReport) {
            $whoops = new \Whoops\Run;
            $whoops->allowQuit(false);
            $whoops->writeToOutput(false);
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
            $html = $whoops->handleException($e);

            echo $html;
            return;
        }

        if (is_array($page)) {
            $_GET['controller'] = $page[0];
            $_GET['action']     = $page[1];

            try {
                 (new jRouter())->route()->display();
            } catch (\Exception $e) {
                jRouter::showErrorPage('', $e, true);
            }
        } else {
            include($page);
        }
    }
}
