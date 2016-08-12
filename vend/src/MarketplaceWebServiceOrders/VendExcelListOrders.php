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
            fwrite($fo, "Amazon Order Id" . ","
            ."Seller Order Id" . ","
            ."Purchase Date" . ","
            ."Last Update Date" . ","
            ."Order Status" . ","
            ."Fulfillment Channel" . ","
            ."Sales Channel" . ","
            ."Order Channel" . ","
            ."Ship Service Level" . ","
            ."Name" . ","
            ."Address Line 1" . ","
            ."Address Line 2" . ","
            ."Address Line 3" . ","
            ."City" . ","
            ."County" . ","
            ."District" . ","
            ."State Or Region" . ","
            ."Postal Code" . ","
            ."Country Code". ","
            ."Phone" . ","
            ."Order Total" . ","
            ."Number Of Items Shipped" . ","
            ."Number Of Items Unshipped" . ","
            ."Payment Execution Detail" . ","
            ."Payment Method" . ","
            ."Marketplace Id" . ","
            ."Buyer Email" . ","
            ."Buyer Name" . ","
            ."Shipment Service Level Category" . ","
            ."Shipped By Amazon TFM" . ","
            ."TFM Shipment Status" . ","
            ."Cba Displayable Shipping Label" . ","
            ."Order Type" . ","
            ."Earliest Ship Date" . ","
            ."Latest Ship Date" . ","
            ."Earliest Delivery Date" . ","
            ."Latest Delivery Date" . ","
            ."Is Business Order" . ","
            ."Purchase Order Number" . ","
            ."Is Prime" . ","
            ."Is Premium Order" . ","
            . "ASIN" . ","
            . "Seller SKU" . ","
            . "Order Item Id" . ","
            . "Title" . ","
            . "Quantity Ordered" . ","
            . "Quantity Shipped" . ","
            . "Points Granted" . ","
            . "Points Granted Amount" . ","
            . "Points Granted Monetary Value"
            . "Item Price" . ","
            . "Shipping Price" . ","
            . "GiftWrap Price" . ","
            . "Item Tax" . ","
            . "Shipping Tax" . ","
            . "GiftWrap Tax" . ","
            . "Shipping Discount" . ","
            . "Promotion Discount" . ","
            . "Promotion Ids" . ","
            . "COD Fee" . ","
            . "COD Fee Discount" . ","
            . "Gift Message Text" . ","
            . "GiftWrap Level" . ","
            . "Invoice Requirement" . ","
            . "Buyer Selected Inventory Category" . ","
            . "Invoice Title" . ","
            . "Invoice Information" . ","
            . "Condition Note" . ","
            . "Condition Id" . ","
            . "Condition Subtype Id" . ","
            . "Scheduled Delivery Start Date" . ","
            . "Scheduled Delivery End Date" . ","
            . "Price Designation" . ","
            . "Buyer Customized Info" . "\n");
            fclose($fo);
   }

  function writeOrderItemsToDisk($orderItems, $order) {
          foreach ($orderItems as $orderItem) {

              $payload = $order->getAmazonOrderId() . " , "
              . $order->getSellerOrderId() . ","
              . $order->getPurchaseDate() . ","
              . $order->getLastUpdateDate() . ","
              . $order->getOrderStatus() . ","
              . $order->getFulfillmentChannel() . ","
              . $order->getSalesChannel() . ","
              . $order->getOrderChannel() . ","
              . $order->getShipServiceLevel() . ",";

               if($order->getShippingAddress()->getName())
                {
                    $payload .= $order->getShippingAddress()->getName() . ",";
                }else
                {
                    $payload .= ",";
                }
                if($order->getShippingAddress()->getAddressLine1())
                {
                    $payload .= $order->getShippingAddress()->getAddressLine1() . ",";
                }else
                {
                    $payload .= ",";
                }
                if($order->getShippingAddress()->getAddressLine2())
                {
                    $payload .= $order->getShippingAddress()->getAddressLine2() . ",";
                }else
                {
                    $payload .= ",";
                }

                if($order->getShippingAddress()->getAddressLine3()){
                    $payload .= $order->getShippingAddress()->getAddressLine3() . ",";
                }else
                {
                    $payload .= ",";
                }
                if($order->getShippingAddress()->getCity()){
                    $payload .= $order->getShippingAddress()->getCity() . ",";
               }else{
                    $payload .= ",";
                    }
                if($order->getShippingAddress()->getCounty()){
                    $payload .= $order->getShippingAddress()->getCounty() . ",";
                 }else
                 {
                    $payload .= ",";
                 }
                if($order->getShippingAddress()->getDistrict()){
                    $payload .= $order->getShippingAddress()->getDistrict() . ",";
                }else
                {
                    $payload .= ",";
                }
                if($order->getShippingAddress()->getStateOrRegion()){
                    $payload .= $order->getShippingAddress()->getStateOrRegion() . ",";
                 }else
                 {
                    $payload .= ",";
                 }
                if($order->getShippingAddress()->getPostalCode()){
                    $payload .= $order->getShippingAddress()->getPostalCode() . ",";
                }else
                {
                    $payload .= ",";
                }

                if($order->getShippingAddress()->getCountryCode()){
                    $payload .= $order->getShippingAddress()->getCountryCode() . ",";
                }
              if($order->getShippingAddress()->getPhone()){
                    $payload .= $order->getShippingAddress()->getPhone() . ",";
              }else
              {
                    $payload .= ",";
              }
                if($order->getOrderTotal()->getAmount()){
                    $payload .= $order->getOrderTotal()->getAmount() . ",";
                }else{
                    $payload .= ",";
                }
                $payload .= $order->getNumberOfItemsShipped() . ","
              . $order->getNumberofItemsUnshipped() . ",";

              if ($order->getPaymentExecutionDetail()){
                 $payload .= $order->getPaymentExecutionDetail()->getPayment() . ","
               . $payload .= $order->getPaymentExecutionDetail()->getPaymentMethod() . ",";
              }else
              {
                    $payload .= ",";
              }
              $payload .= $order->getPaymentMethod() . ","
              . $order->getMarketplaceId() . ","
              . $order->getBuyerEmail() . ","
              . $order->getBuyerName() . ","
              . $order->getShipmentServiceLevelCategory() . ","
              . $order->getShippedByAmazonTFM() . ","
              . $order->getTFMShipmentStatus() . ","
              . $order->getCbaDisplayableShippingLabel() . ","
              . $order->getOrderType() . ","
              . $order->getEarliestShipDate() . ","
              . $order->getLatestShipDate() . ","
              . $order->getEarliestDeliveryDate() . ","
              . $order->getLatestDeliveryDate() . ","
              . $order->getIsBusinessOrder() . ","
              . $order->getPurchaseOrderNumber() . ","
              . $order->getIsPrime() . ","
              . $order->getIsPremiumOrder() . ","
              . $orderItem->getASIN() . ","
              . $orderItem->getSellerSKU() . ","
              . $orderItem->getOrderItemId() . ","
              . $orderItem->getTitle() . ","
              . $orderItem->getQuantityOrdered() . ","
              . $orderItem->getQuantityShipped() . ","
              . $orderItem->getPointsGranted() . ","
              . $orderItem->getPointsGranted() . ",";
              if($orderItem->getItemPrice()->getAmount()){
                $payload .= $orderItem->getItemPrice()->getAmount() . ",";
              }else
              {
                $payload .= ",";
              }
              /*if($orderItem->getShippingPrice()->getAmount()){
                              $payload .= $orderItem->getShippingPrice()->getAmount() . ",";
                            }else
                            {
                              $payload .= ",";
                            }

*/
              $payload .= $orderItem->getGiftWrapPrice() . ","
              . $orderItem->getItemTax()->getAmount() . ","
             /* . $orderItem->getShippingTax()->getAmount() . "."*/
              . $orderItem->getGiftWrapTax() . ","
             /* . $orderItem->getShippingDiscount()->getAmount() . ","*/
              . $orderItem->getPromotionDiscount()->getAmount() . ",";

               if($orderItem->getPromotionIds()){
                    $payload .= $orderItem->getPromotionIds() . ".";
               }else
               {
                    $payload .= ",";
               }

              $payload .= $orderItem->getCODFee() . ","
              . $orderItem->getCODFeeDiscount() . ","
              . $orderItem->getGiftMessageText() . "."
              . $orderItem->getGiftWrapLevel() . ",";


              if ($orderItem->getInvoiceData()) {
                  $payload .= $orderItem->getInvoiceData()->getInvoiceRequirement() . ","
              . $orderItem->getInvoiceData()->getBuyerSelectedInvoiceCategory() . ","
              . $orderItem->getInvoiceData()->getInvoiceTitle() . ","
              . $orderItem->getInvoiceData()->getInvoiceInformation() . ",";
              } else {
                  $payload .= ",,,,";
              }
              $payload .= $orderItem->getConditionNote() . ","
              . $orderItem->getConditionId() . ","
              . $orderItem->getConditionSubtypeId() . ","
              . $orderItem->getScheduledDeliveryStartDate() . ","
              . $orderItem->getScheduledDeliveryEndDate() . "."
              . $orderItem->getPriceDesignation() . "."
              . $orderItem->getBuyerCustomizedInfo() . ","
              . "\n";

              $fo = fopen('c:\wamp64\www\vendexcel.csv', 'a+');
              fwrite($fo, $payload);
              fclose($fo);
              sleep(30);
              echo("Order: ".$order->getAmazonOrderId()." OrderItem: ".$orderItem->getTitle()."\n");


          }
    }