<?php
  require_once('mainHeader.php');
  doHtmlHeader('Σπιτικές Αγγελίες: Logout page');

  session_start();

  if ( isset($_SESSION['validUser']) ) 
    // remember the username for later
    $oldUser = stripslashes($_SESSION['validUser']);
  session_destroy();

  echo '<h1>Αποσύνδεση</h1>';
  if (!empty($oldUser)) {
    // successfully logged out
    echo '<p>Αποσυνδεθήκατε επιτυχώς.</p><br />';
  }
  else {
    // wasn't logged in in the first place
    echo '<p>Δεν ήσαστε συνδεδεμένος, οπότε δεν αποσυνδεθήκατε.</p><br />';
    echo '<br /><br /><br />';
  }

  doHtmlFooter();
?>
