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

require_once('samples/.config.inc.php');
require_once('client.php');
require_once('/Model/listOrdersRequest.php');
require_once('/Model/listOrdersByNextTokenRequest.php');
require_once('/Model/listOrderItemsRequest.php');
require_once('/Model/listOrderItemsByNextTokenRequest.php');

$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
$request->setSellerId(MERCHANT_ID);
writeFileHeading();

listOrdersStartingAtDate('2016-07-23T00:00:00');


function setupService() {
// More endpoints are listed in the MWS Developer Guide
// North America:
$serviceUrl = "https://mws.amazonservices.com/Orders/2013-09-01";
// Europe
//$serviceUrl = "https://mws-eu.amazonservices.com/Orders/2013-09-01";
// Japan
//$serviceUrl = "https://mws.amazonservices.jp/Orders/2013-09-01";
// China
//$serviceUrl = "https://mws.amazonservices.com.cn/Orders/2013-09-01";
  //  $serviceUrl = "https://mws.amazonservices.com/Orders/2013-09-01";

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

function listOrdersStartingAtDate($date){

    $request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
    $request->setSellerId(MERCHANT_ID);
    $request->setMarketplaceId(MARKETPLACE_ID);
    //echo('MarketPlaceId ' . var_dump($request));

    $request->setCreatedAfter($date);
    //$request->setCreatedBefore($date);

    invokeListOrders(setupService(), $request);
}

function invokeListOrders(MarketplaceWebServiceOrders_Interface $service, $request){

    try {
        $response = $service->ListOrders($request);
        $result = $response->getListOrdersResult();
        $orders = $result->getOrders();
        processOrders($orders);

        $nextToken = $result->getNextToken();
        if($nextToken != null){
            listOrdersWithNextToken($nextToken);
        }

    } catch(MarketplaceWebServiceOrders_Exception $ex){
        showException($ex);
    }

}

function processOrders($orders){
    foreach ($orders as $order) {
        $orderId = $order->getAmazonOrderId();
        /* Set Address here? */
        
        $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
        $request->setSellerId(MERCHANT_ID);
        $request->setAmazonOrderId($orderId);

        invokeListOrderItems(setupService(), $request, $order);
    }
}

function invokeListOrderItems(MarketplaceWebServiceOrders_Interface $service, $request, $order){
    try{
        $response = $service->ListOrderItems($request);
        $result = $response->getListOrderItemsResult();
        $orderItems = $result->getOrderItems();
        writeOrderItemsToDisk($orderItems, $order);

        $nextToken = $result->getNextToken();
        if ($nextToken != null){
        listOrderItemsWithNextToken($nextToken, $order);
        }
       }catch (MarketplaceWebServiceOrders_Exception $ex) {
            showException($ex);
       }
}

function listOrdersWithNextToken($nextToken){
   $request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
  $request->setSellerId(MERCHANT_ID);
  $request->setNextToken($nextToken);

  invokelistOrdersByNextToken(setupService, $request);
}


function listOrderItemsWithNextToken($nextToken, $order){
    $request = new MarketplaceWebServiceOrders_Model_ListOrderItemsByNextTokenRequest();
    $request->setSellerId(MERCHANT_ID);
    $request->setNextToken($nextToken);

    invokeListOrderItemsByNextToken(setupService, $request, $order);
}



function invokelistOrdersByNextToken(MarketplaceWebServiceOrders_Interface $service, $request){

        try {

        $response = $service->ListOrdersByNextToken($request);

         $result = $response->getListOrdersByNextTokenResult();

         $orders = $result->getOrders();
         processOrders($orders);

         $nextToken = $result->getNextToken();
         if ($nextToken != null) {
           listOrdersWithNextToken($nextToken);
         }
      } catch (MarketplaceWebServiceOrders_Exception $ex) {
         showException($ex);
      }
}



function getAddress(MarketplaceWebServiceOrders_Model_Address $service)
  {
       $oAddress = $service->getName();
       $oAddress = $service->getAddressLine1();
       $oAddress = $service->getAddressLine2();


  }

function invokeListOrderItemsByNextToken(MarketplaceWebServiceOrders_Interface $service, $request, $order)
   {
       try {

         $response = $service->ListOrderItemsByNextToken($request);

         $result = $response->getListOrderItemsByNextTokenResult();

         $orderItems = $result->getOrderItems();
         writeOrderItemsToDisk($orderItems, $order);

         $nextToken = $result->getNextToken();
         if ($nextToken != null) {
           listOrderItemsWithNextToken($nextToken, $order);
         }
      } catch (MarketplaceWebServiceOrders_Exception $ex) {
        showException($ex);
      }
  }

  function showException($ex) {
      echo("Caught Exception: " . $ex->getMessage() . "\n");
      echo("Response Status Code: " . $ex->getStatusCode() . "\n");
      echo("Error Code: " . $ex->getErrorCode() . "\n");
      echo("Error Type: " . $ex->getErrorType() . "\n");
      echo("Request ID: " . $ex->getRequestId() . "\n");
      echo("XML: " . $ex->getXML() . "\n");
      echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "\n");
  }



  function writeFileHeading(){
            $fo = fopen('c:\wamp64\www\vendexcel.csv', 'a+');
            fwrite($fo, "AmazonOrderId" . "," ."SellerOrderId" . "," ."PurchaseDate" . "," ."LastUpdateDate" . "," ."OrderStatus" . "," ."FulfillmentChannel" . "," ."SalesChannel" . "," ."OrderChannel" . "," ."ShipServiceLevel" . "," ."ShippingAddress" . "," ."OrderTotal" . "," ."NumberOfItemsShipped" . "," ."NumberOfItemsUnshipped" . "," ."PaymentExecutionDetail" . "," ."PaymentMethod" . "," ."MarketplaceId" . "," ."BuyerEmail" . "," ."BuyerName" . "," ."ShipmentServiceLevelCategory" . "," ."ShippedByAmazonTFM" . "," ."TFMShipmentStatus" . "," ."CbaDisplayableShippingLabel" . "," ."OrderType" . "," ."EarliestShipDate" . "," ."LatestShipDate" . "," ."EarliestDeliveryDate" . "," ."LatestDeliveryDate" . "," ."IsBusinessOrder" . "," ."PurchaseOrderNumber" . "," ."IsPrime" . "," ."IsPremiumOrder" . "," . "ASIN" . "," . "SellerSKU" . "," . "OrderItemId" . "," . "Title" . "," . "QuantityOrdered" . "," . "QuantityShipped" . "," . "PointsGranted" . "," . "ItemPrice" . "," . "ShippingPrice" . "," . "GiftWrapPrice" . "," . "ItemTax" . "," . "ShippingTax" . "," . "GiftWrapTax" . "," . "ShippingDiscount" . "," . "PromotionDiscount" . "," . "PromotionIds" . "," . "CODFee" . "," . "CODFeeDiscount" . "," . "GiftMessageText" . "," . "GiftWrapLevel" . "InvoiceData" . "," . "ConditionNote" . "," . "ConditionId" . "," . "ConditionSubtypeId" . "ScheduledDeliveryStartDate" . "," . "ScheduledDeliveryEndDate" . "," . "PriceDesignation" . "," . "BuyerCustomizedInfo" . "\n");
            fclose($fo);
   }

  function writeOrderItemsToDisk($orderItems, $order) {
        foreach ($orderItems as $orderItem) {
            //$oAddress = new MarketplaceWebServiceOrders_Model_Address($order);
            //echo("THe address: " . $oAddress->getName() . "\n");
            $fo = fopen('c:\wamp64\www\vendexcel.csv', 'a+');
            fwrite($fo, $order->getAmazonOrderId() . " , " . $order->getSellerOrderId() . "," . $order->getPurchaseDate() . "," . $order->getLastUpdateDate() . "," . $order->getOrderStatus() . "," . $order->getFulfillmentChannel() . "," . $order->getSalesChannel() . "," . $order->getOrderChannel() . "," . $order->getShipServiceLevel() . "," . "Shipping Address" . "," . "Order Total" . "," . $order->getNumberOfItemsShipped() . "," . $order->getNumberofItemsUnshipped() . "," . $order->getPaymentExecutionDetail() . "," . $order->getPaymentMethod() . "," . $order->getMarketplaceId() . "," . $order->getBuyerEmail() . "," . $order->getBuyerName() . "," . $order->getShipmentServiceLevelCategory() . "," . $order->getShippedByAmazonTFM() . "," . $order->getTFMShipmentStatus() . "," . $order->getCbaDisplayableShippingLabel() . "," . $order->getOrderType() . "," . $order->getEarliestShipDate() . "," . $order->getLatestShipDate() . "," . $order->getEarliestDeliveryDate() . "," . $order->getLatestDeliveryDate() . "," . $order->getIsBusinessOrder() . "," . $order->getPurchaseOrderNumber() . "," . $order->getIsPrime() . "," . $order->getIsPremiumOrder() . "," . $orderItem->getASIN() . "," . $orderItem->getSellerSKU() . "," . $orderItem->getOrderItemId() . "," . $orderItem->getTitle() . "," . $orderItem->getQuantityOrdered()-> . "," . $orderItem->getQuantityShipped() . "," . "\n");
            fclose($fo);
            sleep(30);
            echo("Order: ".$order->getAmazonOrderId()." OrderItem: ".$orderItem->getTitle()."\n");
            echo("Number Of Items: " . $order->getNumberOfItemsShipped() . "\n");
            //echo("Shipping Address: " . $order->ShippingAddress->Name(0) . "\n");
            //echo("Var dump" . var_dump($order) . "\n");
        }
  }