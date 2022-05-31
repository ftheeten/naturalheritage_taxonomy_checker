<?php
    require_once("Encoding.php");
    use \ForceUTF8\Encoding;
    ini_set('default_socket_timeout', 5);
	
	

	class ParseFishbaseJSON
	{
        protected $url_fishbase="https://fishbase.ropensci.org";
        protected  $EXCEPTION_FILE="/var/www/html/nh_taxonomy_checker/debug/exception.log";
        protected $sc_name;
		protected $full_name;
        protected $genus=false;
        protected $species=false;
        protected $mode="";
        protected $limit=1000;
        protected $res_array;
        protected $results_fishbase;
        protected $iMaxGenus=0;
        protected $iMaxSpecies=0;
		
		public function debug($text)
		{
			$myfile = fopen($this->EXCEPTION_FILE, "a+") ;
               fwrite($myfile,$text);
               fclose($myfile);
			     flush();
		}
        
        public function  __construct( $p_sc_name, $p_full_name)
        {
			  
			
            $this->sc_name=$p_sc_name;
			$this->full_name=$p_full_name;
            $this->results_fishbase=Array();
            $this->results_fishbase["fishbase_genus_key"]="";
            $this->results_fishbase["fishbase_url"]="";
            $this->results_fishbase["fishbase_match_type"]="";
			//$this->debug("LOAd\n");
			//$this->debug($this->sc_name);
			//$this->debug("_");
			//$this->debug($this->full_name);
        }
        
        protected function doCURL($url)
        {
            $curl = curl_init();
            curl_setopt_array($curl, [
                            CURLOPT_RETURNTRANSFER => 1,
                            CURLOPT_URL =>  $url,
                            CURLOPT_USERAGENT => 'Codular Sample cURL Request',
                           
                  ]);                       
             $resp = curl_exec($curl);                    
             curl_close($curl);
             return $resp;
        }
        
         protected function parseReplyGenus($p_data, $idx, $mode)
        {
            $data=json_decode($p_data, True);
            $data=$data["data"];
            foreach($data as $row)
            {
				$tmp=(string)($idx+1);
				$tmp=str_pad($tmp,2, "0", STR_PAD_LEFT);
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_author"]=$row["GenAuthorYear"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_status"]=$row["GenStatus"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_available"]=$row["Available"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_sub_family"]=$row["Subfamily"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_is_marine"]=str_replace('"\u0000"', 'False',str_replace('"\u0001"', 'True',json_encode($row["Marine"], JSON_UNESCAPED_UNICODE)));
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_is_brackish"]=str_replace('"\u0000"', 'False',str_replace('"\u0001"', 'True',json_encode($row["Brackish"], JSON_UNESCAPED_UNICODE)));
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_is_freshwater"]=str_replace('"\u0000"', 'False',str_replace('"\u0001"', 'True',json_encode($row["Freshwater"], JSON_UNESCAPED_UNICODE)));
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_comments"]=$row["Comment"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_remarks"]=$row["Remark"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_diagnosis"]=$row["Diagnosis"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_etymology"]=$row["Etymology"];
				
				
            }
        }
		
		 protected function parseReplySpecies($p_data, $idx, $mode)
        {
            $data=json_decode($p_data, True);
            $data=$data["data"];
            foreach($data as $row)
            {
				$tmp=(string)($idx+1);
				$tmp=str_pad($tmp,2, "0", STR_PAD_LEFT);
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_author"]=$row["Author"];                
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_sub_family"]=$row["Subfamily"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_is_dangerous"]=$row["Dangerous"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_is_brackish"]=str_replace('"\u0000"', 'False',str_replace('"\u0001"', 'True',json_encode($row["Brack"], JSON_UNESCAPED_UNICODE)));
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_is_freshwater"]=str_replace('"\u0000"', 'False',str_replace('"\u0001"', 'True',json_encode($row["Fresh"], JSON_UNESCAPED_UNICODE)));
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_comments"]=$row["Comments"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_remarks"]=$row["Remark"];			
				
            }
        }
		
		 protected function parseReplySynonyms($p_data, $idx, $mode)
        {
            $data=json_decode($p_data, True);
            $data=$data["data"];
			$idxSyn=1;
            foreach($data as $row)
            {
				$tmp=(string)($idx+1);
				$tmp=str_pad($tmp,2, "0", STR_PAD_LEFT);
				$tmp2=(string)($idxSyn+1);
				$tmp2=str_pad($tmp2,2, "0", STR_PAD_LEFT);
				$this->results_fishbase["fishbase_".$tmp."_".$mode."_".$idxSyn."_id"]=$row["SynCode"];
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_".$idxSyn."_name"]=$row["SynGenus"]." ".($row["SynSpecies"]??"")." ".($row["Author"]??"");                
                $this->results_fishbase["fishbase_".$tmp."_".$mode."_".$idxSyn."_valid"]=$row["Valid"];     
				$this->results_fishbase["fishbase_".$tmp."_".$mode."_".$idxSyn."_status"]=$row["Status"]; 
				$this->results_fishbase["fishbase_".$tmp."_".$mode."_".$idxSyn."_synonymy"]=$row["Synonymy"];
				$this->results_fishbase["fishbase_".$tmp."_".$mode."_".$idxSyn."_combination"]=$row["Combination"];	
				$this->results_fishbase["fishbase_".$tmp."_".$mode."_".$idxSyn."_comment"]=$row["Comment"];				
				$idxSyn++;				
				
            }
        }
        
        
        protected function getGenera($id, $idx)
        {
            $url_genera=$this->url_fishbase."/genera?".http_build_query(array("GenCode"=>$id));
           
            $result=$this->doCurl($url_genera);            
           
            if($result!==false)
            {
                $this->parseReplyGenus($result, $idx, "genus");
            }
        }
		
	    protected function getSpecies($id, $idx)
        {
            $url_species=$this->url_fishbase."/species?".http_build_query(array("SpecCode"=>$id));
            
            $result=$this->doCurl($url_species);
           
            if($result!==false)
            {
                $this->parseReplySpecies($result, $idx, "species");
            }
			
			$url_synonym=$this->url_fishbase."/synonyms?".http_build_query(array("SpecCode"=>$id));
            
            $result=$this->doCurl($url_synonym);
           
            if($result!==false)
            {
                $this->parseReplySynonyms($result, $idx, "synonyms");
            }
        }
        
        protected function sort_len($a,$b)
        {
            return strlen($a)-strlen($b);
        }

        protected function curl_detail_taxa($mode, $taxa_array)//, $species=NULL, $family=Null)
        {
            if($mode=="genus")
            {
			    $this->$iMaxGenus=0;
                foreach($taxa_array as $id=>$taxa)
                {
					$tmp=(string)($this->$iMaxGenus+1);
					$tmp=str_pad($tmp,2, "0", STR_PAD_LEFT);
                    $this->results_fishbase["fishbase_".$tmp."_genus_id"]=$id;
                    $this->results_fishbase["fishbase_".$tmp."_genus_taxa"]=$taxa;
                    $this->getGenera($id,$this->$iMaxGenus );
                    $this->$iMaxGenus++;
                }
            }
            elseif($mode=="species")
            {
				$this->$iMaxSpecies=0;
                foreach($taxa_array as $id=>$taxa)
                {
					$tmp=(string)($this->$iMaxGenus+1);
					$tmp=str_pad($tmp,2, "0", STR_PAD_LEFT);
                    $this->results_fishbase["fishbase_".$tmp."_species_id"]=$id;
                    $this->results_fishbase["fishbase_".$tmp."_species_taxa"]=$taxa;
                    $this->getSpecies($id,$this->$iMaxSpecies );
                    $this->$iMaxSpecies++;
                }
            }
            $this->results_fishbase["fishbase_nb_genera"]=$this->$iMaxGenus;
			//$this->results_fishbase["fishbase_nb_species"]=$this->$iMaxSpecies;
        }
        
        
 
        protected function parseJSON()
        {
			
            $data=$this->res_array["data"];
            $match_type="NOT_FOUND";
            if($this->mode=="genus")
            {
            
                $simplified=array_column($data, 'Genus', "GenCode");               
                uasort($simplified, array($this, 'sort_len'));
                $match_type="FUZZY_ON_GENUS";
                $result_fuzzy=Array();
                $result_exact=Array();
                foreach($simplified as $key => $value)
                {
					
                    if(strcmp(strtolower($value),strtolower($this->genus))===0)
                    {
											
                         $result_exact[$key]=$value;
                         $match_type="EXACT_ON_GENUS";
                    }
                    else
                    {
                         $result_fuzzy[$key]=$value;
                    }
                }
                if( $match_type=="EXACT_ON_GENUS")
                {
                    $array_genus=$result_exact;
                }
                else
                {
                    $array_genus = array_slice($result_fuzzy, 0, 1);
                }
                $this->curl_detail_taxa("genus", $array_genus);
                //$this->results_fishbase["fishbase_genus_key"]=print_r($simplified, TRUE);
            }
            elseif($this->mode=="species")
            {
				
				//$nominal= $this->genus.' '.$this->species;

				$this->$iMaxSpecies=0;
				 $result_fuzzy=Array();
                $result_exact=Array();
				$array_species=Array();
				$match_type="FUZZY_ON_SPECIES";
				foreach($data as $row)
				{
								
					if(strcasecmp(trim($row["Genus"]." ".$row["Species"]." ".$row["Author"]), trim($this->full_name))===0)
					{
						 $result_exact[$row["SpecCode"]]=trim($row["Genus"]." ".$row["Species"]." ".$row["Author"]);
						$match_type="EXACT_ON_SPECIES";
					}
					elseif(strcasecmp(trim($row["Genus"]." ".$row["Species"]),trim($this->sc_name))===0)
					{
						$result_exact[$row["SpecCode"]]=trim($row["Genus"]." ".$row["Species"]);
						if($match_type=="FUZZY_ON_SPECIES")
						{
							$match_type="EXACT_ON_SPECIES_DIFFERENT_AUTHOR";
						}
					}
					else
					{
						$result_fuzzy[$row["SpecCode"]]=trim($row["Genus"]." ".$row["Species"]);
					}
					
				}
				if($match_type=="FUZZY_ON_SPECIES")
					{
						 $array_species = array_slice($result_fuzzy, 0, 1);
					}
					else
					{
						$array_species = $result_exact;
					}
					
					$this->curl_detail_taxa("species", $array_species);
				
            }
            $this->results_fishbase["fishbase_nb_species"]=$this->$iMaxSpecies;
            $this->results_fishbase["fishbase_match_type"]=$match_type;
        }
        
        
        public function returnResult()
        {
            
            
             try
            {
                $name_array = explode(" ", $this->sc_name);
                if(count($name_array)>0)
                {
                    $criterias=array();
                    $this->genus=$name_array[0];
                    $criterias["limit"]=$this->limit;
                    $criterias["Genus"]=urlencode(Encoding::toUTF8($this->genus));
                    $this->mode="genus";
                    if(count($name_array)>1)
                    {
                        $this->species=$name_array[1];
                        $criterias["Species"]=urlencode(Encoding::toUTF8($this->species));
                        $this->mode="species";
                    }
					//$this->debug($this->mode);
                    $url_fb_1=$this->url_fishbase."/taxa?".http_build_query($criterias);
                    $resp=$this->doCURL($url_fb_1);
                    if($resp===FALSE)
                    {
                         $this->results_fishbase["fishbase_debug"]="ERR";
                         $this->results_fishbase["fishbase_url"]=$url_fb_1;
                    }
                    else
                    {       
                
                        //$this->results_fishbase["fishbase_debug"]=$resp;
                        $this->results_fishbase["fishbase_url"]=$url_fb_1;                       
                        $this->res_array=json_decode($resp, TRUE);                         
                        $this->parseJSON();
                    }
                    
                }
                return $this->results_fishbase;
              }            
            catch(Exeption $e)
            {
               $myfile = fopen($this->EXCEPTION_FILE, "a+") ;
                fwrite($myfile, $e->getMessage());
				fwrite($myfile, $e->getTraceAsString());
                fclose($myfile);
            }  
        }
        
		public static function array_merge_custom(&$nested1, &$nested2)
		{
			foreach($nested1 as $key=>$value)
			{
				if(!array_key_exists($key, $nested2))
				{
					$nested2[$key]="";
				}
			}
			foreach($nested2 as $key=>$value)
			{
				if(!array_key_exists($key, $nested1))
				{
					$nested1[$key]="";
				}
			}
			ksort($nested1);
			ksort($nested2);
		}
		
		public static function pad_results(&$dataset)
		{
			$count=count($dataset);
			if($count>1)
			{
				for($i=1; $i<$count; $i++)
				{
					$array_1=$dataset[$i-1]["parsed_data"];
					$array_2=$dataset[$i]["parsed_data"];
					ParseFishbaseJSON::array_merge_custom($array_1,$array_2);
					$dataset[$i-1]["parsed_data"]=$array_1;
					$dataset[$i]["parsed_data"]=$array_2;
				}
			}
			$headers=Array();
			if($count>0)
			{
				$line=$dataset[0];
				foreach($line as $field=>$val)
				{
						$headers[]=$field;
				}
				$_SESSION[session_id()]['headers']=$headers;
			}
		}
		
       
    
    }
    
?>