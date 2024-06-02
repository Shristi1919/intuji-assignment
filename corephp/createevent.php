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
        // If no refresh token is available, force the user to re-authenticate.
        header('Location: index.php');
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $service = new Google_Service_Calendar($client);

    $event = new Google_Service_Calendar_Event(array(
        'summary' => $_POST['summary'],
        'start' => array(
            'dateTime' => $_POST['start'],
            'timeZone' => 'America/Los_Angeles',
        ),
        'end' => array(
            'dateTime' => $_POST['end'],
            'timeZone' => 'America/Los_Angeles',
        ),
    ));

    $calendarId = 'primary';
    $event = $service->events->insert($calendarId, $event);
    header('Location: events.php?created=true');
    exit;
}
?>