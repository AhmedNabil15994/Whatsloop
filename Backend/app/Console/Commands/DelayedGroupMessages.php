<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\GroupMsg;
use App\Models\Contact;
use App\Jobs\GroupMessageJob;

class DelayedGroupMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'groupMsg:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Delayed Group Messages';

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
        $now = date('Y-m-d H:i:s');
        $messages = GroupMsg::NotDeleted()->where('later',1)->whereRaw('contacts_count != (sent_count+unsent_count)')->whereRaw('contacts_count >= sent_count')->whereRaw('contacts_count >= unsent_count')->get();
        foreach ($messages as $message) {
            $dataObj = (array) GroupMsg::getData($message);
            $chunks = 400;
            if($dataObj['publish_at'] <= $now){
                $contacts = Contact::NotDeleted()->whereHas('Reports',function($whereQuery) use ($dataObj){
                    $whereQuery->where('status',0)->where('group_message_id',$dataObj['id']);
                })->where('group_id',$message->group_id)->where('status',1)->chunk($chunks,function($data) use ($dataObj){
                    try {
                        dispatch(new GroupMessageJob($data,(object)$dataObj))->onConnection('cjobs');
                    } catch (Exception $e) {
                        
                    }
                });
            }
            $message->later = 0;
            $message->save();
        }
    }
}
