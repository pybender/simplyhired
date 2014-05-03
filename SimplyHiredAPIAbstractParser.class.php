<?php
/**
 * @file
 * Abstract API parser object which should be extended by all API parser objects
 * to parse specific data formats.
 */

require_once dirname(__FILE__) . '/SimplyHiredJob.class.php';
require_once dirname(__FILE__) . '/SimplyHiredAPIParser.interface.php';

abstract class SimplyHiredAPIAbstractParser implements SimplyHiredAPIParser {

  protected $data;

  protected $results;

  public function __construct($data=NULL) {
    if (!empty($data)) {
      $this->setData($data);
    }
  }

  public function getResults() {
    return $this->results;
  }

  public function setData($data) {
    $this->data = $data;
    $this->parse();
  }

  public function getData() {
    return $this->data;
  }

  public abstract function parse();

}