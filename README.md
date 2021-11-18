# 501st Legion Troop Tracker
A troop tracker for the 501st Legion developed for the Florida Garrison.

## Use
You are free to download, modify, and use freely for non-commerical purposes.

## How to setup

<ol>
<li>Upload all the files to your web server, execute "SQL.sql" on your database, and create a "cred.php" file in the root directory. Cred.php should look like this:</li>
 
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

// Forum User Database
define('forum_user_database', 'DATABASE.TABLE HERE');

// E-mail Creds
// This is the email server and account you are going to send e-mails from
define('emailUser', 'EMAIL_USERNAME');
define('emailPassword', 'EMAIL_PASSWORD');
define('emailServer', 'EMAIL_SERVER');
define('emailPort', 587);

// Google Maps
// Make sure you get a Google Maps API key to use the Google API. The Google Maps API is used to automatically detect where an event is located
define('googleKey', 'GOOGLE_MAP_API_KEY_HERE');

// PayPal Info
// See PayPal IPN documentation for more information. This file is used to store donations into the database for tracking.
define('ipn', 'script/php/paypal.php');

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
</ol>


## Please contact me with any questions, comments, or concerns
drennanmattheww@gmail.com

## Visit the live website here:
https://trooptracking.com
