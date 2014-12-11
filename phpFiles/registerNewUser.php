<?php 
  // Called when the registration form is submitted.
  // Will check the form and, if everything is ok, register the new user.

  require_once('mainHeader.php');
  session_start();

  // Make sure all the necessary info was given.
  if ( !isset($_POST['username'])  ||
       !isset($_POST['password'])  ||
       !isset($_POST['password2'])  ||
       !isset($_POST['email'])  ||
       !isset($_POST['telephone'])  ) {
    // Call displayProblemPage() --it is in outputFunctions.php-- to display the problem
    // and exit.
    displayProblemPage('Δεν έχετε συμπληρώσει όλα τα απαραίτητα πεδία. '
                             .'Παρακαλούμε πηγαίνετε πίσω και ξαναπροσπαθήστε.');
  }    

  // Create short variable names.
  $username = $_POST['username'];
  $password = $_POST['password'];
  $password2 = $_POST['password2'];
  $email = $_POST['email'];
  $telephone = $_POST['telephone'];
  $name = $_POST['name'];
  $surname = $_POST['surname'];

  try {
    // Call validEmail() --it is in otherFunctions.php-- to check if
    // the email is valid.
    if ( !validEmail($email) ) {
      throw new Exception('Η διεύθυνση ηλεκτρονικού ταχυδρομείου δεν '
                                 .'είναι έγκυρη - παρακαλώ πηγαίνετε πίσω '
                                 .'και ξαναπροσπαθήστε.');
    }

    if ($password != $password2) {
      throw new Exception('Οι κωδικοί πρόσβασης δεν ταιριάζουν '
                    .' - παρακαλώ πηγαίνετε πίσω και ξαναπροσπαθήστε.');
    }

    if (strlen($password) < 5) {
      throw new Exception('Το μήκος του κωδικού πρόσβασης πρέπει να '
                            .'είναι τουλάχιστον 5 χαρακτήρες - παρακαλώ '
                            .'πηγαίνετε πίσω και ξαναπροσπαθήστε.');
    }

    if (!validTelephone($telephone)) {
      throw new Exception('Μη έγκυρος αριθμός τηλεφώνου '
                    .' - παρακαλώ πηγαίνετε πίσω και ξαναπροσπαθήστε.');
    }

    // Call register() --it is in databaseRelatedFunctions.php-- to put the 
    // user's info into the database.
    register($username, $password, $email, $telephone, $name, $surname);
    
    // Make a new session using the username.
    $_SESSION['validUser'] = $username;

    // Successfully registered.
    // Call displaySimplePage() --it is in outputFunctions.php-- to display a simple message.
    displaySimplePage('Η εγγραφή σας ήταν επιτυχής. Μπορείτε τώρα να αρχίσετε να '
           .'προσθέτετε αγγελίες πατώντας το link Καταχώρηση ή να δείτε '
           .' τις αγαπημένες σας αγγελίες στο link Αγαπημένα! </p><p>'
           .'<a href="/index.php">Κεντρική σελίδα.</a>');
  }
  catch (Exception $e) {
    displayProblemPage($e->getMessage());
  }
?>

