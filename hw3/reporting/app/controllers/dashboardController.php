<?php
if (empty($_SESSION['user'])) {
    header('Location: /login');
    exit;
}
require '/var/www/ah-nguyen.site/public_html/final-project/dashboard.html';
?>