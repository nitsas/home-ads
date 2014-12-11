<?php
  require_once('mainHeader.php');
  session_start();

  if (isset($_SESSION['validUser']))
    displayProblemPage('Είστε ήδη συνδεδεμένος, δεν μπορεί να μην θυμάστε τον '
                             .'κωδικό!');

  doHtmlHeader('Σπιτικές Αγγελίες: Επαναφορά κωδικού');

  // create a short variable name
  $username = $_POST['username'];

  try {
    $password = resetPassword($username);
    notifyPassword($username, $password);
    echo '<p>Ο νέος σας κωδικός σας έχει αποσταλεί με e-mail. Προτείνουμε να τον '
           .'αλλάξετε αμέσως μόλις συνδεθείτε.</p><br />';
  } 
  catch (Exception $e) {
    echo '<p>Πρόβλημα: '.$e->getMessage().'<br />Δεν μπορέσαμε να επαναφέρουμε τον '
          .'κωδικό σας - παρακαλώ ξαναπροσπαθήστε αργότερα.<br />';
    echo '<a href="/phpFiles/loginPage.php">Σελίδα σύνδεσης</a></p>';
  }

  doHtmlFooter();
?>






<?php
function notifyPassword($username, $password)
// Sends an email to user $username containing his new password, $password.
{
  $email = getEmail($username);
  $from = "From: support@mikresaggelies.projectweb \r\n";
  $mesg = "Ο κωδικός σας στις Σπιτικές Αγγελίες (project web 2010) άλλαξε σε "
             .$password.".\r\nΠροτείνουμε να τον αλλάξετε την επόμενη φορά που θα "
             ."συνδεθείτε. \r\n";

  if ( mail($email, 'Σπιτικές Αγγελίες (project web): Αλλαγή κωδικού', $mesg, $from) )
    return true;
  else
    throw new Exception('Δεν μπόρεσε να σταλεί το ενημερωτικό mail.');
}

?>

