<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

session_start();

if (!isset($_SESSION['access_token']) || !$_SESSION['access_token']) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: events.php');
    exit;
}

$client = new Google_Client();
$client->setAccessToken($_SESSION['access_token']);
$guzzleClient = new GuzzleHttp\Client(['verify' => false]);
$client->setHttpClient($guzzleClient);
$service = new Google_Service_Calendar($client);

$eventId = $_GET['id'];
$service->events->delete('primary', $eventId);

header('Location: events.php?deleted=true');
exit;
?>
