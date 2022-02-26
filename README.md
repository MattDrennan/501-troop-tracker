# 501st Legion Troop Tracker
A troop tracker for the 501st Legion developed for the Florida Garrison.

## Use
You are free to download, modify, and use freely for non-commerical purposes.

## How to setup

<ol>
<li>Upload all the files to your web server, execute "other/SQL.sql" on your database, and create a "cred.php" file in the root directory. Cred.php should look like this:</li>
 
```
<?php

// DB Info
// Main database
define('dbServer', 'MY_SERVER_HERE');
define('dbUser', 'DB_USER_HERE');
define('dbPassword', 'DB_PASSWORD_HERE');
define('dbName', 'DB_NAME_HERE');

// DB Info Merge
// This may not be necessary, this is for merging the old troop tracker data to the new troop tracker, see auto.php for more info
define('dbServer2', 'MY_SERVER_HERE');
define('dbUser2', 'DB_USER_HERE');
define('dbPassword2', 'DB_PASSWORD_HERE');
define('dbName2', 'DB_NAME_HERE');

// Path - Absolute path to directory
define('aPath', 'ABSOLUTE_PATH_HERE_TO_MAIN_DIRECTORY');

// Placeholder Account ID (Used for signing up non-members)
define('placeholder', 1196);

// Forum API Key (Xenforo)
define('xenforoAPI_superuser', 'API_SUPER_USER_KEY_HERE');

// E-mail Creds
// This is the email server and account you are going to send e-mails from
define('emailFrom', 'EMAIL_ADDRESS');
define('emailUser', 'SMTP_USER');
define('emailPassword', 'SMTP_PASSWORD');
define('emailServer', 'SMTP_SERVER');
define('emailPort', SMTP_PORT);

// Twitter
define('consumerKey', 'consumer_key_here');
define('consumerSecret', 'consumer_secret_here');
define('bearerToken', 'bearer_token_here');
define('accessToken', 'access_token_here');
define('accessTokenSecret', 'access_secret_here');

// Google Maps
// Make sure you get a Google Maps API key to use the Google API. The Google Maps API is used to automatically detect where an event is located
define('googleKey', 'GOOGLE_MAP_API_KEY_HERE');

// PayPal Info
// See PayPal IPN documentation for more information. This file is used to store donations into the database for tracking.
define('ipn', 'script/php/paypal.php');

// Discord Web Hook
define('discordWeb1', 'WEBHOOK_HERE');

// Garrison
// Your garrison and the logo image file name in the images folders
define('garrison', 'Florida Garrison');
define('garrisonImage', 'garrison_emblem.png');

// Please note: Do not change the order of squads and clubs after your set up your troop tracker, otherwise you will mess up the squad IDs
// Make sure you run queries on your database if you need to change the order after installation.

// Dual costume
$dualCostume = 5;

// Squads
// name: Name of squad
// logo: Image logo of squad
// db: Membership database (EX: p501)
// db2: Custom DB code (not required)
// rankRegular: Title image of regular members
// rankReserve: Title image of reserve members
// rankRetired: Title image of retired members

$squadArray = array(
	array(
		"name" => "Everglades Squad",
		"logo" => "everglades_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501",
		"db2" => "",
		"rankRegular" => "everglades_sm.png",
		"rankReserve" => "",
		"rankRetired" => ""),
	array(
		"name" => "Makaze Squad",
		"logo" => "makaze_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501",
		"db2" => "",
		"rankRegular" => "makaze_sm.png",
		"rankReserve" => "",
		"rankRetired" => ""),
	array(
		"name" => "Parjai Squad",
		"logo" => "parjai_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501", "db2" => "",
		"rankRegular" => "parjai_sm.png",
		"rankReserve" => "",
		"rankRetired" => ""),
	array(
		"name" => "Squad 7",
		"logo" => "squad7_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501",
		"db2" => "",
		"rankRegular" => "s7_sm.png",
		"rankReserve" => "",
		"rankRetired" => ""),
	array(
		"name" => "Tampa Bay Squad",
		"logo" => "tampabay_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501",
		"db2" => "",
		"rankRegular" => "tampa_sm.png",
		"rankReserve" => "",
		"rankRetired" => "")
);

// Clubs
// name: Name of club
// logo: Image logo of club
// costumes: Array of costume ID's assigned to this club
// db: Membership database (EX: p501)
// db2: Custom DB code (not required)
// db3: ID / Username of club DB table
// db3Name: Name of db3 (EX: ID / Username)
// db3Require: Instructions for how to handle sign up requirements
// dbLimit: DB table in events where this club will have a set limit
// rankRegular: Title image of regular members
// rankReserve: Title image of reserve members
// rankRetired: Title image of retired members

$clubArray = array(
	array(
		"name" => "Rebel Legion",
		"logo" => "test",
		"costumes" => array(1, 5),
		"db" => "pRebel",
		"db2" => "rebelforum != \"\" AND ",
		"db3" => "rebelforum",
		"db3Name" => "Rebel Legion Forum Username",
		"db3Require" => "0,0,squad:6",
		"dbLimit" => "limitRebels",
		"rankRegular" => "rebel.png",
		"rankReserve" => "rebel_reserve.png",
		"rankRetired" => "rebel_retired.png"),
	array(
		"name" => "Droid Builders",
		"logo" => "test", "costumes" => array(3),
		"db" => "pDroid",
		"db2" => "",
		"db3" => "",
		"db3Name" => "",
		"db3Require" => "0,0,0",
		"dbLimit" => "limitDroid",
		"rankRegular" => "r2.png",
		"rankReserve" => "r2_reserve.png",
		"rankRetired" => "r2_retired.png"),
	array(
		"name" => "Mando Mercs",
		"logo" => "test",
		"costumes" => array(2),
		"db" => "pMando",
		"db2" => "",
		"db3" => "mandoid",
		"db3Name" => "Mando Mercs CAT #",
		"db3Require" => "0,digits,squad:8",
		"db3Link" => "",
		"dbLimit" => "limitMando",
		"rankRegular" => "mercs.png",
		"rankReserve" => "mercs_reserve.png",
		"rankRetired" => "mercs_retired.png"), 
	array(
		"name" => "Other",
		"logo" => "test",
		"costumes" => array(4),
		"db" => "pOther",
		"db2" => "",
		"db3" => "sgid",
		"db3Name" => "Saber Guild SG #",
		"db3Require" => "0,digits,0",
		"db3Link" => "",
		"dbLimit" => "limitOther",
		"rankRegular" => "saberguildmember.png",
		"rankReserve" => "",
		"rankRetired" => "")
);

?>
```

<li>Set file permissions to 'images/uploads' to 777</li>
<li>Manually modify getSquad() function in 'config.php' to fit your needs</li>
<li>Set up a Google Cloud API for Google Sheets, then create a service account under "Credentials"</li>
<li>Download the JSON file from the service account, rename it to "sheets_api_secret.json", and upload it the root directory"</li>
<li>On a live server, set up cron jobs located in other/cron.txt</li>
<li>To use 'nodescraper', create a '.env' file in same directory. Add the following:</li>
</ol>

```
USERNAME_FORUM=
PASSWORD_FORUM=
MYSQL_HOST=
MYSQL_USER=
MYSQL_PASSWORD=
MYSQL_TABLE=
GOOGLE_SHEET_KEY=
```


## Please contact me with any questions, comments, or concerns
drennanmattheww@gmail.com

## Visit the live website here:
https://trooptracking.com
