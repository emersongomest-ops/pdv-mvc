<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Support\Domain\DomainScaffolder;
use Illuminate\Console\Command;
use RuntimeException;

final class CreateDomainCommand extends Command
{
    protected $signature = 'create {domain : PascalCase bounded context name (e.g. Sales, Promotions)}';

    protected $description = 'Scaffold Clean Architecture folder structure for a bounded context';

    public function handle(): int
    {
        $domain = (string) $this->argument('domain');

        try {
            DomainScaffolder::validateName($domain);
        } catch (RuntimeException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $created = (new DomainScaffolder($domain))->scaffold();

        if ($created === []) {
            $this->components->warn("Domain [{$domain}] already exists — nothing created.");

            return self::SUCCESS;
        }

        $this->components->info("Domain [{$domain}] scaffolded.");
        $this->newLine();
        $this->line('Created:');

        foreach ($created as $path) {
            $this->line("  <fg=gray>→</> {$path}");
        }

        return self::SUCCESS;
    }
}
