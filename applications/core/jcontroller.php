<?php
class jController {
    protected $loadTemplate = true;
    protected $parseApi = false;
    protected $template = 'default';
    protected $data = array();

    public function __construct($api = false) {
	if ($api) {
	    $this->parseApi = true;
	}
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

    public function init() {
        // empty
    }

    public function end() {
        // empty
    }
}
