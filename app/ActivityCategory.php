<?php
  
  namespace App;

  use Illuminate\Database\Eloquent\Model;
use DB;  
class ActivityCategory extends Model
{
    
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name','parent_id'
    ];

    public static function getActivityList(){
        return self::where('parent_id',0)->orderBy('name')->get();
    }

    public static function getActivityListDD(){
        return self::where('parent_id',0)->pluck('name','id')->sortBy('name');
    }

    public static function getCategoryByParent($id){
        return self::where('parent_id',$id)->orderBy('name')->get();
    }

    public static function getCategoryId($id){
        return self::where('id',$id)->orderBy('name')->first();
    }
  
    public static function deleteProject(){
        
        $project->delete();
        return redirect()->route('activity_categories.index')
                        ->with('success','Activity category deleted successfully');
    }

    public static function getSiteActivityList($site_id){
        $result = DB::table('site_activities')
            ->select('site_activities.*','activities.name as activity_name','sites.name as site_name')
            ->join('activities','activities.id','=','site_activities.activity_id')               
            ->join('sites','sites.id','=','site_activities.site_id')                                 
            ->groupBy('site_activities.activity_id')                                     
            ->where('site_activities.site_id',$site_id)            
            ->get();            
        return $result;
    }


     // Menu builder function, parentId 0 is the root
     public function build_menu($parent, $menu) {
        $html = "<ol class='dd-list'>";
        if (isset($menu['parents'][$parent])) {
            
            foreach ($menu['parents'][$parent] as $itemId) {
                if(!isset($menu['parents'][$itemId]))
                {
                    $id = $menu['items'][$itemId]['id'];
                    $html .= "<li class='dd-item dd3-item'><div class='dd-handle dd3-handle'></div><div class='dd3-content'>".$menu['items'][$itemId]['name']."<span style='float:right'><a href='".url("/")."/activity_categories/".$id."/edit'>Edit</a> | <a class='deletemenu' id='".$id."' href='javascript:void(0)'>Delete</a></span></div></li>";
                }
                if(isset($menu['parents'][$itemId]))
                {
                    $id = $menu['items'][$itemId]['id'];
                    $html .= "</ol>";
                    $html .= "<ol class='dd-list'>
                    <li class='dd-item dd3-item'><div class='dd-handle dd3-handle'></div><div class='dd3-content'>".$menu['items'][$itemId]['name']."<span style='float:right'><a href='".url("/")."/activity_categories/".$id."/edit'>Edit</a> | <a class='deletemenu' id='".$id."' href='javascript:void(0)'>Delete</a></span></div>";
                    $html .= $this->build_menu($itemId, $menu);
                    $html .= "</li></ol>";
                }
            }
            //$html .= "</ol>";
        }
        return $html;
    }

    public function build_menu1($parent, $menu) {
        $html = "";
        if (isset($menu['parents'][$parent])) {
            //$html .= "<ul>";
            foreach ($menu['parents'][$parent] as $itemId) {


                if(!isset($menu['parents'][$itemId]))
                {
                    $id = $menu['items'][$itemId]['slug'];
                    
                    $html .= "<li class='nav-item'><a class='nav-link btn btn-primary' href='".url("/pages/$id")."'>".$menu['items'][$itemId]['name']."</a></li>";
                }
                if(isset($menu['parents'][$itemId]))
                {   
                    if($menu['items'][$itemId]['parent_id']==0){
                        $id = $menu['items'][$itemId]['slug'];
                        $html .= "<li class='nav-item dropdown'><a class='nav-link dropdown-toggle btn btn-primary' href='".url("/pages/$id")."'>".$menu['items'][$itemId]['name']."</a>";
                    }
                    if($parent==0){
                        $html .= "<ul class='dropdown-menu'>";
                    }
                    //$html .= "<li><a href='".$menu['items'][$itemId]['link']."'>".$menu['items'][$itemId]['name']."</a>";
                    $html .= $this->build_menu1($itemId, $menu);
                    $html .= "</li>";
                    if($parent==0){
                        $html .= "</ul>";
                    }

                    if($menu['items'][$itemId]['parent_id']==0){
                        $html .= "</li>";
                    }
                }
            }
            //$html .= "</ul>";
        }
        return $html;
    }
    
}
