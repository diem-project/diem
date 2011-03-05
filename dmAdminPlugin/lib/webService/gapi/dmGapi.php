<?php

require_once(dmOs::join(sfConfig::get('dm_core_dir'), 'lib/vendor/gapi/gapi.class.php'));

class dmGapi extends gapi
{
  protected
  $cacheManager,
  $reportId,
  $defaultReportOptions;

  public function __construct(dmCacheManager $cacheManager)
  {
    $this->cacheManager = $cacheManager;
  }

  /**
   * Set up authenticate with Google and get auth_token
   *
   * @param String $email
   * @param String $password
   * @param String $token
   * @return gapi
   */
  public function authenticate($email, $password, $token = null)
  {
    if (!($email && $password) && !$token)
    {
      throw new dmGapiException('No google analytics account configured');
    }

    $this->reportId = null;

    if($token !== null)
    {
      $this->auth_token = $token;
    }
    else
    {
      $this->authenticateUser($email, $password);
    }

    $this->getReportId();

    return $this;
  }
  
  public function getTotalPageViews()
  {
    $report = $this->getReport(array(
      'dimensions'  => array('year'),
      'metrics'     => array('pageviews')
    ));
    
    $pageviews = 0;
    foreach($report as $entry)
    {
      $pageviews += $entry->get('pageviews');
    }
    
    unset($report);
    
    return $pageviews;
  }
  
  public function getReport(array $options)
  {
    $options = array_merge($this->getDefaultReportOptions(), $options);
    
    return $this->requestReportData(
    $this->getReportId(),
    $options['dimensions'],
    $options['metrics'],
    $options['sort_metric'],
    $options['filter'],
    $options['start_date'],
    $options['end_date'],
    $options['start_index'],
    $options['max_results']
    );
  }
  
  public function getDefaultReportOptions()
  {
    if (null === $this->defaultReportOptions)
    {
      $this->defaultReportOptions = array(
        'dimensions'  => array(),
        'metrics'     => array(),
        'sort_metric' => null,
        'filter'      => null,
        'start_date'  => date('Y-m-d',strtotime('11 months ago')),
        'end_date'    => null,
        'start_index' => 1,
        'max_results' => 30
      );
    }
    
    return $this->defaultReportOptions;
  }
  
  public function getReportId()
  {
    if ($this->reportId)
    {
      return $this->reportId;
    }
    
    if (!$gaKey = dmConfig::get('ga_key'))
    {
      throw new dmGapiException('You must configure a ga_key in the configuration panel');
    }
    
    $start_index = 1;

    while($accounts = $this->requestAccountData($start_index++))
    {
      foreach($accounts as $account)
      {
        if ($account->getWebPropertyId() === $gaKey)
        {
          return $this->reportId = $account->getProfileId();
        }
      }
    }

    throw new dmGapiException('Current report not found for ga key : '.$gaKey);
  }
  
  public function setCacheManager(dmCacheManager $cacheManager)
  {
    $this->cacheManager = $cacheManager;
  }
  
  /**
   * Request account data from Google Analytics
   *
   * @param Int $start_index OPTIONAL: Start index of results
   * @param Int $max_results OPTIONAL: Max results returned
   */
  public function requestAccountData($start_index=1, $max_results=20)
  {
    if ($this->cacheManager)
    {
      $cacheKey = 'account-data-'.$start_index.'-to-'.$max_results;
      
      if ($this->cacheManager->getCache('gapi/request')->has($cacheKey))
      {
        return $this->cacheManager->getCache('gapi/request')->get($cacheKey);
      }
    }

    try
    {
      $result = parent::requestAccountData($start_index, $max_results);
    }
    catch(Exception $e)
    {
      throw new dmGapiException($e->getMessage());
    }
    
    if ($this->cacheManager)
    {
      $this->cacheManager->getCache('gapi/request')->set($cacheKey, $result);
    }
    
    return $result;
  }
  
  /**
   * Request report data from Google Analytics
   *
   * $report_id is the Google report ID for the selected account
   * 
   * $parameters should be in key => value format
   * 
   * @param String $report_id
   * @param Array $dimensions Google Analytics dimensions e.g. array('browser')
   * @param Array $metrics Google Analytics metrics e.g. array('pageviews')
   * @param Array $sort_metric OPTIONAL: Dimension or dimensions to sort by e.g.('-visits')
   * @param String $filter OPTIONAL: Filter logic for filtering results
   * @param String $start_date OPTIONAL: Start of reporting period
   * @param String $end_date OPTIONAL: End of reporting period
   * @param Int $start_index OPTIONAL: Start index of results
   * @param Int $max_results OPTIONAL: Max results returned
   */
  public function requestReportData($report_id, $dimensions, $metrics, $sort_metric=null, $filter=null, $start_date=null, $end_date=null, $start_index=1, $max_results=30)
  {
    if ($this->cacheManager)
    {
      $cacheKey = 'report-data-'.md5(serialize(func_get_args()));
      
      if ($this->cacheManager->getCache('gapi/request')->has($cacheKey))
      {
        return $this->cacheManager->getCache('gapi/request')->get($cacheKey);
      }
    }
    
    $result = parent::requestReportData($report_id, $dimensions, $metrics, $sort_metric, $filter, $start_date, $end_date, $start_index, $max_results);

    if ($this->cacheManager)
    {
      $this->cacheManager->getCache('gapi/request')->set($cacheKey, $result);
    }
    
    return $result;
  }
  
  /**
   * Authenticate Google Account with Google
   *
   * @param String $email
   * @param String $password
   */
  protected function authenticateUser($email, $password)
  {
    try
    {
      return parent::authenticateUser($email, $password);
    }
    catch(Exception $e)
    {
      throw new dmGapiException('GAPI: Failed to authenticate with email '.$email.'. Please configure email and password in the admin configuration panel');
    }
  }
  
  /**
   * Report Object Mapper to convert the XML to array of useful PHP objects
   *
   * @param String $xml_string
   * @return Array of gapiReportEntry objects
   */
  protected function reportObjectMapper($xml_string)
  {
    $xml = simplexml_load_string($xml_string);
    
    $this->results = null;
    $results = array();
    
    $report_root_parameters = array();
    $report_aggregate_metrics = array();
    
    //Load root parameters
    
    $report_root_parameters['updated'] = strval($xml->updated);
    $report_root_parameters['generator'] = strval($xml->generator);
    $report_root_parameters['generatorVersion'] = strval($xml->generator->attributes());
    
    $open_search_results = $xml->children('http://a9.com/-/spec/opensearchrss/1.0/');
    
    foreach($open_search_results as $key => $open_search_result)
    {
      $report_root_parameters[$key] = intval($open_search_result);
    }
    
    $google_results = $xml->children('http://schemas.google.com/analytics/2009');

    foreach($google_results->dataSource->property as $property_attributes)
    {
      $report_root_parameters[str_replace('ga:','',$property_attributes->attributes()->name)] = strval($property_attributes->attributes()->value);
    }
    
    $report_root_parameters['startDate'] = strval($google_results->startDate);
    $report_root_parameters['endDate'] = strval($google_results->endDate);
    
    //Load result aggregate metrics
    
    foreach($google_results->aggregates->metric as $aggregate_metric)
    {
      $metric_value = strval($aggregate_metric->attributes()->value);
      
      //Check for float, or value with scientific notation
      if(preg_match('/^(\d+\.\d+)|(\d+E\d+)|(\d+.\d+E\d+)$/',$metric_value))
      {
        $report_aggregate_metrics[str_replace('ga:','',$aggregate_metric->attributes()->name)] = floatval($metric_value);
      }
      else
      {
        $report_aggregate_metrics[str_replace('ga:','',$aggregate_metric->attributes()->name)] = intval($metric_value);
      }
    }
    
    //Load result entries
    
    foreach($xml->entry as $entry)
    {
      $metrics = array();
      foreach($entry->children('http://schemas.google.com/analytics/2009')->metric as $metric)
      {
        $metric_value = strval($metric->attributes()->value);
        
        //Check for float, or value with scientific notation
        if(preg_match('/^(\d+\.\d+)|(\d+E\d+)|(\d+.\d+E\d+)$/',$metric_value))
        {
          $metrics[str_replace('ga:','',$metric->attributes()->name)] = floatval($metric_value);
        }
        else
        {
          $metrics[str_replace('ga:','',$metric->attributes()->name)] = intval($metric_value);
        }
      }
      
      $dimensions = array();
      foreach($entry->children('http://schemas.google.com/analytics/2009')->dimension as $dimension)
      {
        $dimensions[str_replace('ga:','',$dimension->attributes()->name)] = strval($dimension->attributes()->value);
      }
      
      $results[] = new dmGapiReportEntry($metrics,$dimensions);
    }
    
    $this->report_root_parameters = $report_root_parameters;
    $this->report_aggregate_metrics = $report_aggregate_metrics;
    $this->results = $results;
    
    return $results;
  }
}