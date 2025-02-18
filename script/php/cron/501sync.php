<?php

/**
 * Optimized 501st Data Scraper
 *
 * This script updates trooper and costume data for the 501st Legion.
 * It is designed to be executed weekly via a cron job.
 *
 * @author Matthew Drennan
 */

// Include config
include(dirname(__DIR__) . '/../../config.php');

// Check last sync date to prevent unnecessary updates
$query = "SELECT syncdate FROM settings";
$result = $conn->query($query);

if ($result && $db = $result->fetch_object()) {
    if (strtotime($db->syncdate) >= strtotime("-7 days")) {
        die("Already updated recently.");
    }
}

// Set unlimited execution time (0 means no limit)
set_time_limit(0);

// Reset databases
$conn->query("TRUNCATE TABLE 501st_troopers");
$conn->query("TRUNCATE TABLE 501st_costumes");

// Fetch trooper data
$json = file_get_contents("https://www.501st.com/memberAPI/v3/garrisons/$garrisonIdAPI/members");
$trooperData = json_decode($json, true);

if (!$trooperData || empty($trooperData['unit']['members'])) {
    die("Failed to retrieve trooper data.");
}

// Prepare database insertion queries
$trooperStmt = $conn->prepare("INSERT INTO 501st_troopers (legionid, name, thumbnail, link, squad, approved, status, standing, joindate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
$costumeStmt = $conn->prepare("INSERT INTO 501st_costumes (legionid, costumeid, prefix, costumename, photo, thumbnail, bucketoff) VALUES (?, ?, ?, ?, ?, ?, ?)");

// Process members
foreach ($trooperData['unit']['members'] as $trooper) {
    $legionId = $trooper['legionId'];

    // Fetch detailed member data
    $json2 = file_get_contents("https://www.501st.com/memberAPI/v3/legionId/$legionId");
    $memberData = json_decode($json2, true);
	
	print_r($memberData);
	
	print_r('<br /><br /><hr /><br /><br />');

    if (!$memberData) continue;

    $trooperStmt->bind_param(
        "ssssiiiss",
        $legionId,
        $trooper['fullName'],
        $trooper['thumbnail'],
        $trooper['link'],
        convertSquadId($trooper['squadId']),
        convertMemberApproved($memberData['memberApproved']),
        convertMemberStatus($memberData['memberStatus']),
        convertMemberStanding($memberData['memberStanding']),
        $memberData['joinDate']
    );
    $trooperStmt->execute();

    // Fetch and insert costume data
    $json3 = file_get_contents("https://www.501st.com/memberAPI/v3/legionId/$legionId/costumes");
    $costumeData = json_decode($json3, true);

    if ($costumeData && !empty($costumeData['costumes'])) {
        foreach ($costumeData['costumes'] as $costume) {
            $costumeStmt->bind_param(
                "sssssss",
                $legionId,
                $costume['costumeId'],
                $costume['prefix'],
                $costume['costumeName'],
                $costume['photoURL'],
                $costume['thumbnail'],
                $costume['bucketOffPhoto']
            );
            $costumeStmt->execute();
        }
    }
}

// Close statements
$trooperStmt->close();
$costumeStmt->close();

// Generate statistics
$trooperCounts = [
    "Total Members" => $conn->query("SELECT COUNT(*) AS count FROM 501st_troopers")->fetch_object()->count,
    "No Squad" => $conn->query("SELECT COUNT(*) AS count FROM 501st_troopers WHERE squad = '0'")->fetch_object()->count,
    "Everglades" => $conn->query("SELECT COUNT(*) AS count FROM 501st_troopers WHERE squad = '1'")->fetch_object()->count,
    "Makaze" => $conn->query("SELECT COUNT(*) AS count FROM 501st_troopers WHERE squad = '2'")->fetch_object()->count,
    "Parjai" => $conn->query("SELECT COUNT(*) AS count FROM 501st_troopers WHERE squad = '3'")->fetch_object()->count,
    "Squad 7" => $conn->query("SELECT COUNT(*) AS count FROM 501st_troopers WHERE squad = '4'")->fetch_object()->count,
    "Tampa" => $conn->query("SELECT COUNT(*) AS count FROM 501st_troopers WHERE squad = '5'")->fetch_object()->count,
];

// Display statistics
foreach ($trooperCounts as $label => $count) {
    echo "$label: $count <br />";
}

echo "COMPLETE!";

// Update sync date
$conn->query("UPDATE settings SET syncdate = NOW()");

/**
 * Converts the member approval string value to an interger
 *
 * @param string $value The string value to be formatted
 * @return int Returns 1 for yes and 0 for all else
 */
function convertMemberApproved($value) {
    return ($value === "YES") ? 1 : 0;
}

/**
 * Returns an interger based on the member status
 *
 * @param string $value The string value to be formatted
 * @return int Returns 1 for active, 2 for reserve, and 0 for all else
 */
function convertMemberStatus($value) {
    return ($value === "Active") ? 1 : (($value === "Reserve") ? 2 : 0);
}

/**
 * Returns an interger based on the member standing
 *
 * @param string $value The string value to be formatted
 * @return int Returns 1 for good, and 0 for all else
 */
function convertMemberStanding($value) {
    return ($value === "Good") ? 1 : 0;
}

/**
 * Returns the squad's ID for troop tracker
 *
 * @param int $value The string value to be formatted
 * @return int Returns squad ID based on value
 */
function convertSquadId($value) {
    $squads = [
        110 => 5,  // Tampa Bay Squad
        136 => 4,  // Squad 7
        126 => 3,  // Parjai Squad
        124 => 2,  // Makaze Squad
        113 => 1   // Everglades Squad
    ];
    return $squads[$value] ?? 0;
}

?>