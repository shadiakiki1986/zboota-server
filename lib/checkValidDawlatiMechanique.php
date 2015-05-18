<?php

function checkValidDawlatiMechanique($fat31,$t,$tn) {
	$fat32 = array();
	for($i=0;$i<$fat31->length;$i++) array_push($fat32,$fat31->item($i)->nodeValue);
	if(!in_array($t,$fat32)) throw new Exception(sprintf("Invalid $tn '$t'. Please use one of: %s",implode(", ",$fat32)));
}
