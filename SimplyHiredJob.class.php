<?php
/**
 * @file
 * SimplyHired Job model object
 */
class SimplyHiredJob {
  const DATE_SEEN = 1;
  const DATE_POSTED = 2;

  protected $job_title;
  protected $company;
  protected $source;
  protected $type;
  protected $location;
  protected $last_seen;
  protected $date_posted;
  protected $excerpt;
  protected $permalink;

  public function __construct() {
    $this->job_title = NULL;
    $this->company = array('name' => NULL, 'url' => NULL);
    $this->source = array('name' => NULL, 'url' => NULL);
    $this->type = NULL;
    $this->location = array(
      'city' => NULL,
      'state' => NULL,
      'postal_code' => NULL,
      'county' => NULL,
      'region' => NULL,
      'country' => NULL,
      'latitude' => NULL,
      'longitude' => NULL,
      'raw' => NULL
    );
    $this->last_seen = 0;
    $this->date_posted = 0;
    $this->excerpt = NULL;
    $this->permalink = NULL;
  }

  public function getJobTitle() {
    return $this->job_title;
  }

  public function setJobTitle($job_title) {
    $this->job_title = trim($job_title);
  }

  public function getCompany() {
    if (empty($this->company['name'])) {
      return NULL;
    }
    return $this->company;
  }

  public function setCompany($name, $url='') {
    $name = trim($name);
    $url = trim($url);
    if (!empty($name)) {
      $this->company['name'] = $name;
    }

    if (!empty($url)) {
      $this->company['url'] = $url;
    }
  }

  public function getSource() {
    if (empty($this->source['name'])) {
      return NULL;
    }
    return $this->source;
  }

  public function setSource($name, $url='') {
    $name = trim($name);
    $url = trim($url);
    if (!empty($name)) {
      $this->source['name'] = $name;
    }

    if (!empty($url)) {
      $this->source['url'] = $url;
    }
  }

  public function setType($type) {
    $this->type = trim($type);
  }

  public function getType() {
    return $this->type;
  }

  public function setLocation($location) {
    $this->location['raw'] = trim($location);
  }

  public function getLocation() {
    return (object) $this->location;
  }

  public function getRawLocation() {
    return $this->location['raw'];
  }

  public function setCity($city) {
    $this->location['city'] = trim($city);
    $this->checkDC();
  }

  public function getCity() {
    return (empty($this->location['city']) ? NULL : $this->location['city']);
  }

  public function setState($state) {
    $state = trim($state);
    if (!empty($state)) {
      $this->location['state'] = $state;
      $this->checkDC();
    }
  }

  public function getState() {
    return (empty($this->location['state']) ? NULL : $this->location['state']);
  }

  public function setPostalCode($code) {
    $code = trim($code);
    if (!empty($code)) {
      $this->location['postal_code'] = $code;
    }
  }

  public function getPostalCode() {
    return (empty($this->location['postal_code']) ? NULL : $this->location['postal_code']);
  }

  public function setCounty($county) {
    $county = trim($county);
    if (!empty($county)) {
      $this->location['county'] = $county;
    }
  }

  public function getCounty() {
    return (empty($this->location['county']) ? NULL : $this->location['county']);
  }

  public function setRegion($region) {
    $region = trim($region);
    if (!empty($region)) {
      $this->location['region'] = $region;
    }
  }

  public function getRegion() {
    return (empty($this->location['region']) ? NULL : $this->location['region']);
  }

  public function setCountry($country) {
    $country = trim($country);
    if (!empty($country)) {
      $this->location['country'] = $country;
    }
  }

  public function getCountry() {
    return (empty($this->location['country']) ? NULL : $this->location['country']);
  }

  public function setLatLng($lat, $lng) {
    $lat = (is_numeric($lat) ? $lat : 0);
    $lng = (is_numeric($lng) ? $lng : 0);
    $this->location['latitude'] = trim($lat);
    $this->location['longitude'] = trim($lng);
  }

  public function getLatLng() {
    if ($this->location['latitude'] == 0 && $this->location['longitude'] == 0) {
      return NULL;
    }

    return (object) array('latitude' => $this->location['latitude'], 'longitude' => $this->location['longitude']);
  }

  private function setDate($date, $which) {
    if (!is_numeric($date)) {
      $date = strtotime($date);
    }
    elseif ($date < 0) {
      $date = 0;
    }

    switch ($which) {
      case self::DATE_POSTED:
        $this->date_posted = $date;
        break;
      case self::DATE_SEEN:
        $this->last_seen = $date;
        break;
    }
  }

  private function getDate($which, $format='int') {
    switch ($which) {
      case self::DATE_SEEN:
        $date = $this->last_seen;
        break;
      case self::DATE_POSTED:
        $date = $this->date_posted;
        break;
      default:
        return FALSE;
    }

    if (strtolower($format) == 'int') {
      return $date;
    }

    return date($format, $date);
  }


  public function setPostDate($date) {
    $this->setDate($date, self::DATE_POSTED);
  }

  public function getPostDate($format='int') {
    return $this->getDate(self::DATE_POSTED, $format);
  }

  public function setLastSeen($date) {
    $this->setDate($date, self::DATE_SEEN);
  }

  public function getLastSeen($format='int') {
    return $this->getDate(self::DATE_SEEN, $format);
  }

  public function setExerpt($text) {
    $this->excerpt = trim($text);
  }

  public function getExerpt() {
    return (empty($this->excerpt) ? NULL : $this->excerpt);
  }

  public function setPermalink($url) {
    $this->permalink = trim($url);
  }

  public function getPermalink() {
    return $this->permalink;
  }

  private function checkDC() {
    // City is empty if the state is DC, so populate the city variable
    // so we have the correct display.
    if ($this->location['state'] == 'DC' && empty($this->location['city'])) {
      $this->location['city'] = 'Washington';
      $this->location['raw'] = 'Washington, DC';
    }
  }

}