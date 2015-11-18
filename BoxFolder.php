<?php

class BoxFolder {
    
  private $folderid,                // base folder id
          $header_forauth,          // Authorization header
          $auth_token,              // Auth token
          $box_api_url,             // URL of box API 2.0
          $folder_contents;         // an object from the folder call
  
  
  public function __construct($folderid, $auth_token) {
      
      $this->auth_token = $auth_token;
      $this->folderid = $folderid;
      $this->box_api_url = 'https://api.box.com/2.0/folders/' . $this->folderid;
      $this->header_forauth = 'Authorization: Bearer ' . $this->auth_token;
      
      // Get the contents of the folder on the init call
      $this->doGetFolderContents();
      
  }

  /**
   * Internal helper function, called on itit that stores a JSON/PHP array into 
   * $this->folder_contents;
   * 
   * Attribution: http://stackoverflow.com/a/6609181 gave me a bit of insight 
   * how to begin setting up this call to box's API 2
  */
  private function doGetFolderContents() {

    $options = [];
    $options['http']['header']  = $this->header_forauth . "\r\n";
    $context  = stream_context_create($options);
    $result = file_get_contents($this->box_api_url, false, $context);
    
    // put raw contents into variable for later processing
    $this->folder_contents = json_decode($result);
    
    if(! $this->folder_contents) {
        error_log( __CLASS__ . ": Failed to get any useable results from $this->box_api_url ");
    }
       
  }    
    
    
    
  
  /**
   * Returns raw folder contents
   * @return type 
   */
  public function getFolderContents() {
      return $this->folder_contents;
  }
  
  
 /**
  * Returns value of folder name
  * @return String - name of the folder
  */
  public function getFolderName() {
      return (String) $this->folder_contents->name;
  }
  
  
  /**
   * Returns an array of items from the folder
   * @return array of items from the folder
   */
  public function getItems() {
      $collection = [];
      foreach($this->folder_contents->item_collection->entries as $item) {
          $collection[] = $item;
      }
      return $collection;
  }

}




