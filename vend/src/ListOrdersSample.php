<?php
/*******************************************************************************
 * Copyright 2009-2015 Amazon Services. All Rights Reserved.
 * Licensed under the Apache License, Version 2.0 (the "License");
 *
 * You may not use this file except in compliance with the License.
 * You may obtain a copy of the License at: http://aws.amazon.com/apache2.0
 * This file is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR
 * CONDITIONS OF ANY KIND, either express or implied. See the License for the
 * specific language governing permissions and limitations under the License.
 *******************************************************************************
 * PHP Version 5
 * @category Amazon
 * @package  Marketplace Web Service Orders
 * @version  2013-09-01
 * Library Version: 2015-09-24
 * Generated: Fri Sep 25 20:06:28 GMT 2015
 */

/**
 * List Orders Sample
 */

require_once('.config.inc.php');
require_once('Client.php');
require_once('Model/ListOrdersRequest.php');
require_once('Model/ListOrdersByNextTokenRequest.php');

/************************************************************************
 * Instantiate Implementation of MarketplaceWebServiceOrders
 *
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
 * are defined in the .config.inc.php located in the same
 * directory as this sample
 ***********************************************************************/
// More endpoints are listed in the MWS Developer Guide

// start
listOrdersStartingAtDate('2016-06-22');

function setupService() {
  // North America:
  $serviceUrl = "https://mws.amazonservices.com/Orders/2013-09-01";
  // Europe
  //$serviceUrl = "https://mws-eu.amazonservices.com/Orders/2013-09-01";
  // Japan
  //$serviceUrl = "https://mws.amazonservices.jp/Orders/2013-09-01";
  // China
  //$serviceUrl = "https://mws.amazonservices.com.cn/Orders/2013-09-01";

  $config = array (
    'ServiceURL' => $serviceUrl,
    'ProxyHost' => null,
    'ProxyPort' => -1,
    'ProxyUsername' => null,
    'ProxyPassword' => null,
    'MaxErrorRetry' => 3,
  );

  $service = new MarketplaceWebServiceOrders_Client(
         AWS_ACCESS_KEY_ID,
         AWS_SECRET_ACCESS_KEY,
         APPLICATION_NAME,
         APPLICATION_VERSION,
         $config);

  return $service;
}

function listOrdersStartingAtDate($date) {
  $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
  $request->setSellerId(MERCHANT_ID);
  $request->setMarketplaceId(MARKETPLACE_ID);
  $request->setCreatedAfter($date);

  invokeListOrders(setupService(), $request);
}

function listOrdersWithNextToken($nextToken) {
  if ($nextToken == null || $nextToken->nodeValue == null) {
    return;
  }

  $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
  $request->setSellerId(MERCHANT_ID);
  $request->setNextToken($nextToken->nodeValue);

  invokeListOrdersByNextToken(setupService(), $request);
}

/**
  * Get List Orders Action Sample
  * Gets competitive pricing and related information for a product identified by
  * the MarketplaceId and ASIN.
  *
  * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
  * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrders or array of parameters
  */

  function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request)
  {
      try {
        $response = $service->ListOrders($request);

        echo ("\nService Response\n");
        echo ("=============================================================================\n");

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        echo $dom->saveXML();
        echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

        $nextToken = $dom->getElementsByTagName('NextToken')->item(0);
        listOrdersWithNextToken($nextToken);

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
        echo("\nCaught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }

 /**
   * Get List Orders By Next Token Action Sample
   * Gets competitive pricing and related information for a product identified by
   * the MarketplaceId and ASIN.
   *
   * @param MarketplaceWebServiceOrders_Interface $service instance of MarketplaceWebServiceOrders_Interface
   * @param mixed $request MarketplaceWebServiceOrders_Model_ListOrdersByNextToken or array of parameters
   */

   function invokeListOrdersByNextToken(MarketplaceWebServiceOrders_Interface $service, $request)
   {
       try {
         $response = $service->ListOrdersByNextToken($request);

         echo ("\nService Response\n");
         echo ("=============================================================================\n");

         $dom = new DOMDocument();
         $dom->loadXML($response->toXML());
         $dom->preserveWhiteSpace = false;
         $dom->formatOutput = true;
         echo $dom->saveXML();
         echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

         $nextToken = $dom->getElementsByTagName('NextToken')->item(0);
         listOrdersWithNextToken($nextToken);

      } catch (MarketplaceWebServiceOrders_Exception $ex) {
         echo("\nCaught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
      }
  }
