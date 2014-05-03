<?php
/**
 * @file
 * SimplyHired API JSON parser object.
 */
require_once dirname(__FILE__) . '/SimplyHiredAPIAbstractParser.class.php';

class SimplyHiredAPIJSONParser extends SimplyHiredAPIAbstractParser {

  public function setData($data) {
    $this->data = json_decode($data);
  }

  public function parse() {
    $data = array(
      'title' => 'Search Results',
      'start_index' => 0,
      'num_results' => 0,
      'total_results' => 0,
      'total_visible' => 0,
      'items' => array(),
      'error' => ''
    );

    $record_set = $this->data->jobs;

    /*
     * Create an object out of each of the XML recordset records
     * for easier handeling.
     */
    foreach ($record_set as $record) {

      $job = new SimplyHiredJob();
      $job->setJobTitle($record->title);
      $job->setCompany($record->company);
      $job->setSource($record->source, $record->url);
      $job->setPermalink($record->permalink);


      $job->setType('undefined');
      $job->setLocation($record->location);
      $job->setLatLng($record->latitude, $record->longitude);


      $job->setPostDate($record->date);
      $job->setExerpt($record->description);
      $data['items'][] = $job;
    }

    /*
     * Since the JSON API doesn't give us the details of the query, we will set
     * the results counter variables to the number of items in the query results
     * so any pagination created should still work correctly.
     */
    $data['num_results'] = count($data['items']);
    $data['total_results'] = count($data['items']);
    $data['total_visible'] = count($data['items']);

    return $data;
  }
}