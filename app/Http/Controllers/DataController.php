<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $file = storage_path('app/public/evaluation-20190711.json');
        $content = file_get_contents($file);
        $datas = json_decode($content);

        return view('welcome', compact('datas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function csv($id)
    {
        $file = storage_path('app/public/evaluation-20190711.json');
        $content = file_get_contents($file);
        $datas = json_decode($content);

        foreach($datas->data as $data) {
            if($data->id == $id){
                $filename = $data->name.'.csv';
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=$filename");
                $fp = fopen('php://output', 'w');             
                $header = array('Title', 'Test', 'Score', 'Evaluated At');               
                fputcsv($fp, $header); 
                    
                foreach ($data->evaluation->score as $score) {

                    $utcTimezone = new \DateTimeZone('UTC');
                    $time = new \DateTime($data->evaluation->created_at, $utcTimezone);
                    $asiaTimezone = new \DateTimeZone('Asia/Kuala_Lumpur');
                    $date = $time->setTimeZone($asiaTimezone);
                    $timestamp = \DateTime::createFromFormat('d/m/Y', $date->format('d/m/Y'), $asiaTimezone);
                    
                    $csv = array(
                        'title' => $data->evaluation->title,
                        'test' => key($score),
                        'score' => reset($score),
                        'date' => date('d/m/Y', strtotime($data->evaluation->created_at)),
                        
                    );

                    fputcsv($fp, $csv);
                    
                }    
            }
        }
        fclose($fp);
    }
}
