<?php
/**
 * Acceso directo al login
 * Redirige automáticamente al sistema de login
 */
header('Location: index.php?controller=Usuario&action=login');
exit;
?>