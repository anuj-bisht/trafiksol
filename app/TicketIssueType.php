<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class TicketIssueType extends Model
{
    protected $table = "ticket_issue_types";

    protected $guarded = []; 
    //public $timestamps = false;

    
    //protected $fillable = ['name'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    

    public static function getCategoryId($id){
        return self::where('id',$id)->orderBy('name')->first();
    }
  
    public static function getALlIssueType(){
      return self::where('id','<>',0)->get();
    }    
}
