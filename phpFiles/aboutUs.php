<?php 
  // This is aboutUs.php.
  require_once('mainHeader.php');
  session_start();

  $userKind = getUserKind();

  doHtmlHeader('Σπιτικές Αγγελίες: About us', $userKind);

?>
  <h1>Καλωσορίσατε</h1>

  <p>Καλωσορίσατε στον ιστότοπό μας!</p>

  <p>Ο σκοπός του ιστοτόπου αυτού είναι να παρέχει 
υπηρεσίες εύρεσης, καταχώρησης και διαχείρισης ενός ιστοτόπου αγγελιών.
Αναπτύχθηκε στα πλαίσια του μαθήματος Προγραμματισμός και Συστήματα στον Παγκόσμιο Ιστό και ελπίζουμε να βαθμολογηθεί με 10 ;p Μπορείτε να πλοηγείστε στις διάφορες λειτουργίες του μέσω των links που υπάρχουν.</p>
  <p>Καλή διασκέδαση!

<?php
  doHtmlFooter();
?>