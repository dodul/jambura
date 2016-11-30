<?php
class jController {
    protected $loadTemplate = true;
    protected $template = 'default';
    protected $cache = null;
    protected $data = array();

    private $getVars  = array();
    private $postVars = array();
    private $requests = array();

    public function __construct($api = false) {
	if ($api) {
	    $this->parseApi = true;
	}
        $this->assets = new jAssets();
        $this->cache  = jCache::init();
	// FIXME base controller should have been defined as an abstruct
	// class if this init is kept like this.
	$this->init();
    }

    public function __set($var, $value) {
	$this->data[$var] = $value;
    }

    public function __isset($name){
        return isset($this->data[$name]);
    }

    public function __get($var) {
        if (preg_match('/^__/', $var)) {
            $requestVar = preg_replace('/(^__)(.+)/', '${2}', $var);
            return $this->request($requestVar);
        }

        if (array_key_exists($var, $this->data)) {
            return $this->data[$var];
        }
    }
	
    public function render($view, $variables = array()) {
	if (!file_exists($viewFile = JAMBURA_VIEWS.$view.'.php')) {
	    throw new Exception('View file not found :'.$viewFile);
        }
    	if ($this->parseApi) {
            if(is_array($variables)){
                return json_encode($variables);
            }
	} else {
	    $variables = empty($variables) ? $this->data : array_merge($this->data, $variables);
	    extract($variables);
            ob_start();
            if ($this->loadTemplate) {
		include(JAMBURA_TEMPLATES.$this->template.'/header.php');
	    }
            include JAMBURA_VIEWS.$view.'.php';
            if ($this->loadTemplate) {
		include(JAMBURA_TEMPLATES.$this->template.'/footer.php');
	    }
            $renderedView = ob_get_clean();
            echo $renderedView;
	}
        $this->end();
    }

    protected function get($var) {
        if (!isset($this->getVars[$var])) {
            if (!isset($_GET[$var])) {
                return false;
            }
            $this->getVars[$var] = $this->cleanRequest($_GET[$var]);
        }
        return $this->getVars[$var];
    }

    protected function post($var) {
        if (!isset($this->postVars[$var])) {
            if (!isset($_POST[$var])) {
                return false;
            }
            $this->postVars[$var] = $this->cleanRequest($_POST[$var]);
        }
        return $this->postVars[$var];
    }

    protected function request($var) {
        if (!isset($this->requests[$var])) {
            if (!isset($_REQUEST[$var])) {
                return false;
            }
            $this->requests[$var] = $this->cleanRequest($_REQUEST[$var]);
        }
        return $this->requests[$var];
    }

    private function cleanRequest($var) {
        return preg_replace('/[^-a-zA-Z0-9_@ \.]/', '', $var);
    }

    public function getRenderData() {
        return $this->data;
    }

    public function init() {
        // empty
    }

    public function end() {
        // empty
    }
}
