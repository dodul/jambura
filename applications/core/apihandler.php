<?php
class apihandler
{
    public $request = null;
    public $method = null;
    public $error = null;
    public $params = null;
    public $apiKey = null;
    public $secretKey = null;
    public $controller = null;
		        
    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];		
        $_HEADER = apache_request_headers();
        $this->apiKey = (isset($_HEADER['X-Public']))?$_HEADER['X-Public']:'';
        $this->secretKey = (isset($_HEADER['X-Hash']))?$_HEADER['X-Hash']:'';        
        $this->controller = (isset($_REQUEST['controller']))?$_REQUEST['controller']:''; 
	    $this->set_params();
        $this->error = $this->is_authenticate();

        if ($this->error != 0) {
          die($this->sendResponse($this->error));
        }
    }
    
    public function set_params()
    {
        switch ($this->method) {
            case 'PUT':	
            case 'DELETE':
                parse_str(file_get_contents("php://input"), $this->params);
                break;
            case 'POST':
		$this->params = $_POST;
		break;
        }            
    }
    
    public function processRequest()
    {      
        switch ($this->method) {
            case 'PUT':	
                return $this->rest_put();  
                break;
            case 'POST':
		        return $this->rest_post();
		        break;
            case 'DELETE':
		        return $this->rest_delete();
		        break;
            default:
		        return $this->rest_error();
                break;
        }
    }
    
    
    public function is_authenticate()
    {

	    //controller/apiKey/secretKey

        $return = 501;

        if($this->apiKey != '' && $this->secretKey != '' && $this->controller != ''){
            include 'applications/models/apiauth.php';
            $apiauth = new Model_apiauth($this->apiKey);

            if($apiauth->checkAuth($this->secretKey,$this->params)){
                if ($apiauth->checkController($this->controller)) {
                    $return = 0;
                }
                else {
                    $return = 401;
                }
            } else {
                $return = 203;
            }
        } else {
            $return = 400;
        }
        return $return;
    }
    
    
    public function rest_post()
    {
	    if ($action = $_POST['action']) {
            $object = 'action_'.$action;
	    } else {
            $object = 'action_index';
        }

	    if (file_exists('applications/controllers/'.$this->controller.'.php')) {
            include('applications/controllers/'.$this->controller.'.php');
	    } else {
            throw new Exception('Invalid controller file : '.$this->controller);
	}

	    $class = 'Controller_'.$this->controller;
	    $cntr = new $class(TRUE);
	    $cntr->init();
	    //$this->jsonData = $cntr->$object();
        return $cntr->$object();
	    //return $this->params;
    }
	
    public function rest_put()
    {
        return $this->params;		 
    }
	
    public function rest_delete()
    {
        return $this->params;
    }
    
    public function rest_error()
    {
	    die($this->sendResponse(400));
    }   
	
	
	
    public function sendResponse($status = 200, $body = '', $content_type = 'text/html')
    {
        $status_header = 'HTTP/1.1 ' . $status . ' ' . $this->getStatusCodeMessage($status);
        // set the status
        header($status_header);
        // set the content type
        header('Content-type: ' . $content_type . '; charset=utf-8');

        // pages with body are easy
        if ($body != '') {
    	    // send the body
            echo $body;
            exit;
        }
        // we need to create the body if none is passed
        else {
            // create some body messages
            $message = '';

            // this is purely optional, but makes the pages a little nicer to read
            // for your users.  Since you won't likely send a lot of different status codes,
            // this also shouldn't be too ponderous to maintain
            switch ($status) {
                case 401:
                    $message = 'You must be authorized to view this page.';
                    break;
                case 404:
                    $message = 'The requested URL ' . $_SERVER['REQUEST_URI'] . ' was not found.';
                    break;
                case 500:
                    $message = 'The server encountered an error processing your request.';
                    break;
                case 501:
                    $message = 'The requested method is not implemented.';
                    break;
            }

            // servers don't always have a signature turned on (this is an apache directive "ServerSignature On")
            $signature = 'RESTFUL API at ' . $_SERVER['SERVER_NAME'] . ' Port ' . $_SERVER['SERVER_PORT'];

            // this should be templatized in a real-world solution
            $body = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
                <html>
                    <head>
                    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
                    <title>' . $status . ' ' . $this->getStatusCodeMessage($status) . '</title>
                </head>
                <body>
                    <h1>' . $this->getStatusCodeMessage($status) . '</h1>
                    <p>' . $message . '</p>
                    <hr />
                    <address>' . $signature . '</address>
                </body>
                </html>';

            echo $body;
            exit;
        }
    }


    public function getStatusCodeMessage($status)
    {
        // these could be stored in a .ini file and loaded
        // via parse_ini_file()... however, this will suffice
        // for an example
        $codes = Array(
            100 => 'Continue',
            101 => 'Switching Protocols',
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found',
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            306 => '(Unused)',
            307 => 'Temporary Redirect',
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported'
        );

        return (isset($codes[$status])) ? $codes[$status] : '';
    } 
}
