<?php
declare(strict_types=1);

/**
 * Conexión a la base de datos usando PDO — Patrón Singleton.
 * Garantiza una única conexión activa durante toda la petición.
 */
class Database
{
    private static ?PDO $instance = null;

    // Parámetros de conexión
    private const HOST     = 'localhost';
    private const PORT     = 3306;
    private const DB_NAME  = 'inventario_mvc';
    private const USERNAME = 'root';
    private const PASSWORD = '';
    private const CHARSET  = 'utf8mb4';

    /**
     * No se puede instanciar desde fuera.
     */
    private function __construct() {}
    private function __clone() {}

    /**
     * Retorna la instancia PDO (la crea solo una vez).
     */
    public static function getConnection(): PDO
    {
        if (self::$instance === null) {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                self::HOST,
                self::PORT,
                self::DB_NAME,
                self::CHARSET
            );

            self::$instance = new PDO($dsn, self::USERNAME, self::PASSWORD, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }

        return self::$instance;
    }
}
