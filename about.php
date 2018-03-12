<?php
include ('header.php');
?>
<h1>Flipbook</h1>
<div class="leftalign">
<h2>How it Started</h2>
<p>Flipbook was originally developed to because I, nor any of the server admins where I worked, could walk into the server room, point to a server
and say "if I turn off this server these people will be affected", it was always a "turn off the server and see who complains",
not the greatest way to do things in my opinion.  The original intention was to develop a way to be able to specify a 
server and determine what application(s) are installed on that server, what services that server provides and who uses that
application/service.</p>
<p>During development, the concept grew into not just a specific application for a single use-case, but a framework allowing users to be able to define objects 
their properties and relationships with other objects.</p>
<p>My goal is to be able to have many "canned" object and relationship definitions which can be easily imported for various situations; 
<ul>
    <li>Tracking servers, applications, support staff, services and customers</li>
        <ul>
            <li>Objects</li>
            <ul>
                <li>Server</li>
                <li>Application</li>
                <li>License</li>
                <li>Support Person</li>
                <li>Service</li>
                <li>Customer</li>
            </ul>
            <li>Relationships</li>
            <ul>
                <li>is managed by/manages (server and application/support person</li>
                <li>uses/is used by (customer/application or service)</li>
                <li>is dependent on/supports (appliation/license, service/application)</li>
                <li>is installed on/has installed (application/server)</li>
            </ul>
        </ul>
    <li>Equipment Tracking and Rentals; track equipment, customers and rentals</li>
    <li>Student Course Enrollments; track students, parents/guardians, classs they are in, etc.</li>
</ul>
</p>
<h2>What is it?</h2>
<p>Flipbook is a framework which allows users to track objects, object properties, relationships between objects and those relationship's properties.</p>
<h2>What can be done with it?</h2>
<p>The initial use case was to track Servers, Services, Software and People.</p>
<h2>Why was it developed?</h2>
<p>The closest thing I could find which just tracks objects and it's properties is Sharepoint's List funcionality, but Sharepoint's lists do not have relationships with
objects on other lists, if so, it's minor.</p>
<h2>How to use it</h2>
<p>Start by defining your objects, object properties, relationships and relationship properties in the Admin area.  Once those are defined you can begin inputting 
objects and their poperties and developing relationships between objects.</p>
<p>Removing or editing an object type, object property, relationship definition or relationship property does not delete those objects, poperties or relationshiops, it just 
prevents any new items being created with those definitions.  This allows the system admin to modify the system at any time without fear of losing data.</p>
</div>
<?php
include ('footer.php');
?>
