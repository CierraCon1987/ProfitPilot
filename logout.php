<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php

    session_start();
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();

?>
