<?php
/* this file displays the definitions information */
/* it is just a php file to include the header and footer */

include ('header.php');
?>
<div id="top">
    <h1>Definitions</h1>
    <p>Modifications to these definitions will not effect existing objects or relationships.</p>
</div>
<div id="area1">
    <h1>Object Types</h1>
    <div id="objecttypenew"></div>
    <div id="objecttypepropertieslist"></div>
</div>
<div id="area2">
    <h1>Relationships</h1>
    <div id="relationshiptypenew"></div>
    <div id="relationshiptypepropertieslist"></div>
</div>
<script src="definitions.js"></script>

<?php
include ('footer.php');
?>