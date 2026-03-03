<?php
/**
 * Test the Validator class to ensure all validation rules work
 */

declare(strict_types=1);

require_once __DIR__ . '/src/Utils/Validator.php';

use App\Utils\Validator;

echo "=== Test Validator Rules ===\n\n";

$validator = new Validator();

// Test 1: Required field
echo "TEST 1: Required Field Validation\n";
$data = ['name' => '', 'email' => 'test@example.com'];
$rules = ['name' => 'required', 'email' => 'required'];
$errors = $validator->validate($data, $rules);
echo "Empty name field: " . (isset($errors['name']) ? "✓ Error caught" : "✗ No error") . "\n";
$data = ['name' => 'John', 'email' => 'test@example.com'];
$errors = $validator->validate($data, $rules);
echo "Valid data: " . (empty($errors) ? "✓ No errors" : "✗ Unexpected errors") . "\n";

// Test 2: String validation
echo "\nTEST 2: String Validation\n";
$data = ['marca' => 'Intel'];
$rules = ['marca' => 'string'];
$errors = $validator->validate($data, $rules);
echo "String value: " . (empty($errors) ? "✓ Valid" : "✗ Invalid") . "\n";

$data = ['marca' => 123];
$errors = $validator->validate($data, $rules);
echo "Integer value: " . (isset($errors['marca']) ? "✓ Error caught" : "✗ No error") . "\n";

// Test 3: Numeric validation
echo "\nTEST 3: Numeric Validation\n";
$data = ['precio' => '99.99'];
$rules = ['precio' => 'numeric'];
$errors = $validator->validate($data, $rules);
echo "String number: " . (empty($errors) ? "✓ Valid" : "✗ Invalid") . "\n";

$data = ['precio' => 'abc'];
$errors = $validator->validate($data, $rules);
echo "Non-numeric: " . (isset($errors['precio']) ? "✓ Error caught" : "✗ No error") . "\n";

// Test 4: Integer validation
echo "\nTEST 4: Integer Validation\n";
$data = ['stock' => 5];
$rules = ['stock' => 'integer'];
$errors = $validator->validate($data, $rules);
echo "Integer: " . (empty($errors) ? "✓ Valid" : "✗ Invalid") . "\n";

$data = ['stock' => 5.5];
$errors = $validator->validate($data, $rules);
echo "Float: " . (isset($errors['stock']) ? "✓ Error caught" : "✗ No error") . "\n";

// Test 5: Min length
echo "\nTEST 5: Min Length Validation\n";
$data = ['marca' => 'In'];
$rules = ['marca' => 'min:3'];
$errors = $validator->validate($data, $rules);
echo "2 characters (min 3): " . (isset($errors['marca']) ? "✓ Error caught" : "✗ No error") . "\n";

$data = ['marca' => 'Intel'];
$errors = $validator->validate($data, $rules);
echo "5 characters (min 3): " . (empty($errors) ? "✓ Valid" : "✗ Invalid") . "\n";

// Test 6: Max length
echo "\nTEST 6: Max Length Validation\n";
$data = ['marca' => 'VeryLongBrandNameThatExceedsLimit'];
$rules = ['marca' => 'max:10'];
$errors = $validator->validate($data, $rules);
echo "35 characters (max 10): " . (isset($errors['marca']) ? "✓ Error caught" : "✗ No error") . "\n";

$data = ['marca' => 'Intel'];
$errors = $validator->validate($data, $rules);
echo "5 characters (max 10): " . (empty($errors) ? "✓ Valid" : "✗ Invalid") . "\n";

// Test 7: Email validation
echo "\nTEST 7: Email Validation\n";
$data = ['email' => 'test@example.com'];
$rules = ['email' => 'email'];
$errors = $validator->validate($data, $rules);
echo "Valid email: " . (empty($errors) ? "✓ Valid" : "✗ Invalid") . "\n";

$data = ['email' => 'invalid-email'];
$errors = $validator->validate($data, $rules);
echo "Invalid email: " . (isset($errors['email']) ? "✓ Error caught" : "✗ No error") . "\n";

// Test 8: Multiple rules
echo "\nTEST 8: Multiple Rules (required|string|min:2)\n";
$data = ['marca' => 'Intel'];
$rules = ['marca' => 'required|string|min:2'];
$errors = $validator->validate($data, $rules);
echo "Valid data: " . (empty($errors) ? "✓ All rules passed" : "✗ Unexpected errors") . "\n";

$data = ['marca' => 'I'];
$errors = $validator->validate($data, $rules);
echo "Too short: " . (isset($errors['marca']) ? "✓ Min length error caught" : "✗ No error") . "\n";

$data = ['marca' => ''];
$errors = $validator->validate($data, $rules);
echo "Empty value: " . (isset($errors['marca']) ? "✓ Required error caught" : "✗ No error") . "\n";

// Test 9: Complete hardware validation
echo "\nTEST 9: Complete Hardware Form Validation\n";
$data = [
    'marca' => 'Intel',
    'modelo' => 'i7-9700K',
    'precio' => '349.99',
    'stock' => '10',
    'id_categoria' => '1'
];
$rules = [
    'marca' => 'required|string|min:2',
    'modelo' => 'required|string|min:2',
    'precio' => 'required|numeric|min:0.01',
    'stock' => 'required|integer|min:0',
    'id_categoria' => 'required|integer|min:1'
];
$errors = $validator->validate($data, $rules);
echo "Valid hardware: " . (empty($errors) ? "✓ All fields valid" : "✗ Unexpected errors: " . json_encode($errors)) . "\n";

$data['precio'] = '-10.00';
$errors = $validator->validate($data, $rules);
echo "Negative price: " . (isset($errors['precio']) ? "✓ Min validation error" : "✗ No error") . "\n";

echo "\n=== All Validation Tests Complete ===\n";
