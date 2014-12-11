<?php
  require_once('mainHeader.php');
  session_start();

  if ( isset($_SESSION['validUser']) )
    displayProblemPage('Είστε ήδη συνδεδεμένος σαν '.$_SESSION['validUser'].'.');
  else {
    doHtmlHeader('Σπιτικές Αγγελίες: Login page');
    echo '<h1>Σύνδεση μέλους</h1>';

    displayLoginForm();

    doHtmlFooter();
  }
?>





<?php
function displayLoginForm()
{
?>
  <br />
  Δεν έχετε λογαριασμό? <a href='/phpFiles/registrationPage.php'>Εγγραφή!</a>
  <br />
  <br />
  <form id="content2" method='post' action='/index.php'>
    <fieldset><legend align="top">&#160;Σύνδεση μελών:&#160;</legend>
      <p>
        <b>Username:&#160;</b> <input type="text" name="username" size="25"
                                          maxlength="30" />
      </p>
      <p>
      <b>Password:&#160;</b> <input type="password" name="password" size="25"
                                        maxlength="50" />
      </p>
      <br />
      <div align="center">
        <input id="submit" type="submit" name="submit" value="Είσοδος" />
      </div>
      <br />
      <p>
        <b><a href="forgotPasswordPage.php">Ξέχασα τον κωδικό μου.</a> </b>
      </p>
    </fieldset>
  </form>
  <br />
<?php
}

?>
