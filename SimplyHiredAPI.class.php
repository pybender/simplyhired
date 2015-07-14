<?php
/**
 * @file
 * SimplyHired's REST API implementation.
 * 
 * Version 2
 *
 * Executes calls to SimplyHired's API and returns
 * formatted results.
 */

define('SIMPLYHIRED_XML_PARSERERROR', 1001);
define('FSR_PRIMARY', 'primary');
define('FSR_JOB_BOARD', 'job_board');

require_once dirname(__FILE__) . '/SimplyHiredAPIParserFactory.class.php';

class SimplyHiredAPI {
  /**
   * @var string $mode
   * How will data be retrieved? XML or JSON
   */
  protected $mode;
  /**
   * @var string $source
   * The API Source URL.
   */
  protected $source;
  /**
   * @var object $parser
   * Based on @see $mode, a data parser for the specific data type.
   */
  protected $parser;
	/**
	 * Publisher key
	 * @var string $pshid
	 */
  protected $pshid;
	/**
	 * API key provided by Simply Hired
	 * @var string $auth_key
	 */
  protected $auth_key;
	/**
	 * Required search parameter. Only known usage is some sort of flag when
	 * making calls to other than the US api.
	 * @var integer $ssty
	 */
  protected $ssty;
	/**
	 * Unknown usage
	 * @var string $cflg
	 */
  protected $cflg;
	/**
	 * Client IP address
	 * @var string $clip
	 */
  protected $clip;
	/**
	 * Error message returned by API calls upon failure
	 * @var string $error
	 */
  protected $error;
	/**
	 * HTTP 1.1 status code of the API calls.
	 * @var integer $code
	 */
  protected $code;
	/**
	 * Whether the class will return errors or fail silently.
	 * @var boolean $returnerrors
	 */
  protected $returnerrors;
	/**
	 * The URL (including all parameters) that will be used for the current call.
	 * @var string $url
	 */
  protected $url;

  /**
   * Constructor.
   */
  public function __construct($pshid, $auth_key, $mode = 'xml', $source='us', $returnerrors = FALSE) {
    $mode = trim(strtolower($mode));
    if ($mode != 'json' && $mode != 'xml') {
      $mode = 'xml';
    }

    $this->pshid = $pshid;
    $this->auth_key = trim($auth_key);
    $this->ssty = ($source == 'us') ? 2 : 3;
    $this->cflg = 'r';
    $this->error = FALSE;
    $this->code = NULL;
    $this->returnerrors = $returnerrors;
    $this->mode = strtoupper($mode);
    $this->parser = SimplyHiredAPIParserFactory::getParser($this->mode, NULL);
    $this->source = strtolower(trim($source));
    $this->url = NULL;

    $this->setClip($_SERVER['REMOTE_ADDR']);
  }
  
	/**
	 * Get the URL that will be used for the api call (inclding all parameters)
	 * 
	 * @return mixed
	 */
  public function getURL() {
    return $this->url;
  }

  /**
   * Sets the client IP address.
   */
  public function setClip($clip) {
    $this->clip = trim($clip);
  }

  /**
   * Returns web service call status code.
   */
  public function getCode() {
    return intval($this->code) != 0 ? intval($this->code) : -1000;
  }

  /**
   * Returns class error message.
   */
  public function getError() {
    return $this->error;
  }
  
	/**
	 * Set the API country to use for all calls.
	 * @param string $source
	 */
  public function setSource($source) {
    $this->source = trim($source);
  }
  
	/**
	 * Get the API country currently being used for all calls.
	 * @return string
	 */
  public function getSource() {
    return $this->source;
  }

  /**
   * Prepare and call Jobamatic search service.
   */
  public function search($query, $frag = TRUE, $location = '', $miles = 5, $sort = 'rd', $type = '', $size = 10, $page = 0) {
		
    $params = array(
      'q' => urlencode(trim($query)),
      'sb' => $sort,
      'ws' => $size,
      'pn' => (intval($page) < 1 ? 0 : intval($page)),
    );
		
		$type = trim($type);
    
    if (!empty($type) && ($type == FSR_PRIMARY || $type == FSR_JOB_BOARD)) {
      $params['fsr'] = $type;
    }

    if (!is_null($location) && $location != '') {
      $params['l'] = $location;
    }

    if (!is_null($location) && intval($miles) > 0) {
      $params['mi'] = $miles;
    }
		
		if (!empty($page)) {
			$params['pn'] = intval($page);
		}

    $results = $this->call($params, $frag);

    return $results;
  }

  /**
   * Protected function that executes the web service request.
   *
   * @access protected
   */
  protected function call($criteria, $frag_only=TRUE) {
    if (empty($this->clip)) {
      throw new Exception('Client IP address can not be empty. Please set the client IP using SimplyHiredAPI::setClip(\'IP address\').');
      exit;
    }
    $data = FALSE;
    
    $url = 'http://' . $this->getAPIBase() . '/a/jobs-api/' . ($this->mode == 'json' ? 'json' : 'xml-v2') . '/%s?';
    
    $api_identity = array();

    foreach ($criteria as $key => $value) {
      $api_identity[] = $key . '-' . $value;
    }

    $params = array(
      'pshid' => $this->pshid,
      'auth' => $this->auth_key,
      'ssty' => $this->ssty,
      'clip' => $this->clip,
      'cflg' => $this->cflg,
    );

    if (!$frag_only) {
      $params['frag'] = 'false';
    }

    $param_string = array();
    foreach ($params as $key => $value) {
      $param_string[] = $key . '=' . $value;
    }

    $url .= implode('&', $param_string);
    $url = sprintf($url, implode('/', $api_identity));
    $this->url = $url;

    $code = 0;
    $message = 'No matches found.';

    try {
      $response_header = NULL;

      $data = @file_get_contents($url);

      if (isset($http_response_header)) {

        $response_header = $http_response_header;

        preg_match('/^HTTP\/1.1\s([0-9]{3})\s(.*)$/', $response_header[0], $matches);

        if (count($matches)) {
          $code = intval($matches[1]);
          $message = $matches[2];
        }
      }
    }
    catch (Exception $e) {
      throw new Exception($e->getMessage());
      exit;
    }

    $this->code = $code;

    if ($this->code != 200) {
      $this->error = $message;
      $data = FALSE;
    }
    else {
      $this->parser->setData($data);
      $data = $this->parser->parse();
    }

    return $data;
  }


  protected function getAPIBase() {
    switch ($this->source) {
      case 'ar':
        return 'api.simplyhired.com.ar';
      case 'au':
        return 'api.simplyhired.com.au';
      case 'at':
        return 'api.simplyhired.com.at';
      case 'be':
        return 'api.simplyhired.com.be';
      case 'br':
        return 'api.simplyhired.com.br';
      case 'ca':
        return 'api.simplyhired.ca';
      case 'cn':
        return 'api.simplyhired.cn';
      case 'fr':
        return 'api.simplyhired.fr';
      case 'de':
        return 'api.simplyhired.de';
      case 'in':
        return 'api.simplyhired.co.in';
      case 'ie':
        return 'api.simplyhired.ie';
      case 'it':
        return 'api.simplyhired.it';
      case 'jp':
        return 'api.simplyhired.jp';
      case 'kr':
        return 'api.simplyhired.kr';
      case 'mx':
        return 'api.simplyhired.mx';
      case 'nl':
        return 'api.simplyhired.nl';
      case 'pt':
        return 'api.simplyhired.pt';
      case 'ru':
        return 'api.simplyhired.ru';
      case 'za':
        return 'api.za.simplyhired.com';
      case 'es':
        return 'api.simplyhired.es';
      case 'se':
        return 'api.simplyhired.se';
      case 'ch':
        return 'api.simplyhired.ch';
      case 'gb':
        return 'api.simplyhired.co.uk';
      default:
        return 'api.simplyhired.com';
    }
  }


}
