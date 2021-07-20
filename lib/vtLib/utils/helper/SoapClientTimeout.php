<?php
/**
 * Created by PhpStorm.
 * User: tiennx6
 * Date: 19/02/2016
 * Time: 9:00 SA
 */

class SoapClientTimeout extends SoapClient
{
  private $timeout;
  private $connectTimeout;

  public function __setConnectTimeout($connectTimeout)
  {
    if (!is_int($connectTimeout) && !is_null($connectTimeout))
    {
      throw new Exception("Invalid connect timeout value");
    }

    $this->connectTimeout = $connectTimeout;
  }

  public function __setTimeout($timeout)
  {
    if (!is_int($timeout) && !is_null($timeout))
    {
      throw new Exception("Invalid timeout value");
    }

    $this->timeout = $timeout;
  }

  public function __doRequest($request, $location, $action, $version, $one_way = FALSE)
  {
    if (!$this->timeout)
    {
      // Call via parent because we require no timeout
      $response = parent::__doRequest($request, $location, $action, $version, $one_way);
    }
    else
    {
      // Call via Curl and use the timeout
      $curl = curl_init($location);

      curl_setopt($curl, CURLOPT_VERBOSE, FALSE);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($curl, CURLOPT_POST, TRUE);
      curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
      curl_setopt($curl, CURLOPT_HEADER, FALSE);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-Type: text/xml"));
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
      curl_setopt($curl, CURLOPT_TIMEOUT, $this->timeout);

      $response = curl_exec($curl);

      if (curl_errno($curl))
      {
        throw new Exception(curl_error($curl));
      }

      curl_close($curl);
    }

    // Return?
    if (!$one_way)
    {
      return ($response);
    }
  }
}