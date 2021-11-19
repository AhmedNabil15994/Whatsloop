<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Variable;
use App\Models\CentralChannel;
use App\Models\User;

class SyncLabels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:labels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync User Labels Every Minute';

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

        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['limit'] = 0;
        $updateResult = $mainWhatsLoopObj->labelsList($data);
        $updateResult = $updateResult->json();

        if(isset($updateResult['data']) && !empty($updateResult['data'])){
            $labels = $updateResult['data']['labels'];
            $value = 1;
            if(empty($labels)){
                $value = 0;
            }

            $varObj = Variable::where('var_key','BUSINESS')->first();
            if(!$varObj){
                $varObj = new Variable;
                $varObj->var_key = 'BUSINESS';
            }
            $varObj->var_value = $value;
            // $varObj->save();

            $channelObj = CentralChannel::where('global_user_id',User::first()->global_id)->first();
            foreach($labels as $label){
                $labelObj = Category::NotDeleted()->where('labelId',$label['id'])->first();
                if(!$labelObj){
                    $labelObj = new Category;
                    $labelObj->channel = $channelObj->instanceId;
                    $labelObj->sort = Category::newSortIndex();
                }
                $labelObj->labelId = $label['id'];
                $labelObj->name_ar = $label['name'];
                $labelObj->name_en = $label['name'];
                $labelObj->color_id = Category::getColorData($label['hexColor'])[0];
                $labelObj->status = 1;
                $labelObj->save();
            }
        }
        
    }
}
