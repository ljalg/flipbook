$(document).ready(function() {
/* global $ used to get rid of IDE error*/
    // get rid from URL
    function GetURLParameter(sParam) {
        var sPageURL = window.location.search.substring(1);
        var sURLVariables = sPageURL.split('&');
        for (var i = 0; i < sURLVariables.length; i++) {
            var sParameterName = sURLVariables[i].split('=');
            if (sParameterName[0] == sParam) {
                return sParameterName[1];
            }
        }
    }
    var rid = GetURLParameter('rid');
    
    //use pid to query database and get relationship info
   
    // if oid is 0 new object
    if (rid > 0) {
        var url_name_type="relationship_read.php?do=relationshipinfo&rid="+rid;
        $.getJSON(url_name_type,function(data){
            $.each(data, function(i,object){
                $(".parent").html("<a href='object.php?oid="+object.poid+"'>"+object.pname+":"+object.ptype+"</a>");
                $(".pRelDesc").html(object.pdesc);
                $(".child").html("<a href='object.php?oid="+object.coid+"'>"+object.cname+":"+object.ctype+"</a>");
                $(".cRelDesc").html(object.cdesc);
                $("#releditlink").html("<i class='fa fa-2x fa-pencil' id='relationship_edit' aria-hidden='true'></i>");
                edit_relationship_form(object.poid, object.coid, object.pdesc);
            });
        });
        
    } else {
        // if pid is undefined error
        $(".parent").html("No Parent");
        $(".pRelDesc").html("Selected");
        $(".child").html("No Child");
        $(".cRelDesc").html("Selected");
    } // close else if
    
    // populate properties
    var url_properties = "relationship_read.php?do=properties&rid="+rid;
    $.getJSON(url_properties, function(data) {
        if (!$.isEmptyObject(data)) {
            $.each(data, function(i, object) {
                newRow = "<div class='property'>"+object.pname+" : "+object.pvalue+" <i class='fa fa-pencil' id='relationshipPropertyEdit"+object.pid+"' aria-hidden='true'></i></div>";
                $("#plist").append(newRow);
                edit_property_form (object.pid, object.pvalue, object.ptype);
            });
        }
        else {
            var newRow = "No properties defined<br>";
            $("#plist").append(newRow);
        } //end if data  is empty
        $("#plist").append("<i class='fa fa-plus-circle fa-2x' id='new_relationship_property' aria-hidden='true'></i>");
        new_relationship_property_form();
    });

// edit relationship form 
function edit_relationship_form (parent_oid, child_oid, pdesc){
    var string_to_append = "<div id='form_edit_relationship' class='crud_form'>";
    string_to_append += "<form action='relationship_crud.php' method='post'>";
    
    // select parent
    var url1="object_read.php?do=allobjects";
    $.getJSON(url1,function(data){
        string_to_append += "Parent: <select name='pri_oid'>";
        
        var object_option_list = ''; //variable for second drop-down
        if (!$.isEmptyObject(data)){
            $.each(data, function(i,object){
                // populate first drop-down
                string_to_append += "<option ";
                if (object.oid === parent_oid) { string_to_append += "selected "}
                string_to_append += "value='"+object.oid+"'>"+object.name+" : "+object.type+"</option>";
                
                //populate second drop-down with same results but different selected option
                object_option_list += "<option ";
                if (object.oid === child_oid) { object_option_list += "selected "}
                object_option_list += "value='"+object.oid+"'>"+object.name+" : "+object.type+"</option>";
            });
        } else {
            $('#new_relationhip_property').hide();
            string_to_append += "<option>No Relationship Descriptions Defined</option>";
        } //end if data  is empty
        
        string_to_append += "</select>";
    
        // select relationship
        var url2="relationship_read.php?do=relationshipsdropdown";   
        $.getJSON(url2,function(data){     
            string_to_append += "Type: <select name='type'>";
            
            if (!$.isEmptyObject(data)){
                $.each(data, function(i,object){
                    string_to_append += "<option ";
                    if (pdesc === object.description) {string_to_append += "selected "}
                    string_to_append += "value='"+object.role+","+object.did+"'>"+object.description+"</option>";
                });
            } else {
                $('#new_relationship_property').hide();
                string_to_append += "<option>No Relationship Descriptions Defined</option>";
            } //end if data  is empty
            
            string_to_append += "</select>";
            
            
            // select child
            string_to_append += "Child: <select name='sec_oid'>";
            string_to_append += object_option_list;
            
            string_to_append += "</select>";
            
            string_to_append += "<input type='hidden' name='rid' value="+rid+">";
            string_to_append += "<input type='hidden' name='do' value='editrelationship'>";
           
            string_to_append += "<button type='submit' value='submit' name='submit'>Update</button>";
            string_to_append += "<br><button value='delete' name='submit'>Delete</button>";
            string_to_append += "</form></div>";
        
            $("#relationship_edit").after (string_to_append);
            $("#relationship_edit").on("click", function() {
                $("#form_edit_relationship").toggle();
            });
        });
    });
}

//new property form
function new_relationship_property_form () {
    var url="relationship_read.php?do=emptyproperties&rid="+rid;
    var string_to_append = "";
    $.getJSON(url,function(data){
        if (!$.isEmptyObject(data)){
            string_to_append = "<div id='form_new_relationship_property' class='crud_form'>";
            string_to_append += "<form action='relationship_crud.php' method='post'>";
            string_to_append += "Name: <select name='name'>";
            $.each(data, function(i,object){
                string_to_append += "<option value='"+object.name+"'>"+object.name+"</option>";
            });
            string_to_append += "</select>";
            string_to_append += "Value: <input type='text' size=10 name='value'>";
            string_to_append += "<input type='hidden' name='rid' value="+rid+">";
            string_to_append += "<input type='hidden' name='do' value='newrelationshipproperty'>";
            string_to_append += "<input type='submit' value='Submit'>";
            string_to_append += "</form></div>";
        } else {
            $('#new_relationship_property').hide();
            //string_to_append += "<option>All Properties Assigned</option>";
        }
        $("#new_relationship_property").after (string_to_append);
        $("#new_relationship_property").on("click", function() {
            $("#form_new_relationship_property").toggle();
        });
    });
}


// edit relationship propety form
//Edit property form
function edit_property_form (pid, pvalue, ptype){
    var url="object_read.php?do=property&pid="+pid;
    var string_to_append = "<div id='form_edit_property_"+pid+"' class='crud_form'>";
    string_to_append += "<form action='relationship_crud.php' method='post'>";
    
    $.getJSON(url,function(data){
        if (!$.isEmptyObject(data)){
            $.each(data, function(i,object){
                string_to_append += object.pname;
            });
        } else {
            $('#objectPropertyEdit"+pid').hide();
            //string_to_append += "<option>All Properties Assigned</option>";
        } //end if data  is empty
        
        string_to_append += " : <input type='text' value='"+pvalue+"' size="+pvalue.length+" name='value'>";
        string_to_append += "<input type='hidden' name='pid' value="+pid+">";
        string_to_append += "<input type='hidden' name='rid' value="+rid+">";
        string_to_append += "<input type='hidden' name='type' value='"+ptype+"'>";
        string_to_append += "<input type='hidden' name='do' value='editrelationshipproperty'>";
       
        string_to_append += "<button type='submit' value='submit' name='submit'>Update</button>";
        string_to_append += "<br><button value='delete' name='submit'>Delete</button>";
        string_to_append += "</form></div>";

        $("#relationshipPropertyEdit"+pid).after (string_to_append);
        $("#relationshipPropertyEdit"+pid).on("click", function() {
            $("#form_edit_property_"+pid).toggle();
        });
    });
}




}); // close document ready function