<?php
/**
 * @file
 * Factory pattern object to create the correct data parser for API calls.
 */

require_once dirname(__FILE__) . '/SimplyHiredAPIXMLParser.class.php';
require_once dirname(__FILE__) . '/SimplyHiredAPIJSONParser.class.php';

class SimplyHiredAPIParserFactory {
  const XML = 'XML';
  const JSON = 'JSON';


  public function __construct() {}

  public static function getParser($type=self::XML, $data=NULL) {

    $parser = 'SimplyHiredAPI' . strtoupper($type) . 'Parser';

    if (class_exists($parser)) {
      return new $parser($data);
    }
    else {
      throw new Exception('Class ' . $parser . ' not found.');
      exit;
    }

  }
}