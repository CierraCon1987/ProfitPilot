

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
