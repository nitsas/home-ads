<?php
  // Called when the new advertisement form is submitted.
  // Will check the form and, if everything is ok, add the new advertisement
  // to the database.

  require_once('mainHeader.php');
  session_start();

  // Check if a guest tried to get here.
  if ( !isset($_SESSION['validUser']) )
    displayProblemPage('Μόνο οι συνδεδεμένοι χρήστες μπορούν να δουν αυτή '
                             .'τη σελίδα.');

  $username = $_SESSION['validUser'];


  // Make sure all the necessary info was given.
  if ( !isset($_POST['aggeliaType'])  ||  !isset($_POST['categoryID'])  ||
       !isset($_POST['cost'])  ||  !isset($_POST['street'])  ||
       !isset($_POST['streetNumber'])  ||  !isset($_POST['size'])  ||
       !isset($_POST['yearMade'])  ) {
    // Call displayProblemPage() --it is in outputFunctions.php-- to display the problem
    // and exit.
    displayProblemPage('Δεν έχετε συμπληρώσει όλα τα απαραίτητα πεδία '
                             .'- παρακαλώ πηγαίνετε πίσω και ξαναπροσπαθήστε.');
  }
       
  // Create short variable names.
  $aggeliaType = $_POST['aggeliaType'];
  $categoryID = $_POST['categoryID'];
  $cost = $_POST['cost'];
  $street = $_POST['street'];
  $streetNumber = $_POST['streetNumber'];
  $size = $_POST['size'];
  $yearMade = $_POST['yearMade'];
  if (isset($_POST['area']))
    $area = $_POST['area'];
  else
    $area = false;
 if (isset($_POST['paroxi']))
    $paroxi = $_POST['paroxi'];
  else
    $paroxi = false;

  try {
    // Check the array of uploaded photos and if all were valid photo
    // files (and no error occured) get an array containing their temporary paths.
    $photo = checkAndGetUploadedPhotosArray();

    // Check $cost.
    if (!ctype_digit($cost)) {
      throw new Exception('Η τιμή στο πεδίο <b>κόστος</b> πρέπει να είναι '
                                 .'αριθμητική '
                                 .'- παρακαλώ πηγαίνετε πίσω και ξαναπροσπαθήστε.');
    }

    // Check $size.
    if (!ctype_digit($size)) {
      throw new Exception('Η τιμή στο πεδίο <b>μέγεθος</b> πρέπει να είναι '
                                 .'αριθμητική '
                                 .'- παρακαλώ πηγαίνετε πίσω και ξαναπροσπαθήστε.');
    }

    // Check $streetNumber.
    if (!ctype_digit($streetNumber)) {
      throw new Exception('Η τιμή στο πεδίο <b>αριθμός</b> πρέπει να είναι '
                                 .'αριθμητική '
                                 .'- παρακαλώ πηγαίνετε πίσω και ξαναπροσπαθήστε.');
    }

    if (!ctype_digit($yearMade)) {
      throw new Exception('Η τιμή στο πεδίο <b>χρονολογία κατασκευής</b> πρέπει '
                                 .'να είναι αριθμητική '
                                 .'- παρακαλώ πηγαίνετε πίσω και ξαναπροσπαθήστε.');
    }

    // Geocode the address (get latitude and longitude).
    $geoRequestUrl = "http://maps.google.com/maps/geo?output=csv&q=";
    $geoRequestUrl .= urlencode($street." ".$streetNumber.", Πάτρα, Ελλάδα");
    $csv = file_get_contents($geoRequestUrl) or ($csv = false);
    if ($csv) {
      $splittedCsv = explode(",", $csv);
      $status = $splittedCsv[0];
      $latitude = $splittedCsv[2];
      $longitude = $splittedCsv[3];
      if (strcmp($status, "200") != 0 || 
          ($latitude == "38.254465" && $longitude == "21.7370665") ) {
        // Geocoding not successful or address not found.
        $latitude = false;
        $longitude = false;
      }
    }

    // Call registerNewAdvertisement() --it is in databaseRelatedFunctions.php-- to add
    // the new advertisement to the database.
    registerNewAdvertisement($username, $aggeliaType, $categoryID, $cost, $street, 
                                     $streetNumber, $size, $yearMade, $area, $paroxi, $photo,
                                     $latitude, $longitude);

    // Successfully added the new advertisement.
    // Call displaySimplePage() --it is in outputFunctions.php-- to display a simple message.
    displaySimplePage('Η αγγελία σας καταχωρήθηκε επιτυχώς. Ωστόσο δεν θα '
                           .'εμφανίζεται στους χρήστες μέχρι να εγκριθεί από κάποιον '
                           .'διαχειριστή.</p><p>'
                           .'<a href="/phpFiles/newAdvertisementPage.php">'
                           .'Θέλω να καταχωρήσω νέα αγγελία.</a><br />'
                           .'<a href="/phpFiles/searchPage.php">Σελίδα αναζήτησης</a>');
  }
  catch (Exception $e) {
    displayProblemPage($e->getMessage());
  }
?>





