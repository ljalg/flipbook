<?php
/* this is a test file to learn to save information using ajax */

include ('header.php');
?>
<div id="message" style="display:none;"></div>

<div id="top">
    <h1><span id='name'></span> : <span id='type'></span></h1>
    <div id='objectEditLink'></div>
</div>
<div id="area1">
    <h2>Properties</h2>
    <div class='list' id='plist'></div>
</div>
<div id="area2">
    <h2>Relationships</h2>
    <div class='list' id='rlist'></div>
</div>
<script language="javascript" type="text/javascript" src="object.js"></script>

<div class="viz" style="clear:both;">
<div id="inner-details"></div>
<div id="log"></div>
<div id="infovis"></div>    
</div>
<div id="dialogConfirmObject" style="display:none;">
  <p>This object, all of it's properties and relationships will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>
<div id="dialogConfirmProperty" style="display:none;">
  <p>This object's property will be permanently deleted and cannot be recovered. Are you sure?</p>
</div>

<?php
include ('footer.php');
?>