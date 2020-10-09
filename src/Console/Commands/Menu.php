<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Sagartakle\Laracrud\Helpers\CustomHelper;

class Menu extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stlc:menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command menu generate';

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
        CustomHelper::generateMenu();
    }
}
