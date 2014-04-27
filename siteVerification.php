<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/config.php';

$key = file_get_contents(__DIR__.'/'.KEY_FILE_LOCATION);
$credentials = new Google_Auth_AssertionCredentials(
    CLIENT_EMAIL,
    array('https://www.googleapis.com/auth/siteverification'),
    $key
);

$client = new Google_Client();
$client->setApplicationName(APPLICATION_NAME);
$client->setAssertionCredentials($credentials);

$resource = new Google_Service_SiteVerification($client);

switch ($_GET['method']) {
    case 'getToken':
        $site = new Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequestSite();
        $site->setIdentifier($_GET['domain']);
        $site->setType('INET_DOMAIN');

        $request = new Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequest();
        $request->setSite($site);
        $request->setVerificationMethod('DNS_CNAME');

        $response = $resource->webResource->getToken($request);
        break;

    case 'insert':
        $site = new Google_Service_SiteVerification_SiteVerificationWebResourceResourceSite();
        $site->setIdentifier($_GET['domain']);
        $site->setType('INET_DOMAIN');

        $request = new Google_Service_SiteVerification_SiteVerificationWebResourceResource();
        $request->setSite($site);

        try {
            $response = $resource->webResource->insert('DNS_CNAME', $request);
        } catch (Google_Service_Exception $exception) {
            $response = array('error'=>$exception->getErrors()[0]['message']);
        }
        break;

    default:
        $response = array('error'=>'Invalid method specified.');
        break;
}

header('Content-type: application/json');
print json_encode($response);
