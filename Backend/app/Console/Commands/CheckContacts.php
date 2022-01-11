<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\CheckWhatsappJob;
use App\Models\Contact;

class CheckContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Contacts Whatsapp Availability';

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
     * @return int
     */
    public function handle()
    {   
        $data = Contact::NotDeleted()->where('has_whatsapp','!=',1)->get();
        try {
            dispatch(new CheckWhatsappJob($data))->onConnection('cjobs');
        } catch (Exception $e) {
            
        }
    }
}
