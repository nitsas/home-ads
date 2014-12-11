<?php
  require_once('../phpFiles/mainHeader.php');

  if (!isset($_GET['what']))
    exit;
  else
    $what = $_GET['what'];

  try {
    if ($what === 'changeUsername') {
      whatChangeUsername();       // the code follows below
    }
    else if ($what === 'changeEmail') {
      whatChangeEmail();            // the code follows below
    }
    else if ($what === 'addTelephone') {
      whatAddTelephone();          // the code follows below
    }
    else if ($what === 'removeTelephone') {
      whatRemoveTelephone();     // the code follows below
    }
    else if ($what === 'changeName') {
      whatChangeName();           // the code follows below
    }
    else if ($what === 'changeSurname') {
      whatChangeSurname();       // the code follows below
    }
    else if ($what === 'changePassword') {
      whatChangePassword();      // the code follows below
    }
  }
  catch (Exception $e) {
    echo $e->getMessage();
    exit;
  }
?>





<?php
// ---------- FUNCTIONS USED ABOVE -------------


function whatChangeUsername()
// Changes a user's username.
{
  if (!isset($_GET['oldUsername']) || !isset($_GET['newUsername']))
    return false;

  $ok = changeUsername(urldecode($_GET['oldUsername']), urldecode($_GET['newUsername']));
  if ($ok) {
    echo 'ok';
    return true;
  }
  else 
    return false;
}




function whatChangeEmail()
// Changes a user's email address.
{
  if (!isset($_GET['username']) || !isset($_GET['email']))
    return false;

  $ok = changeEmail(urldecode($_GET['username']), urldecode($_GET['email']));
  if ($ok) {
    echo 'ok';
    return true;
  }
  else
    return false;
}




function whatAddTelephone()
// Adds a new telephone to a user.
{
  if ( !isset($_GET['username']) || !isset($_GET['telephone']) )
    return false;
  else {
    $listTelephones = addTelephone(urldecode($_GET['username']), $_GET['telephone']);
    
    echo '<ul>';
    if ($listTelephones && !empty($listTelephones)) {
      foreach ($listTelephones as $telephone)
        echo '<li>'.$telephone.'</li>';
    }
    else {
      echo '<li>Δεν βρέθηκαν τηλέφωνα επικοινωνίας.</li>';
    }
    echo '</ul>';
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

    echo '<ul>';
    if ($listTelephones && !empty($listTelephones)) {
      foreach ($listTelephones as $telephone)
        echo '<li>'.$telephone.'</li>';
    }
    else {
      echo '<li>Δεν βρέθηκαν τηλέφωνα επικοινωνίας.</li>';
    }
    echo '</ul>';
  }

  return true;
}




function whatChangeName()
// Changes a user's name.
{
  if (!isset($_GET['username']) || !isset($_GET['name']))
    return false;

  $ok = changeName(urldecode($_GET['username']), urldecode($_GET['name']));
  if ($ok) {
    echo 'ok';
    return true;
  }
  else 
    return false;
}




function whatChangeSurname()
// Changes a user's surname.
{
  if (!isset($_GET['username']) || !isset($_GET['surname']))
    return false;

  $ok = changeSurname(urldecode($_GET['username']), urldecode($_GET['surname']));
  if ($ok) {
    echo 'ok';
    return true;
  }
  else
    return false;
}



function whatChangePassword()
// Changes a user's password.
{
  if (!isset($_GET['username']) || !isset($_GET['oldPassword']) || !isset($_GET['newPassword']))
    return false;

  $ok = changePassword(urldecode($_GET['username']), urldecode($_GET['oldPassword']),
                               urldecode($_GET['newPassword']));
  if ($ok) {
    echo 'ok';
    return true;
  }
  else
    return false;
}

    