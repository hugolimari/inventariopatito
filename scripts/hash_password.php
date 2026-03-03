<?php

if ($argc < 2) {
    echo "Uso: php hash_password.php <password>\n";
    exit(1);
}

$password = $argv[1];
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash generado: $hash\n";
