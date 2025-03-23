<?php

/**
 * API for mobile app troop data
 * 
 * @author Matthew Drennan
 */

// Include config
include "config.php";

// Mobile API
function generateApiKey($trooperId) {
    global $conn;

    // Ensure trooperId is provided
    if (empty($trooperId)) {
        throw new InvalidArgumentException("Trooper ID cannot be empty.");
    }

    // Function to generate a random 64-character API key
    function generateRandomApiKey() {
        return bin2hex(random_bytes(32)); // 64 characters in length
    }

    // Generate a unique API key
    $apiKey = generateRandomApiKey();

    // Check uniqueness and regenerate if necessary
    do {
        $stmt = $conn->prepare("SELECT COUNT(*) FROM trooper_api_codes WHERE api_code = ?");
        $stmt->bind_param("s", $apiKey);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            $apiKey = generateRandomApiKey(); // Generate a new one
        }
    } while ($count > 0);

    // Insert the API key into the database
    $stmt = $conn->prepare("INSERT INTO trooper_api_codes (trooperid, api_code) VALUES (?, ?)");
    $stmt->bind_param("is", $trooperId, $apiKey);
    $success = $stmt->execute();
    $stmt->close();

    if (!$success) {
        throw new RuntimeException("Failed to insert API key into the database.");
    }

    return $apiKey; // Return the generated API key
}

function validateApiKey($headers) {
    global $conn;

    // Check if the API-Key is present in the headers
    if (!isset($headers['API-Key']) || empty($headers['API-Key'])) {
        // If API key is missing or empty, allow the function to proceed
        return null; // Return null or a default value for cases with no API key
    }

    $apiKey = $headers['API-Key'];

    // Prepare SQL query to verify the API key
    $sql = "SELECT trooperid FROM trooper_api_codes WHERE api_code = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        // Gracefully handle database errors
        return null;
    }

    // Bind the API key parameter and execute
    $stmt->bind_param("s", $apiKey);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // If the API key is invalid, allow the function to proceed
        return null;
    }

    // Fetch the associated trooper ID
    $row = $result->fetch_assoc();
    $trooperId = $row['trooperid'];

    // Close statement and return trooper ID
    $stmt->close();
    return $trooperId;
}

function ensureTrooperIdMatch($headers, $trooperId) {
    // Validate the API key and retrieve the associated trooper ID
    $associatedTrooperId = validateApiKey($headers);

    // Allow the function to proceed if trooperId is 0
    if ($trooperId === 0) {
        return; // Skip the validation logic
    }

    // Check if the trooperId matches the associatedTrooperId (if API key is valid)
    if ($associatedTrooperId !== null && (int)$associatedTrooperId !== (int)$trooperId) {
        http_response_code(403); // Forbidden
        echo json_encode(['error' => 'You are not authorized to modify this trooper ID.']);
        exit();
    }

    // If validation passes or associatedTrooperId is null, proceed
    return;
}

// Set JSON response header
header('Content-Type: application/json');

// Initialize response data
$data = new stdClass();

try {
    // Get headers
    $headers = getallheaders();

    // Assume trooper ID is sent as a parameter
    $trooperId = $_GET['trooperid'] ?? 0;

    // Ensure the API key is valid and the trooper ID matches
    ensureTrooperIdMatch($headers, $trooperId);

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
            $tempObject->squad = $db->squad;

            $data->troops[] = $tempObject;
        }

        // Close resources
        $statement->close();
    } else if (isset($_GET['action'], $_POST['login'], $_POST['password']) && $_GET['action'] === 'login_with_forum') { // Login to forum
        $forumLogin = loginWithForum($_POST['login'], $_POST['password']);

        // Check credentials
        if(isset($forumLogin['success']) && $forumLogin['success'] == 1)
        {
            $trooperId = getIDFromUserID($forumLogin['user']['user_id']);
            
            $apiKey = generateApiKey($trooperId);

            $data = [
                'success' => true,
                'user' => $forumLogin['user'],
                'apiKey' => $apiKey,
            ];
        }
    } else if (isset($_GET['action']) && $_GET['action'] === 'is_closed') { // Get if site is closed
        $statement = $conn->prepare("SELECT * FROM settings LIMIT 1");
        $statement->execute();

        if ($result = $statement->get_result()) {
            while ($db = mysqli_fetch_object($result)) {
                $data->isWebsiteClosed = $db->siteclosed;
                $data->siteMessage = $db->sitemessage;
            }
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
            $tempObject->link = isLink($db->id);
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
    // Get costumes
} else if (isset($_GET['trooperid'], $_GET['friendid'], $_GET['action']) && $_GET['action'] === 'get_costumes_for_trooper') {
	// Determine which trooper ID to use: $_GET['trooperid'] > friendid > session
	if (isset($_GET['trooperid']) && $_GET['trooperid'] > 0) {
		$trooperId = getIDFromUserID($_GET['trooperid']);
	} else if (isset($_GET['friendid']) && $_GET['friendid'] > 0) {
		$trooperId = $_GET['friendid'];
	}
	
	// For main costume sort
	$mainCostumesQuery = $mainCostumes . mainCostumesBuild($trooperId) . getMyCostumes(getTKNumber($trooperId), getTrooperSquad($trooperId));

	// Dual Costume Flag
	$allowDualCostume = (isset($_GET['allowDualCostume']) && $_GET['allowDualCostume'] === 'true');

	// Use new costume restriction builder
	$costumeQueryFilter = costume_restrict_query($trooperId, true, $allowDualCostume);

	// Build full query
	$query = "SELECT * FROM costumes 
			  $costumeQueryFilter 
			  ORDER BY FIELD(costume, $mainCostumesQuery) DESC, costume";

	// Prepare and execute
	$statement = $conn->prepare($query);

	if ($statement) {
		$statement->execute();
		$data = [];

		if ($result = $statement->get_result()) {
			while ($db = $result->fetch_object()) {
				$tempObject = new stdClass();
				$tempObject->id = $db->id;
				$tempObject->club = $db->club;
				$tempObject->abbreviation = getCostumeAbbreviation($db->club);
				$tempObject->name = $db->costume;
				$data[] = $tempObject;
			}
		}
	} else {
		die("Error preparing statement: " . $conn->error);
	}

	$statement->close();
    // Set status/costume
    } else if(isset($_GET['trooperid'], $_GET['troopid'], $_GET['status'], $_GET['action']) && $_GET['action'] === 'set_status_costume') {
        // Replace Trooper ID
        $_GET['trooperid'] = getIDFromUserID($_GET['trooperid']);

        // Check if 'costume' is set in the GET parameters
        if (isset($_GET['costume']) && $_GET['costume'] > 0) {
            // Update status and costume
            $statement = $conn->prepare("UPDATE event_sign_up SET status = ?, costume = ? WHERE trooperid = ? AND troopid = ?");
            $statement->bind_param("iiii", $_GET['status'], $_GET['costume'], $_GET['trooperid'], $_GET['troopid']);
        } else {
            // Update status only
            $statement = $conn->prepare("UPDATE event_sign_up SET status = ? WHERE trooperid = ? AND troopid = ?");
            $statement->bind_param("iii", $_GET['status'], $_GET['trooperid'], $_GET['troopid']);
        }

        // Execute the prepared statement
        $statement->execute();

        // Return
        $data->success = true;
    // Get events that need confirming
    } else if(isset($_GET['trooperid'], $_GET['action']) && $_GET['action'] === 'get_confirm_events_trooper') {
        // Replace Trooper ID
        $_GET['trooperid'] = getIDFromUserID($_GET['trooperid']);

        // Load events that need confirmation
        $statement = $conn->prepare("SELECT events.squad, events.id AS eventId, events.name, events.dateStart, events.dateEnd, event_sign_up.id AS signupId, event_sign_up.troopid, event_sign_up.trooperid, event_sign_up.status FROM events LEFT JOIN event_sign_up ON event_sign_up.troopid = events.id WHERE event_sign_up.trooperid = ? AND events.dateEnd < NOW() AND event_sign_up.status < 3 AND events.closed = 1 ORDER BY events.dateEnd DESC");
        $statement->bind_param("i", $_GET['trooperid']);
        $statement->execute();

        if ($result = $statement->get_result())
        {
            while ($db = mysqli_fetch_object($result))
            {
                $tempObject = new stdClass();
                $tempObject->troopid = $db->eventId;
                $tempObject->name = $db->name;
                $tempObject->dateStart = $db->dateStart;
                $tempObject->dateEnd = $db->dateEnd;

                $data->troops[] = $tempObject;
            }
        }
    // Check if trooper is in event - won't show canceled
    } else if(isset($_GET['trooperid'], $_GET['troopid'], $_GET['action']) && $_GET['action'] === 'trooper_in_event') {
        // Replace Trooper ID
        $_GET['trooperid'] = getIDFromUserID($_GET['trooperid']);

        // Set up
        $inEvent = false;

        // Prevent bug of getting signed up twice
        $eventCheck = inEvent($_GET['trooperid'], $_GET['troopid']);

        // Check if already in troop
        if($eventCheck['inTroop'] == 1 && $eventCheck['status'] != 4)
        {
            $inEvent = true;
        }

        $data->inEvent = $inEvent;
    // Cancel event for trooper
    } else if(isset($_GET['trooperid'], $_GET['troopid'], $_GET['action']) && $_GET['action'] === 'cancel_troop') {
        // Replace Trooper ID
        $_GET['trooperid'] = getIDFromUserID($_GET['trooperid']);

        // Update SQL
        $statement = $conn->prepare("UPDATE event_sign_up SET status = 4 WHERE trooperid = ? AND troopid = ?");
        $statement->bind_param("ii", $_GET['trooperid'], $_GET['troopid']);
        $statement->execute();

        // Send to database to send out notifictions later
        $statement = $conn->prepare("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES (?, ?, '2')");
        $statement->bind_param("ii", $_GET['troopid'], $_GET['trooperid']);
        $statement->execute();

        $data->success = true;
    // Sign Up for troop
    } else if(isset($_GET['trooperid'], $_GET['troopid'], $_GET['addedby'], $_GET['status'], $_GET['costume'], $_GET['backupcostume'], $_GET['action']) && $_GET['action'] === 'sign_up') {
        // Replace Trooper ID
        $_GET['trooperid'] = getIDFromUserID($_GET['trooperid']);

        // Set trooper ID
        $trooperID = 0;

        // Set up success message
        $success = "success";
        $success_message = "Success!";

        // Get number of troopers that trooper signed up for event
        $statement = $conn->prepare("SELECT id FROM event_sign_up WHERE addedby = ? AND troopid = ?");
        $statement->bind_param("ii", $_GET['trooperid'], $_GET['troopid']);
        $statement->execute();
        $statement->store_result();
        $numFriends = $statement->num_rows;

        // Check if this is add friend
        if(isset($_GET['addedby']) && $_GET['addedby'] > 0)
        {
            // Prevent bug of getting signed up twice
            $eventCheck = inEvent($_GET['addedby'], $_GET['troopid']);

            // Set
            $trooperID = $_GET['addedby'];
        }
        else
        {
            // Prevent bug of getting signed up twice
            $eventCheck = inEvent($_GET['trooperid'], $_GET['troopid']);

            // Set
            $trooperID = $_GET['trooperid'];
        }

        // Check if already in troop and exclude placeholder account // For mobile, we use this to update records
        if($eventCheck['inTroop'] == 1 && $trooperID != placeholder)
        {
            $statement = $conn->prepare("
                UPDATE event_sign_up 
                SET costume = ?, 
                    status = ?, 
                    costume_backup = ?, 
                    addedby = ?
                WHERE trooperid = ? AND troopid = ?
            ");

            $statement->bind_param("iiiiii", $_GET['costume'], $_GET['status'], $_GET['backupcostume'], $_GET['addedby'], $_GET['trooperid'], $_GET['troopid']);
            $statement->execute();
            $statement->close();

            // Send to database to send out notifictions later
            $statement = $conn->prepare("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES (?, ?, '1')");
            $statement->bind_param("ii", $_GET['troopid'], $_GET['trooperid']);
            $statement->execute();

            $data->sucess = $success;
            $data->success_message = $success_message;
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            exit();
        }

        // End prevent bug of getting signed up twice

        // Get status post variable
        $status = $_GET['status'];

        // Check to see if this event is full

        // Set up limits
        $limit501st = "";

        // Set up limit total
        $limit501stTotal = eventClubCount($_GET['troopid'], 0);

        // Set up club count
        $clubCount = 1;

        // Loop through clubs
        foreach($clubArray as $club => $club_value)
        {
            // Set up limits
            ${$club_value['dbLimit']} = "";

            // Set up limit totals
            ${$club_value['dbLimit'] . "Total"} = eventClubCount($_GET['troopid'], $clubCount);

            // Increment club count
            $clubCount++;
        }

        // Set limit total
        $limitTotal = 0;

        // Set handler total
        $limitHandlers = 0;

        // Set friend limit
        $friendLimit = 0;

        // Is this a total trooper event?
        $totalTrooperEvent = false;


        // Query to get limits
        $statement = $conn->prepare("SELECT * FROM events WHERE id = ?");
        $statement->bind_param("i", $_GET['troopid']);
        $statement->execute();

        // Output
        if ($result = $statement->get_result())
        {
            while ($db = mysqli_fetch_object($result))
            {
                // Set 501
                $limit501st = $db->limit501st;
                
                // Set handlers
                $limitHandlers = $db->limitHandlers;
                
                // Set friend limit
                $friendLimit = $db->friendLimit;

                // Add
                $limitTotal += $db->limit501st;


                // Loop through clubs
                foreach($clubArray as $club => $club_value)
                {
                    // Set
                    ${$club_value['dbLimit']} = $db->{$club_value['dbLimit']};

                    // Add
                    $limitTotal += $db->{$club_value['dbLimit']};
                }

                // Check for total limit set, if it is, replace limit with it
                if($db->limitTotalTroopers > 500 || $db->limitTotalTroopers < 500)
                {
                    $limitTotal = $db->limitTotalTroopers;
                    $totalTrooperEvent = true;
                }
            }
        }

        // Set troop full - not used at the moment, but will keep it here for now
        $troopFull = false;

        // Check for total limit set, if it is, check if troop is full based on total
        if(strpos(strtolower(getCostume($_GET['costume'])), "handler") === false)
        {
            if($totalTrooperEvent)
            {
                /* TOTAL TROOPERS */
                
                if($limitTotal - eventClubCount($_GET['troopid'], "all") <= 0 && $status != 4)
                {
                    // Troop is full, set to stand by
                    $status = 1;

                    // Set troop full
                    $troopFull = true;
                }
            }
            else
            {
                /* CHECK IF SQUADS / CLUB ARE FULL */

                // 501
                if((getCostumeClub($_GET['costume']) == 0 && ($limit501st - eventClubCount($_GET['troopid'], 0)) <= 0) && $status != 4)
                {
                    // Troop is full, set to stand by
                    $status = 1;

                    // Set troop full
                    $troopFull = true;
                }

                // Loop through clubs
                foreach($clubArray as $club => $club_value)
                {
                    // Loop through costumes
                    foreach($club_value['costumes'] as $costume)
                    {
                        // Make sure not a dual costume
                        if(!in_array($costume, $dualCostume))
                        {
                            // Check club
                            if((getCostumeClub($_GET['costume']) == $costume && (${$club_value['dbLimit']} - eventClubCount($_GET['troopid'], $costume)) <= 0) && $status != 4)
                            {
                                // Troop is full, set to stand by
                                $status = 1;

                                // Set troop full
                                $troopFull = true;
                            }
                        }
                    }
                }
            }
        }
        else
        {
            // Handler check
            if(($limitHandlers - handlerEventCount($_GET['troopid'])) <= 0 && $status != 4)
            {
                // Troop is full, set to stand by
                $status = 1;

                // Set troop full
                $troopFull = true;
            }
        }

        // End of check to see if this event is full

        // Check if this is add friend
        if(isset($_GET['addedby']) && $_GET['addedby'] > 0)
        {
            // Check if can add friend based on friend count
            if($numFriends >= $friendLimit)
            {
                $success = "friend_fail";
                $success_message = "You cannot add anymore friends!";
            }
            else if(!checkLinkedEvents($_GET['addedby'], $_GET['troopid']))
            {
                $success = "check_linked_fail";
                $success_message = "This friend has exceeded their sign ups for these linked events.";
            }
            else
            {
                // Query the database
                $statement = $conn->prepare("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup, addedby) VALUES (?, ?, ?, ?,?, ?)");
                $statement->bind_param("iiiiii", $_GET['addedby'], $_GET['troopid'], $_GET['costume'], $status, $_GET['backupcostume'], $_GET['trooperid']);
                $statement->execute();
                
                // Send to database to send out notifictions later
                $statement = $conn->prepare("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES (?, ?, '1')");
                $statement->bind_param("ii", $_GET['troopid'], $_GET['addedby']);
                $statement->execute();
            }
        }
        else
        {
            if(!checkLinkedEvents($_GET['trooperid'], $_GET['troopid'])) {
                $success = "check_linked_fail";
                $success_message = "You have exceeded your sign ups for these linked events.";
            } else {
                // Query the database
                $statement = $conn->prepare("INSERT INTO event_sign_up (trooperid, troopid, costume, status, costume_backup) VALUES (?, ?, ?, ?, ?)");
                $statement->bind_param("iiiii", $_GET['trooperid'], $_GET['troopid'], $_GET['costume'], $status, $_GET['backupcostume']);
                $statement->execute();
                
                // Send to database to send out notifictions later
                $statement = $conn->prepare("INSERT INTO notification_check (troopid, trooperid, trooperstatus) VALUES (?, ?, '1')");
                $statement->bind_param("ii", $_GET['troopid'], $_GET['trooperid']);
                $statement->execute();
            }
        }

        $data->sucess = $success;
        $data->success_message = $success_message;
        $data->numFriends = ($friendLimit - $numFriends);
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
    } else if(isset($_POST['fcm'], $_POST['apiKey'], $_POST['action']) && $_POST['action'] === 'logoutFCM') { // Logout (FCM)
        // Logout (FCM)

        // Get values
        $fcmToken = $_POST['fcm'];
        $apiKey = $_POST['apiKey'];

        // Begin transaction
        $conn->begin_transaction();

        try {
            // Delete from mobile_app table
            $sqlMobileApp = "
                DELETE 
                FROM 
                    mobile_app 
                WHERE 
                    fcm = ?";
            
            $stmtMobileApp = $conn->prepare($sqlMobileApp);
            if (!$stmtMobileApp) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmtMobileApp->bind_param("s", $fcmToken);
            $stmtMobileApp->execute();
            $stmtMobileApp->close();

            // Delete from trooper_api_codes table
            $sqlApiKey = "
                DELETE 
                FROM 
                    trooper_api_codes 
                WHERE 
                    api_code = ?";
            
            $stmtApiKey = $conn->prepare($sqlApiKey);
            if (!$stmtApiKey) {
                throw new Exception('Database error: ' . $conn->error);
            }
            $stmtApiKey->bind_param("s", $apiKey);
            $stmtApiKey->execute();
            $stmtApiKey->close();

            // Commit transaction
            $conn->commit();

            // Return success response
            http_response_code(200);
            echo json_encode(['success' => 'Records deleted!']);
        } catch (Exception $e) {
            // Rollback transaction on error
            $conn->rollback();
            
            // Return error response
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete records: ' . $e->getMessage()]);
        } finally {
            // Ensure resources are closed
            if (isset($stmtMobileApp)) {
                $stmtMobileApp->close();
            }
            if (isset($stmtApiKey)) {
                $stmtApiKey->close();
            }
        }

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
