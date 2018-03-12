<?php
/* this file returns the json for objects */

header("Content-Type: application/json; charset=UTF-8");

include ("config.php");
include ("db.php"); 


// confirm get variable do is set
if (isset($_GET['do'])) {

// return all object types and their properties
    if( $_GET['do']=="definitionobjecttypes"){
        $sql = "SELECT p.did as odid, p.name as odname, d.did as pdid, d.name as pdname, d.dataType as pdtype, d.format as pdformat, d.default as pddefault
                FROM definitions d right join definitions p on p.did=d.parent
                WHERE d.type='op' or p.type='o'
                ORDER BY p.name asc, d.name asc;";
        $result = getJson ($sql);
        echo ($result);
        
// return all relationship types and their properties
    } else if( $_GET['do']=="definitionrealationshiptypes"){
        $sql = "SELECT p.did as rdid, p.name as rdname, p.relationshipParentDesc as parentdesc, p.relationshipChildDesc as childdesc, 
                d.did as pdid, d.name as pdname, d.dataType as pdtype, d.format as pdformat, d.default as pddefault
                FROM definitions d right join definitions p on p.did=d.parent
                WHERE d.type='rp' or p.type='r'
                ORDER BY p.name asc, d.name asc;";
        $result = getJson ($sql);
        echo ($result);
        
    } else {
        echo ("error, nothing specified");
    }

} // close check if do is set
