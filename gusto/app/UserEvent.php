<?php 
namespace App;
  
use Illuminate\Database\Eloquent\Model;
  
class UserEvent extends Model
{
     
     protected $fillable = ['mobapp_id', 'fan_id', 'section','section_id','event','sub_event','event_props','transaction_id','notification_section','extra','timestamp','created'];
     
}
?>