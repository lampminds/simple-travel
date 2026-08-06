<?php

namespace App\Console\Commands;

use App\Services\FakeAccountGeneratorService;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Throwable;

/**
 * Generate fake operator or agency accounts with a verified owner user.
 *
 * Run from project root (Debian):
 *   php artisan generate-accounts operator 5
 *   php artisan generate-accounts agency 3
 */
class GenerateAccountsCommand extends Command
{
    protected $signature = 'generate-accounts
                            {type : operator or agency}
                            {count=1 : Number of accounts to create}';

    protected $description = 'Generate fake operator or agency accounts with owner users';

    public function handle(FakeAccountGeneratorService $generator): int
    {
        $type = (string) $this->argument('type');
        $count = (int) $this->argument('count');

        if ($count < 1) {
            $this->error('Count must be at least 1.');

            return self::FAILURE;
        }

        try {
            $created = $generator->generate($type, $count);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Created '.count($created).' account(s).');
        $this->newLine();

        $this->table(
            ['Company', 'Nick', 'Owner email', 'Password', 'City'],
            array_map(
                static fn (array $row): array => [
                    $row['company_name'],
                    $row['nick'],
                    $row['email'],
                    $row['password'],
                    $row['city'] ?? '—',
                ],
                $created
            )
        );

        return self::SUCCESS;
    }
}
