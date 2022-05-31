<?php

	class GNNameParser
	{
		
		protected $scientific_name;
		protected $json;
		protected $flag = FALSE;
        protected $result;
		protected $service_url="http://naturalheritage.africamuseum.be:8989/api?";
		protected  $EXCEPTION_FILE="/var/www/html/natural_heritage_webservice/taxonomy/debug/exception.log";

		public function debug($text)
		{
			$myfile = fopen($this->EXCEPTION_FILE, "a+") ;
               fwrite($myfile,$text);
               fclose($myfile);
			   flush();
		}
		
		
		function callAPI($method, $url, $data)
		{
			
		   $curl = curl_init();
		   switch ($method){
			  case "POST":
				 curl_setopt($curl, CURLOPT_POST, 1);
				 if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
				 break;
			  case "PUT":
				 curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
				 if ($data)
					curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
				 break;
			  default:
				 if ($data)
					$url = sprintf("%s?%s", $url, http_build_query($data));
		   }
		   // OPTIONS:
		   curl_setopt($curl, CURLOPT_URL, $url);
		   curl_setopt($curl, CURLOPT_HTTPHEADER, array(			 
			  'Content-Type: application/json',
		   ));
		   curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		   curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		   // EXECUTE:
		   $result = curl_exec($curl);
		   if(!$result){die("Connection Failure");}
		   curl_close($curl);
		   return $result;
		}
		
		public function __construct($p_scientific_name)
		{
			$this->scientific_name=trim(trim($p_scientific_name,"'"),'"');
			$this->parse();
		}
		
		protected function parse()
		{
			try
            {
				$tmp=$this->scientific_name;
				print($this->service_url."q=".urlencode($tmp));
				$this->result=$this->callAPI('GET', $this->service_url."q=".urlencode($tmp), false);            
				
				$this->json=json_decode($this->result, TRUE);
				$this->json=$this->json[0];
				
			}
			catch(Exeption $e)
            {
               
                $this->debug($e->getMessage());
                
            }
			
		}
		
		public function getCanonicalName()
		{
			
            $returned=$this->json["canonicalName"]["simple"];			
           
			return $returned;
		}
	}
?>