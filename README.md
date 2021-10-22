# 501st Legion Troop Tracker
A troop tracker for the 501st Legion developed for the Florida Garrison.

## Use
You are free to download, modify, and use freely for non-commerical purposes.

## How to setup

Upload all the files to your web server, execute "SQL.sql" on your database, and create a "cred.php" file in the root directory. Cred.php should look like this:
 
```
<?php

// DB Info
define('dbServer', 'MY_SERVER_HERE');
define('dbUser', 'DB_USER_HERE');
define('dbPassword', 'DB_PASSWORD_HERE');
define('dbName', 'DB_NAME_HERE');

// DB Info Merge
define('dbServer2', 'MY_SERVER_HERE');
define('dbUser2', 'DB_USER_HERE');
define('dbPassword2', 'DB_PASSWORD_HERE');
define('dbName2', 'DB_NAME_HERE'); 

// E-mail Creds
define('emailUser', 'EMAIL_USERNAME');
define('emailPassword', 'EMAIL_PASSWORD');
define('emailServer', 'EMAIL_SERVER');
define('emailPort', 587);

// Google Maps
define('googleKey', 'GOOGLE_MAP_API_KEY_HERE');

// PayPal Info
define('ipn', 'script/php/paypal.php');

// Garrison
define('garrison', 'Florida Garrison');
define('garrisonImage', 'garrison_emblem.png');

// Squads
$squadArray = array("Everglades Squad" => "everglades_emblem.png", "Makaze Squad" => "makaze_emblem.png", "Parjai Squad" => "parjai_emblem.png", "Squad 7" => "squad7_emblem.png", "Tampa Bay Squad" => "tampabay_emblem.png");

// Clubs
$clubArray = array("Rebel Legion" => "test", "Droid Builders" => "test", "Mando Mercs" => "test", "Other" => "test");

?>
```


## Please contact me with any questions, comments, or concerns
drennanmattheww@gmail.com

## Visit the live website here:
https://trooptracking.com
