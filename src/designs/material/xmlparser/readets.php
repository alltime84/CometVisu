<?php

function sortByNumber($a, $b)
{
	$a = str_replace('.', '', $a["Number"]);
	$b = str_replace('.', '', $b["Number"]);
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? -1 : 1;
}

function translateAddress($address) { 
	$Level1 = floor($address/2048);
	$Level1rest = $address%2048;
	$Level2 = floor($Level1rest/256);
	$Level3 = $Level1rest%256;
	return $Level1."/".$Level2."/".$Level3;
} 

function translateDatapointType($datapointType){
	$level1 = substr($datapointType, strpos($datapointType, "-") + 1, strrpos($datapointType, "-") - strpos($datapointType, "-") - 1);
	$level2 = substr($datapointType, strrpos($datapointType, "-") + 1);
	while(strlen($level2) < 3){
		$level2 = "0".$level2;
	}
	return "DPT:".$level1.".".$level2;
}

function printTab($level){
	$tab = "";
	for ($i = 0; $i < $level; $i++){
		$tab .= "&nbsp "; 
	}
	return $tab;
}

function printFunctions($xmlDoc, $node, $tab, $floor, $room, $name){
	if($node->Function != null){
		$functionnr = 1;
		
		$functions = array();
		$functions["Licht"] = array();
		$functions["Fenster"] = array();
		$functions["Heizung"] = array();
		$functions["Sonstiges"] = array();
		
		foreach($node->Function as $function){
			switch ($function["Type"]){
				case "SwitchableLight":
					$functions["Licht"][] = $function;
					break;
				case "DimmableLight":
					$functions["Licht"][] = $function;
					break;
				case "SunProtection":
					$functions["Fenster"][] = $function;
					break;
				case "HeatingFloor":
					$functions["Heizung"][] = $function;
					break;
				case "HeatingRadiator":
					$functions["Heizung"][] = $function;
					break;
				default:
					$functions["Sonstiges"][] = $function;
					break;
			}
		}
		if ($functions["Fenster"][0]->GroupAddressRef["Id"] != null || $functions["Heizung"][0]->GroupAddressRef["Id"] != null){
			echo printTab($tab)."&lt;page name=\"[Tabs][".$floor.".".$room.".0]".$name."\" visible=\"false\"&gt;"."<br/>";
			$tab++;
		}

		if ($functions["Fenster"][0]->GroupAddressRef["Id"] != null || $functions["Heizung"][0]->GroupAddressRef["Id"] != null){
			echo printTab($tab)."&lt;navbar position=\"top\"&gt;"."<br/>";
			$tab++;
			
			if($functions["Licht"][0]->GroupAddressRef["Id"] != null){
				echo printTab($tab)."&lt;pagejump target=\"[Licht][".$floor.".".$room.".1]".$name."\"&gt;"."<br/>";
				$tab++;
				echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
				echo printTab($tab)."&lt;label&gt;"."Licht"."&lt;/label&gt;<br/>";
				$tab--;
				echo printTab($tab)."&lt;/pagejump&gt;<br/>";
			}
			
			if($functions["Fenster"][0]->GroupAddressRef["Id"] != null){
				echo printTab($tab)."&lt;pagejump target=\"[Fenster][".$floor.".".$room.".2]".$name."\"&gt;"."<br/>";
				$tab++;
				echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
				echo printTab($tab)."&lt;label&gt;"."Fenster"."&lt;/label&gt;<br/>";
				$tab--;
				echo printTab($tab)."&lt;/pagejump&gt;<br/>";
			}
			
			if($functions["Heizung"][0]->GroupAddressRef["Id"] != null){
				echo printTab($tab)."&lt;pagejump target=\"[Heizung][".$floor.".".$room.".3]".$name."\"&gt;"."<br/>";
				$tab++;
				echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
				echo printTab($tab)."&lt;label&gt;"."Heizung"."&lt;/label&gt;<br/>";
				$tab--;
				echo printTab($tab)."&lt;/pagejump&gt;<br/>";
			}	
				
			$tab--;
			echo printTab($tab)."&lt;/navbar&gt;"."<br/>";
		}
		
		$functionnr = 1;
		foreach ($functions as $key => $functionType){
			if($functionType[0]->GroupAddressRef["Id"] != null || $functionType->GroupAddressRef["Id"] != null){
				if ($functions["Fenster"][0]->GroupAddressRef["Id"] != null || $functions["Heizung"][0]->GroupAddressRef["Id"] != null){
					echo printTab($tab)."&lt;page name=\"[".$key."][".$floor.".".$room.".".$functionnr."]".$name."\"&gt;"."<br/>";
				} else {
					echo printTab($tab)."&lt;page name=\"[Header][".$floor.".".$room.".".$functionnr."]".$name."\"&gt;"."<br/>";
				}
				$tab++;
				
				foreach($functionType as $function){
					if($function->GroupAddressRef["Id"] != null){
						$functionName = utf8_decode($function['Name']);
						
						if (strrpos($functionName, "]") > 0){
							$functionName = substr($functionName, strrpos($functionName, "]") + 2);
						}
						
						switch ($function["Type"]){
							case "SwitchableLight":
								echo printTab($tab)."&lt;group name=\"".$functionName."\"&gt;<br/>";
								$tab++;
								
								foreach($function->GroupAddressRef as $GroupAddressRef){
									$GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
									$GroupAddressAttributes = $GroupAddress[0]->attributes();
									switch($GroupAddressAttributes['DatapointType']){
										case "DPST-1-1":
											echo printTab($tab)."&lt;switch mapping=\"OnOff\" bind_click_to_widget=\"false\"&gt;<br/>";
											$tab++;
											echo printTab($tab)."&lt;label&gt;"."Schalten"."&lt;/label&gt;<br/>";
											echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GroupAddressAttributes['DatapointType'])."\" mode=\"readwrite\"&gt;".translateAddress($GroupAddressAttributes['Address'])."&lt;/address&gt;<br/>";
											$tab--;
											echo printTab($tab)."&lt;/switch&gt;<br/>";
											break;
										// //Sperren
										// case "DPST-1-3":
											// echo printTab($tab)."&lt;switch mapping=\"checkbox\" bind_click_to_widget=\"false\"&gt;<br/>";
											// $tab++;
											// echo printTab($tab)."&lt;label&gt;"."Sperren"."&lt;/label&gt;<br/>";
											// echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GroupAddressAttributes['DatapointType'])."\" mode=\"readwrite\"&gt;".translateAddress($GroupAddressAttributes['Address'])."&lt;/address&gt;<br/>";
											// $tab--;
											// echo printTab($tab)."&lt;/switch&gt;<br/>";
											// break;
									}
								}
								
								$tab--;
								echo printTab($tab)."&lt;/group&gt;<br/>";
								break;
							case "DimmableLight":
								echo printTab($tab)."&lt;group name=\"".$functionName."\"&gt;<br/>";
								$tab++;
								
								foreach($function->GroupAddressRef as $GroupAddressRef){
									$GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
									$GroupAddressAttributes = $GroupAddress[0]->attributes();
									switch($GroupAddressAttributes['DatapointType']){
										case "DPST-5-1":
											echo printTab($tab)."&lt;slide min=\"0\" max=\"100\" step=\"5\" format=\"%d%%\" &gt;<br/>";
											$tab++;
											echo printTab($tab)."&lt;label&gt;"."Dimmen"."&lt;/label&gt;<br/>";
											echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GroupAddressAttributes['DatapointType'])."\" mode=\"readwrite\"&gt;".translateAddress($GroupAddressAttributes['Address'])."&lt;/address&gt;<br/>";
											$tab--;
											echo printTab($tab)."&lt;/slide&gt;<br/>";
											break;
										// //Sperren
										// case "DPST-1-3":
											// echo printTab($tab)."&lt;switch mapping=\"checkbox\" bind_click_to_widget=\"false\"&gt;<br/>";
											// $tab++;
											// echo printTab($tab)."&lt;label&gt;"."Sperren"."&lt;/label&gt;<br/>";
											// echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GroupAddressAttributes['DatapointType'])."\" mode=\"readwrite\"&gt;".translateAddress($GroupAddressAttributes['Address'])."&lt;/address&gt;<br/>";
											// $tab--;
											// echo printTab($tab)."&lt;/switch&gt;<br/>";
											// break;
									}
								}
								
								$tab--;
								echo printTab($tab)."&lt;/group&gt;<br/>";
								break;
							case "SunProtection":
								$GroupAddresses = array();
							
								foreach($function->GroupAddressRef as $GroupAddressRef){
									$GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
									$GroupAddressAttributes = $GroupAddress[0]->attributes();
									$GroupAddresses[".".$GroupAddressAttributes['DatapointType']][] = $GroupAddressAttributes;
								}
								
								echo printTab($tab)."&lt;group name=\"".$functionName."\"&gt;<br/>";
								$tab++;
								echo printTab($tab)."&lt;layout colspan=\"3\" /&gt;<br/>";
								echo printTab($tab)."&lt;group nowidget=\"true\" &gt;<br/>";
								$tab++;
								echo printTab($tab)."&lt;layout colspan=\"3\" /&gt;<br/>";
								
								if($GroupAddresses[".DPST-1-19"] != null){
									echo printTab($tab)."&lt;switch mapping=\"OpenClose\" styling=\"OpenClose\" bind_click_to_widget=\"false\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
									echo printTab($tab)."&lt;label&gt;"."Status"."&lt;/label&gt;<br/>";
									echo printTab($tab)."&lt;address transform=\"DPT:1.019\" mode=\"read\"&gt;".translateAddress($GroupAddresses[".DPST-1-19"][0]['Address'])."&lt;/address&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/switch&gt;<br/>";
									echo printTab($tab)."&lt;break/&gt;<br/>";
								}
								
								if($GroupAddresses[".DPST-1-8"] != null && $GroupAddresses[".DPST-1-9"] != null){
									echo printTab($tab)."&lt;trigger value=\"0\" mapping=\"AufAbSymbol\" align=\"right\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;layout colspan=\"1\"/&gt;<br/>";
									echo printTab($tab)."&lt;address transform=\"DPT:1.008\" mode=\"readwrite\"&gt;".translateAddress($GroupAddresses[".DPST-1-8"][0]['Address'])."&lt;/address&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/trigger&gt;<br/>";
									
									echo printTab($tab)."&lt;trigger value=\"0\" mapping=\"Stop\" align=\"right\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;layout colspan=\"1\"/&gt;<br/>";
									echo printTab($tab)."&lt;address transform=\"DPT:1.009\" mode=\"readwrite\"&gt;".translateAddress($GroupAddresses[".DPST-1-9"][0]['Address'])."&lt;/address&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/trigger&gt;<br/>";
									
									echo printTab($tab)."&lt;trigger value=\"1\" mapping=\"AufAbSymbol\" align=\"right\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;layout colspan=\"1\"/&gt;<br/>";
									echo printTab($tab)."&lt;address transform=\"DPT:1.008\" mode=\"readwrite\"&gt;".translateAddress($GroupAddresses[".DPST-1-8"][0]['Address'])."&lt;/address&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/trigger&gt;<br/>";
									
									echo printTab($tab)."&lt;break/&gt;<br/>";
								}
								
								if($GroupAddresses[".DPST-5-1"] != null){
									echo printTab($tab)."&lt;text align=\"left\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
									echo printTab($tab)."&lt;label&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;icon name=\"fts_shutter_10\" color=\"#888888\"/&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/label&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/text&gt;<br/>";
									
									echo printTab($tab)."&lt;slide min=\"0\" max=\"100\" step=\"10\" format=\"%d%%\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;layout colspan=\"2\"/&gt;<br/>";
									echo printTab($tab)."&lt;address transform=\"DPT:5.001\" mode=\"readwrite\"&gt;".translateAddress($GroupAddresses[".DPST-5-1"][0]['Address'])."&lt;/address&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/slide&gt;<br/>";
									
									echo printTab($tab)."&lt;text align=\"right\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
									echo printTab($tab)."&lt;label&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;icon name=\"fts_shutter_100\" color=\"#888888\"/&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/label&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/text&gt;<br/>";
									
									echo printTab($tab)."&lt;break/&gt;<br/>";
									
									if($GroupAddresses[".DPST-5-1"][1] != null){
										echo printTab($tab)."&lt;text align=\"left\"&gt;<br/>";
										$tab++;
										echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
										echo printTab($tab)."&lt;label&gt;<br/>";
										$tab++;
										echo printTab($tab)."&lt;icon name=\"fts_blade_s_00\" color=\"#888888\"/&gt;<br/>";
										$tab--;
										echo printTab($tab)."&lt;/label&gt;<br/>";
										$tab--;
										echo printTab($tab)."&lt;/text&gt;<br/>";
										
										echo printTab($tab)."&lt;slide min=\"0\" max=\"100\" step=\"10\" format=\"%d%%\"&gt;<br/>";
										$tab++;
										echo printTab($tab)."&lt;layout colspan=\"2\"/&gt;<br/>";
										echo printTab($tab)."&lt;address transform=\"DPT:5.001\" mode=\"readwrite\"&gt;".translateAddress($GroupAddresses[".DPST-5-1"][1]['Address'])."&lt;/address&gt;<br/>";
										$tab--;
										echo printTab($tab)."&lt;/slide&gt;<br/>";
										
										echo printTab($tab)."&lt;text align=\"right\"&gt;<br/>";
										$tab++;
										echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
										echo printTab($tab)."&lt;label&gt;<br/>";
										$tab++;
										echo printTab($tab)."&lt;icon name=\"fts_blade_s_100\" color=\"#888888\"/&gt;<br/>";
										$tab--;
										echo printTab($tab)."&lt;/label&gt;<br/>";
										$tab--;
										echo printTab($tab)."&lt;/text&gt;<br/>";
										
										echo printTab($tab)."&lt;break/&gt;<br/>";
									}
								}
								
								$tab--;
								echo printTab($tab)."&lt;/group&gt;<br/>";
								
								$tab--;
								echo printTab($tab)."&lt;/group&gt;<br/>";
								break;
							case "HeatingFloor":
								$GroupAddresses = array();
							
								foreach($function->GroupAddressRef as $GroupAddressRef){
									$GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
									$GroupAddressAttributes = $GroupAddress[0]->attributes();
									$GroupAddresses[".".$GroupAddressAttributes['DatapointType']][] = $GroupAddressAttributes;
								}
							
								echo printTab($tab)."&lt;group name=\"".$functionName."\"&gt;<br/>";
								$tab++;
								echo printTab($tab)."&lt;layout colspan=\"12\"/&gt;<br/>";
								
								if($GroupAddresses[".DPST-9-1"][0] != null){
									echo printTab($tab)."&lt;info format=\"%.1f ".utf8_decode(°)."C\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;label&gt;Ist&lt;/label&gt;<br/>";
									echo printTab($tab)."&lt;address transform=\"DPT:9.001\" mode=\"read\"&gt;".translateAddress($GroupAddresses[".DPST-9-1"][0]['Address'])."&lt;/address&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/info&gt;<br/>";
								}
								
								if($GroupAddresses[".DPST-9-1"][1] != null){
									echo printTab($tab)."&lt;slide min=\"17\" max=\"23\" step=\"0.5\" format=\"%.1f ".utf8_decode(°)."C\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;layout colspan=\"6\"/&gt;<br/>";
									echo printTab($tab)."&lt;label&gt;Soll&lt;/label&gt;<br/>";
									echo printTab($tab)."&lt;address transform=\"DPT:9.001\" mode=\"readwrite\"&gt;".translateAddress($GroupAddresses[".DPST-9-1"][1]['Address'])."&lt;/address&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/slide&gt;<br/>";
								}
								
								if($GroupAddresses[".DPST-20-102"] != null){
									echo printTab($tab)."&lt;multitrigger button1label=\"Auto\" button1value=\"auto\" button2label=\"Komfort\" button2value=\"comfort\" button3label=\"Standy By\" button3value=\"standby\" button4label=\"Economy\" button4value=\"economy\" showstatus=\"true\"&gt;<br/>";
									$tab++;
									echo printTab($tab)."&lt;label&gt;Betriebsart&lt;/label&gt;<br/>";
									echo printTab($tab)."&lt;address transform=\"DPT:20.102\" mode=\"readwrite\"&gt;".translateAddress($GroupAddresses[".DPST-20-102"][0]['Address'])."&lt;/address&gt;<br/>";
									$tab--;
									echo printTab($tab)."&lt;/multitrigger&gt;<br/>";
								}
								
								$tab--;
								echo printTab($tab)."&lt;/group&gt;<br/>";
								break;
							case "HeatingRadiator":
								//tbd
								break;
							default:
								
								break;
						}
					}
				}
				
				$tab--;
				echo printTab($tab)."&lt;/page&gt;"."<br/>";
			}
			
			$functionnr++;
		}
		//---------------------------------------
		if ($functions["Fenster"][0]->GroupAddressRef["Id"] != null || $functions["Heizung"][0]->GroupAddressRef["Id"] != null){
			$tab--;
			echo printTab($tab)."&lt;/page&gt;"."<br/>";
		}
	}
}

//----------------------------------------
$xmlDoc = simplexml_load_file("0.xml") or die("Error: Cannot create object");

foreach($xmlDoc->getDocNamespaces() as $strPrefix => $strNamespace) {
    if(strlen($strPrefix)==0) {
        $strPrefix = "knx"; //Assign an arbitrary namespace prefix.
    }
    $xmlDoc->registerXPathNamespace($strPrefix,$strNamespace);
}
//----------------------------------------

//$buildings = $xmlDoc->xpath("/knx:KNX/knx:Project/knx:Installations/knx:Installation/knx:Buildings");

$buildingXML = $xmlDoc->Project->Installations->Installation->Buildings->BuildingPart;

$building = array();
$floors = array();

//floors
foreach ($buildingXML->BuildingPart as $floorsXML) {
	$floors[] = $floorsXML;
}

usort($floors, "sortByNumber");

for ($i = 0; $i < sizeof($floors); $i++){
	$rooms = array();
	foreach($floors[$i]->BuildingPart as $roomsXML) {
		$rooms[] = $roomsXML;
	}
	usort($rooms, "sortByNumber");
	$building[$i]["floor"] = $floors[$i];
	$building[$i]["rooms"] = $rooms;
}

$tab = 1;

echo printTab($tab)."&lt;page name=\"Start\" showtopnavigation=\"true\" showfooter=\"false\" shownavbar-left=\"true\"&gt;"."<br/>";
$tab++;

echo printTab($tab)."&lt;navbar position=\"top\"&gt;"."<br/>";
$tab++;
echo printTab($tab)."&lt;header&gt;&lt;/header&gt;"."<br/>";
$tab--;
echo printTab($tab)."&lt;/navbar&gt;"."<br/>";
echo printTab($tab)."&lt;navbar position=\"left\" dynamic=\"true\" width=\"250\"&gt;"."<br/>";
$tab++;
echo printTab($tab)."&lt;text&gt;"."<br/>";
$tab++;
echo printTab($tab)."&lt;label&gt;CometVisu&lt;/label&gt;"."<br/>";
$tab--;
echo printTab($tab)."&lt;/text&gt;"."<br/>";

echo printTab($tab)."&lt;line/&gt;"."<br/>";
echo printTab($tab)."&lt;menu&gt;"."<br/>";
echo printTab($tab)."&lt;/menu&gt;"."<br/>";

echo printTab($tab)."&lt;line/&gt;"."<br/>";
echo printTab($tab)."&lt;pagejump target=\"[Settings]Settings\"&gt;"."<br/>";
$tab++;
echo printTab($tab)."&lt;layout colspan=\"6\"/&gt;"."<br/>";
echo printTab($tab)."&lt;label&gt;Settings&lt;/label&gt;"."<br/>";
$tab--;
echo printTab($tab)."&lt;/pagejump&gt;"."<br/>";
$tab--;
echo printTab($tab)."&lt;/navbar&gt;"."<br/>";

echo printTab($tab)."&lt;page name=\"[Settings]Settings\" visible=\"false\"&gt;"."<br/>";
$tab++;
echo printTab($tab)."&lt;group name=\"setup\"&gt;"."<br/>";
$tab++;
echo printTab($tab)."&lt;layout colspan=\"12\"/&gt;"."<br/>";
echo printTab($tab)."&lt;web src=\"designs/material/xmlparser/parser.html\" width=\"800px\" height=\"400px\" frameborder=\"false\" background=\"white\"&gt;"."<br/>";
$tab++;
echo printTab($tab)."&lt;layout colspan=\"12\"/&gt;"."<br/>";
$tab--;
echo printTab($tab)."&lt;/web&gt;"."<br/>";
$tab--;
echo printTab($tab)."&lt;/group&gt;"."<br/>";
$tab--;
echo printTab($tab)."&lt;/page&gt;"."<br/>";

//-------------------------------
printFunctions($xmlDoc, $buildingXML, $tab, 0, 0, "Zentral");
//-------------------------------

$floornr = 1;
foreach($building as $floor){
	echo printTab($tab)."&lt;page name=\"[Menu][".$floornr.".0.0]".utf8_decode(substr($floor["floor"]["Name"], strrpos($floor["floor"]["Name"], "]") + 2))."\" visible=\"false\"&gt;"."<br/>";
	$tab++;
	if($floor["floor"]->Function != null){
			printFunctions($xmlDoc, $floor["floor"], $tab, $floornr, 0, "Zentral");
	}
	$roomnr = 1;
	foreach($floor["rooms"] as $room){
		printFunctions($xmlDoc, $room, $tab, $floornr, $roomnr, utf8_decode(substr($room["Name"], strrpos($room["Name"], "]") + 2)));
		$roomnr++;
	}
	$tab--;
	echo printTab($tab)."&lt;/page&gt;"."<br/>";
	$floornr++;
}

$tab--;
echo printTab($tab)."&lt;/page&gt;"."<br/>";

?>