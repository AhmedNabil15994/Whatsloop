<?php 
namespace App\Exports;

use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class ContactExport implements FromView,WithColumnWidths
{	

	public function __construct($group_id){
		$this->group_id = $group_id;
	}

    public function view() : View
    {	
    	return view('Tenancy.Contact.Views.smallTable', [
            'data' => Contact::generateObj(Contact::NotDeleted()->where('group_id',$this->group_id),true)['data']
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 55,            
            'C' => 55,            
            'D' => 55,            
            'E' => 55,            
            'F' => 55,            
            'G' => 55,            
        ];
    }
}