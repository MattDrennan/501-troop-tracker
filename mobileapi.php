<?php

/**
 * API for mobile app troop data
 * @author Matthew Drennan
 */

// Include config
include "config.php";

// Set JSON response header
header('Content-Type: application/json');

// Initialize response data
$data = new stdClass();

try {
    // Get troops for trooper
    if (isset($_GET['user_id'], $_GET['action']) && $_GET['action'] === 'troops') {
        // Prepare SQL query
        $sql = "
            SELECT 
                events.id AS id, 
                events.name, 
                events.location, 
                events.dateStart, 
                events.dateEnd, 
                events.squad, 
                events.thread_id, 
                events.post_id, 
                event_sign_up.id AS signupId, 
                event_sign_up.troopid, 
                event_sign_up.trooperid, 
                events.link, 
                events.limit501st, 
                events.limitTotalTroopers, 
                events.limitHandlers, 
                events.closed,
                troopers.user_id
            FROM 
                events 
            LEFT JOIN 
                event_sign_up 
            ON 
                event_sign_up.troopid = events.id 
          	LEFT JOIN
          		troopers
      		ON
      			event_sign_up.trooperid = troopers.id
            WHERE 
                troopers.user_id = ? 
                AND events.dateEnd > NOW() - INTERVAL 1 DAY 
                AND (event_sign_up.status < 3 OR event_sign_up.status = 2) 
                AND (events.closed = 0 OR events.closed = 3 OR events.closed = 4) 
            ORDER BY 
                events.dateStart";

        // Prepare statement
        $statement = $conn->prepare($sql);
        if (!$statement) {
            throw new Exception('Database error: ' . $conn->error);
        }

        // Bind parameters
        $statement->bind_param("i", $_GET['user_id']);
        $statement->execute();

        // Process query results
        $result = $statement->get_result();
        while ($db = $result->fetch_object()) {
            $tempObject = new stdClass();
            $tempObject->troopid = $db->id;
            $tempObject->name = $db->name;
            $tempObject->dateStart = $db->dateStart;
            $tempObject->dateEnd = $db->dateEnd;
            $tempObject->location = $db->location;
            $tempObject->thread_id = $db->thread_id;
            $tempObject->post_id = $db->post_id;

            $data->troops[] = $tempObject;
        }

        // Close resources
        $statement->close();
    // Get event
    } else if (isset($_GET['troopid'], $_GET['action']) && $_GET['action'] === 'event') {
        // Prepare SQL query
        $sql = "
            SELECT 
                *
            FROM 
                events 
            WHERE 
                id = ?";

        // Prepare statement
        $statement = $conn->prepare($sql);
        if (!$statement) {
            throw new Exception('Database error: ' . $conn->error);
        }

        // Bind parameters
        $statement->bind_param("i", $_GET['troopid']);
        $statement->execute();

        // Process query results
        $result = $statement->get_result();
        while ($db = $result->fetch_object()) {
            $tempObject = new stdClass();

            // Dynamically assign all columns to $data
            foreach ($db as $key => $value) {
                $data->$key = $value; // Equivalent to $data->name = $db->name;
            }
        }

        // Close resources
        $statement->close();
        // Get roster for event
    } else if (isset($_GET['troopid'], $_GET['action']) && $_GET['action'] === 'get_roster_for_event') {
        // Prepare SQL query with JOINs
        $sql = "
            SELECT 
                esu.*,
                c.costume AS costume_name,
                b.costume AS backup_costume_name,
                t.name AS trooper_name,
                t.forum_id AS forum_id,
                t.name AS trooper_name,
                t.tkid AS tkid,
                t.squad AS squad
            FROM 
                event_sign_up esu
            LEFT JOIN 
                costumes c ON esu.costume = c.id
            LEFT JOIN 
                costumes b ON esu.costume_backup = b.id
            LEFT JOIN 
                troopers t ON esu.trooperid = t.id
            WHERE 
                esu.troopid = ?
        ";

        // Prepare statement
        $statement = $conn->prepare($sql);
        if (!$statement) {
            throw new Exception('Database error: ' . $conn->error);
        }

        // Bind parameters
        $statement->bind_param("i", $_GET['troopid']);
        $statement->execute();

        // Process query results
        $result = $statement->get_result();

        // Initialize an array to store all results
        $data = [];

        while ($db = $result->fetch_object()) {
            $tempObject = new stdClass();

            // Dynamically assign all columns to $tempObject
            foreach ($db as $key => $value) {
                $tempObject->$key = $value;
            }

            // Get formatted TK Number
            $tempObject->tkid_formatted = readTKNumber($tempObject->tkid, $tempObject->squad, $tempObject->trooperid);

            // Get status
            $tempObject->status_formatted = getStatus($tempObject->status);

            $data[] = $tempObject; // Add the object to the results array
        }

        // Close resources
        $statement->close();
    } else if (isset($_GET['squad'], $_GET['action']) && $_GET['action'] === 'get_troops_by_squad') {
        // Prepare SQL query
        $sql = "
        SELECT 
            e.*, 
            COUNT(CASE WHEN es.status = '0' OR es.status = '2' THEN es.trooperid END) AS trooper_count,
            (
                SELECT 
                    COUNT(*) 
                FROM 
                    event_sign_up es2
                WHERE 
                    (es2.status = '0' OR es2.status = '2') 
                    AND es2.troopid = e.id 
                    AND (SELECT costume FROM costumes WHERE id = es2.costume) LIKE '%handler%'
            ) AS num_of_handlers
        FROM 
            events e
        LEFT JOIN 
            event_sign_up es ON e.id = es.troopid
        WHERE 
            e.dateStart >= CURDATE()
            AND (e.squad = ? OR ? = 0)
            AND (e.closed = '0' OR e.closed = '3' OR e.closed = '4')
        GROUP BY 
            e.id
        ORDER BY 
            e.dateStart;";

        // Prepare statement
        $statement = $conn->prepare($sql);
        if (!$statement) {
            throw new Exception('Database error: ' . $conn->error);
        }

        // Bind parameters
        $statement->bind_param("ii", $_GET['squad'], $_GET['squad']);
        $statement->execute();

        // Process query results
        $result = $statement->get_result();
        while ($db = $result->fetch_object()) {
            $tempObject = new stdClass();
            $tempObject->troopid = $db->id;
            $tempObject->name = $db->name;
            $tempObject->dateStart = $db->dateStart;
            $tempObject->dateEnd = $db->dateEnd;
            $tempObject->location = $db->location;
            $tempObject->thread_id = $db->thread_id;
            $tempObject->post_id = $db->post_id;
            $tempObject->squad = $db->squad;
            $tempObject->notice = '';

            // Event Sign Up
            $tempObject->trooper_count = $db->trooper_count;
            $tempObject->num_of_handlers = $db->num_of_handlers;

            // Return special notifications on event
            // Set total
            $limitTotal = $db->limit501st;

            // Loop through clubs
            foreach($clubArray as $club => $club_value)
            {
                // Add
                $limitTotal += $db->{$club_value['dbLimit']};
            }

            // Check for total limit set, if it is, replace limit with it
            if($db->limitTotalTroopers > 500 || $db->limitTotalTroopers < 500)
            {
                $limitTotal = $db->limitTotalTroopers;
            }

            // Troop set to full
            if($db->closed == 4) {
                $tempObject->notice = 'THIS TROOP IS FULL!';      
            } else if($db->trooper_count <= 1) {   // If not enough troopers
                $tempObject->notice = 'NOT ENOUGH TROOPERS FOR THIS EVENT!';  
            } else if(($db->trooper_count - handlerEventCount($db->id)) >= $limitTotal && ($db->limitHandlers > 500 || $db->limitHandlers < 500) && (handlerEventCount($db->id) >= $db->limitHandlers)) {    // If full (w/ handlers)
                $tempObject->notice = 'THIS TROOP IS FULL!';
            } else if(($db->trooper_count - handlerEventCount($db->id)) >= $limitTotal && $db->limitHandlers == 500) {   // If full
                // Check handler count
                if($db->limitHandlers == 500) {
                    $tempObject->notice = 'THIS TROOP IS FULL!';
                } else {
                    // Check if handlers full
                    if($db->num_of_handlers >= $db->limitHandlers) {
                        $tempObject->notice = 'THIS TROOP IS FULL!';
                    } else {
                        $tempObject->notice = '';
                    }
                }
            }

            $data->troops[] = $tempObject;
        }

        // Close resources
        $statement->close();
    // Get Squad/Club Name
    } else if(isset($_GET['squad'], $_GET['action']) && $_GET['action'] === 'get_squad_club_name') {
        $data->squadName = getSquadName($_GET['squad']);

        // Add squad names
        foreach ($squadArray as $squad) {
            $data->squadNames[] = $squad['name'];
        }

        // Add club names
        foreach ($clubArray as $club) {
            $data->clubNames[] = $club['name'];
        }
    // Save FCM
    } else if(isset($_POST['userid'], $_POST['fcm'], $_POST['action']) && $_POST['action'] === 'saveFCM') {
        // Get values
        $userId = $_POST['userid'];
        $fcmToken = $_POST['fcm'];

        // Prepare SQL query
        $sql = "
            SELECT 
                id 
            FROM 
                mobile_app 
            WHERE 
                userid = ? AND fcm = ?";

        // Prepare statement
        $statement = $conn->prepare($sql);
        if (!$statement) {
            throw new Exception('Database error: ' . $conn->error);
        }

        // Bind parameters
        $statement->bind_param("is", $userId, $fcmToken);
        $statement->execute();

        // Process query results
        $result = $statement->get_result();
        if ($result->num_rows === 0) {
            // Record does not exist, insert a new one
            $insertSql = "
                INSERT INTO mobile_app (userid, fcm) 
                VALUES (?, ?)";
            
            $insertStatement = $conn->prepare($insertSql);
            if (!$insertStatement) {
                throw new Exception('Database error: ' . $conn->error);
            }

            $insertStatement->bind_param("is", $userId, $fcmToken);
            $insertStatement->execute();
            $insertStatement->close();

            http_response_code(200); // Success
            echo json_encode(['success' => 'Record created!']);
            exit();
        } else {
            http_response_code(200); // Success
            echo json_encode(['success' => 'Record exists!']);
            exit();
        }

        // Close resources
        $statement->close();
    // Logout (FCM)
    } else if(isset($_POST['fcm'], $_POST['action']) && $_POST['action'] === 'logoutFCM') {
        // Get values
        $fcmToken = $_POST['fcm'];

        // Prepare SQL query
        $sql = "
            DELETE 
            FROM 
                mobile_app 
            WHERE 
                fcm = ?";

        // Prepare statement
        $statement = $conn->prepare($sql);
        if (!$statement) {
            throw new Exception('Database error: ' . $conn->error);
        }

        // Bind parameters
        $statement->bind_param("s", $fcmToken);
        $statement->execute();
        http_response_code(200); // Success
        echo json_encode(['success' => 'Record deleted!']);
        // Close resources
        $statement->close();
        exit();
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['error' => 'Invalid request parameters.']);
        exit();
    }
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// Output JSON response
echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
?>
