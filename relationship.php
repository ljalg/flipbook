<?php
/* this file displays the relationship information and it's properties */
/* it is just a php file to include the header and footer */

include ('header.php');
?>

<h1> 
    <span class='parent'></span> - <span class='pRelDesc'></span> - <span class='child'></span><br>
    <span class='child'></span> - <span class='cRelDesc'></span> - <span class='parent'></span><br>
</h1>
    <div id='releditlink'></div>

<h2>Properties</h2>
<div class='list' id='plist'></div>


<script src="relationship.js"></script>


<?php
include ('footer.php');
?>