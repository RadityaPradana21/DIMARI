<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EnsureAppKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:ensure-key';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure APP_KEY is set and valid; generate one if it is missing or malformed';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $key = config('app.key');

        if ($this->isValidKey($key)) {
            $this->info('APP_KEY is already set and valid. No action needed.');
            return self::SUCCESS;
        }

        $this->warn('APP_KEY is missing or invalid. Generating a new key...');

        $exitCode = $this->call('key:generate', ['--force' => true]);

        if ($exitCode === 0) {
            $this->info('APP_KEY generated successfully.');
            return self::SUCCESS;
        }

        $this->error('Failed to generate APP_KEY. Please run "php artisan key:generate" manually.');
        return self::FAILURE;
    }

    /**
     * Determine whether the given key is a valid Laravel application key.
     *
     * A valid key must:
     *  - be a non-empty string
     *  - start with the "base64:" prefix
     *  - decode to exactly 32 bytes (required by AES-256-CBC)
     */
    private function isValidKey(mixed $key): bool
    {
        if (empty($key) || ! is_string($key)) {
            return false;
        }

        if (! str_starts_with($key, 'base64:')) {
            return false;
        }

        $decoded = base64_decode(substr($key, 7), strict: true);

        return $decoded !== false && strlen($decoded) === 32;
    }
}
