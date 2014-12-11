<?php
  //This is displayAdvertisementPage.php.
  require_once('mainHeader.php');
  session_start();

  if (!isset($_GET['aggeliaID']))
    displayProblemPage('Δεν έχει επιλεγεί αγγελία για προβολή.');
  else {
    $aggeliaID = $_GET['aggeliaID'];
    if ( !is_string($aggeliaID) || !ctype_digit($aggeliaID) )
      displayProblemPage('Μη έγκυρος κωδικός αγγελίας προς προβολή.');
  }

  try {
    $userKind = getUserKind();
    doHtmlHeader('Σπιτικές Αγγελίες: Προβολή αγγελίας', $userKind, 'displayAdPage.js', true);
    displayAdvertisement($aggeliaID, $userKind);
    doHtmlFooter();
  }
  catch (Exception $e) {
    echo '<p>'.$e->getMessage().'</p>';
  }

?>







<?php

function displayAdvertisement($aggeliaID, $userKind='guest')
// Displays all information related to the advertisement with id $aggeliaID.
{
  $searchOptions = array();
  $searchOptions['aggeliaID'] = $aggeliaID;
  $aggeliaInfo = searchAds($searchOptions, 'full');
  if (!$aggeliaInfo)
    throw new Exception('Μη έγκυρος κωδικός αγγελίας.');
  else if (count($aggeliaInfo) != 1)
    throw new Exception('Πρόβλημα στη βάση! Βρέθηκαν περισσότερες από μία '
                               .'αγγελίες με τον ίδιο κωδικό.');
  else {
    $aggeliaInfo = $aggeliaInfo[0];
    if (is_array($aggeliaInfo['eidosParoxis']))
      sort($aggeliaInfo['eidosParoxis']);
  }

  if ($userKind === 'admin')
    $isAdmin = true;
  else
    $isAdmin = false;

  // Don't let users see non-approved ads.
  if (!$isAdmin && !$aggeliaInfo['approved'])
    throw new Exception('Η αγγελία που ζητήσατε προς το παρόν δεν είναι εγκεκριμένη '
                               .'από κάποιον διαχειριστή.');

  if ($isAdmin) {
    $eidiParoxwn = getParoxes();
    if (!$eidiParoxwn)
      $eidiParoxwn = array();
  }

  if ($userKind != 'guest') {
    $favorites = getFavorites($_SESSION['validUser'], 'idsOnly');
    if (!$favorites)
      $favorites = array();
  }

  $str = createMainAdInfo($aggeliaInfo);

  echo '<br /><span class="forTable"><span id="content2">';
  echo '<table cellspacing="10px" id="theTable"><tr>';
  // --- Approve or disapprove (admin only) ---
  if ($isAdmin) {
    echo '<td><span id="favoritesButtonSpan">';
    if ( !in_array($aggeliaID, $favorites) ) {
      echo '<input type="button" id="addToFavoritesButton" class="button" '
             .'value="Προσθήκη στα αγαπημένα" '
             .'onclick="addToFavorites(\''.$_SESSION['validUser'].'\', \''.$aggeliaID.'\')" />';
    }
    echo '</span>';
    echo '<input type="button" class="button" value="Διαγραφή αγγελίας" '
           .' onclick="deleteAd('.$aggeliaID.')" />';
    echo '</td><td>';
    if (!$aggeliaInfo['approved']) {
      echo '<input type="button" class="button" value="Έγκριση αγγελίας!" '
            .'id="approvalButton" '
            .'onclick="changeApproved('.$aggeliaID.')" />';
    }
    else {
      echo '<input type="button" class="button" value="Σήμανση ως μη εγκεκριμένη!" '
            .'id="approvalButton" '
            .'onclick="changeApproved('.$aggeliaID.')" />';
    }
    echo '</td></tr><tr>';
  }
  else if ( isset($_SESSION['validUser']) ) { 
    echo '<td colspan="2"><div class="centerAlign">';
    if (!in_array($aggeliaID, $favorites)) {
      echo '<span id="favoritesButtonSpan">';
      echo '<input type="button"  id="addToFavoritesButton" class="button" '
             .'value="Προσθήκη στα αγαπημένα" '
             .'onclick="addToFavorites(\''.$_SESSION['validUser'].'\', \''.$aggeliaID.'\')" />';
      echo '</span>';
    }
    if ($_SESSION['validUser'] == $aggeliaInfo['username'] ) {
      echo '<input type="button" class="button" value="Διαγραφή αγγελίας" '
            .' onclick="deleteAd('.$aggeliaID.')" />';
    }
    echo '</div></td></tr><tr>';
  }
  // --- Main info ---
  echo '<td width=auto>';
  echo '<h2>Περιγραφή:</h2>';
  echo '<span id="mainInfoSpan"><p>'.$str.'</p></span><br />';
  echo '</td>';
  if ($isAdmin) {
    echo '<td width=200>';
    echo '<span id="setMainInfoSpan">';
    echo '<div class="centerAlign">';
    echo '<input type="button" value="Αλλαγή!" id="allagiButton" class="button" '
           .'onclick="changeInfo('.$aggeliaID.')" />';
    echo '</div></span>';
    echo '</td>';
  }
  echo '</tr><tr>';
  // --- Paroxes ---
  echo '<td>';
  echo '<h2>Παροχές:</h2>';
  echo '<ul id="list">';
  if ($aggeliaInfo['eidosParoxis']) {
    foreach ($aggeliaInfo['eidosParoxis'] as $paroxi)
      echo '<li>'.$paroxi.'</li>';
  }
  else {
    echo '<li>Δεν βρέθηκαν παροχές.</li>';
  }
  echo '</ul><br />';
  echo '</td>';
  if ($isAdmin) {
    echo '<td><span id="paroxesSpan">';
    echo 'Προσθήκη παροχής:<br />';
    echo '<select name="addParoxi" onchange="addParoxi('.$aggeliaID.', this.value)">'
           .'<option selected="selected" value="false">Καμία</option>';
    foreach ($eidiParoxwn as $key => $value) 
      if (!in_array($key, $aggeliaInfo['eidosParoxis']))
        echo '<option value="'.$key.'">'.$key.'</option>';
    echo '</select><br /><br />';
    echo 'Αφαίρεση παροχής:<br />';
    echo '<select name="removeParoxi" onchange="removeParoxi('.$aggeliaID.', this.value)">'
           .'<option selected="selected" value="false">Καμία</option>';
    foreach ($aggeliaInfo['eidosParoxis'] as $paroxi) 
      echo '<option value="'.$paroxi.'">'.$paroxi.'</option>';
    echo '</select><br />';
    echo '</span></td>';
  }
  echo '</tr><tr>';
  // --- Telephones ---
  echo '<td>';
  echo '<h2>Τηλέφωνα επικοινωνίας:</h2><span id="telephonesSpan"><ul id="list">';
  if ($aggeliaInfo['telephone']) {
    foreach ($aggeliaInfo['telephone'] as $telephone)
      echo '<li>'.$telephone.'</li>';
  }
  else {
    echo '<li>Δεν βρέθηκαν τηλέφωνα επικοινωνίας.</li>';
  }
  echo '</ul></span><br />';
  echo '</td>';
  if ($isAdmin) {
    echo '<td>';
    echo 'Προσθήκη/Αφαίρεση τηλεφώνου:<br />';
    echo '<input type="text" id="newTelephone" name="newTelephone" size="11" maxlength="10" />&nbsp;';
    echo '<input type="button" value=" + " name="addTelephone" '
           .'onclick="addTelephone(\''.$aggeliaInfo['username'].'\')" />';
    echo '&nbsp;<input type="button" value=" - " name="removeTelephone" '
           .'onclick="removeTelephone(\''.$aggeliaInfo['username'].'\')" /><br />';
    echo '</td>';
  }
  echo '</tr><tr>';
  // --- Map ---
  echo '<td>';
  echo '<h2>Χάρτης:</h2><br /><div id="map_canvas" style="width:500px; height:300px">';
  echo '<noscript>Απαιτείται η Javascript για να μπορέσετε να δείτε τον χάρτη.</noscript>';
  echo '</div><br /></td>';
  echo '</tr><tr>';
  // --- Photos ---
  echo '<td colspan="2">';
  echo '<h2>Φωτογραφίες:</h2><ul id="list">';
  if ($aggeliaInfo['photoPath']) {
    foreach ($aggeliaInfo['photoPath'] as $path)
      echo '<li><a href="'.$path.'"><img src="'.$path.'" /></a></li>';
  }
  else {
    echo '<li>Δεν βρέθηκαν φωτογραφίες.</li>';
  }
  echo '</ul>';
  echo '</td>';
  echo '</tr></table></span></span>';

  echo '<br /><span id="hiddenLat" class="hidden">'.$aggeliaInfo['latitude'].'</span>';
  echo '<br /><span id="hiddenLng" class="hidden">'.$aggeliaInfo['longitude'].'</span>';

  // Increase the timesViewed counter of this ad by one.
  viewedAd($aggeliaID);

}


?>
