<?php

session_start();   // initailise la session
session_unset();   // désactive la session
session_destroy(); // détruire la session
setcookie('auth', '', time()-1, '/', null, false, true); // détruire le cookie

header('location: ../index.php');
exit();

?>