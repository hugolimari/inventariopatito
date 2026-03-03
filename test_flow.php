<?php
/**
 * Test script to verify the complete hardware inventory system flow
 * Tests:
 * 1. Database connectivity
 * 2. Authentication system
 * 3. Hardware repository
 * 4. Category repository
 * 5. Hardware creation and retrieval
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';

echo "=== Test Hardware Inventory System ===\n\n";

// Test 1: Database connectivity
echo "TEST 1: Database Connectivity\n";
try {
    $dbConfig = new DatabaseConfig();
    $pdo = new PDO($dbConfig->getDSN(), $dbConfig->getUsername(), $dbConfig->getPassword());
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connected successfully\n";
} catch (Exception $e) {
    echo "✗ Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check usuarios table
echo "\nTEST 2: Check Usuarios Table\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM usuarios");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Usuarios table exists with {$result['count']} user(s)\n";
} catch (Exception $e) {
    echo "✗ Error checking usuarios table: " . $e->getMessage() . "\n";
}

// Test 3: Check categorias table
echo "\nTEST 3: Check Categorias Table\n";
try {
    $stmt = $pdo->query("SELECT * FROM categorias LIMIT 5");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "✓ Categorias table exists with " . count($categories) . " category/ies\n";
    foreach ($categories as $cat) {
        echo "  - {$cat['id_categoria']}: {$cat['nombre']}\n";
    }
} catch (Exception $e) {
    echo "✗ Error checking categorias table: " . $e->getMessage() . "\n";
}

// Test 4: Check hardware table
echo "\nTEST 4: Check Hardware Table\n";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM hardware WHERE estado = 1");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "✓ Hardware table exists with {$result['count']} active item(s)\n";
} catch (Exception $e) {
    echo "✗ Error checking hardware table: " . $e->getMessage() . "\n";
}

// Test 5: Test AuthRepository
echo "\nTEST 5: Test AuthRepository\n";
try {
    $container = require __DIR__ . '/config/di-container.php';
    $authRepo = $container->get('AuthRepository');
    $user = $authRepo->findByUsername('admin');
    if ($user) {
        echo "✓ AuthRepository found admin user\n";
        echo "  - ID: {$user->getId()}\n";
        echo "  - Username: {$user->getUsername()}\n";
        
        // Test password verification
        if (password_verify('Admin@2026', $user->getPasswordHash())) {
            echo "✓ Password verification successful\n";
        } else {
            echo "✗ Password verification failed\n";
        }
    } else {
        echo "✗ Admin user not found\n";
    }
} catch (Exception $e) {
    echo "✗ AuthRepository test failed: " . $e->getMessage() . "\n";
}

// Test 6: Test CategoryRepository
echo "\nTEST 6: Test CategoryRepository\n";
try {
    $categoryRepo = $container->get('CategoryRepository');
    $categories = $categoryRepo->findAll();
    echo "✓ CategoryRepository retrieved " . count($categories) . " categories\n";
    foreach ($categories as $cat) {
        echo "  - {$cat['id_categoria']}: {$cat['nombre']}\n";
    }
} catch (Exception $e) {
    echo "✗ CategoryRepository test failed: " . $e->getMessage() . "\n";
}

// Test 7: Test HardwareRepository
echo "\nTEST 7: Test HardwareRepository\n";
try {
    $hardwareRepo = $container->get('HardwareRepository');
    $items = $hardwareRepo->findAll();
    echo "✓ HardwareRepository retrieved " . count($items) . " hardware items\n";
    if (!empty($items)) {
        $item = $items[0];
        echo "  - Sample item ID: {$item['id_hardware']}, Marca: {$item['marca_nombre']}, Modelo: {$item['modelo']}\n";
    }
} catch (Exception $e) {
    echo "✗ HardwareRepository test failed: " . $e->getMessage() . "\n";
}

// Test 8: Test creating a new hardware item
echo "\nTEST 8: Test Hardware Creation\n";
try {
    $marcaRepo = $container->get('MarcaRepository');
    $firstBrand = $marcaRepo->findAll()[0] ?? null;
    $brandId = $firstBrand['id_marca'] ?? 0;

    $newData = [
        'id_marca' => $brandId,
        'modelo' => 'TestModel',
        'precio' => 99.99,
        'stock' => 5,
        'id_categoria' => 1  // Assuming category 1 exists
    ];
    $newId = $hardwareRepo->save($newData);
    echo "✓ Hardware created successfully with ID: $newId\n";
    
    // Retrieve it back
    $saved = $hardwareRepo->findById($newId);
    if ($saved && ($saved['id_marca'] ?? 0) === $brandId) {
        echo "✓ Retrieved created hardware successfully\n";
        echo "  - Marca ID: {$saved['id_marca']} nombre: {$saved['marca_nombre']}\n";
        echo "  - Modelo: {$saved['modelo']}\n";
        echo "  - Precio: {$saved['precio']}\n";
        echo "  - Stock: {$saved['stock']}\n";
        
        // Clean up - delete the test item
        $hardwareRepo->delete($newId);
        echo "✓ Test item cleaned up\n";
    } else {
        echo "✗ Could not retrieve created hardware\n";
    }
} catch (Exception $e) {
    echo "✗ Hardware creation test failed: " . $e->getMessage() . "\n";
}

echo "\n=== All Tests Complete ===\n";

