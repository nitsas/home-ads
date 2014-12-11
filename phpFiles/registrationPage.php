<?php
  require_once('mainHeader.php');
  session_start();

  if (isset($_SESSION['validUser']))
    displayProblemPage('Είστε ήδη εγγεγραμμένος σαν '.$_SESSION['validUser'].'.');
  else {
    doHtmlHeader('Σπιτικές Αγγελίες: Registration page', 'guest', 'registrationPage.js');
    echo '<h1>Εγγραφή νέου χρήστη</h1><br />';

    displayRegistrationForm();

    doHtmlFooter();
  }
?>



<?php
function displayRegistrationForm()
{
?>
  <br />

  <form id="content2" action="registerNewUser.php" method='post'>
    <fieldset>
      <legend align="top">
        &#160;Παρακαλώ συμπληρώστε τα στοιχεία σας:&#160;
      </legend><object class="forTable">
      <table cellspacing="10px" id="theTable">
        <tr>
          <td width="270px" align="right"><b>Προτιμώμενο username (*):</b></td>
          <td width="auto"><input type="text" name="username" size="25" maxlength="25" /></td>
        </tr><tr>
          <td align="right"><b>Password (*):</b></td>
          <td><input type="password" name="password" size="25" maxlength="30" /></td>
        </tr><tr>
          <td align="right"><b>Επαλήθευση password (*):</b></td>
          <td><input type="password" name="password2" size="25" maxlength="30" /></td>
        </tr><tr>
          <td align="right"><b>E-mail (*):</b></td>
          <td><input type="text" name="email" size="25" maxlength="30" /></td>
        </tr><tr>
          <td align="right"><b>Τηλέφωνο (*):</b></td>
          <td><input type="text" name="telephone[0]" size="25" maxlength="40" /></td>
        </tr><tr>
          <td align="right"><b>Όνομα:</b></td>
          <td><input type="text" name="name" size="25" maxlength="25" /></td>
        </tr><tr>
          <td align="right"><b>Επίθετο:</b></td>
          <td><input type="text" name="surname" size="25" maxlength="30" /></td>
        </tr><tr>
          <td colspan="2">
            <div class="centerAlign">
              <input id="submit" type="submit" name="submit" value="Εγγραφή" />
            </div>
          </td></tr></table></object>
    </fieldset></form>
    <br />
    <p><b>(*): </b>Απαραίτητα πεδία. </p>
    <p><input type="button" class="button" value="+ τηλέφωνο" id="addTelephone" onclick="addTelephone()" />
        <input type="button" class="button" value=" - τηλέφωνο" id="rmvTelephone" onclick="rmvTelephone()" /></p>

  <br />

<?php 
}

?>
