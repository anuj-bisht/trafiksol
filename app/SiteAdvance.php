<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class SiteAdvance extends Model
{
     protected $table = "site_advances";

     protected $fillable = [
          'site_id','amount','reason','paid_to','added_by'
      ];

     public static function getSiteAdvance($site_id){
          $result = DB::table('site_advances')
            ->select('site_advances.*','users.name as username','sites.name as site_name')
            ->join('sites', 'sites.id', '=', 'site_advances.site_id')
            ->join('users', 'users.id', '=', 'site_advances.paid_to')
            ->where('site_advances.site_id',$site_id)
            ->orderBy('site_advances.created_at','desc')
            ->get();

        return $result;
     }
     public function getsiteDetailsById($id)
     {
         $result = DB::table("sites")->select('*')->where("id",$id)->get();
         return $result;
     }
}
