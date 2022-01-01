<?php

include "config.php";

   /*// Create Thread
   $curl = curl_init();

   curl_setopt_array($curl, [
     CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/threads/40629",
     CURLOPT_POST => 1,
     CURLOPT_POSTFIELDS => "title=LOL&message=TEST",
     CURLOPT_CUSTOMREQUEST => "POST",
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_ENCODING => "",
     CURLOPT_TIMEOUT => 0,
     CURLOPT_HTTPHEADER => [
       "XF-Api-Key: " . xenforoAPI,
     ],
   ]);

   $response = curl_exec($curl);

   curl_close($curl);

   print_r( json_decode($response, true));*/

   $test = deleteThread(40584, true);

   print_r($test);

      // Create Thread
   /*$curl = curl_init();

   curl_setopt_array($curl, [
     CURLOPT_URL => "https://www.fl501st.com/forums/index.php?api/posts/628795",
     CURLOPT_POST => 1,
     CURLOPT_POSTFIELDS => "message=TESTfsdafdsfsadf",
     CURLOPT_CUSTOMREQUEST => "POST",
     CURLOPT_RETURNTRANSFER => true,
     CURLOPT_ENCODING => "",
     CURLOPT_TIMEOUT => 0,
     CURLOPT_HTTPHEADER => [
       "XF-Api-Key: " . xenforoAPI,
     ],
   ]);

   $response = curl_exec($curl);

   curl_close($curl);

   print_r( json_decode($response, true));*/

?>