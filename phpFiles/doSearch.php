<?php
  require_once('mainHeader.php');
  session_start();

  $aggeliaType = $_POST['aggeliaType'];
  $categoryID = $_POST['categoryID'];
  $minCost = intval($_POST['minCost']);
  $maxCost = intval($_POST['maxCost']);
  $minSize = intval($_POST['minSize']);
  $maxSize = intval($_POST['maxSize']);
  $minYear = intval($_POST['minYear']);
  if ($minYear < 1901 || $minYear > 2155)
    $minYear = false;
  $maxYear = intval($_POST['maxYear']);
  if ($maxYear < 1901 || $maxYear > 2155)
    $maxYear = false;
  $street = $_POST['street'];
  $street = preg_replace("/[^a-zA-Zα-ωΑ-Ω0-9-άίόύέώ'πρστυφχψςϋϊ]/", "", $street);
  $streetNumber = intval($_POST['streetNumber']);
  if (isset($_POST['paroxi']))
    $paroxi = $_POST['paroxi'];
  else
    $paroxi = false;
  if (isset($_POST['approved']))
    $approved = $_POST['approved'];
  else
    $approved = 'true';

  // Set the options array (to pass to searchAds()).
  $options = array();
  if ( ($aggeliaType) && ($aggeliaType !== 'dontCare') )
    $options['aggeliaType'] = $aggeliaType;
  if ($categoryID !== 'false')
    $options['categoryID'] = $categoryID;
  if ($minCost)
    $options['minCost'] = $minCost;
  if ($maxCost)
    $options['maxCost'] = $maxCost;
  if ($minSize)
    $options['minSize'] = $minSize;
  if ($maxSize)
    $options['maxSize'] = $maxSize;
  if ($minYear)
    $options['minYear'] = $minYear;
  if ($maxYear)
    $options['maxYear'] = $maxYear;
  if ($street)
    $options['street'] = $street;
  if ($streetNumber)
    $options['streetNumber'] = $streetNumber;
  if ($paroxi)
    $options['hasParoxi'] = $paroxi;
  if ( $approved === 'true' )
    $options['approved'] = true;
  else if ( $approved === 'false' )
    $options['approved'] = false;
  else if ( $approved === 'dontCare' ) 
    $options['approved'] = 'dontCare';

  // Do the search.    
  try {
    $listAds = searchAds($options);
    if (!$listAds) {
      displaySimplePage('Δεν βρέθηκαν αγγελίες που να πληρούν τα κριτήρια. '
                             .'<br /><br /><a href="/phpFiles/searchPage.php">Νέα αναζήτηση!</a>');
    }
    else {
      doHtmlHeader('Σπιτικές Αγγελίες: Αποτελέσματα αναζήτησης', getUserKind());
      echo '<h1>Αποτελέσματα αναζήτησης</h1><br />';
      echo '<h3>Βρέθηκαν οι παρακάτω αγγελίες:</h3>';
      displayListOfDescriptions($listAds);
      echo '<br />';
      echo '<p><i>Κάντε κλικ σε όποια θέλετε για να δείτε αναλυτικές πληροφορίες.</i></p>';
      doHtmlFooter();
    }
  }
  catch (Exception $e) {
    echo $e->getMessage();
  }
    

?>
