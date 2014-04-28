<?php
/**
 * @file
 * SimplyHired API XML parser object.
 */

require_once dirname(__FILE__) . '/SimplyHiredAPIAbstractParser.class.php';

class SimplyHiredAPIXMLParser extends SimplyHiredAPIAbstractParser {

  public function parse() {
    $xml = @simplexml_load_string($this->data);

    if ($xml !== FALSE) {
      // No error tag.
      if ($xml->error == '') {
        $data = array(
          'title' => (string) $xml->rq->t,
          'start_index' => (int) $xml->rq->si,
          'num_results' => (int) $xml->rq->rpd,
          'total_results' => (int) $xml->rq->tr,
          'total_visible' => (int) $xml->rq->tv,
          'items' => array(),
          'error' => ''
        );

        $record_set = $xml->rs;

        $idx = 1;
        /*
         * Create an object out of each of the XML recordset records
         * for easier handeling.
         */
        foreach ($record_set->r as $record) {

          $job = new SimplyHiredJob();
          $job->setJobTitle((string) $record->jt);
          $job->setCompany((string) $record->cn, $record->cn['url']);
          $job->setSource((string) $record->src, $record->src['url']);


          $job->setType((string) $record->ty);
          $job->setLocation((string) $record->loc);
          /*
           * The location has a lot of attributes for extended information, so
           * set these individually for the job object.
           */
          $job->setCity($record->loc['cty']);
          $job->setState($record->loc['st']);
          $job->setPostalCode($record->loc['postal']);
          $job->setCounty($record->loc['county']);
          $job->setRegion($record->loc['region']);
          $job->setCountry($record->loc['country']);
          /*
           * The XML API doesn't return the latitude and longitude, but be aware
           * that the job object has these properties available if you should
           * want to use a geocode library to set these for the postal code
           * or city/state. To set the latitude/longitude point, call
           * $obj->setLatLng('latitude value', 'longitude value);
           */

          $job->setLastSeen((string) $record->ls);
          $job->setPostDate((string) $record->dp);
          $job->setExerpt((string) $record->e);
          $data['items'][] = $job;
          $idx++;
        }

      }
      else {
        if ($xml->error['type'] == 'noresults') {
          $message = 'No results found.';
        }
        else {
          $message = 'Unknown error occured. Error code: ' . $xml->error['code'];
        }
        $data = array(
          'title' => '',
          'start_index' => 0,
          'num_results' => 0,
          'total_results' => 0,
          'total_visible' => 0,
          'items' => array(),
          'error' => $message,
        );
      }

      return $data;
    }
    else {
      return FALSE;
    }
  }
}