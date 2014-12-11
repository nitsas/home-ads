<?php
  require_once('../phpFiles/mainHeader.php');

  if (!isset($_GET['what']))
    exit;
  else
    $what = $_GET['what'];

  try {  
    if ($what === 'addParoxi') {
      whatAddParoxi();            // the code follows below
    }  
    else if ($what === 'removeParoxi') {
      whatRemoveParoxi();       // the code follows below
    }
    else if ($what === 'changeApproval') {
      whatChangeApproval();    // the code follows below
    }
    else if ($what === 'addTelephone') {
      whatAddTelephone();       // the code follows below
    }
    else if ($what === 'removeTelephone') {
      whatRemoveTelephone();  // the code follows below
    }
    else if ($what === 'changeInfo') {
      whatDisplayChangeInfo(); // the code follows below
    }
    else if ($what === 'confirmChangeInfo') {
      whatConfirmChangeInfo(); // the code follows below
    }
    else if ($what === 'deleteAd') {
      whatDeleteAd();             // the code follows below
    }
    else if ($what === 'addToFavorites') {
      whatAddToFavorites();     // the code follows below
    }
    exit;
  }
  catch (Exception $e) {
    echo $e->getMessage();
    exit;
  }
?>




<?php
// ---------- FUNCTIONS USED ABOVE -------------

function whatAddParoxi()
// Add a new paroxi to an add.
{
  if (!isset($_GET['aggeliaID']) || !isset($_GET['newParoxi']))
    return false;
  else {
    $aggeliaID = $_GET['aggeliaID'];
    $newParoxi = urldecode($_GET['newParoxi']);
  }
  $listParoxes = addParoxi($aggeliaID, $newParoxi);
  $allParoxes = getParoxes();

  if ($listParoxes) {
    // Now the javaScript will have to change the page so that the new info is shown.
    // So, we'll create the output in here (it's easier).
    echo '<h2>Παροχές:</h2>';
    echo '<ul id="list">';
    foreach ($listParoxes as $paroxi)
      echo '<li>'.$paroxi.'</li>';
    echo '</ul><br />';
    echo '<separator>';
    echo 'Προσθήκη παροχής:<br />';
    echo '<select name="addParoxi" onchange="addParoxi('.$aggeliaID.', this.value)">'
           .'<option selected value="false">Καμία</option>';
    foreach ($allParoxes as $key => $value) 
      if (!in_array($key, $listParoxes))
        echo '<option value="'.$key.'">'.$key.'</option>';
    echo '</select><br /><br />';
    echo 'Αφαίρεση παροχής:<br />';
    echo '<select name="removeParoxi" onchange="removeParoxi()">'
           .'<option selected value="false">Καμία</option>';
    foreach ($listParoxes as $paroxi) 
      echo '<option value="'.$paroxi.'">'.$paroxi.'</option>';
    echo '</select><br />';
    exit;
  }
  else 
    // A problem occured, don't do anything.
    exit;
}


function whatRemoveParoxi()
// Remove a paroxi from the ad.
{
  if (!isset($_GET['aggeliaID']) || !isset($_GET['eidosParoxis']))
    return false;
  else {
    $aggeliaID = $_GET['aggeliaID'];
    $eidosParoxis = urldecode($_GET['eidosParoxis']);
    $listParoxes = removeParoxi($aggeliaID, $eidosParoxis);
    $allParoxes = getParoxes();

    // The javaScript will have to change the page to reflect the change made.
    // So, we'll create the output in here (it's easier).
    echo '<h2>Παροχές:</h2>';
    echo '<ul id="list">';
    if ( empty($listParoxes) )
      echo '<li>Δεν βρέθηκαν παροχές.</li>';
    else {
      foreach ($listParoxes as $paroxi)
        echo '<li>'.$paroxi.'</li>';
    }
    echo '</ul><br />';
    echo '<separator>';
    echo 'Προσθήκη παροχής:<br />';
    echo '<select name="addParoxi" onchange="addParoxi('.$aggeliaID.', this.value)">'
           .'<option selected value="false">Καμία</option>';
    if ( is_array($allParoxes) ) {
      foreach ($allParoxes as $key => $value) 
        if (!in_array($key, $listParoxes))
          echo '<option value="'.$key.'">'.$key.'</option>';
    }
    echo '</select><br /><br />';
    echo 'Αφαίρεση παροχής:<br />';
    echo '<select name="removeParoxi" onchange="removeParoxi()">'
           .'<option selected value="false">Καμία</option>';
    foreach ($listParoxes as $paroxi) 
      echo '<option value="'.$paroxi.'">'.$paroxi.'</option>';
    echo '</select><br />';
    exit;
  }
}



function whatChangeApproval()
// If ad $aggeliaID is approved make it non-approved,
// else make it approved.
{
  if (!isset($_GET['aggeliaID']))
    return false;
  else {
    $result = changeApproval($_GET['aggeliaID']);
    echo $result;
  }
  
  return true;
}



function whatAddTelephone()
// Adds a new telephone to a user.
{
  if ( !isset($_GET['username']) || !isset($_GET['telephone']) )
    return false;
  else {
    $listTelephones = addTelephone(urldecode($_GET['username']), $_GET['telephone']);
    
    echo '<h2>Τηλέφωνα επικοινωνίας:</h2><span id="telephonesSpan"><ul id="list">';
    if ($listTelephones && !empty($listTelephones)) {
      foreach ($listTelephones as $telephone)
        echo '<li>'.$telephone.'</li>';
    }
    else {
      echo '<li>Δεν βρέθηκαν τηλέφωνα επικοινωνίας.</li>';
    }
    echo '</ul></span><br />';
  }

  return true;
}



function whatRemoveTelephone()
// Removes a telephone number from a user.
{
  if (!isset($_GET['username']) || !isset($_GET['telephone']) )
    return false;
  else {
    $listTelephones = removeTelephone(urldecode($_GET['username']), $_GET['telephone']);

    echo '<h2>Τηλέφωνα επικοινωνίας:</h2><span id="telephonesSpan"><ul id="list">';
    if ($listTelephones && !empty($listTelephones)) {
      foreach ($listTelephones as $telephone)
        echo '<li>'.$telephone.'</li>';
    }
    else {
      echo '<li>Δεν βρέθηκαν τηλέφωνα επικοινωνίας.</li>';
    }
    echo '</ul></span><br />';
  }

  return true;
}




function whatDisplayChangeInfo()
// Displays options to change an ad's info.
{
  if (!isset($_GET['aggeliaID']))
    return false;
  else {
    // Get the ad's current info.
    $options = array('aggeliaID' => $_GET['aggeliaID']);
    $aggeliaInfo = searchAds($options);
    if (!$aggeliaInfo || !is_array($aggeliaInfo))
      return false;
    $category = getCategories();
    if (!$category)
      throw new Exception('Δεν υπάρχουν κατηγορίες στο σύστημα.');

    // aggeliaInfo is an array of arrays (one for each ad fulfilling options)
    // so we'll only keep the first row (first array)
    $aggeliaInfo = $aggeliaInfo[0];

    // Now output everything as it should look when javaScript updates the 
    // displayAdvertisementPage content.
    echo '<h2>Νέα Περιγραφή:</h2><p>';
    echo '<select id="aggeliaType">';
    if ($aggeliaInfo['aggeliaType'] == 'sale') {
      echo '<option selected="selected" value="sale">Πωλείται</option>';
      echo '<option value="rent">Ενοικιάζεται</option>';
    }
    else {
      echo '<option selected="selected" value="rent">Ενοικιάζεται</option>';
      echo '<option value="sale">Πωλείται</option>';
    }
    echo '</select> <select id="categoryID">';
    while ( list($categoryName, $categoryID) = each($category) )
      if ($categoryName == $aggeliaInfo['categoryName'])
        echo "<option selected=\"selected\" value=\"$categoryID\">$categoryName</option>";
      else
        echo "<option value=\"$categoryID\">$categoryName</option>";
    echo '</select>';
    echo ' του ';
    echo '<input type="text" id="yearMade" size="4" maxlength="4" '
           .'value="'.$aggeliaInfo['yearMade'].'" />, ';
    echo '<input type="text" id="size" size="5" maxlength="5" value="'.$aggeliaInfo['size']
           .'" /> τ.μ., οδός ';
    echo '<input type="text" id="street" size="30" maxlength="40" '
           .'value="'.ucfirst(strtolower($aggeliaInfo['street'])).'" /> ';
    echo '<input type="text" id="streetNumber" size="3" maxlength="5" '
           .'value="'.$aggeliaInfo['streetNumber'].'" />';
    echo ', περιοχή ';
    if (!is_null($aggeliaInfo['area']))
      echo '<input type="text" id="area" size="15" maxlength="30" value="'.$aggeliaInfo['area'].'" />';
    else 
      echo '<input type="text" id="area" size="15" maxlength="30" />';
    echo ', τιμή &euro;';
    echo '<input type="text" id="cost" size="9" maxlength="9" value="'.$aggeliaInfo['cost'].'" />';
    if ($aggeliaInfo['aggeliaType'] == 'rent')
      echo '/μήνα';
    echo '.';
    echo '</p><p></p></br>';

    return true;
  }
}




function whatConfirmChangeInfo()
// Changes an ad's info.
{
  if (!isset($_GET['aggeliaID']) || !isset($_GET['aggeliaType']) || 
     !isset($_GET['categoryID']) || !isset($_GET['street']) )
    return false;
  
  if (isset($_GET['yearMade']))
    $yearMade = $_GET['yearMade'];
  else
    $yearMade = false;
  if (isset($_GET['size']))
    $size = $_GET['size'];
  else
    $size = false;
  if (isset($_GET['streetNumber']))
    $streetNumber = $_GET['streetNumber'];
  else
    $streetNumber = false;
  if (isset($_GET['area']))
    $area = $_GET['area'];
  else
    $area = false;
  if (isset($_GET['cost']))
    $cost = $_GET['cost'];
  else
    $cost = false;

  // Geocode the address.
  $geoRequestUrl = "http://maps.google.com/maps/geo?output=csv&q=";
  $geoRequestUrl .= urlencode($_GET['street']." ".$streetNumber.", Πάτρα, Ελλάδα");
  $csv = file_get_contents($geoRequestUrl) or ($csv = false);
  $splittedCsv = explode(",", $csv);
  $status = $splittedCsv[0];
  $latitude = $splittedCsv[2];
  $longitude = $splittedCsv[3];
  echo $latitude.'<separator>'.$longitude;              // so that the javaScript script knows
  if (strcmp($status, "200") != 0 || 
        ($latitude == "38.254465" && $longitude == "21.7370665") ) {
    // Geocoding not successful or address not found.
    $latitude = false;
    $longitude = false;
  }

  // Make the changes in the database.
  changeAdInfo($_GET['aggeliaID'], $_GET['aggeliaType'], $_GET['categoryID'], $yearMade,
                    $size, $_GET['street'], $streetNumber, $area, $cost, $latitude, $longitude);

  // Get the new ad info.
  $options = array('aggeliaID' => $_GET['aggeliaID']);
  $aggeliaInfo = searchAds($options);
  // aggeliaInfo is an array of arrays (one for each ad satisfying options), 
  // so we'll keep only the first row
  $aggeliaInfo = $aggeliaInfo[0];

  // Generate the output the javaScript function will use to change the page (the new ad info).
  $str = createMainAdInfo($aggeliaInfo);
  echo '<separator>';
  echo '<h2>Περιγραφή:</h2>';
  echo '<span id="setMainInfoSpan"><p>'.$str.'</p></span><br />';

  return true;
}




function whatDeleteAd()
// Deletes the currently viewed ad from the database.
{
  if (!isset($_GET['aggeliaID']))
    return false;

  $aggeliaID = $_GET['aggeliaID'];
  $result = deleteAd($aggeliaID);
  if ($result) {
    displaySimplePage('Η αγγελία διαγράφηκε επιτυχώς.</p><p>'
            .'<a href="/phpFiles/searchPage.php">Αναζήτηση αγγελίας</a><br />'
            .'<a href="/phpFiles/newAdvertisementPage.php">Καταχώρηση νέας αγγελίας</a>');
  }
}




function whatAddToFavorites()
// Adds an ad to a user's favorites.
{
  if (!isset($_GET['username']) || !isset($_GET['aggeliaID']))
    return false;

  $r = addToFavorites($_GET['username'], $_GET['aggeliaID']);
  if ($r)
    echo 'ok';

  return true;
}


?>