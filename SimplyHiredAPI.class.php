<?php
/**
 * @file
 * SimplyHired's Job-a-matic REST API implementation.
 *
 * Executes calls to SimplyHired's Job-a-matic API and returns
 * formatted results for use within Drupal.
 */

define('JOBAMATIC_XML_PARSERERROR', 1001);

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
  protected $pshid;
  protected $jbd;
  protected $ssty;
  protected $cflg;
  protected $clip;
  protected $error;
  protected $code;
  protected $returnerrors;

  /**
   * Constructor.
   */
  public function __construct($pshid, $jbd, $mode = 'xml', $source='us', $returnerrors = FALSE) {
    $mode = trim(strtolower($mode));
    if ($mode != 'json' && $mode != 'xml') {
      $mode = 'xml';
    }

    $this->pshid = $pshid;
    $this->jbd = $jbd;
    $this->ssty = 2;
    $this->cflg = 'r';
    $this->error = FALSE;
    $this->code = NULL;
    $this->returnerrors = $returnerrors;
    $this->mode = $mode;
    $this->parser = SimplyHiredAPIParserFactory::getParser($this->mode, NULL);
    $this->source = strtolower(trim($source));
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
   * Prepare and call Jobamatic search service.
   */
  public function search($query, $location = '', $miles = 5, $sort = 'rd', $size = 10, $page = 0) {
    $params = array(
      'q' => urlencode(trim($query)),
      'sb' => $sort,
      'ws' => $size,
      'pn' => (intval($page) < 1 ? 0 : intval($page)),
    );

    if (!is_null($location) && $location != '') {
      $params['l'] = $location;
    }

    if (!is_null($location) && intval($miles) > 0) {
      $params['m'] = $miles;
    }

    $results = $this->call($params);

    return $results;
  }

  /**
   * Protected function that executes the web service request.
   *
   * @access protected
   */
  protected function call($criteria) {
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
      'jbd' => $this->jbd,
      'ssty' => $this->ssty,
      'clip' => $this->clip,
      'cflg' => $this->cflg,
    );
    $param_string = array();
    foreach ($params as $key => $value) {
      $param_string[] = $key . '=' . $value;
    }

    $url .= implode('&', $param_string);
    $url = sprintf($url, implode('/', $api_identity));

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
