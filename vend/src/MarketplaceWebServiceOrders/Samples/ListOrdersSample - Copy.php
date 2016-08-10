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

/************************************************************************
 * Instantiate Implementation of MarketplaceWebServiceOrders
 *
 * AWS_ACCESS_KEY_ID and AWS_SECRET_ACCESS_KEY constants
 * are defined in the .config.inc.php located in the same
 * directory as this sample
 ***********************************************************************/
// More endpoints are listed in the MWS Developer Guide
// North America:
$serviceUrl = "https://mws.amazonservices.com/Orders/2013-09-01";
// Europe
//$serviceUrl = "https://mws-eu.amazonservices.com/Orders/2013-09-01";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp/Orders/2013-09-01";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn/Orders/2013-09-01";


function getService() {
    $serviceUrl = "https://mws.amazonservices.com/Orders/2013-09-01";

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

/************************************************************************
 * Uncomment to try out Mock Service that simulates MarketplaceWebServiceOrders
 * responses without calling MarketplaceWebServiceOrders service.
 *
 * Responses are loaded from local XML files. You can tweak XML files to
 * experiment with various outputs during development
 *
 * XML files available under MarketplaceWebServiceOrders/Mock tree
 *
 ***********************************************************************/
 // $service = new MarketplaceWebServiceOrders_Mock();

/************************************************************************
 * Setup request parameters and uncomment invoke to try out
 * sample for List Orders Action
 ***********************************************************************/
 // @TODO: set request. Action can be passed as MarketplaceWebServiceOrders_Model_ListOrders
 $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
 $request->setSellerId(MERCHANT_ID);

  // List all orders udpated after a certain date
  $request->setCreatedAfter('2016-07-03T00:00:00');
  $request->setCreatedBefore('2016-07-04T00:00:00');
  // Set the marketplaces queried in this ListOrdersRequest
  $request->setMarketplaceId(MARKETPLACE_ID);

 // object or array of parameters
 invokeListOrders(getService(), $request);

 $nextTokenCounter = 0;
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

//        echo ("Service Response<br>");
//        echo ("=============================================================================<br>");

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = true;
        $str = $dom->saveXML();
        $nextToken = $dom->getElementsByTagName('NextToken')->item(0);
        $Orderstr = $dom->getElementsByTagName('AmazonOrderId')->item(0);
        $OrderId = $Orderstr->textContent;
        $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
        $request->setSellerId(MERCHANT_ID);
        $request->setAmazonOrderId($OrderId);
        invokeListOrderItems($service, $request);

        if ($nextToken != null) {
            $fo = fopen('c:\wamp64\www\vendexcel.txt', 'a+');
            fwrite($fo, $str."<br>");
            fclose($fo);
            echo("Next Token: ".$nextToken->nodeValue."<br>");
            listOrdersWithNextToken($nextToken);
        }

//        echo $dom->saveXML();
//        echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n</ br>");

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }
//put throttling here
 function listOrdersWithNextToken($nextToken) {
        global $nextTokenCounter;
        echo("next Token Counter" . $nextTokenCounter);

      $nextTokenString = $nextToken->nodeValue;
      if ($nextTokenString != null) {
            if($nextTokenCounter <= 4){
              $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
              $request->setSellerId(MERCHANT_ID);
              $request->setNextToken($nextToken->nodeValue);
                echo("next Token Counter inside loop" . $nextTokenCounter);
              // object or array of parameters
              invokeListOrdersByNextToken(getService(), $request);
            }
       if($nextTokenCounter > 4){
            sleep(60);
            $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
            $request->setSellerId(MERCHANT_ID);
            $request->setNextToken($nextToken->nodeValue);
            echo("next Token Counter inside loop" . $nextTokenCounter);
                          // object or array of parameters
            invokeListOrdersByNextToken(getService(), $request);
            }

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
        global $nextTokenCounter;
       try {
         $response = $service->ListOrdersByNextToken($request);

//         echo ("Service Response\n");
//         echo ("=============================================================================\n");

         $dom = new DOMDocument();
         $dom->loadXML($response->toXML());
         $dom->preserveWhiteSpace = false;
         $dom->formatOutput = true;
         $str = $dom->saveXML();
         $nextToken = $dom->getElementsByTagName('NextToken')->item(0);
         $Orderstr = $dom->getElementsByTagName('AmazonOrderId')->item(0);
                 $OrderId = $Orderstr->textContent;
         $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
                 $request->setSellerId(MERCHANT_ID);
                 $request->setAmazonOrderId($OrderId);
          invokeListOrderItems($service, $request);

         if ($nextToken != null) {
                      $fo = fopen('c:\wamp64\www\vendexcel.txt', 'a+');
                      fwrite($fo, $str . "<br>");
                      fclose($fo);
                      echo("Next Token: ".$nextToken->nodeValue."\n<br>\n");
                      $nextTokenCounter++;
                      listOrdersWithNextToken($nextToken);
                 }



//         echo $dom->saveXML();
//         echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

      } catch (MarketplaceWebServiceOrders_Exception $ex) {
         echo("Next Token call failed <br>");

         echo("Caught Exception: " . $ex->getMessage() . "\n");
         echo("Response Status Code: " . $ex->getStatusCode() . "\n");
         echo("Error Code: " . $ex->getErrorCode() . "\n");
         echo("Error Type: " . $ex->getErrorType() . "\n");
         echo("Request ID: " . $ex->getRequestId() . "\n");
         echo("XML: " . $ex->getXML() . "\n");
         echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
      }
  }

 function invokeListOrderItems(MarketplaceWebServiceOrders_Interface $service, $request)
  {
      try {
        $response = $service->ListOrderItems($request);

        echo ("Service Response\n");
        echo ("=============================================================================\n");

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $str = $dom->saveXML();
        $fo = fopen('c:\wamp64\www\vendOrder.txt', 'a+');
        fwrite($fo, $str  . "<br>");
        fclose($fo);


        echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }
 function invokeListOrderItemsByNextToken(MarketplaceWebServiceOrders_Interface $service, $request)
  {
      try {
        $response = $service->ListOrderItemsByNextToken($request);

        echo ("Service Response\n");
        echo ("=============================================================================\n");

        $dom = new DOMDocument();
        $dom->loadXML($response->toXML());
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        echo $dom->saveXML();
        echo("ResponseHeaderMetadata: " . $response->getResponseHeaderMetadata() . "\n");

     } catch (MarketplaceWebServiceOrders_Exception $ex) {
        echo("Caught Exception: " . $ex->getMessage() . "\n");
        echo("Response Status Code: " . $ex->getStatusCode() . "\n");
        echo("Error Code: " . $ex->getErrorCode() . "\n");
        echo("Error Type: " . $ex->getErrorType() . "\n");
        echo("Request ID: " . $ex->getRequestId() . "\n");
        echo("XML: " . $ex->getXML() . "\n");
        echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
     }
 }
