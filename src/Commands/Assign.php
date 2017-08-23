<?php

namespace QD\Lib\Commands;

use Illuminate\Console\Command;

class Assign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qd:assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'QD Assign Task';

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
        $this->info("Assign Task [start]");
        assign_task();
        $this->info("Assign Task [done]");
    }
}
