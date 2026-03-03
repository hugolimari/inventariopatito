<?php
declare(strict_types=1);

namespace App\Utils;

/**
 * Validador de datos del sistema
 */
class Validator {

    /**
     * Valida un conjunto de datos contra reglas de validación
     * Retorna array de errores indexado por campo: ['campo' => ['error1', 'error2']]
     * 
     * Reglas soportadas: required, string, numeric, integer, email, min:N, max:N, in:val1,val2,val3
     */
    public function validate(array $data, array $rules): array {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $fieldErrors = [];

            foreach ($fieldRules as $rule) {
                $error = $this->validateRule($field, $data[$field] ?? null, $rule);
                if ($error) {
                    $fieldErrors[] = $error;
                }
            }

            if (!empty($fieldErrors)) {
                $errors[$field] = $fieldErrors;
            }
        }

        return $errors;
    }

    /**
     * Valida un campo contra una regla específica
     */
    private function validateRule(string $field, mixed $value, string $rule): ?string {
        // Separar regla y parámetros (ej: "min:2" -> "min" y "2")
        [$ruleName, $param] = array_pad(explode(':', $rule, 2), 2, null);

        return match ($ruleName) {
            'required' => $this->requerido($value) 
                ? null 
                : "El campo $field es requerido",
            
            'string' => is_string($value) 
                ? null 
                : "El campo $field debe ser texto",
            
            'numeric' => is_numeric($value) 
                ? null 
                : "El campo $field debe ser un número",
            
            'integer' => is_int($value) || (is_numeric($value) && intval($value) == $value)
                ? null 
                : "El campo $field debe ser un número entero",
            
            'email' => $this->email((string)($value ?? ''))
                ? null 
                : "El campo $field debe ser un email válido",
            
            'min' => (is_numeric($value) ? (float)$value >= (float)$param : strlen((string)($value ?? '')) >= (int)$param)
                ? null 
                : "El campo $field debe ser mayor a $param",
            
            'max' => (is_numeric($value) ? (float)$value <= (float)$param : strlen((string)($value ?? '')) <= (int)$param)
                ? null 
                : "El campo $field no debe exceder $param",
            
            'in' => in_array($value, explode(',', $param ?? ''))
                ? null 
                : "El campo $field tiene un valor no permitido",
            
            default => null
        };
    }

    /**
     * Valida email
     */
    public static function email(?string $email): bool {
        return filter_var($email ?? '', FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Valida carnet de identidad boliviano
     */
    public static function ci(?string $ci): bool {
        $ci = trim($ci ?? '');
        return preg_match('/^\d{6,8}(-[A-Z]{2})?$/', $ci) === 1;
    }

    
    /**
     * Valida rango de edad
     */
    public static function edad(?int $edad): bool {
        return $edad !== null && $edad >= 16 && $edad <= 100;
    }

    /**
     * Valida longitud de texto
     */
    public static function longitud(?string $texto, int $min, int $max): bool {
        $longitud = strlen($texto ?? '');
        return $longitud >= $min && $longitud <= $max;
    }

    /**
     * Valida teléfono boliviano
     */
    public static function telefono(?string $tel): bool {
        $tel = preg_replace('/\s+/', '', $tel ?? '');
        return preg_match('/^\+?591[67]\d{7}$/', $tel) === 1;
    }

    /**
     * Valida que no esté vacío
     */
    public static function requerido(mixed $valor): bool {
        if (is_string($valor)) {
            return trim($valor) !== '';
        }
        return !empty($valor);
    }
}

