<?php
/**
 * A simplified version of Token which only recognizes
 * a couple of different token types
 *
 */
Class TokenSimple extends Token {
    public function TokenSimple($code, $addTags=true, $onlyRecognize=false, $othersAreCalled="T_ALLOTHER") {
        $this->_addTags = $addTags;
        $code = $this->_surroundTags($code);
        $this->_tokenized = $this->_tokenizeString($code, '\n');
        
        // What makes it Simple
        $this->_tokenized = $this->_simplify($onlyRecognize, $othersAreCalled);
        
    }

    
    public function getTypes() {
        $cont = array();
        foreach ($this->_tokenized as $i=>$token) {
            $cont[] = $token["type"];
        }
        return $cont;
    }        

    
    public function getTokenized() {
        return $this->_tokenized;
    }
        
    private function _simplify($onlyRecognize=false, $othersAreCalled="T_ALLOTHER") {
        if (!$onlyRecognize) {
            $onlyRecognize = array(
                "T_CONSTANT_ENCAPSED_STRING", 
                "T_OPEN_TAG",
                "T_COMMENT"
            );
        }
        /*
            $storeAt["content"] = "";
            $storeAt["code"]    = "";
            $storeAt["type"]    = "";
            $storeAt["row"]     = $token["row"];
            $storeAt["col"]     = 0;
            $storeAt["len"]     = 0;
        */
        
        $newTokenized = array();
        $typePrev = -1;
        foreach ($this->_tokenized as $i=>$token) {
            
            $recognized = in_array($token["type"], $onlyRecognize);
            
            // Adjust type to generalize
            if ($recognized) {
                $typeCur = $token["type"];
            } else {
                $typeCur = $othersAreCalled;
            }
            
            if ($typeCur != $typePrev) {
                // Change: Create new
                $store = &$newTokenized[];
                
                // Clone the first token of this kind
                $store = $token;
                
                $store["type"] = $typeCur;
            } else {
                // Add new token to last one of same kind
                // ($store still points to last element in array)
                $store["content"] .= $token["content"];
                $store["len"]     += $token["len"];
            }
            
            $typePrev = $typeCur;
        }
        
        return $newTokenized;
    }
}
?>