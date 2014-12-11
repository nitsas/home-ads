<?php
  require_once('mainHeader.php');
  session_start();

  if (isset($_SESSION['validUser']))
    displayProblemPage('Είστε ήδη συνδεδεμένος, δεν μπορεί να μην θυμάστε τον '
                             .'κωδικό!');

  doHtmlHeader('Σπιτικές Αγγελίες: Ξέχασα τον κωδικό μου');

  displayForgotPasswordForm();

  doHtmlFooter();
?>







<?php


function displayForgotPasswordForm()
// Displays HTML form to reset a user's password.
{
?>
  <br />
  <form id="content2" action='resetPassword.php' method='post'>
    <p>Δώστε το username σας:
      <input type='text' name='username' size=25 maxlength=30 /></p>
    <br />
    <div align="center"><input id="submit" type='submit' name="submit" 
                                    value='Αλλαγή password' /></div>
  </form>
<?php
}


?>
