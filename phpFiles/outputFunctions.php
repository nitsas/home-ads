<?php

function doHtmlHeader($title, $userKind='guest', $useJavaScript=false, $useGoogleMaps=false)
// Prints an HTML header.
{
  // how many links should the navigation bar have?
  if ($userKind == 'admin')
    $numLinks = "sixLinks";
  else if ($userKind == 'member')
    $numLinks = "fiveLinks";
  else
    $numLinks = "fourLinks";
?>
<!DOCTYPE html>
  <html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="content-language" content="el" />
    <link rel="stylesheet" href="/styles/style.css" type="text/css" media="screen" />
    <title><?php echo $title;?></title>
    <link rel="alternate" type="application/rss+xml" title="Mikres Aggelies RSS" 
          href="/phpFiles/createRssFile.php" />
<?php

    if ($useGoogleMaps) {
      echo '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
    }
    if ($useJavaScript)
      echo "<script type='text/javascript' src='/javaScriptFiles/$useJavaScript'></script>";

  echo '  </head>';
  if ($useGoogleMaps) 
    echo '<body onload="initializeMap()">';
  else
    echo '<body>';

?>
    <div id="header">
      <h1>Σπιτικές Αγγελίες</h1>
      <a type="application/rss+xml" style="float:left" href="/phpFiles/createRssFile.php"><img src="/img/rss.gif" /></a>
      <h2>ό,τι θέλετε, όταν το θέλετε</h2>
    </div>
<?php

      if ($userKind != 'guest') {
        $username = $_SESSION['validUser'];
        echo '<div id="signedAs">';
        echo 'Συνδεθήκατε σαν <a href="/phpFiles/profilePage.php?username='.$username.'">'
               .'<b>'.$username.'</b></a>';
        if ($userKind == 'admin') 
          echo ', administrator';
        echo '. <a href="/phpFiles/logoutPage.php">(aποσύνδεση)</a>';
        echo '</div>';
      }

?>
    <div id="navigation">
      <ul>
<?php

        echo "<li class='$numLinks'><a href='/index.php'>Κεντρική Σελίδα</a></li>";
        echo "<li class='$numLinks'><a href='/phpFiles/searchPage.php'>Αναζήτηση</a></li>";
        if ( $userKind != 'guest' ) {
          echo "<li class='$numLinks'><a href='/phpFiles/newAdvertisementPage.php'>Καταχώρηση</a></li>";
          echo "<li class='$numLinks'><a href='/phpFiles/favoritesPage.php'>Αγαπημένα</a></li>";
          if ( $userKind == 'admin' ) 
            echo "<li class='$numLinks'><a href='/phpFiles/adminPage.php'>Διαχείριση</a></li>";
        }
        else 
          echo "<li class='$numLinks'><a href='/phpFiles/loginPage.php'>Εγγραφή / Σύνδεση</a></li>";
        echo "<li class='$numLinks'><a href='/phpFiles/aboutUs.php'>About us</a></li>";

?>
      </ul>
    </div>

    <div id="content">
<?php 
}





function doHtmlFooter()
{
  // Prints an HTML footer.
?>
    </div>
  </body>
</html>

<?php 
}





function displayListOfDescriptions($descriptions, $ordered=true)
// Takes a two-dimensional array as an argument.
// Each row contains all the necessary information to create a mini description 
// for an advertisement. The function displays an ordered list of these mini descriptions.
{
  if ($ordered)
    echo '<ol>';
  else
    echo '<ul>';

  if (!$descriptions)
    echo '<li>Δεν υπάρχουν στοιχεία για προβολή.</li>';
  else
    foreach ($descriptions as $row) {
      $str = createMainAdInfo($row, false);
      echo '<li><a href="/phpFiles/displayAdvertisementPage.php?aggeliaID='
            .$row['aggeliaID'].'">'.$str.'</a></li>';
    }

  if ($ordered)
    echo '</ol>';
  else
    echo '</ul>';
} 





function displaySimplePage($content)
// Displays a page containing a simple message.
{
  if (!isset($_SESSION))
    session_start();

  try {
    if (!is_string($content))
      throw new Exception('Πρόβλημα με το όρισμα της displaySimplePage().');

    doHtmlHeader('Σπιτικές Αγγελίες: Ενημερωτική σελίδα', getUserKind());
    echo '<br /><p>';
    echo $content;
    echo '</p><br />';
    doHtmlFooter();
    exit;
  }
  catch (Exception $e) {
    // Call displayProblemPage() --it is in this file-- to display the problem.
    displayProblemPage($e->getMessage());
  }
}
    




function displayProblemPage($content, $noHeader=false)
// Displays a page containing a message. (used when problems occur)
{
  if (!isset($_SESSION))
    session_start();

  try {
    if (!is_string($content))
      throw new Exception('Πρόβλημα με το όρισμα της displayProblemPage().');

    if (!$noHeader)
      doHtmlHeader('Σπιτικές Αγγελίες: Πρόβλημα', getUserKind());
    echo '<br /><p>';
    echo $content;
    echo '</p><br />';
    doHtmlFooter();
    exit;
  }
  catch (Exception $e) {
    // Display a very simple page containing the problem message.
    echo '<!DOCTYPE hrml PUBLIC "//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
    echo '<html xmlns="http://www.w3.org/1999/xhtml">';
    echo '<head><title>Σπιτικές Αγγελίες: Fatal error</title></head>';
    echo '<body><p>'.$e->getMessage().'</p></body></html>';
  }
}

?>










