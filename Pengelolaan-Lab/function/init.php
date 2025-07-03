
<?php
require_once __DIR__ . '/koneksi.php';
require_once __DIR__ . '/config.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
    
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/pagination.php';

}
?>