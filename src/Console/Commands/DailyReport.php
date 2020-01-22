<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dailyreport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'daily report by mail';

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
        $details = (object)[
            'type' => 'DailyReport_mail_queue'
        ];
        dispatch(new \App\Jobs\SendEmailJob($details));
        \CustomHelper::execInBackground('php artisan queue:work --tries=2');
        // $this->info('Display this on the screen');
    }
}
