<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh () -->

<?php

    session_start();
    session_destroy();
    header("Location: login.php");
    exit();

?>