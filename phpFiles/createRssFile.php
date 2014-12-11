<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
<channel>
  <title>Σπιτικές Αγγελίες - Πιο πρόσφατες αγγελίες</title>
  <link>/index.php</link>
  <description>Αγγελίες ακινήτων (project Παγκόσμιου Ιστού 2010)</description>

<?php
  require_once('mainHeader.php');
  $mostRecentAds = getMostRecentAds(10);

  foreach ($mostRecentAds as $aggelia) {
    echo '<item><title>';
    if ($aggelia['aggeliaType'] === 'rent')
      echo 'Ενοικιάζεται '.$aggelia['categoryName'];
    else
      echo 'Πωλείται '.$aggelia['categoryName'];
    echo '</title><link>';
    echo '/phpFiles/displayAdvertisementPage.php?aggeliaID='.$aggelia['aggeliaID'].'</link>';
    echo '<description>'.createMainAdInfo($aggelia, false, false).'</description>';
    echo '</item>
';
  }
?>

</channel></rss>
