<?php

namespace QD\Lib\Commands;

use Illuminate\Console\Command;

class Route extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qd:route';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'QD Route Generator';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Generating Routes [start]");
        generate_routes();
        $this->info("Generating Routes [done]");
        echo "\n";
        $this->info("Generating Privileges [start]");
        generate_privileges();
        $this->info("Generating Privileges [done]");
    }
}
