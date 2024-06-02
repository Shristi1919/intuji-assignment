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
        header('Location: index.php');
        exit;
    }
}

$service = new Google_Service_Calendar($client);
$calendarId = 'primary';
$events = $service->events->listEvents($calendarId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Calendar Events</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        h1 {
            color: #333;
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
            margin: 10px 0;
        }
        .button-logout {
            background-color: #DB4437;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .delete-icon {
            color: #DB4437;
            cursor: pointer;
        }
    </style>
    <script>
        $(document).ready(function() {
            <?php if (isset($_GET['created']) && $_GET['created'] == 'true') : ?>
                toastr.success('Event created successfully!');
                setTimeout(function() {
                    window.location.href = 'events.php';
                }, 5);  
            <?php endif; ?>
        });

        $(document).ready(function() {
            <?php if (isset($_GET['deleted']) && $_GET['deleted'] == 'true') : ?>
                toastr.error('Event successfully Deleted!');
                setTimeout(function() {
                    window.location.href = 'events.php';
                }, 5); 
            <?php endif; ?>
        });
    </script>
</head>
<body>
    <h1>Google Calendar Events</h1>
    <a href="create_event.php" class="button">Create Event</a>
    <a href="index.php?logout" class="button button-logout">Logout</a>

    <table>
        <tr>
            <th>Event Summary</th>
            <th>Start Date/Time</th>
            <th>Actions</th>
        </tr>
        <?php
        foreach ($events->getItems() as $event) {
            $start = $event->getStart()->getDateTime();
            $startDate = $start ? $start : $event->getStart()->getDate(); // If no time is set, get the date.
            echo "<tr>
                    <td>{$event->getSummary()}</td>
                    <td>{$startDate}</td>
                    <td><a href='delete_event.php?id={$event->getId()}' class='delete-icon'><i class='fas fa-trash-alt'></i></a></td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>
