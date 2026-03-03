<?php
// Utility script para generar un hash a partir de una contraseña.
// Ejecutar desde la línea de comandos: php scripts/hash_password.php "miContraseña"

if ($argc < 2) {
    echo "Uso: php hash_password.php <password>\n";
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash generado: $hash\n";
