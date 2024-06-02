<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <title>Google Calendar Integration</title>
</head>

<body>
    <div class="container">
        <?php
        require_once 'vendor/autoload.php';
        require_once 'config.php';

        session_start();

        $client = new Google_Client();
        $client->setClientId(GOOGLE_CLIENT_ID);
        $client->setClientSecret(GOOGLE_CLIENT_SECRET);
        $client->setRedirectUri(GOOGLE_REDIRECT_URI);
        $client->addScope(Google_Service_Calendar::CALENDAR_READONLY);
        $client->addScope(Google_Service_Calendar::CALENDAR_EVENTS);

        $guzzleClient = new GuzzleHttp\Client(['verify' => false]);
        $client->setHttpClient($guzzleClient);

        if (isset($_GET['logout'])) {
            unset($_SESSION['access_token']);
            header('Location: index.php');
            exit;
        }

        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            if (isset($token['error'])) {
                echo 'Error: ' . $token['error'];
                exit;
            }
            $client->setAccessToken($token);
            $_SESSION['access_token'] = $token;
            header('Location: events.php');
            exit;
        }

        if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
            if (is_array($_SESSION['access_token']) && isset($_SESSION['access_token']['access_token'])) {
                $client->setAccessToken($_SESSION['access_token']);
                header('Location: events.php');
                exit;
            } else {
                unset($_SESSION['access_token']);
                echo 'Invalid token format. Please re-authenticate.';
            }
        } else {
            $authUrl = $client->createAuthUrl();
            echo "<h1>Assignment to integrate google Calender</h1>";
            echo "<br>";
            echo "<a href='$authUrl' class='btn btn-success'>Connect to Google Calendar</a>";
        }
        ?>
    </div>
</body>

</html>