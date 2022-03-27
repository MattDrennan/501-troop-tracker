# 501st Legion Troop Tracker
A troop tracker for the 501st Legion developed for the Florida Garrison. Xenforo (https://xenforo.com/) is required to use Troop Tracker.

## Use
You are free to download, modify, and use freely for non-commerical purposes.

## How to setup

<ol>
<li>Upload all the files to your web server, execute "other/SQL.sql" on your database, and create a "cred.php" file in the root directory. Cred.php should look like this:</li>
 
```
<?php

/**
 * 
 * This is the main MySQL database information.
 * 
 * dbServer: The MySQL server address
 * dbUser: The user for MySQL server
 * dbPassword: The password for MySQL server
 * dbName: The database for MySQL server
*/

define('dbServer', 'MY_SERVER_HERE');
define('dbUser', 'DB_USER_HERE');
define('dbPassword', 'DB_PASSWORD_HERE');
define('dbName', 'DB_NAME_HERE');

/**
 * 
 * This is used for merging old troop tracker data with the new tracker. This is not needed to run Troop Tracker.
 * See auto.php in the archive folder for more information.
 *
 * dbServer2: The MySQL server address
 * dbUser2: The user for MySQL server
 * dbPassword2: The password for MySQL server
 * dbName2: The database for MySQL server
*/

define('dbServer2', 'MY_SERVER_HERE');
define('dbUser2', 'DB_USER_HERE');
define('dbPassword2', 'DB_PASSWORD_HERE');
define('dbName2', 'DB_NAME_HERE');

/**
 * forumURL: The forum endpoint for the API
*/

$forumURL = "index.php?api";

/**
 * placeholder: This variable is used for assigning a user account to be a placeholder account. A placeholder account can be signed up multiple times for the same event, and is used to sign up non-members.
*/

define('placeholder', 1196);

/**
 * xenforoAPI_superuser: This variable is the API key for Xenforo. Ensure it is a super key.
 * xenforoAPI_userID: This variable is the user ID of a super user. This is the account that will publish information to the forum.
 * https://xenforo.com/docs/dev/rest-api/
*/

define('xenforoAPI_superuser', 'API_SUPER_USER_KEY_HERE');
define('xenforoAPI_userID', 'API_USER_SUPER_ID_HERE');

/**
 * emailFrom: This is the e-mail address that users will see when they receive e-mail from Troop Tracker
 * emailUser: This is the username to access the e-mail server
 * emailPassword: This is the password to access the e-mail server
 * emailServer: This is the address of the e-mail server
 * emailPort: This is the port of the e-mail server
*/

define('emailFrom', 'EMAIL_ADDRESS');
define('emailUser', 'SMTP_USER');
define('emailPassword', 'SMTP_PASSWORD');
define('emailServer', 'SMTP_SERVER');
define('emailPort', SMTP_PORT);

/**
 * consumerKey: This is the consumer key for Twitter API
 * consumerSecret: This is the consumer secret for Twitter API
 * bearerToken: This is the bearer token for Twitter API
 * accessToken: This is the access token for Twitter API
 * accessTokenSecret: This is the access token secret for Twitter API
 * https://developer.twitter.com/en/docs/twitter-api
*/

define('consumerKey', 'consumer_key_here');
define('consumerSecret', 'consumer_secret_here');
define('bearerToken', 'bearer_token_here');
define('accessToken', 'access_token_here');
define('accessTokenSecret', 'access_secret_here');

/**
 * googleKey: This is a Google API key which is used to access Google services. This is used for automatically detecting where an event is located, and it will automatically assign a squad based on the location.
 * https://developers.google.com/maps/documentation/javascript/get-api-key
*/

define('googleKey', 'GOOGLE_MAP_API_KEY_HERE');

/**
 * ipn: This variable is where all the PayPal payment webhooks are sent. This is how the Troop Tracker knows which trooper has donated.
 * https://developer.paypal.com/api/nvp-soap/ipn/IPNIntro/
*/

define('ipn', 'script/php/paypal.php');

/**
 * discordWeb1: This variable is the webhook URL from the Discord server.
 * https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks
*/

define('discordWeb1', 'WEBHOOK_HERE');

/**
 * garrison: This variable is used to display the garrison name, and to make it easier to replace all mentions of the garrison
 * garrisonImage: This variable is used to display the garrison logo. The logom should be located in the images folder.
*/

define('garrison', 'Florida Garrison');
define('garrisonImage', 'garrison_emblem.png');

/**
 * virtualTroop: This variable is used to determine which forum to post virtual troops
*/

$virtualTroop = 445;

/**
 * dualCostume: This variable is used to determine which costume club ID should be counted as a dual costume.
*/

$dualCostume = 5;

/**
 * dualNA: This variable is used to determine which costume ID is the dual N/A
*/

$dualNA = 721;

/**
 * userGroupGarrison: The Xenforo user group ID for the garrison
*/

$userGroupGarrison = 18;

/**
 * userGroup501st: The Xenforo user group ID for the 501st or other group
*/

$userGroup501st = 1415;

/**
 * userGroupRetired: The Xenforo user group ID for retired members
*/

$userGroupRetired = 1429;

/**
 * troopTrackerUserGroup: The Xenforo user group ID for Troop Tracker connected troopers
*/

$troopTrackerUserGroup = 1489;

/**
 * handlerUserGroup: The Xenforo user group ID for Troop Tracker handler
*/

$handlerUserGroup = 1490;

// Please note: Do not change the order of squads and clubs after your set up your troop tracker, otherwise you will mess up the squad IDs
// Make sure you run queries on your database if you need to change the order after installation.

/**
 * squadArray
 * name: Name of the squad
 * logo: Image of the squad logo located in images folder
 * costumes: An array of costumes that this squad accepts as troop credit
 * db: The field in the troopers table that determines if a trooper is a member of the club
 * db2: A blank variable with no use as of now
 * rankRegular: An image file that will show on troopers profiles of this squad (Active Regular Members)
 * rankReserve: An image file that will show on troopers profiles of this squad (Reserve Members)
 * rankRetired: An image file that will show on troopers profiles of this squad (Inactive Retired Members)
 * eventForum: The forum ID in Xenforo that corresponds to the event forum for this squad
 * userGroup: The Xenforo user group ID assigned to this squad
 */


// Squads
$squadArray = array(
	array(
		"name" => "Everglades Squad",
		"logo" => "everglades_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501",
		"db2" => "",
		"rankRegular" => "everglades_sm.png",
		"rankReserve" => "",
		"rankRetired" => "",
		"eventForum" => 9,
		"userGroup" => 44),
	array(
		"name" => "Makaze Squad",
		"logo" => "makaze_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501",
		"db2" => "",
		"rankRegular" => "makaze_sm.png",
		"rankReserve" => "",
		"rankRetired" => "",
		"eventForum" => 8,
		"userGroup" => 45),
	array(
		"name" => "Parjai Squad",
		"logo" => "parjai_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501",
		"db2" => "",
		"rankRegular" => "parjai_sm.png",
		"rankReserve" => "",
		"rankRetired" => "",
		"eventForum" => 186,
		"userGroup" => 250),
	array(
		"name" => "Squad 7",
		"logo" => "squad7_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501",
		"db2" => "",
		"rankRegular" => "s7_sm.png",
		"rankReserve" => "",
		"rankRetired" => "",
		"eventForum" => 7,
		"userGroup" => 683),
	array(
		"name" => "Tampa Bay Squad",
		"logo" => "tampabay_emblem.png",
		"costumes" => array(0, 5),
		"db" => "p501",
		"db2" => "",
		"rankRegular" => "tampa_sm.png",
		"rankReserve" => "",
		"rankRetired" => "",
		"eventForum" => 73,
		"userGroup" => 43)
);

/**
 * clubArray
 * name: Name of the club
 * logo: Image of the squad logo located in images folder
 * costumes: An array of costumes that this club accepts as troop credit
 * db: The field in the troopers table that determines if a trooper is a member of the club
 * db2: A blank variable with no use as of now
 * db3: A field that stores a corresponding identifer for this club, for example a forum username or ID number
 * db3Name: A field that stores a corresponding name for the identifer used in db3
 * db3Require: Special code used to determine if form validation needs to ensure db3 has a value
 * dbLimit: The field in events table that holds the amount of allowed members from this club
 * rankRegular: An image file that will show on troopers profiles of this club (Active Regular Members)
 * rankReserve: An image file that will show on troopers profiles of this club (Reserve Members)
 * rankRetired: An image file that will show on troopers profiles of this club (Inactive Retired Members)
 * naCostume: The ID of the costume that is the clubs other or N/A
 * userGroup: The Xenforo user group ID assigned to this club
 */

// Clubs
$clubArray = array(
	array(
		"name" => "Rebel Legion",
		"logo" => "test",
		"costumes" => array(1, 5),
		"db" => "pRebel",
		"db2" => "",
		"db3" => "rebelforum",
		"db3Name" => "Rebel Legion Forum Username",
		"db3Require" => "0,0,squad:6",
		"dbLimit" => "limitRebels",
		"rankRegular" => "rebel.png",
		"rankReserve" => "rebel_reserve.png",
		"rankRetired" => "rebel_retired.png",
		"naCostume" => 720,
		"userGroup" => 1486),
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
		"rankRetired" => "r2_retired.png",
		"naCostume" => 716,
		"userGroup" => 1487),
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
		"rankRetired" => "mercs_retired.png",
		"naCostume" => 715,
		"userGroup" => 1488),
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
		"rankRetired" => "",
		"naCostume" => 717,
		"userGroup" => 1415)
);

?>
```

<li>Change ownership for 'images/uploads' to the web server user</li>
<li>Set file permissions to 'images/uploads' to 750</li>
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
