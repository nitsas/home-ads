<?php
  // This is favoritesPage.php.
  require_once('mainHeader.php');
  session_start();

  if (!isset($_SESSION['validUser']))
    displayProblemPage('Δεν είστε συνδεδεμένος. Λυπούμαστε αλλά μόνο τα '
                             .'εγγεγραμμένα μέλη μπορούν να δουν αυτή τη σελίδα.'
                             .'</p><p><a href="registrationPage.php">Θέλω να εγγραφώ!</a>');
  else {
    doHtmlHeader('Σπιτικές Αγγελίες: Αγαπημένα', getUserKind());

    try {
      displayFavorites();
    }
    catch (Exception $e) {
      echo '<p>'.$e->getMessage().'</p><br />';
    }

    doHtmlFooter();
  }

?>







<?php
function displayFavorites()
// Displays the currently logged in user's favorites.
{
  $username = $_SESSION['validUser'];
  
  $favorites = getFavorites($username);
  if (!$favorites) {
    echo '<p>Δεν έχετε προσθέσει ακόμα αγγελίες στα αγαπημένα σας.</p><br />';
    return;
  }

  echo '<h1>Οι αγαπημένες μου αγγελίες:</h1><p>';
  displayListOfDescriptions($favorites);
  echo '</p>';
}

?>
