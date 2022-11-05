<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
  
class Attendance extends Model
{

    public function user(){
        return $this->belongsTo('App\User');
    }
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'mark_datetime','geo_point', 'ostype','image','file_path',
    ];
  
    public static function deleteProject(){
        
        $project->delete();
        return redirect()->route('projects.index')
                        ->with('success','Project deleted successfully');
    }
   
    
}
