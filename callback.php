<?php
require_once 'vendor/autoload.php';
require_once 'corephp/config.php';

session_start();

require_once 'corephp/googleclientconfig.php';



$guzzleClient = new GuzzleHttp\Client(['verify' => false]);
$client->setHttpClient($guzzleClient);


if (!isset($_GET['code'])) {
    header('Location: index.php');
    exit;
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
$_SESSION['access_token'] = $token;

header('Location: view/events.php');
?>
