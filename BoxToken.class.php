<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BoxToken
 *
 * @author richard
 */
class BoxToken {

    private $baseURI,
            $responseType,
            $clientId,
            $redirectUrl

    ;

    function __construct($baseURI, $responseType, $clientId, $redirectUrl) {
        $this->baseURI = $baseURI;
        $this->responseType = $responseType;
        $this->clientId = $clientId;
        $this->redirectUrl = $redirectUrl;
    }

}
