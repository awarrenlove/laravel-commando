<?php

namespace Vkovic\LaravelCommandos\Console\Commands;

use Illuminate\Console\Command;
use Vkovic\LaravelCommandos\Handlers\Database\AbstractDbHandler;

class DbDropCommand extends Command
{
    /**
     * Database operations handler
     *
     * @var AbstractDbHandler
     */
    protected $dbHandler;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:drop
                               {database? : Database (name) to be created. If passed env DB_DATABASE will be ignored} 
                           ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop database';

    public function __construct(AbstractDbHandler $dbHandler)
    {
        parent::__construct();

        $this->dbHandler = $dbHandler;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $database = $this->argument('database')
            ?: config('database.connections.' . config('database.default') . '.database');

        // Check if db exists
        if (!$this->dbHandler->databaseExists($database)) {
            $this->output->warning("Database `$database` doesn`t exist");

            return 1;
        }

        // Confirm
        if (!$this->confirm("Do you really wish to drop `$database` database?")) {
            $this->output->note('Command aborted');

            return 255;
        }

        $this->dbHandler->dropDatabase($database);

        $this->output->success("Database `$database` dropped successfully");
    }
}
