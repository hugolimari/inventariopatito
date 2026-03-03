<?php
declare(strict_types=1);

use App\Utils\DIContainer;
use App\Repositories\HardwareRepository;
use App\Repositories\CategoryRepository;
use App\Repositories\AuthRepository;
use App\Utils\Validator;
use App\Utils\Helpers;

// cargar configuración de base de datos para poder crear PDO
require_once __DIR__ . '/database.php';

$container = new DIContainer();

// registro de conexión PDO y repositorio de autenticación
$container->register(
    'PDO',
    function(DIContainer $c): PDO {
        $cfg = new \DatabaseConfig();
        $pdo = new PDO($cfg->getDSN(), $cfg->getUsername(), $cfg->getPassword());
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    },
    true
);

$container->register(
    'AuthRepository',
    function(DIContainer $c): AuthRepository {
        return new AuthRepository($c->get('PDO'));
    },
    true
);

$container->register(
    'HardwareRepository',
    function(DIContainer $c): HardwareRepository {
        return new HardwareRepository($c->get('PDO'));
    },
    true
);

$container->register(
    'CategoryRepository',
    function(DIContainer $c): CategoryRepository {
        return new CategoryRepository($c->get('PDO'));
    },
    true
);

$container->register(
    'MarcaRepository',
    function(DIContainer $c): \App\Repositories\MarcaRepository {
        return new \App\Repositories\MarcaRepository($c->get('PDO'));
    },
    true
);

$container->register(
    'Validator',
    function(DIContainer $c): Validator {
        return new Validator();
    },
    true
);

$container->register(
    'RoleRepository',
    function(DIContainer $c): \App\Repositories\RoleRepository {
        return new \App\Repositories\RoleRepository($c->get('PDO'));
    },
    true
);

$container->register(
    'MovementRepository',
    function(DIContainer $c): \App\Repositories\MovementRepository {
        return new \App\Repositories\MovementRepository($c->get('PDO'));
    },
    true
);

$container->register(
    'Helpers',
    function(DIContainer $c): Helpers {
        return new Helpers();
    },
    true
);

return $container;
