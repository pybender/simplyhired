# SimplyHired Job-a-matic

## Overview

Simply Hired Job-a-matic is a great way to earn additional revenue from your
existing website and provide valuable content for your users with a free hosted
job board. The Job-a-matic module uses SimplyHired's XML API to display job
postings from their database directly within your Drupal site.

## Requirements and Restrictions

### Restrictions

The SimplyHired Terms of Service requires that an attribution be displayed on
any page that contains SimplyHired data. This can be disabled in the module
configuration, but by doing so, you knowingly violate the
[SimplyHired Terms of Service Agreement](www.jobamatic.com/jbb-static/terms-of-service).

The following code must be used anywhere jobs data is displayed.

    <div style="text-align: right;">
      <span style="font-size:10px; position:relative; top:-5px; font-family:Arial,sans-serif;color: rgb(51, 51, 51);">
        <a style="color:#333;text-decoration:none" href="http://www.simplyhired.com/" rel="nofollow">Jobs</a> by
      </span>
      <a STYLE="text-decoration:none" href="http://www.simplyhired.com/">
        <img src="http://www.jobamatic.com/c/jbb/images/simplyhired.png" alt="Simply Hired">
      </a>
    </div>

## Classes

## API Types

### Job Object using the XML API

### Job Object using the JSON API
