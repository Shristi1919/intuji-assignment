<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

session_start();

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);


$guzzleClient = new GuzzleHttp\Client(['verify' => false]);
$client->setHttpClient($guzzleClient);


if (!isset($_GET['code'])) {
    header('Location: index.php');
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$_SESSION['access_token'] = $token;

header('Location: events.php');
?>
