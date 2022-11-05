<?php

namespace App\Http\Controllers\Traits;
use Config;
use DB;
trait Common {
    protected function DateDiff($data_from,$date_to) {
        try{
            $t1 = strtotime(date('Y-m-d H:i:s'));
            // $t1 = strtotime($data_from);
            $t2 = strtotime($date_to);
            $diff = $t2 - $t1;
            $hours = $diff / ( 60 * 60 );
            $hourFraction = $hours - (int)$hours;
            $minute = number_format($hourFraction*60,0);
            // if($minute>0 && $hours>0)
            // {
                $data = floor($hours).':'.$minute;
            // }
            // else{
            //     $data = '0:0';
            // }

            return $data;
          }catch(Exception $e){
            return 0;
          }
    }

    
    public function getRemainingHoursByTicketId($ticket_id){
      $result = DB::table('tickets')
          ->select(DB::raw('SEC_TO_TIME(SUM(time_to_sec(TIMEDIFF(ticket_pauses.pause_to,ticket_pauses.pause_from)))) as pause_hours'),
          DB::raw('TIMEDIFF(tickets.sla_end,tickets.sla_start) as ticket_hours')
          )
          ->join('ticket_pauses','ticket_pauses.ticket_id','=','tickets.id')
          ->groupBy('ticket_pauses.ticket_id')
          ->where('tickets.id',$ticket_id)
          ->where('ticket_pauses.is_approved','Y')
          ->where('ticket_pauses.start','Y')
          ->where('ticket_pauses.end','Y')
          ->get();
      return $result;
    }
}
