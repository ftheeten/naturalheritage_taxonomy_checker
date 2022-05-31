<?php
 $client = new SoapClient("http://www.marinespecies.org/aphia.php?p=soap&wsdl=1");
$var="Solea solea";
 $AphiaID=$client->getAphiaID($var);
 $taxon=$client->getAphiaRecordByID($AphiaID);
 echo "<b>AphiaID</b>: ".$taxon->AphiaID."<br />\n";
 echo "<b>Displayname</b>: ".$taxon->scientificname." ".$taxon->authority."<br />\n";
 echo "<b>URL</b>: ".$taxon->url."<br />\n";
 echo "<b>Accepted name</b>: ".$taxon->valid_name." ".$taxon->valid_authority."<br />\n";
 $class=$client->getAphiaClassificationByID($AphiaID);
 echo "<b>Classification</b>: ";
 show_classification($class);

 function show_classification($class){
  echo $class->scientificname." (".$class->rank.") > ";
  if ($class->child) show_classification($class->child);
 }
?>