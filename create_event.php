<?php
require_once 'vendor/autoload.php';
require_once 'config.php';

session_start();

if (!isset($_SESSION['access_token']) || !$_SESSION['access_token']) {
    header('Location: index.php');
    exit;
}

$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="//cdn.jsdelivr.net/npm/jquery-datetimepicker@2.5.20/build/jquery.datetimepicker.full.min.js"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/jquery-datetimepicker@2.5.20/build/jquery.datetimepicker.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"] {
            padding: 8px;
            width: 30%;
            margin-bottom: 20px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #4285F4;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
        .backbutton {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: green;
            border: none;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
            cursor: pointer;
        }
        .button-back {
            background-color: #DB4437;
        }
        .error-message {
            color: red;
            margin-bottom: 20px;
        }
    </style>
    <script src="script.js"></script>
</head>
<body>
    <h1>Create Event</h1>
    <a href="events.php" class='backbutton button-logout'>Back to Events</a>
    <form method="post">
        <label for="summary">Summary:</label>
        <input type="text" id="summary" name="summary">
        <label for="start">Start DateTime (YYYY-MM-DDTHH:MM:SS±HH:MM):</label>
        <input type="text" id="start" name="start" readonly>
        <label for="end">End DateTime (YYYY-MM-DDTHH:MM:SS±HH:MM):</label>
        <input type="text" id="end" name="end" readonly> <br>
        <button type="submit" class="button">Create Event</button>
    </form>
</body>
</html>
