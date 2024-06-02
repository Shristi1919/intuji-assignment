<?php
require_once '../corephp/eventlist.php';
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
    <link rel="stylesheet" href="../css/style.css">
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
    <a href="../index.php?logout" class="button button-logout">Logout</a>

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
                    <td><a href='../corephp/delete_event.php?id={$event->getId()}' class='delete-icon'><i class='fas fa-trash-alt'></i></a></td>
                  </tr>";
        }
        ?>
    </table>
</body>
</html>
