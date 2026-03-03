<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Utils\DIContainer;
use App\Utils\Validator;

abstract class BaseController {
    protected DIContainer $container;
    protected Validator $validator;

    public function __construct(DIContainer $container) {
        $this->container = $container;
        $this->validator = $container->get('Validator');
    }

    protected function getService(string $serviceName): object {
        return $this->container->get($serviceName);
    }

    protected function validate(array $data, array $rules): array {
        return $this->validator->validate($data, $rules);
    }

    protected function render(string $view, array $data = []): void {
        extract($data);
        
        $viewPath = __DIR__ . "/../../views/{$view}.php";
        
        if (!file_exists($viewPath)) {
            throw new \Exception("Vista no encontrada: {$viewPath}");
        }
        
        require_once $viewPath;
    }

    protected function json(
        array $data,
        int $statusCode = 200,
        array $headers = []
    ): void {
        http_response_code($statusCode);
        
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        foreach ($headers as $name => $value) {
            header("{$name}: {$value}");
        }
        
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url, int $statusCode = 302): void {
        http_response_code($statusCode);
        header("Location: {$url}");
        exit;
    }

    protected function getQuery(string $key, mixed $default = null): mixed {
        return $_GET[$key] ?? $default;
    }

    protected function getPost(string $key, mixed $default = null): mixed {
        return $_POST[$key] ?? $default;
    }

    protected function getMethod(): string {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    protected function isAjax(): bool {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    /**
     * Devuelve el usuario autenticado (datos mínimos) o null si no existe.
     */
    protected function getCurrentUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    /**
     * Redirige a la pantalla de login si no hay usuario en sesión.
     */
    protected function ensureAuthenticated(): void {
        if ($this->getCurrentUser() === null) {
            $this->redirect('/auth/login.php');
        }
    }

    protected function getJsonBody(): array {
        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    protected function setFlash(string $key, string $message): void {
        $_SESSION['flash'][$key] = $message;
    }

    protected function getFlash(string $key, mixed $default = null): mixed {
        $message = $_SESSION['flash'][$key] ?? $default;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    protected function error(
        string $message,
        int $statusCode = 400,
        array $details = []
    ): void {
        $this->json([
            'success' => false,
            'error' => $message,
            ...$details
        ], $statusCode);
    }

    protected function success(array $data = [], int $statusCode = 200): void {
        $this->json([
            'success' => true,
            ...$data
        ], $statusCode);
    }
}
