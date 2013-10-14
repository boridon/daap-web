<?php
/*
** Name         : PHP_DMAP_Classes
**
** Author:      : T.M. Tromp
** Version      : 0.4    
** Description  : Native PHP Classes for DMAP and DAAP object parsing
**                and offering data serialization to XML and JSON
**
** Examples:   // parsing binairy DAAP data and displaying the contents as XML data
**             $dmap_doc->parse_binairydata($data);
**             header("content-type: text/xml");   
**             print($dmap_doc->get_xmlcontent());
**
**             // parsing and updating the default content-codes and displaying the strcuture
**             $dmap_doc->parse_contentcodes($data);
**             print_r($dmap_doc->contentcodes);
**
*/
class dmap_element {
    var $contentcodesnumber = 'hallo';
    var $tagname            = '';
    var $contentcodesname   = '';
    var $contentcodestype   = 0;
    var $value              = '';
    var $children           = array();
    
    function dmap_element($contentcodesnumber, $contentcodesname, $contentcodestype) {
        $this->contentcodesnumber = $contentcodesnumber;
        $this->contentcodesname   = $contentcodesname;
        $this->contentcodestype   = $contentcodestype;
    }
}

class dmap_document {
    var $elements     = array();
    var $contentcodes = array();
    
    function dmap_document() {
        $this->initializeDefaultContentCodes();
    }
    
    function initializeDefaultContentCodes() {
        $this->contentcodes["miid"] = new dmap_element( "miid", "dmap.itemid"          , 5);
        $this->contentcodes["minm"] = new dmap_element( "minm", "dmap.itemname"        , 9);
        $this->contentcodes["mikd"] = new dmap_element( "mikd", "dmap.itemkind"        , 1);
        $this->contentcodes["mper"] = new dmap_element( "mper", "dmap.persistentid"    , 7);
        $this->contentcodes["mcon"] = new dmap_element( "mcon", "dmap.container"       , 12);
        $this->contentcodes["mcti"] = new dmap_element( "mcti", "dmap.containeritemid" , 5);
        $this->contentcodes["mpco"] = new dmap_element( "mpco", "dmap.parentcontainerid" , 5);
        $this->contentcodes["mstt"] = new dmap_element( "mstt", "dmap.status"            , 5);
        $this->contentcodes["msts"] = new dmap_element( "msts", "dmap.statusstring"      , 9);
        $this->contentcodes["mimc"] = new dmap_element( "mimc", "dmap.itemcount"         , 5);
        $this->contentcodes["mctc"] = new dmap_element( "mctc", "dmap.containercount"    , 5);
        $this->contentcodes["mrco"] = new dmap_element( "mrco", "dmap.returnedcount"     , 5);
        $this->contentcodes["mlcl"] = new dmap_element( "mlcl", "dmap.listing"           , 12);
        $this->contentcodes["mlit"] = new dmap_element( "mlit", "dmap.listingitem"           , 12); // 12 but can also be 9
        
        $this->contentcodes["mbcl"] = new dmap_element( "mbcl", "dmap.bag"           , 12);
        $this->contentcodes["mdcl"] = new dmap_element( "mdcl", "dmap.dictionary"           , 12);
        $this->contentcodes["msrv"] = new dmap_element( "msrv", "dmap.serverinforesponse"           , 12);
        $this->contentcodes["msau"] = new dmap_element( "msau", "dmap.authenticationmethod"           , 1);
        $this->contentcodes["mslr"] = new dmap_element( "mslr", "dmap.loginrequired"           , 1);
        $this->contentcodes["mpro"] = new dmap_element( "mpro", "dmap.protocolversion"           , 11);
        $this->contentcodes["msal"] = new dmap_element( "msal", "dmap.supportsautologout"           , 1);
        $this->contentcodes["msup"] = new dmap_element( "msup", "dmap.supportsupdate"           , 1);
        $this->contentcodes["mspi"] = new dmap_element( "mspi", "dmap.supportspersistentids"           , 1);
        $this->contentcodes["msex"] = new dmap_element( "msex", "dmap.supportsextensions"           , 1);
        $this->contentcodes["msbr"] = new dmap_element( "msbr", "dmap.supportsbrowse"           , 1);
        $this->contentcodes["msqy"] = new dmap_element( "msqy", "dmap.supportsquery"           , 1);
        $this->contentcodes["msix"] = new dmap_element( "msix", "dmap.supportsindex"           , 1);
        $this->contentcodes["msrs"] = new dmap_element( "msrs", "dmap.supportsresolve"           , 1);
        $this->contentcodes["mstm"] = new dmap_element( "mstm", "dmap.timeoutinterval"           , 5);
        $this->contentcodes["msdc"] = new dmap_element( "msdc", "dmap.databasescount"           , 5);
        $this->contentcodes["mlog"] = new dmap_element( "mlog", "dmap.loginresponse"           , 12);
        $this->contentcodes["mlid"] = new dmap_element( "mlid", "dmap.sessionid"           , 5);
        $this->contentcodes["mupd"] = new dmap_element( "mupd", "dmap.updateresponse"           , 12);
        $this->contentcodes["musr"] = new dmap_element( "musr", "dmap.serverrevision"           , 5);
        $this->contentcodes["muty"] = new dmap_element( "muty", "dmap.updatetype"           , 1);
        $this->contentcodes["mudl"] = new dmap_element( "mudl", "dmap.deletedidlisting"           , 12);
        $this->contentcodes["mccr"] = new dmap_element( "mccr", "dmap.contentcodesresponse"           , 12);
        $this->contentcodes["mcnm"] = new dmap_element( "mcnm", "dmap.contentcodesnumber"           , 5);
        $this->contentcodes["mcna"] = new dmap_element( "mcna", "dmap.contentcodesname"           , 9);
        $this->contentcodes["mcty"] = new dmap_element( "mcty", "dmap.contentcodestype"           , 3);
        $this->contentcodes["apro"] = new dmap_element( "apro", "daap.protocolversion"           , 11);
        $this->contentcodes["avdb"] = new dmap_element( "avdb", "daap.serverdatabases"           , 12);
        $this->contentcodes["abro"] = new dmap_element( "abro", "daap.databasebrowse"           , 12);
        $this->contentcodes["abal"] = new dmap_element( "abal", "daap.browsealbumlisting"           , 12);
        $this->contentcodes["abar"] = new dmap_element( "abar", "daap.browseartistlisting"           , 12);
        $this->contentcodes["abcp"] = new dmap_element( "abcp", "daap.browsecomposerlisting"           , 12);
        $this->contentcodes["abgn"] = new dmap_element( "abgn", "daap.browsegenrelisting"           , 12);
        $this->contentcodes["adbs"] = new dmap_element( "adbs", "daap.databasesongs"           , 12);
        $this->contentcodes["asal"] = new dmap_element( "asal", "daap.songalbum"           , 9);
        $this->contentcodes["asar"] = new dmap_element( "asar", "daap.songartist"           , 9);
        $this->contentcodes["asbt"] = new dmap_element( "asbt", "daap.songbeatsperminute"           , 3);
        $this->contentcodes["asbr"] = new dmap_element( "asbr", "daap.songbitrate"           , 3);
        $this->contentcodes["ascm"] = new dmap_element( "ascm", "daap.songcomment"           , 9);
        $this->contentcodes["asco"] = new dmap_element( "asco", "daap.songcompilation"           , 1);
        $this->contentcodes["ascp"] = new dmap_element( "ascp", "daap.songcomposer"           , 9);
        $this->contentcodes["asda"] = new dmap_element( "asda", "daap.songdateadded"           , 10);
        $this->contentcodes["asdm"] = new dmap_element( "asdm", "daap.songdatemodified"           , 10);
        $this->contentcodes["asdc"] = new dmap_element( "asdc", "daap.songdisccount"           , 3);
        $this->contentcodes["asdn"] = new dmap_element( "asdn", "daap.songdiscnumber"           , 3);
        $this->contentcodes["asdb"] = new dmap_element( "asdb", "daap.songdisabled"           , 1);
        $this->contentcodes["aseq"] = new dmap_element( "aseq", "daap.songeqpreset"           , 9);
        $this->contentcodes["asfm"] = new dmap_element( "asfm", "daap.songformat"           , 9);
        $this->contentcodes["asgn"] = new dmap_element( "asgn", "daap.songgenre"           , 9);
        $this->contentcodes["asdt"] = new dmap_element( "asdt", "daap.songdescription"           , 9);
        $this->contentcodes["asrv"] = new dmap_element( "asrv", "daap.songrelativevolume"           , 1);
        $this->contentcodes["assr"] = new dmap_element( "assr", "daap.songsamplerate"           , 5);
        $this->contentcodes["assz"] = new dmap_element( "assz", "daap.songsize"           , 5);
        $this->contentcodes["asst"] = new dmap_element( "asst", "daap.songstarttime"           , 5);
        $this->contentcodes["assp"] = new dmap_element( "assp", "daap.songstoptime"           , 5);
        $this->contentcodes["astm"] = new dmap_element( "astm", "daap.songtime"           , 5);
        $this->contentcodes["astc"] = new dmap_element( "astc", "daap.songtrackcount"           , 3);
        $this->contentcodes["astn"] = new dmap_element( "astn", "daap.songtracknumber"           , 3);
        $this->contentcodes["asur"] = new dmap_element( "asur", "daap.songuserrating"           , 1);
        $this->contentcodes["asyr"] = new dmap_element( "asyr", "daap.songyear"           , 3);
        $this->contentcodes["asdk"] = new dmap_element( "asdk", "daap.songdatakind"           , 1);
        $this->contentcodes["asul"] = new dmap_element( "asul", "daap.songdataurl"           , 9);
        $this->contentcodes["aply"] = new dmap_element( "aply", "daap.databaseplaylists"           , 12);
        $this->contentcodes["abpl"] = new dmap_element( "abpl", "daap.baseplaylist"           , 1);
        $this->contentcodes["apso"] = new dmap_element( "apso", "daap.playlistsongs"           , 12);
        $this->contentcodes["arsv"] = new dmap_element( "arsv", "daap.resolve"           , 12);
        $this->contentcodes["arif"] = new dmap_element( "arif", "daap.resolveinfo"           , 12);
        $this->contentcodes["aeNV"] = new dmap_element( "aeNV", "com.apple.itunes.norm-volume"           , 5);
        $this->contentcodes["aeSP"] = new dmap_element( "aeSP", "com.apple.itunes.smart-playlist"           , 1);  
        $this->contentcodes["mtco"] = new dmap_element( "mtco", "dmap.specifiedtotalcount"     , 5);      
    }
    
    function parse_binairydata($bin_data) {
        $this->elements = $this->_parse_binairydata($bin_data);
    }
    
    function parse_contentcodes($bin_data) {
        $this->contentcodes = $this->_parse_contentcodes($this->_parse_binairydata($bin_data));
    }
    
    function get_xmlcontent() {
        return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' . $this->_parse_xmldocument($this->elements);
    }

    function get_jsonobject() {
        return $this->_parse_jsonobject($this->elements);
    }
    
    // protected    
    function _parse_binairydata($bin_data) {
        $elements = array();
        while (strlen($bin_data)>8) {
          $tag = substr($bin_data, 0, 4);
 
          $arr = unpack('Ni',substr($bin_data,4,4));
          
          $data_length = $arr['i'];         

          $elements[sizeof($elements)] = new dmap_element($tag, $this->contentcodes[$tag]->contentcodesname, $this->contentcodes[$tag]->contentcodestype);
          $elements[sizeof($elements)-1]->tagname=$tag; // tag it
          
          $value = '';       
          switch ($this->contentcodes[$tag]->contentcodestype) {
               case 12: // if container contains an empty $tag... a container should be treated as an normal element
                        $next_tag = substr($bin_data, 8,4);                      
                        if ($this->contentcodes[$next_tag]==false) {
                            $value = substr($bin_data, 8, $data_length);
                        } else {
                           $elements[sizeof($elements)-1]->children = $this->_parse_binairydata(substr($bin_data, 8, $data_length));
                           $value = substr($bin_data, 16, 4);  
                        }
                        //$elements[sizeof($elements)-1]->children = $this->_parse_binairydata(substr($bin_data, 8, $data_length));   
                        
                        // This is the TAG used for Content-Codes          
                        //$value = substr($bin_data, 16, 4);  
                        break;                     
               case 5:  // $arr = unpack('c',substr($bin_data, 8, $data_length));
                        //$value = $arr[1];
                        $arr = unpack('Ni',substr($bin_data, 8, $data_length)); // HOW MANY BYTES?
                        $value = $arr['i'];
                        break;
               case 11: $arr = unpack('c',substr($bin_data, 8+1, 2)); 
                        $value = $arr[1];
                  
                        $arr = unpack('c',substr($bin_data, 8+2, 1)); 
                        $value = $value .".". $arr[1];
                     
                        $arr = unpack('c',substr($bin_data, 8+3, 1)); 
                        $value = $value .".". $arr[1];
                        break;
               case 9:  $value = substr($bin_data, 8, $data_length);
                        $elements[sizeof($elements)-1]->value = $value; 
                        break;         
               case 1:  $arr = unpack('c',substr($bin_data, 8, $data_length));
                        $value = $arr[1];
                        break;
               case 3:  $arr = unpack('c',substr($bin_data, 8+1, 2)); // HOW MANY BYTES?
                        $value = $arr[1];
                        //print(" ". $data_length);
                        //$data_length = 2;
                        break;
               default: $arr = unpack('c',substr($bin_data, 8, $data_length));
                        $value = $arr[1];
                        $elements[sizeof($elements)-1]->contentcodesname=$tag; // unknown so we use the tag
                      //print("<br/>Type unknown: ". $tag); 
                      break;       
           }
           $elements[sizeof($elements)-1]->value = $value;
           $bin_data = substr($bin_data, 8+$data_length, strlen($bin_data)-(8+$data_length) ); 
        }
        return $elements; 
    }
    
    function _parse_xmldocument($elements) {
       $xml_data='';
    
       foreach($elements as $key => $value) {
          if (is_a($elements[$key],"dmap_element")) {
              if ((intval($elements[$key]->contentcodestype)==12)) {
                      $xml_data .= "\r\n<".$elements[$key]->contentcodesname .">";
					  if (sizeof($elements[$key]->children)>0) {
                        $xml_data .=  $this->_parse_xmldocument($elements[$key]->children);
					  } else {
                        $xml_data .= "<![CDATA[". trim($elements[$key]->value) ."]]>";
					  }
                      $xml_data .= "</".$elements[$key]->contentcodesname .">";
              } else {
                  $xml_data .= "\r\n<".$elements[$key]->contentcodesname .">";                  
                  $xml_data .= "<![CDATA[". $elements[$key]->value ."]]>";
                  $xml_data .= "</".$elements[$key]->contentcodesname .">";
              }
          }
       }
       return $xml_data;
   }

   function _parse_jsonobject($elements) {
       $json_data='';
    
       for($i=0;$i<sizeof($elements);$i++) {
          if (is_a($elements[$i],"dmap_element")) {
              if (intval($elements[$i]->contentcodestype)==12) {                   
                  if ($i>0) {
                    $json_data .= " , ";
                  }
                  $json_data .= " {";
                  $json_data .= '"contentcodesnumber": "'. $elements[$i]->contentcodesnumber .'",';
                  $json_data .= '"contentcodesname": "'. $elements[$i]->contentcodesname .'",';
                  $json_data .= '"contentcodestype": "'. $elements[$i]->contentcodestype .'",';
                  $json_data .= '"children": ['. $this->_parse_jsonobject($elements[$i]->children) .'] ';                
                  $json_data .= "} ";
              } else {
                  if ($i>0) {
                    $json_data .= " , ";
                  }
                  $json_data .= " {";
                  $json_data .= '"contentcodesnumber": "'. $elements[$i]->contentcodesnumber .'",';
                  $json_data .= '"contentcodesname": "'. $elements[$i]->contentcodesname .'",';
                  $json_data .= '"contentcodestype": "'. $elements[$i]->contentcodestype .'",';
                  $json_data .= '"value": "'. $elements[$i]->value .'"';                
                  $json_data .= "} ";
              }
          }
       }
      
       return $json_data;
   }   

   function _parse_contentcodes($elements) {
       $contentcodes = array();
       
       foreach($elements as $key => $value) {
          if (is_a($elements[$key],"dmap_element")) {
              if (intval($elements[$key]->contentcodestype)==12) {
                  // If dictionary element found
              
                  if ($elements[$key]->contentcodesname=="dmap.dictionary") {
                     // Parse dictionary element
                     
                     $contentcode = new dmap_element("","","");
                     
                     for ($i=0; $i<sizeof($elements[$key]->children); $i++) {
                         
                         switch ($elements[$key]->children[$i]->contentcodesname) {
                             case "dmap.contentcodesnumber":    $contentcode->contentcodesnumber = $elements[$key]->children[$i]->value;
                                                                break;
                             case "dmap.contentcodesname": $contentcode->contentcodesname = $elements[$key]->children[$i]->value;
                                                                break;
                             case "dmap.contentcodestype":      $contentcode->contentcodestype = $elements[$key]->children[$i]->value;
                                                                break;
                         }
                     }
                     $contentcode->tagname = $elements[$key]->value;
                     $contentcodes[$elements[$key]->value] = $contentcode;
                  } else { 
                    $contentcodes = array_merge($contentcodes , $this->_parse_contentcodes($elements[$key]->children));
                  }
              } 
          }
       }
       return $contentcodes;            
   }

   function get_contentcode_byname($content_codename) {
      foreach($this->contentcodes as $key => $value) {
		  if ($this->contentcodes[$key]->contentcodesname==$content_codename) {
			  return $this->contentcodes[$key];
		  }
	  }
	  return false;
   }
}
?>
