<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatDialog;
use App\Models\Contact;
use App\Models\Variable;

class SyncDialogsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $dialogs;
    public function __construct($dialogs)
    {
        $this->dialogs = $dialogs;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Variable::insert([
            'var_key' => 'SYNCING',
            'var_value' => 1,
        ]);
        if(!empty($this->dialogs)){
            foreach ($this->dialogs as $dialog) {
                ChatDialog::newDialog($dialog);
                Contact::newPhone($dialog['id'],$dialog['name']);
            }
        }
        Variable::where('var_key','SYNCING')->delete();
    }
}
