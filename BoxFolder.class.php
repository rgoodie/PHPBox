<?php

class BoxFolder {

    private $folderid, // base folder id
            $header_forauth, // Authorization header
            $auth_token, // Auth token
            $box_api_folder_url, // URL of box API 2.0 (for folder operations)
            $box_api_collab_url, // URL For collaboration operations
            $folder_contents;         // an object from the folder call

    public function __construct($folderid, $auth_token) {

        $this->auth_token = $auth_token;
        $this->folderid = $folderid;
        $this->box_api_folder_url = 'https://api.box.com/2.0/folders/' . $this->folderid;
        $this->box_api_collab_url = 'https://api.box.com/2.0/collaborations';
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
        $options['http']['header'] = $this->header_forauth . "\r\n";
        $context = stream_context_create($options);
        $result = file_get_contents($this->box_api_folder_url, false, $context);

        // put raw contents into variable for later processing
        $this->folder_contents = json_decode($result);

        if (!$this->folder_contents) {
            error_log(__CLASS__ . ": Failed to get any useable results from $this->box_api_folder_url ");
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
        foreach ($this->folder_contents->item_collection->entries as $item) {
            $collection[] = $item;
        }
        return $collection;
    }

    /**
     * Gets only subfolders
     * @return array of subfolder with name and ids
     */
    public function getFolderItems() {
        $collection = [];
        foreach ($this->getItems() as $item) {

            if ($item->type == 'folder') {
                $collection[] = [
                    'name' => $item->name,
                    'id' => $item->id,
                ];
            }
        }
        return $collection;
    }

    /**
    *  curl https://api.box.com/2.0/collaborations \
    *  -H "Authorization: Bearer ACCESS_TOKEN" \
    *  -d '{"item": { "id": "FOLDER_ID", "type": "folder"}, "accessible_by": { "id": "USER_ID", "type": "user" }, "role": "editor"}' \
    *  -X POST
     */
    public function addUser($email_address, $role = 'viewer uploader') {


        // sanitize data
        $email_address = filter_var($email_address, FILTER_SANITIZE_EMAIL);

        // data
        $d = [
            "item" => [
                "id" => $this->folderid,
                "type" => "folder",
            ],
            "accessible_by" => [
                "login" => $email_address,
            ],
            "role" => $role,
        ];


        // cURL magic
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$this->box_api_collab_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($d));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          $this->header_forauth ,
        ));


        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);

        return $result;
    }

}
