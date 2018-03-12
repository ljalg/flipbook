<?php
include ('header.php');
?>
<div id="message" style="display:none;"></div>
<div id="searchInput">
    Search Objects & Properties<br>
    <input id="searchBox" type="text" name="ss" size=30><br>
    <div class="note">
        <?php
            $permissions=true; //check if logged in user has permissions to add object
            if ($permissions) {
               ?> 
               <div id="newobject"><a href="#" onclick="$('#form_add_object').toggle();"><i class='fa fa-plus-circle' aria-hidden='true'></i> Add Object</a></div>
               <div id='form_add_object' class='crud_form'>
                    Type: <select type='text' id='type'>
                        <?php
                        // get json data and use it in php
                        $url = $server_url."object_read.php?do=objecttypesdropdown";
                        $obj = json_decode(file_get_contents($url), true);
                        foreach ($obj as $o) { 
                            echo "<option value='".$o[name]."'>".$o[name]."</option>";
                        }
                        ?> </select>
                        Name: <input type='text' id='name'>
                        <i class='fa fa-floppy-o saveicon' id='addButton' aria-hidden='true'></i>
                    </form>
                </div>
            <?php };
        ?>
        </div>
</div>
<div id="dialog-form"></div>
<div class='list' id="resultList"></div>
        
<script>
/* global $ */

    $(document).ready(function() {
        // Search    
        $("#searchBox").change(function() {
            $("#results").show();
            $("#resultList").empty();
            if ($("#searchBox").val()!=='') {
                var url="object_read.php?do=search&ss="+$("#searchBox").val();
                $.getJSON(url,function(data){
                    if (!$.isEmptyObject(data)){
                        var lastType = '';
                        $.each(data, function(i,object){
                            var newRow = '';
                            if (object.otype !== lastType){
                                newRow = newRow+"<h2>"+object.otype+"</h2>";
                                lastType = object.otype;
                            }
                            newRow = newRow+"<a href='object.php?oid="+object.oid+"'>"+object.oname;
                            if (object.pname !== null){
                                newRow = newRow +" : "+object.pname+" : "+object.pvalue;
                            }
                            newRow = newRow + "</a><br>";
                            $("#resultList").append(newRow);
                        });
                    } else {
                        var newRow = "No objects found";
                        $("#resultList").append(newRow);
                    } //end if data  is empty

                });
            } else {
                var newRow = "Input at least SOMETHING in the search box";
                $("#resultList").append(newRow);
            }//end if nothing in search box
        });

        
        $("#addButton").click(function(){
            $.post("object_crud.php",{name: name.value,type: type.value, do:"newobject"}, function(data, status){
                alert (data);
                data = $.parseJSON(data);
                window.location.href = "object.php?oid="+data.oid;
                /*if (data == false){
                    // if it could not be created, display an error dialog box
                    displayMessage ("message_fail", "object could not be created, try again later.");
                } else {
                    window.location.href = "object.php?oid="+data.oid;
                }*/
            });
        });
    });  // close document ready
</script>
<?php
include ('footer.php');
?>
