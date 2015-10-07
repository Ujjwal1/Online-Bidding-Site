<?php
unset($_SESSION['logged']);
unset($_SESSION['loggedInUser']);
unset($_SESSION['loginID']);
unset($_SESSION['spotID']);
header('Location: login.php');
?>