<?php
abstract class jRest extends jController {
    /**
     * The method of the request made. Can be POST, PUT, GET or DELETE
     * @var string
     */
    protected $method;

    /**
     * Payload of the request received.
     * @var string
     */
    protected $requestPayload;

    /**
     * Response code to be sent back to client. Default is 200 (OK)
     * @var int
     */
    protected $respCode = 200;

    /**
     * Pre-formated response to be sent to the client.
     * @var array
     */
    protected $response = [];

    /**
     * Header content-type of the response. Default is application/json
     * @var string
     */
    protected $responseContentType   = 'application/json';

    /**
     * List of supported content-types that can be formatted by this class
     * @var array
     */
    private $supportedContentTypes = [
        'application/json' => 'formatJSON',
        'text/xml'         => 'formatXML',
        'application/xml'  => 'formatXML',
        'text/HTML'        => 'formatHTML'
    ];

    /**
     * List of PUT data sent from client, parsed into array
     * @var array
     */
    private $putVars = [];

    /**
     * List of DELETE data sent from client, parsed into array
     * @var array
     */
    private $deleteVars = [];

    /**
     * Do the authentication for the REST call
     *
     * @return bool true if authentication successful false otherwise
     */
    abstract protected function authenticate();

    /**
     * Initial method executes prior to execution of the requested action.
     *
     * Overrides and executes parents init(). Loads local properties based 
     * on the request and checks for authentication.
     */
    public function init() {
        parent::init();
        $this->loadTemplate = false;
        $this->parseApi = true;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->setRequestPayload();
        try {
            if (!$this->authenticate()) {
                $this->sendError(401);
            }
        } catch (\Exception $e) {
            $this->sendError(401, $e->getMessage()); 
        }
    }

    /**
     * Sends error response.
     *
     * Sends error response to client based on the error code and message 
     * supplied. exits code after wards to prevent execution of end() method.
     *
     * @param int    $code    HTTP response code
     * @param string $message message to be sent to client
     */
    protected function sendError($code, $message = null) {
        $this->respCode = $code;
        $this->response['error'] = $message ? $message : $this->getStatusCodeMessage($code);
        $this->sendResponse();
        exit();
    }

    /**
     * Overrides parents render.
     *
     * Overrides parent::render() so that end() can not get executed
     * once render is called. The render method echos all the parameter 
     * as in JSON once $parseAPI is set to true.
     *
     * @param string $view      name of the view to be rendered.
     * @param array  $variables list of variables to be rendered.
     */
    public function render($view, $variables = array()) {
        $this->loadResponseHeader();
        parent::render($view, $variables);
        exit(); 
    }

    /**
     * Sets content-type of response.
     *
     * Sets content-type header for HTTP response. Checks if the requested 
     * content-type is supported or not.
     *
     * @throws Exception if supplied content-type is not supported
     */
    protected function setResponseType($contentType) {
        if (!array_key_exists($contentType, $this->supportedContentTypes)) {
            throw new Exception("content-type: $contentType not supported");
        }
        $this->responseContentType = $contentType;
    }

    /**
     * Parse request payload (from client)
     *
     * Parse request payload if request method is PUT or DELETE
     * and store them in an associative array.
     */
    protected function setRequestPayload() {
        switch ($this->method) {
            case 'PUT':
                parse_str(file_get_contents('php://input'), $this->putData);
                break;
            case 'DELETE':
                parse_str(file_get_contents('php://input'), $this->deleteData);
                break;
        }
    }

    /**
     * Returns PUT data
     *
     * Checks if the putdata with given key exists. Returns the value
     * if exists.
     *
     * @param string $key key name of put data
     * @return mixed string if match found boolean false otherwise
     */
    protected function put($key) {
        if (isset($this->putData[$key])) {
            return $this->putData[$key];
        }
        return false;
    }

    /**
     * Returns DELETE data
     *
     * Checks if the delete data with given key exists. Returns the value
     * if exists.
     *
     * @param string $key key name of delete data
     * @return mixed string if match found boolean false otherwise
     */
    protected function delete($key) {
        if (isset($this->deleteData[$key])) {
            return $this->deleteData[$key];
        }
        return false;
    }

    /**
     * Checks the requested method
     *
     * Checks if the method of request matches the expected. Sends a 405 
     * Method not allowed if the method does not match as expected.
     *
     * @param  string  $expected expecetd method
     * @return boolean true if method is matched
     */
    protected function checkRequestMethod($expected) {
        $this->allowedMethod = $expected;
        if ($this->method != $expected) {
            $this->sendError(405);
        }
        return true;
    }

    /**
     * Formates response into JSON.
     * 
     * @param array $response Response to be returned in array
     *
     * @return string JSON to be sent back to client as response.
     */
    private function formatJSON(array $response) {
        return json_encode($response);
    }

    /**
     * Formates response into XML.
     * 
     * @param array $response Response to be returned in array
     *
     * @return string XML to be sent back to client as response.
     */
    private function formatXML(array $response) {
        $xml = new SimpleXMLElement('<root/>');
        array_walk_recursive($response, array ($xml, 'addChild'));
        return $xml->asXML();
    }

    /**
     * Used to allow HTML responses from API classes
     *
     * @param array $response dummy param
     *
     * @return string blank string
     */
    private function formatHTML(array $response) {
        return '';
    }
     

    /**
     * Determins the method to be used for formating the response.
     *
     * @return string name of method to be used for formating response.
     */
    private function getFormatter() {
        return $this->supportedContentTypes[$this->responseContentType];
    }

    /**
     * Executes after the request has been processed.
     *
     * Last method to get executed. Prepares the response header and 
     * sends back the response in the formate specified.
     */
    public function end() {
        parent::end();
        $this->sendResponse();
    }

    /**
     * Loads response header as per response code and content-type
     */ 
    protected function loadResponseHeader() {
        $status_header = 'HTTP/1.1 '.$this->respCode.' '.$this->getStatusCodeMessage($this->respCode);
        // set the status
        header($status_header);
        // set the content type
        header('Content-type: ' . $this->responseContentType . '; charset=utf-8');
    }

    /**
     * Sends response back to client.
     *
     * Loads response header and format response packet based on 
     * specification and sends response back to the client.
     */
    protected function sendResponse() {
        // First prepare the header
        $this->loadResponseHeader();
        // Format and send the respose
        echo $this->{$this->getFormatter()}($this->response);
    }

    /**
     * Returns short description of HTTP status codes.
     *
     * @param int $status HTTP status code
     * return mixed string short description if status exists, bool false otherwise
     */
    public function getStatusCodeMessage($status) {
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

        return (isset($codes[$status])) ? $codes[$status] : false;
    } 
}