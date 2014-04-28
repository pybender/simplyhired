<?php
/**
 * @file
 * Abstract API parser object which should be extended by all API parser objects
 * to parse specific data formats.
 */

require_once dirname(__FILE__) . '/SimplyHiredJob.class.php';

abstract class SimplyHiredAPIAbstractParser {

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

  abstract function parse();

}