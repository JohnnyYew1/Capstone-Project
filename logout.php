<?php
session_start();

if (isset($_SESSION['user_id'])) {

    $_SESSION = array();

    session_destroy();

    header("Location: login.php?loggedout=1");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>
