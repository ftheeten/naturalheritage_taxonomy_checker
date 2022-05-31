<?php
header('Content-type:application/csv');
header('Content-disposition:attachment; filename="checked_taxonomy.txt"');
header("Pragma:no-cache");

require_once("get_data_db.php");
session_start();
 
try
{
  
        $output = fopen('php://output', 'w');
        if(!array_key_exists("results", $_SESSION[$_REQUEST['id']])||!array_key_exists("headers", $_SESSION[$_REQUEST['id']]))
        {

            $db_parser=new GetDataDB();
             $db_parser->getData($_REQUEST['id']);
           
            
        }
        if(array_key_exists("results", $_SESSION[$_REQUEST['id']])&&array_key_exists("headers", $_SESSION[$_REQUEST['id']]))
        {
           
            $returned=Array();
           
            
            fputcsv($output, $_SESSION[$_REQUEST['id']]['headers'], "\t");
            foreach($_SESSION[$_REQUEST['id']]['results'] as $row)
            {
               
                fputcsv($output, array_merge($row['src_data'], $row['parsed_data']), "\t");
            }
           
            
            fclose($output);
	
        }
        
 }
 catch(Exception $e)
 {
    print($e->getMessage()); 

}

?>