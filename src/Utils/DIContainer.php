<?php
declare(strict_types=1);

namespace App\Utils;

use Exception;

class DIContainer {
    private array $services = [];
    private array $singletons = [];

    public function register(
        string $name,
        callable|string $definition,
        bool $singleton = true
    ): self {
        if (isset($this->services[$name])) {
            throw new Exception("Servicio '{$name}' ya está registrado");
        }

        $this->services[$name] = [
            'definition' => $definition,
            'singleton' => $singleton
        ];

        return $this;
    }

    public function get(string $name): object {
        if (!isset($this->services[$name])) {
            throw new Exception(
                "Servicio '{$name}' no registrado en el contenedor. " .
                "Servicios disponibles: " . implode(', ', array_keys($this->services))
            );
        }

        $service = $this->services[$name];

        if ($service['singleton'] && isset($this->singletons[$name])) {
            return $this->singletons[$name];
        }

        $definition = $service['definition'];

        if (is_callable($definition)) {
            $instance = $definition($this);
        } else {
            $instance = new $definition();
        }

        if ($service['singleton']) {
            $this->singletons[$name] = $instance;
        }

        return $instance;
    }

    public function has(string $name): bool {
        return isset($this->services[$name]);
    }

    public function registerMultiple(array $services): self {
        foreach ($services as $name => $definition) {
            if (is_array($definition)) {
                $singleton = $definition['singleton'] ?? true;
                $this->register($name, $definition['class'], $singleton);
            } else {
                $this->register($name, $definition);
            }
        }
        return $this;
    }

    public function getServiceNames(): array {
        return array_keys($this->services);
    }

    public function unregister(string $name): void {
        unset($this->services[$name]);
        unset($this->singletons[$name]);
    }

    public function clearSingletons(): void {
        $this->singletons = [];
    }
}
