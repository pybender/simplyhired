# SimplyHired Job-a-matic

## Overview

Simply Hired Job-a-matic library aims at making it easy to add job listings to your website using simply PHP objects and method calls. The library supports both the XML and JSON API's.

This is an initial port from the [Drupal module](https://drupal.org/project/simply_hired_job_a_matic) and [Wordpress plugin](http://wordpress.org/plugins/sh-jobamatic/) code to form a single library that can be used across multiple projects without maintaining duplicate code. The code is not well documented at this point but will be improving daily until complete. However, the library uses very well documented design patterns and a documented API, so it is not the difficult to use in the short term.

## Requirements

To use the API, you must first create a [free partner job board](https://www.jobamatic.com/a/jbb/partner-register-account). Once you have created your job board, you can then access the XML API tag from the portal dashboard which contains your publisher ID and Job-a-matic domain -- both of which are needed for all API calls.

## Features

The SimplyHired Job-a-matic library attempts to support the full set of features available from the API. The current features include

* support for job listings available in 24 countries (see your job portal for the complete listing)
* results pagination
* use complex search queries including boolean, location (city/state or postal code), search radius, etc.

## Limitations

The JSON API is not as robust as the XML API and therefore the data returned is missing several of the extended pieces of data for a job and for the search query recordset itself. This library attempts to correct these inconsistancies in the API's, but some functionality will be reduced when using the JSON API.


## Attribution

The SimplyHired Terms of Service requires that an attribution be displayed on
any page or screen that contains SimplyHired data. See the
[SimplyHired Terms of Service Agreement](www.jobamatic.com/jbb-static/terms-of-service) for
complete terms of service.

The following code must be used anywhere SimplyHired jobs data is displayed.

    <div style="text-align: right;">
      <span style="font-size:10px; position:relative; top:-5px; font-family:Arial,sans-serif;color: rgb(51, 51, 51);">
        <a style="color:#333;text-decoration:none" href="http://www.simplyhired.com/" rel="nofollow">Jobs</a> by
      </span>
      <a STYLE="text-decoration:none" href="http://www.simplyhired.com/">
        <img src="http://www.jobamatic.com/c/jbb/images/simplyhired.png" alt="Simply Hired">
      </a>
    </div>

## About SimplyHired

**From the SimplyHired "About Us" page.**

_Simply Hired, a technology company based in Sunnyvale, California, operates job search engines in 24 countries and 12 languages. With more than 30 million unique visitors per month, the company provides job seekers access to millions of job openings across all job categories and industries, reaching job seekers on the web, social networks, mobile devices, email, and via thousands of partner sites including LinkedIn, The Washington Post, and Bloomberg Businessweek. With its Sponsored Jobs offering, Simply Hired enables employers to efficiently and cost-effectively reach candidates searching for jobs through its full-service pay-per-click (PPC) and self-service pay-per-post job advertising solutions. Simply Hired was founded in 2005, has offices in Sunnyvale, Los Angeles, New York and Toronto, and is privately held with funding from Foundation Capital and IDG Ventures. For more information, visit [www.simplyhired.com](http://www.simplyhired.com)._

## Reference

### Classes

![class diagram](./simplyhired_api-diag.jpg =600x)  
[View full size](./simplyhired_api-diag.jpg)

**SimplyHiredAPI** - Main class responsible for executing all API calls.

**SimplyHiredAPIParserFactory** - Generates the API data parser (XML or JSON) to parse the data returned by the API calls.

**SimplyHiredAPIParser** - Interface providing the _parse()_ method that all parsers must implement.

**SimplyHiredAPIAbstractParser** - Abstract data parser which must be overriden by child classes to implement the _parse()_ method.

**SimplyHiredAPIJSONParser** - JSON data parser object.

**SimplyHiredAPIXMLParser** - XML data parser object (default parser).

**SimplyHiredJob** - Job object created by parsers to represent a single job returned by SimplyHired API calls.

### Usage

**Requirements**

To use the SimplyHiredAPI object, you must include the _SimplyHiredAPI.class.php_ file in your PHP script. It is best to use the PHP _require_ or _require_once_ construct such as the following:

    <?php
    
      require_once {path_to_library}/SimplyHiredAPI.class.php;
      
      other php code ...
      
    ?>

**Basic usage**

The very minimal usage of the library, you will need to create a variable to hold the API object to run job search queries with. The following demonstrates that basic usage.

    <?php
    
      require_once {path_to_library}/SimplyHiredAPI.class.php;
      
      $api = new SimplyHiredAPI('publisher_id', 'jobamatic_domain');
      
      // Set the client IP address.
      $api->setClip($_SERVER['REMOTE_ADDR']);
      
      // Execute a job search query and store the results in a variable.
      $results = $api->search('PHP AND developer AND NOT (ASP OR Microsoft)');
      
      other php code ...
      
    ?>
    
The publisher_id and jobamatic_domain parameters in the SimplyHiredAPI constructor are assigned to you when  you signed up for your Job-a-matic account. See the Job-a-matic partner portal for this information.

**Search Results**

The job search results are returned as an associative array with the following keys:

* **title** - The title of the job search. (If using the JSON API, the title will always be 'Search Results' as this information is not provided by the API.)
* **start_index** - The starting index for the search query; used for pagination
* **num_results** - The number of job listings per page.
* **total_results** - The total number of results returned by the search query.
* **total_visible** - The total number of results available through API calls; this will most always be less than the _total_results_ key.
* **items** - An indexed array of SimplyHiredJob objects representing each individual job.