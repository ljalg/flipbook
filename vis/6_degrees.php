<?php

function related_csv ($oid) {
    $array = related_array ($oid);
    $string = "";
    foreach ($array as $value){
        $string .= $value[oid].",";
    }
    $string = rtrim($string,",");
    return $string;
}


function related_array ($orig_oid) {

    $relations0 = array();
    $relations0[0]['oid'] = $_GET['oid'];
    $relations0 = add_field($relations0,'level',0);
    //print_r ($relations0);

    $relations1 = get_relations($orig_oid);
    $relations1 = add_field($relations1,'level',1);
    //print_r ($relations1);

    $relations2 = array();
    foreach ($relations1 as $value1) {
        $relations2 = array_merge($relations2,get_relations($value1['oid'],$orig_oid));
        $relations2 = add_field($relations2,'level',2);
        $relations2 = unique_multidim_array($relations2, 'oid');
    }
    //print_r ($relations2);
    
    $relations3 = array();
    foreach ($relations2 as $value2){
        $relations3 = array_merge($relations3,get_relations($value2['oid'],$orig_oid));
        $relations3 = add_field($relations3,'level',3);
        $relations3 = unique_multidim_array($relations3, 'oid');
    }
    //print_r ($relations3);
    
    $relations4 = array();
    foreach ($relations3 as $value3){
        $relations4 = array_merge($relations4,get_relations($value3['oid'],$orig_oid));
        $relations4 = add_field($relations4,'level',4);
        $relations4 = unique_multidim_array($relations4, 'oid');
    }
    //print_r ($relations4);
    
    $relations5 = array();
    foreach ($relations4 as $value4){
        $relations5 = array_merge($relations5,get_relations($value4['oid'],$orig_oid));
        $relations5 = add_field($relations5,'level',5);
        $relations5 = unique_multidim_array($relations5, 'oid');
    }
    //print_r ($relations5);

    $relations6 = array();
    foreach ($relations5 as $value5){
        $relations6 = array_merge($relations6,get_relations($value5['oid'],$orig_oid));
        $relations6 = add_field($relations6,'level',6);
        $relations6 = unique_multidim_array($relations6, 'oid');
    }
    //print_r ($relations6);

    $relations = array_merge($relations0,$relations1,$relations2,$relations3,$relations4,$relations5,$relations6);
    $relations = unique_multidim_array($relations, 'oid');
    //print_r ($relations);
    
    return $relations;

}


function get_relations ($oid, $no_oid) {
    $sql = "SELECT coid as oid FROM relationships where poid = ".$oid."
    UNION ALL
    SELECT poid as oid FROM relationships where coid = ".$oid;
    $result = get_array_from_sql ($sql);
    
    $result = removeElementWithValue($result, 'oid', $no_oid);
    
    return($result);
}

function removeElementWithValue($array, $key, $value){
     foreach($array as $subKey => $subArray){
          if($subArray[$key] == $value){
               unset($array[$subKey]);
          }
     }
     return $array;
}

function unique_multidim_array($array, $key) { 
    $temp_array = array(); 
    $i = 0; 
    $key_array = array(); 
    
    foreach($array as $val) { 
        if (!in_array($val[$key], $key_array)) { 
            $key_array[$i] = $val[$key]; 
            $temp_array[$i] = $val; 
        } 
        $i++; 
    } 
    return $temp_array; 
} 


function add_field ($array, $name, $level) {
     for ($i=0;$i<count($array);$i++) {
         $array[$i][$name] = $level;
     }
     return $array;
 }