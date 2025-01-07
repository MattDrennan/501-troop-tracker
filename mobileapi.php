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
    } else if (isset($_GET['squad'], $_GET['action']) && $_GET['action'] === 'get_troops_by_squad') {
        // Prepare SQL query
        $sql = "
        SELECT 
            e.*, 
            COUNT(es.trooperid) AS trooper_count
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

            // Event Sign Up
            $tempObject->trooper_count = $db->trooper_count;

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
