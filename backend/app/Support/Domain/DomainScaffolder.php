<?php

declare(strict_types=1);

namespace App\Support\Domain;

use Illuminate\Support\Facades\File;
use RuntimeException;

final class DomainScaffolder
{
    private string $domain;

    private string $snake;

    public function __construct(string $domain)
    {
        $this->domain = $domain;
        $this->snake = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $domain) ?? $domain);
    }

    public static function validateName(string $domain): void
    {
        if (! preg_match('/^[A-Z][a-zA-Z0-9]*$/', $domain)) {
            throw new RuntimeException(
                'Domain name must be PascalCase (e.g. Sales, CashShift, Customer).'
            );
        }
    }

    /**
     * @return list<string>
     */
    public function scaffold(): array
    {
        $created = [];

        $docsDomains = dirname(base_path()).'/docs/domains';
        if (! File::isDirectory($docsDomains)) {
            File::makeDirectory($docsDomains, 0755, true);
            $created[] = $this->relative($docsDomains);
        }

        foreach ($this->directoryPaths() as $path) {
            if (! File::isDirectory($path)) {
                File::makeDirectory($path, 0755, true);
                $created[] = $this->relative($path);
            }
        }

        foreach ($this->stubFiles() as $absolute => $stub) {
            if (File::exists($absolute)) {
                continue;
            }

            File::put($absolute, $this->renderStub($stub));
            $created[] = $this->relative($absolute);
        }

        return $created;
    }

    /**
     * @return list<string>
     */
    private function directoryPaths(): array
    {
        $d = $this->domain;

        return [
            app_path("Domain/{$d}/Entities"),
            app_path("Domain/{$d}/ValueObjects"),
            app_path("Domain/{$d}/Events"),
            app_path("Domain/{$d}/Exceptions"),
            app_path("Domain/{$d}/Repositories"),
            app_path("Domain/{$d}/Services"),
            app_path("Application/{$d}/Actions"),
            app_path("Application/{$d}/DTOs"),
            app_path("Application/{$d}/Queries"),
            app_path("Infrastructure/{$d}/Persistence/Models"),
            app_path("Infrastructure/{$d}/Persistence/Repositories"),
            app_path("Http/{$d}/Controllers"),
            app_path("Http/{$d}/Requests"),
            app_path("Http/{$d}/Resources"),
            base_path("tests/Unit/Domain/{$d}"),
            base_path("tests/Feature/{$d}"),
            base_path("tests/BDD/Features/{$this->snake}"),
            base_path("database/factories/{$d}"),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function stubFiles(): array
    {
        $d = $this->domain;
        $snake = $this->snake;

        return [
            app_path("Domain/{$d}/Repositories/{$d}RepositoryInterface.php") => 'repository-interface.stub',
            app_path("Infrastructure/{$d}/Persistence/Repositories/{$d}Repository.php") => 'repository.stub',
            $this->projectDocsPath("domains/{$snake}.md") => 'domain-doc.stub',
        ];
    }

    private function projectDocsPath(string $suffix): string
    {
        return dirname(base_path())."/docs/{$suffix}";
    }

    private function renderStub(string $stub): string
    {
        $path = base_path("stubs/domain/{$stub}");

        if (! File::exists($path)) {
            throw new RuntimeException("Stub not found: {$path}");
        }

        return str_replace(
            ['{{ domain }}', '{{ snake }}'],
            [$this->domain, $this->snake],
            File::get($path)
        );
    }

    private function relative(string $absolute): string
    {
        $base = base_path();

        return str_starts_with($absolute, $base)
            ? ltrim(substr($absolute, strlen($base)), '/\\')
            : $absolute;
    }
}
