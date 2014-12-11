<?php
  require_once('mainHeader.php');
  session_start();

  try {
    $userKind = getUserKind();
    if ( !isset($_SESSION['validUser']) || !isset($_GET['username']) || 
         ($_GET['username'] != $_SESSION['validUser'] && $userKind != 'admin') ) {
      displayProblemPage('Λυπούμαστε, δεν επιτρέπεται να δείτε αυτή τη σελίδα.');
    }
    else {
      displayProfilePage($_GET['username'], $userKind);
    }
  }
  catch (Exception $e) {
    displayProblemPage($e->getMessage());
  }

  exit;

?>








<?php


function displayProfilePage($username, $userKind)
// Displays the profile page for user $username.
{
  $userInfo = getUserInfo($username);

  doHtmlHeader('Σπιτικές Αγγελίες: Προφίλ μέλους', $userKind, 'profilePage.js');

  echo '<h1>Προφίλ μέλους</h1><br />';
  echo '<div class="forTable"><table id="theTable">';
  // Table headers.
  echo '<th colspan="2" width="480px" align="left">Προσωπικές πληροφορίες</th>'
         .'<th width="250px">Αλλαγή</th>';
  // username
  echo '<tr>';
  echo '<td width="160px" style="text-align:center;"><b>username:</b></td><td width="320px">'
        .$username.'</td>';
  if ($userKind == 'admin') {
    echo '<td width="250px"><input type="text" id="usernameField" size="25" maxlength="25" />'
         .' &nbsp; <input type="button" class="button" value="ok" '
         .'onclick="changeUsername(\''.$username.'\')" /></td>';
  }
  else {
    echo '<td width="250px"></td>';
  }
  echo '</tr><tr>';
  // email
  echo '<td style="text-align:center;"><b>email:</b></td><td>'.$userInfo['email'].'</td>'
         .'<td><input type="text" size="25" maxlength="30" id="emailField" /> &nbsp; '
         .'<input type="button" class="button" value="ok" '
         .'onclick="changeEmail(\''.$username.'\')" /></td>';
  echo '</tr><tr>';
  // telephones
  echo '<td style="text-align:center;"><b>Τηλέφωνα:</b></td><td><ul>';
  if (!$userInfo['telephone'])
    echo '<li>Δεν βρέθηκαν τηλέφωνα.</li>';
  else {
    foreach ($userInfo['telephone'] as $telephone)
      echo "<li>$telephone</li>";
  }
  echo '</ul></td>';
  echo '<td><input type="text" size="25" maxlength="30" id="telephoneField" /> &nbsp;&nbsp; '
         .'<input type="button" value=" + " onclick="addTelephone(\''.$username.'\')" /> '
         .'<input type="button" value=" - " onclick="removeTelephone(\''.$username.'\')" /></td>';
  // name
  echo '</tr><tr>';
  echo '<td style="text-align:center;"><b>Όνομα:</b></td><td>'.$userInfo['name'].'</td><td>'
         .'<input type="text" size="25" maxlength="25" id="nameField" /> &nbsp; '
         .'<input type="button" class="button" value="ok" '
         .'onclick="changeName(\''.$username.'\')" /></td>';
  // surname
  echo '</tr><tr>';
  echo '<td style="text-align:center;"><b>Επίθετο:</b></td><td>'.$userInfo['surname'].'</td><td>'
         .'<input type="text" size="25" maxlength="30" id="surnameField" /> &nbsp; '
         .'<input type="button" class="button" value="ok" '
         .'onclick="changeSurname(\''.$username.'\')" /></td>';
  echo '</tr></table></div><br /><br /><br /><br />';

  echo '<h2>Αλλαγή κωδικού πρόσβασης</h2><br />';
  echo '<div class="forTable" style="text-align:center;"><table><tr><td width="450px">';
  echo '<table><tr>';
  // old password
  echo '<td width="auto"><b>Παλιός κωδικός:</b></td><td width="200">'
         .'<input type="text" size="25" maxlength="30" id="oldPasswordField" /></td>';
  echo '</tr><tr>';
  // new password
  echo '<td><b>Νέος κωδικός:</b></td><td>'
         .'<input type="password" size="25" maxlength="30" id="newPasswordField" /></td>'
         .'</tr><tr>';
  // new password (repeat)
  echo '<td><b>Επαλήθευση νέου κωδικού:</b></td><td>'
         .'<input type="password" size="25" maxlength="30" id="newPassword2Field" /></td>'
         .'</tr>';
  echo '</table></td>';
  echo '<td width="100px" align="left"><input type="button" value="αλλαγή!" class="button"'
         .'onclick="changePassword(\''.$username.'\')" /></td>';
  echo '</tr></table></div>';
  echo '<p>Προσοχή, ο κωδικός πρόσβασης πρέπει να αποτελείται από τουλάχιστον '
         .'5 ψηφία.</p><br /><br />';

  doHtmlFooter();
}


?>
