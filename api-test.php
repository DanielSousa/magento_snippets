<?php
// Set the user name here:
$apiUser = '****';
// Set the password here:
$apiKey = '*****';
// Set the url here:
$url = 'https://domain.com/api/soap/';
// Set the Magento store code here:
$magentoStoreCode = 'default';
// Create new SOAP client
$soapClient = new \SoapClient(
    $url . '?wsdl',
    array(
        'trace' => true,
        'exceptions' => true,
        'cache_wsdl' => WSDL_CACHE_BOTH,
        'keep_alive' => true,
    ));
// Login to Magento API and get session ID
$sessionId = $soapClient->login($apiUser, $apiKey);
try {
    // Call method and capture result
    $result = $soapClient->call($sessionId, 'cart.create', array(
        $magentoStoreCode,
    ));
    echo $result;
    echo 'Success!';
    echo "\n\n";
}
catch(\SoapFault $e) {
    // Output result
    echo 'SOAP request headers: ' . "\n";
    echo $soapClient->__getLastRequestHeaders() . "\n";
    echo 'SOAP request body: ' . "\n";
    echo $soapClient->__getLastRequest() . "\n";
    echo 'SOAP response headers: ' . "\n";
    echo $soapClient->__getLastResponseHeaders() . "\n";
    echo 'SOAP response body: ' . "\n";
    echo $soapClient->__getLastResponse() . "\n";
    echo "\n";
}
