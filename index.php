<?php 
  require_once('phpFiles/mainHeader.php');
  session_start();

  // Check how has this script been called.
  // If the login form called it we'll have to log the user in.
  // Else, if the user is a guest or a logged in member, just display the correct page.
  if ( isset($_POST['username']) && isset($_POST['password']) ) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // The login form called this, we'll log the user in.
    try {
      // Call login() --it is in databaseRelatedFunctions.php-- to try and log the user in.
      $ok = login($username, $password);
      if (!$ok)
        throw new Exception('Λάθος username ή password.');
      $_SESSION['validUser'] = $username;
    }
    catch(Exception $e) {
      // login() threw an exception
      displayProblemPage('Ανεπιτυχής προσπάθεια για σύνδεση. Πρέπει να είστε '
                   .'συνδεδεμένος για να δείτε αυτή τη σελίδα.<br />'.$e->getMessage()
                   .'</p><p><a href="/phpFiles/loginPage.php">Σελίδα σύνδεσης.</a>');
    }
  }

  try {
    $userKind = getUserKind();
  }
  catch(Exception $e) {
    echo 'Caught exception: '.$e->getMessage();
    exit;
  }
  
  displayIndexPage($userKind);
  exit;
?>







<?php


function displayIndexPage($userKind)
{
  doHtmlHeader('Σπιτικές Αγγελίες: Κεντρική σελίδα', $userKind, 'indexPage.js', true);

  $top5 = getTop5Ads();
  $mostRecent = getMostRecentAds(5);

  echo '<div class="forTable"><table cellspacing="10px"><tr>';
  echo '<td width="auto"><br />';
  echo '<h3 style="color: red;">Δημοφιλέστερες αγγελίες</h3>';
  displayListOfDescriptions($top5);
  echo '<br /><br /></td></tr><tr><td>';
  echo '<h3 style="color: blue;">Πιο πρόσφατες αγγελίες</h3>';
  displayListOfDescriptions($mostRecent);
  echo '<br /><br /></td></tr><tr><td>';
  echo '<div id="map_canvas" style="width:100%; height:500px">';
  echo '<noscript>Απαιτείται η Javascript για να μπορέσετε να δείτε τον χάρτη.</noscript>';
  echo '</div></td></tr>';
  echo '</table></div><br />';

  // Coordinates for top 5 ads and 5 most recent ads (HIDDEN).
  for ($i=1; $i<6; $i++) {
    if ( isset($top5[$i-1]['latitude']) && isset($top5[$i-1]['longitude']) ) {
      echo '<span class="hidden" id="map_lat_top'.$i.'">'.$top5[$i-1]['latitude'].'</span>';
      echo '<span class="hidden" id="map_lng_top'.$i.'">'.$top5[$i-1]['longitude'].'</span>';
      echo '<br />';
    }
    else {
      echo '<span class="hidden" id="map_lat_top'.$i.'">NULL</span>';
      echo '<span class="hidden" id="map_lng_top'.$i.'">NULL</span>';
      echo '<br />';
    }
  }
  for ($i=1; $i<6; $i++) {
    if ( isset($mostRecent[$i-1]['latitude']) && isset($mostRecent[$i-1]['longitude']) ) {
      echo '<span class="hidden" id="map_lat_recent'.$i.'">'.$mostRecent[$i-1]['latitude'].'</span>';
      echo '<span class="hidden" id="map_lng_recent'.$i.'">'.$mostRecent[$i-1]['longitude'].'</span>';
      echo '<br />';
    }
    else {
      echo '<span class="hidden" id="map_lat_recent'.$i.'">NULL</span>';
      echo '<span class="hidden" id="map_lng_recent'.$i.'">NULL</span>';
      echo '<br />';
    }
  }
  
  doHtmlFooter();
}


?>

