<?php
	/**
	* Generate a unique ID
	*/
	class Guid {
		
        public function __construct(){
            
        }
        
        public function guid(){
            
            if (function_exists('com_create_guid') === true){
                return strtolower(str_replace("-", "", trim(com_create_guid(), '{}')));
            }
        
            $data = openssl_random_pseudo_bytes(16);
            $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
            $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
            //return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)); Format GUID with "-"
            return vsprintf('%s%s%s%s%s%s%s%s', str_split(bin2hex($data), 4));
        }
        
        public function check_guid(){
            
            $b_guid = false;
            global $DB;
            
            while ($b_guid == false):
               
                $unique_guid = Guid::guid();

                $req = $DB->prepare("SELECT guid
                    FROM user
                    WHERE guid = ?");
                $req->execute([$unique_guid]);
                        
                $req = $req->fetch();
                
                if (!isset($req['guid'])){
                    $b_guid = true;
                }
                    
            endwhile;
            
            return $unique_guid;
            
        }

	}
