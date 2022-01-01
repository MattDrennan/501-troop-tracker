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

// Forum User Database (FL 501ST BOARDS DATABASE)
define('forum_user_database', 'DATABASE.TABLE HERE');

// Forum API Key (Xenforo)
define('xenforoAPI', 'API_KEY_HERE');
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
// Make sure you run queries on your database if you need to change the order or add squads

// Squads
// An array of squads in your garrison and their squad logo file name in the images folder
$squadArray = array("Everglades Squad" => "everglades_emblem.png", "Makaze Squad" => "makaze_emblem.png", "Parjai Squad" => "parjai_emblem.png", "Squad 7" => "squad7_emblem.png", "Tampa Bay Squad" => "tampabay_emblem.png");

// Clubs
// An array of squads and their club image file name in the images folder
$clubArray = array("Rebel Legion" => "test", "Droid Builders" => "test", "Mando Mercs" => "test", "Other" => "test");

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
