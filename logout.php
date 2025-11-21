<?php
/**
 * Logout
 * Vigged - Plataforma de Inclusão e Oportunidades
 */

require_once 'config/auth.php';

startSecureSession();
logout();

header('Location: index.php');
exit;

