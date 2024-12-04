<!-- Cierra Bailey-Rice (8998948)
     Harpreet Kaur (8893116)
     Gurkamal Singh (9001186) -->

<?php

    // Sanitize input to prevent XSS
    function sanitizeInput($data) {
        return htmlspecialchars(trim($data));
    }

    // Format Currency
    function formatCurrency($amount) {
        return number_format($amount, 2, '.', ',');
    }

?>
