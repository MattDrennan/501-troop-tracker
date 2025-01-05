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
 * Webmaster E-mail: The e-mail for the webmaster
*/

$webmasterEmail = "gwm@fl501st.com";

/**
 * trackerURL: The base URL for the tracker
*/

$trackerURL = "https://www.fl501st.com/troop-tracker";

/**
 * forumURL: The forum endpoint for the API - include trailing ?
*/

$forumURL = "index.php?api";

/**
 * forumAnnounceID: ID of the forum where announcements are made
*/

$forumAnnounceID = 18;

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
 * googleKey: This is a Google API key which is used to access Google services. This is used for automatically detecting where an event is located, and it will automatically assign a squad based on the location.
 * https://developers.google.com/maps/documentation/javascript/get-api-key
*/

define('googleKey', 'GOOGLE_MAP_API_KEY_HERE');

/**
 * discordWeb1: This variable is the webhook URL from the Discord server.
 * https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks
*/

define('discordWeb1', 'WEBHOOK_HERE');

/**
 * discordInviteLink: This variable is the URL invite for the Discord server
*/

define('discordInviteLink', 'https://discord.gg/C6bCB33gp3');

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
 * virtualTroopArchive: This variable is used to determine which forum to archive virtual troops
*/

$virtualTroopArchive = 513;

/**
 * conventionTroop: This variable is used to determine which forum to post convention troops
*/

$conventionTroop = 213;

/**
 * conventionTroopArchive: This variable is used to determine which forum to archive convention troops
*/

$conventionTroopArchive = 514;

/**
 * lflTroop: This variable is used to determine which forum to post LFL troops
*/

$lflTroop = 212;

/**
 * lflTroopArchive: This variable is used to determine which forum to archive LFL troops
*/

$lflTroopArchive = 224;

/**
 * disneyTroop: This variable is used to determine which forum to post Disney troops
*/

$disneyTroop = 211;

/**
 * disneyTroopArchive: This variable is used to determine which forum to archive Disney troops
*/

$disneyTroopArchive = 225;

/**
 * dualCostume: This array is used to determine which costume club ID should be counted as a dual costume.
*/

$dualCostume = array(5, 7, 8, 9, 10, 11);

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

/**
 * userGroupRIP: The Xenforo user group ID for RIP members
*/

$userGroupRIP = 1496;

/**
 * userGroupSupporter: The Xenforo user group ID for supporter members
*/

$userGroupSupporter = 1507;

// Please note: Do not change the order of squads and clubs after your set up your troop tracker, otherwise you will mess up the squad IDs
// Make sure you run queries on your database if you need to change the order after installation.

/**
 * squadArray
 * name: Name of the squad
 * logo: Image of the squad logo located in images folder
 * costumes: An array of costumes that this squad accepts as troop credit
 * db: The field in the troopers table that determines if a trooper is a member of the club
 * db2: A blank variable with no use as of now
 * eventForum: The forum ID in Xenforo that corresponds to the event forum for this squad
 * userGroup: The Xenforo user group ID assigned to this squad
 */

// Squads
$squadArray = array(
	array(
		"name" => "Everglades Squad",
		"logo" => "everglades_emblem.png",
		"costumes" => array(0, 5, 7, 9, 11),
		"db" => "p501",
		"db2" => "",
		"eventForum" => 9,
		"eventForumArchive" => 107,
		"userGroup" => 44),
	array(
		"name" => "Makaze Squad",
		"logo" => "makaze_emblem.png",
		"costumes" => array(0, 5, 7, 9, 11),
		"db" => "p501",
		"db2" => "",
		"eventForum" => 8,
		"eventForumArchive" => 109,
		"userGroup" => 45),
	array(
		"name" => "Parjai Squad",
		"logo" => "parjai_emblem.png",
		"costumes" => array(0, 5, 7, 9, 11),
		"db" => "p501",
		"db2" => "",
		"eventForum" => 186,
		"eventForumArchive" => 111,
		"userGroup" => 250),
	array(
		"name" => "Squad 7",
		"logo" => "squad7_emblem.png",
		"costumes" => array(0, 5, 7, 9, 11),
		"db" => "p501",
		"db2" => "",
		"eventForum" => 7,
		"eventForumArchive" => 113,
		"userGroup" => 683),
	array(
		"name" => "Tampa Bay Squad",
		"logo" => "tampabay_emblem.png",
		"costumes" => array(0, 5, 7, 9, 11),
		"db" => "p501",
		"db2" => "",
		"eventForum" => 73,
		"eventForumArchive" => 115,
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
 * naCostume: The ID of the costume that is the clubs other or N/A
 * userGroup: The Xenforo user group ID assigned to this club
 */

// Clubs
$clubArray = array(
	array(
		"name" => "Rebel Legion",
		"logo" => "test",
		"costumes" => array(1, 5, 8, 10, 11),
		"db" => "pRebel",
		"db2" => "",
		"db3" => "rebelforum",
		"db3Name" => "Rebel Legion Forum Username",
		"db3Short" => "RL",
		"db3Require" => "0,0,squad:6",
		"dbLimit" => "limitRebels",
		"naCostume" => 720,
		"userGroup" => 1486),
	array(
		"name" => "Droid Builders",
		"logo" => "test", "costumes" => array(3),
		"db" => "pDroid",
		"db2" => "",
		"db3" => "",
		"db3Name" => "",
		"db3Short" => "",
		"db3Require" => "0,0,0",
		"dbLimit" => "limitDroid",
		"naCostume" => 716,
		"userGroup" => 1487),
	array(
		"name" => "Mando Mercs",
		"logo" => "test",
		"costumes" => array(2, 7, 8),
		"db" => "pMando",
		"db2" => "",
		"db3" => "mandoid",
		"db3Name" => "Mando Mercs CAT #",
		"db3Short" => "CAT #",
		"db3Require" => "0,digits,squad:8",
		"db3Link" => "",
		"dbLimit" => "limitMando",
		"naCostume" => 715,
		"userGroup" => 1488),
	array(
		"name" => "Other",
		"logo" => "test",
		"costumes" => array(4),
		"db" => "pOther",
		"db2" => "",
		"db3" => "",
		"db3Name" => "",
		"db3Short" => "",
		"db3Require" => "0,digits,0",
		"db3Link" => "",
		"dbLimit" => "limitOther",
		"naCostume" => 717,
		"userGroup" => 1415),
	array(
		"name" => "Saber Guild",
		"logo" => "test",
		"costumes" => array(6, 9, 10, 11),
		"db" => "pSG",
		"db2" => "",
		"db3" => "sgid",
		"db3Name" => "Saber Guild SG #",
		"db3Short" => "SG #",
		"db3Require" => "0,digits,0",
		"db3Link" => "",
		"dbLimit" => "limitSG",
		"naCostume" => 724,
		"userGroup" => 1491)
);

?>
```

<li>Change ownership for 'images/uploads' to the web server user</li>
<li>Set file permissions to 'images/uploads' to 750</li>
<li>Manually modify getSquad() function in 'config.php' to fit your needs</li>
<li>Set up a Google Cloud API for Google Sheets, then create a service account under "Credentials"</li>
<li>Download the JSON file from the service account, rename it to "sheets_api_secret.json", and upload it the root directory"</li>
<li>On a live server, set up cron jobs located in other/cron.txt</li>
<li>Upload 'other/xenforo_extra/groups.php' and 'other/xenforo_extra/user-upgrades.php' to your Xenforo main directory</li>
</ol>

## Xenforo Template Modifications (OPTIONAL)

message_macros:

Under:

```
<h4 class="message-name"><xf:username user="$user" rich="true" defaultname="{$fallbackName}" itemprop="{{ $includeMicrodata ? 'name' : '' }}" /></h4>
```

Add:

```
<xf:if is="{$user.Profile.custom_fields.trackerid} > 0 && {$user.Profile.custom_fields.fullname} != ''">
<div style="text-align: center; margin-top: 10px;">
	<a href="https://fl501st.com/troop-tracker/index.php?profile={$user.Profile.custom_fields.trackerid}">{$user.Profile.custom_fields.tkid}</a>
	<br />
	{$user.Profile.custom_fields.fullname}
</div>
</xf:if>
```

member_view:

Under:

```
<xf:if contentcheck="true">
	<div class="memberHeader-blurb">
		<dl class="pairs pairs--inline">
			<dt>{{ phrase('last_seen') }}</dt>
			<dd dir="auto">
				<xf:contentcheck><xf:useractivity user="$user" class="pairs--plainLabel" /></xf:contentcheck>
			</dd>
		</dl>
	</div>
</xf:if>
```

Add:

```
<xf:if is="{$user.Profile.custom_fields.trackerid} > 0 && {$user.Profile.custom_fields.fullname} != ''">
	<div class="memberHeader-blurb">
		<dl class="pairs pairs--inline">
			<dt>TKID</dt>
			<dd dir="auto">
				<a href="https://fl501st.com/troop-tracker/index.php?profile={$user.Profile.custom_fields.trackerid}">{$user.Profile.custom_fields.tkid}</a>
			</dd>
		</dl>
	</div>
	
	<div class="memberHeader-blurb">
		<dl class="pairs pairs--inline">
			<dt>Name</dt>
			<dd dir="auto">
				{$user.Profile.custom_fields.fullname}
			</dd>
		</dl>
	</div>
</xf:if>
```

member_tooltip:

Under:

```
<xf:if contentcheck="true">
	<div class="memberTooltip-blurb">
		<dl class="pairs pairs--inline">
			<dt>{{ phrase('last_seen') }}</dt>
			<dd dir="auto">
				<xf:contentcheck><xf:useractivity user="$user" class="pairs--plainLabel" /></xf:contentcheck>
			</dd>
		</dl>
	</div>
</xf:if>
```

Add:

```
<xf:if is="{$user.Profile.custom_fields.trackerid} > 0 && {$user.Profile.custom_fields.fullname} != ''">
	<div class="memberTooltip-blurb">
		<dl class="pairs pairs--inline">
			<dt>TKID</dt>
			<dd dir="auto">
				<a href="https://fl501st.com/troop-tracker/index.php?profile={$user.Profile.custom_fields.trackerid}">{$user.Profile.custom_fields.tkid}</a>
			</dd>
		</dl>
	</div>

	<div class="memberTooltip-blurb">
		<dl class="pairs pairs--inline">
			<dt>Name</dt>
			<dd dir="auto">
				{$user.Profile.custom_fields.fullname}
			</dd>
		</dl>
	</div>
</xf:if>
```

## Please contact me with any questions, comments, or concerns
drennanmattheww@gmail.com

## Check out the mobile app:
https://github.com/MattDrennan/tt_mobile_app

## Visit the live website here:
https://www.fl501st.com/troop-tracker/
