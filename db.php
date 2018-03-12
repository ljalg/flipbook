<?php
/* this file stores the database connection strings and gets passed the SQL to interact with the database */

$link = false;

    function DBConnection() {
        global $link;
        if( $link ) {
            return $link;
        } else {
        global $servername;
        global $username;
        global $password; 
        global $databasename;
        $link=mysqli_connect($servername,$username,$password,$databasename,"3306","/home/ubuntu/lib/mysql/socket/mysql.sock");
        if(mysqli_connect_errno()){
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }
        return $link;
    }
    }
    
    function CloseConn() {
        global $link;
        if( $link != false ) {
            mysqli_close($link);
        }
        $link = false;
    }
    
    // JSON functions
    
    function getJson($sql_string){
        $result = mysqli_query(DBConnection(), $sql_string);
        while($obj = mysqli_fetch_object($result)) {
            $var[] = $obj;
        }
        return json_encode($var);
    }
    
    function postJson($sql_string){
        $con = DBConnection();
        $result = mysqli_query($con, $sql_string);
        while($obj = mysqli_fetch_object($result)) {
            $var[] = $obj;
        }
        return mysqli_affected_rows($con);
    }
    
    // non-JSON functions
    
    function get_array_from_sql ($sql_string){
        if ($sql_string != null) {
            $result = mysqli_query(DBConnection(),$sql_string);
            while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                $found[] = $row;
            } //close while
        } else {
            $found[0] = "Error, missing SQL";
        } //close if
        mysqli_free_result($result);
        $found_no_empties=array_filter($found);
        //print_r ($found_no_empties);
        return $found_no_empties;
    }
    
    function get_single_from_sql ($sql_string) {
        if ($sql_string != null) {
            $conn = DBConnection();
            $result = mysqli_query($conn,$sql_string);
            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                $found = $row;
            } else if (mysqli_num_rows($result) === 0) {
                $found = 0;
            } else {
                $found = "Error: More than one result has been found using sql ".$sql_string;
            } //close if
        } else {
            $found = "Error: Missing SQL";
        } //close if

        mysqli_free_result($result);
        return $found;
    }
    
    Function update_data ($sql_string){
        $conn = DBConnection();
        $result = mysqli_query($conn,$sql_string);
        mysqli_free_result($result);
        return $result;
    }
    
    Function new_data ($sql_string){
        $conn = DBConnection();
        $result = mysqli_query($conn,$sql_string);
        $newid = mysqli_insert_id($conn);
        mysqli_free_result($result);
        return $newid;
    }
