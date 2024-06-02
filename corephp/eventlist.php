<?php 
require_once '../vendor/autoload.php';
require_once 'config.php';

session_start();

if (!isset($_SESSION['access_token']) || !$_SESSION['access_token']) {
    header('Location: index.php');
    exit;
}

require_once 'googleclientconfig.php';


$client->setAccessToken($_SESSION['access_token']);

$guzzleClient = new GuzzleHttp\Client(['verify' => false]);
$client->setHttpClient($guzzleClient);

// Refresh the token if it's expired.
if ($client->isAccessTokenExpired()) {
    $refreshToken = $client->getRefreshToken();
    if ($refreshToken) {
        $client->fetchAccessTokenWithRefreshToken($refreshToken);
        $_SESSION['access_token'] = $client->getAccessToken();
    } else {
        header('Location: index.php');
        exit;
    }
}

$service = new Google_Service_Calendar($client);
$calendarId = 'primary';
$events = $service->events->listEvents($calendarId);

?>