<?php
  require_once('../phpFiles/mainHeader.php');
  session_start();

  $options = array();

  if (isset($_GET['aggeliaType']))
    $options['aggeliaType'] = $_GET['aggeliaType'];
  if (isset($_GET['categoryID']))
    $options['categoryID'] = $_GET['categoryID'];
  if (isset($_GET['minCost']))
    $options['minCost'] = $_GET['minCost'];
  if (isset($_GET['maxCost']))
    $options['maxCost'] = $_GET['maxCost'];
  if (isset($_GET['minSize']))
    $options['minSize'] = $_GET['minSize'];
  if (isset($_GET['maxSize']))
    $options['maxSize'] = $_GET['maxSize'];
  if (isset($_GET['minYear']))
    $options['minYearMade'] = $_GET['minYear'];
  if (isset($_GET['maxYear']))
    $options['maxYearMade'] = $_GET['maxYear'];
  if (isset($_GET['street']))
    $options['street'] = urldecode($_GET['street']);
  if (isset($_GET['streetNumber']))
    $options['streetNumber'] = $_GET['streetNumber'];
  if (isset($_GET['approved'])) {
    if ( ($_GET['approved'] === 'true') || ($_GET['approved'] === '1') )
      $options['approved'] = true;
    else if ( $_GET['approved'] === 'false' )
      $options['approved'] = false;
    else
      $options['approved'] = 'dontCare';
  }
  if (isset($_GET['paroxi'])) 
    $options['hasParoxi'] = $_GET['paroxi'];

  
  try {
    $results = searchAds($options);
  }
  catch (Exception $e) {
    echo 'Πρόβλημα με το php script που χειρίζεται τα XML requests.';
    exit;
  }
  if (!$results) {
    echo '<p>Δεν υπάρχουν αγγελίες που να πληρούν τα κριτήρια.</p>';
    exit;
  }
  else {
    $num = count($results);
    if ($num == 1)
      echo '<p>Υπάρχει μόνο <b>1</b> αγγελία που πληρεί τα κριτήρια.</p>';
    else
      echo '<p>Υπάρχουν <b>'.count($results).'</b> αγγελίες που πληρούν τα κριτήρια.</p>';

  }

?>