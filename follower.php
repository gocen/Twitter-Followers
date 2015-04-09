<?php
header('Content-type: text/html; charset=utf8');
require_once('twitteroauth/twitteroauth.php');
	// consumer ve access
	$consumer_key = '';
	$consumer_secret = '';
	$access_token = '';
	$access_token_secret = '';	
	// sınıfı başlatalım
	$connection = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
 
// Empty array that will be used to store followers.
$profiles = array();
// Get the ids of all followers. Max. 5000
$sc_name = 'mgocenoglu';
$ids = $connection->get("https://api.twitter.com/1.1/followers/ids.json?screen_name=$sc_name");

// Chunk the ids in to arrays of 100.
$ids_arrays = array_chunk($ids->ids, 90);

// Loop through each array of 100 ids.
$i=1;
foreach($ids_arrays as $implode) {
  // Perform a lookup for each chunk of 100 ids.
  $user_ids=implode(',', $implode);

  $results = $connection->get("https://api.twitter.com/1.1/users/lookup.json?user_id=$user_ids");
  
  // Loop through each profile result.
  foreach($results as $profile) {
    //echo $i++."-".$profile->name." ".$profile->followers_count."<br>";
    $profiles[$profile->name] = $profile;
  }
}
//Sorting profiles according to followers count
$sortArray = array();
foreach($profiles as $person){ 
    foreach($person as $key=>$value){ 
        if(!isset($sortArray[$key])){ 
            $sortArray[$key] = array(); 
        } 
        $sortArray[$key][] = $value; 
    }
}
$orderby = "followers_count"; //change this to whatever key you want from the array 
array_multisort($sortArray[$orderby],SORT_DESC,$profiles);
?>
<html><body><table border=1>
<?php
foreach($profiles as $profile) {
	$friendship = $connection->get("https://api.twitter.com/1.1/friendships/show.json?source_screen_name=".$profile->screen_name."&target_screen_name=".$sc_name);
    echo "<tr><td>".$i++."</td><td>".$profile->name."</td><td>".$profile->screen_name."</td><td>";
    echo $profile->followers_count."</td><td>".$friendship->following."</td></tr>"; 
}
?>
</table></body></html>
