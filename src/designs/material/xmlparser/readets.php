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
    $functioncount = 0;
    $functionDivCount = 0;
    
    $functions = array();
    $functions["Licht"] = array();
    $functions["Fenster"] = array();
    $functions["Heizung"] = array();
    $functions["Sicherheit"] = array();
    $functions["Wetter"] = array();
    
    foreach($node->Function as $function){
      if(strpos($function['Description'], "[CV:hidden]") === false){
        switch ($function["Type"]){
          case "SwitchableLight":
            $functions["Licht"][] = $function;
            $functioncount++;
            break;
          case "DimmableLight":
            $functions["Licht"][] = $function;
            $functioncount++;
            break;
          case "SunProtection":
            $functions["Fenster"][] = $function;
            $functioncount++;
            break;
          case "HeatingFloor":
            $functions["Heizung"][] = $function;
            $functioncount++;
            break;
          case "HeatingRadiator":
            $functions["Heizung"][] = $function;
            //$functioncount++;
            break;
          default:
            switch ($function["Name"]){
              case "Wetter":
                $functions["Wetter"][] = $function;
                $functioncount += 3;
                break;
              case "Sicherheit":
                $functions["Sicherheit"][] = $function;
                $functioncount += 2;
                break;
              default:
                break;
            }
            break;
        }
      }
    }
    
    //sort by number
    usort($functions["Licht"], "sortByNumber");
    usort($functions["Fenster"], "sortByNumber");
    usort($functions["Heizung"], "sortByNumber");
    usort($functions["Wetter"], "sortByNumber");
    usort($functions["Sicherheit"], "sortByNumber");
    
    foreach ($functions as $key => $functionType){
      if (sizeof($functionType) > 0){
        $functionDivCount++;
      }
    }
    
    printFunctionsDesktop($xmlDoc, $tab, $floor, $room, $name, $functions);
    printFunctionsMobile($xmlDoc, $tab, $floor, $room, $name, $functions, $functioncount, $functionDivCount);
  }
}

function printFunctionsMobile($xmlDoc, $tab, $floor, $room, $name, $functions, $functioncount, $functionDivCount){
  if ($functioncount > 5 && $functionDivCount > 1){
    echo printTab($tab)."&lt;page name=\"[MobileTabs][".$floor.".".$room.".0]".$name."\" visible=\"false\"&gt;"."<br/>";
    $tab++;

    echo printTab($tab)."&lt;navbar position=\"top\"&gt;"."<br/>";
    $tab++;
    
    if($functions["Licht"][0]->GroupAddressRef["Id"] != null){
      echo printTab($tab)."&lt;pagejump target=\"[MobileLicht][".$floor.".".$room.".1]".$name."\"&gt;"."<br/>";
      $tab++;
      echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
      echo printTab($tab)."&lt;label&gt;"."Licht"."&lt;/label&gt;<br/>";
      $tab--;
      echo printTab($tab)."&lt;/pagejump&gt;<br/>";
    }
    
    if($functions["Fenster"][0]->GroupAddressRef["Id"] != null){
      echo printTab($tab)."&lt;pagejump target=\"[MobileFenster][".$floor.".".$room.".2]".$name."\"&gt;"."<br/>";
      $tab++;
      echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
      echo printTab($tab)."&lt;label&gt;"."Fenster"."&lt;/label&gt;<br/>";
      $tab--;
      echo printTab($tab)."&lt;/pagejump&gt;<br/>";
    }
    
    if($functions["Heizung"][0]->GroupAddressRef["Id"] != null){
      echo printTab($tab)."&lt;pagejump target=\"[MobileHeizung][".$floor.".".$room.".3]".$name."\"&gt;"."<br/>";
      $tab++;
      echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
      echo printTab($tab)."&lt;label&gt;"."Heizung"."&lt;/label&gt;<br/>";
      $tab--;
      echo printTab($tab)."&lt;/pagejump&gt;<br/>";
    }  
    
    if($functions["Sicherheit"][0]->GroupAddressRef["Id"] != null){
      echo printTab($tab)."&lt;pagejump target=\"[MobileSicherheit][".$floor.".".$room.".4]".$name."\"&gt;"."<br/>";
      $tab++;
      echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
      echo printTab($tab)."&lt;label&gt;"."Sicherheit"."&lt;/label&gt;<br/>";
      $tab--;
      echo printTab($tab)."&lt;/pagejump&gt;<br/>";
    }  
    
    if($functions["Wetter"][0]->GroupAddressRef["Id"] != null){
      echo printTab($tab)."&lt;pagejump target=\"[MobileWetter][".$floor.".".$room.".5]".$name."\"&gt;"."<br/>";
      $tab++;
      echo printTab($tab)."&lt;layout colspan=\"0\"/&gt;<br/>";
      echo printTab($tab)."&lt;label&gt;"."Wetter"."&lt;/label&gt;<br/>";
      $tab--;
      echo printTab($tab)."&lt;/pagejump&gt;<br/>";
    }  
      
    $tab--;
    echo printTab($tab)."&lt;/navbar&gt;"."<br/>";
  } else {
    echo printTab($tab)."&lt;page name=\"[MobileHeader][".$floor.".".$room."]".$name."\" visible=\"false\"&gt;"."<br/>";
    $tab++;
  }
  
  $functionnr = 1;
  foreach ($functions as $key => $functionType){
    if($functionType[0]->GroupAddressRef["Id"] != null || $functionType->GroupAddressRef["Id"] != null){
      if ($functioncount > 5 && ($functions["Fenster"][0]->GroupAddressRef["Id"] != null || $functions["Heizung"][0]->GroupAddressRef["Id"] != null)){
        echo printTab($tab)."&lt;page name=\"[Mobile".$key."][".$floor.".".$room.".".$functionnr."]".$name."\" visible=\"false\"&gt;"."<br/>";
        $tab++;
      } else {
        echo printTab($tab)."&lt;text flavour=\"headline\"&gt;<br/>";
        $tab++;
        echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
        echo printTab($tab)."&lt;label&gt;";
        switch ($key){
          case "Licht":
            echo "&lt;icon name=\"lightbulb_on\" color=\"#888888\"/&gt;"; 
            break;
          case "Fenster":
            echo "&lt;icon name=\"shutter\" color=\"#888888\"/&gt;"; 
            break;
          case "Heizung":
            echo "&lt;icon name=\"temperature\" color=\"#888888\"/&gt;"; 
            break;
          case "Sicherheit":
            echo "&lt;icon name=\"alarm_on\" color=\"#888888\"/&gt;"; 
            break;
          case "Wetter":
            echo "&lt;icon name=\"windalarm_on\" color=\"#888888\"/&gt;"; 
            break;
          default:
            break;
        }
        echo $key."&lt;/label&gt;<br/>";
        $tab--;
        echo printTab($tab)."&lt;/text&gt;<br/>";
        
        echo printTab($tab)."&lt;break/&gt;<br/>";
      }
      
      $mobile = true;
      
      printFunctionTypes($xmlDoc, $tab, $functionType, $mobile);
      
      echo printTab($tab)."&lt;break/&gt;<br/>";
      
      if ($functioncount > 5 && $functionDivCount > 1){
        $tab--;
        echo printTab($tab)."&lt;/page&gt;"."<br/>";
      }
    }
    
    $functionnr++;
  }
  //---------------------------------------
  //if ($functions["Fenster"][0]->GroupAddressRef["Id"] != null || $functions["Heizung"][0]->GroupAddressRef["Id"] != null){
    $tab--;
    echo printTab($tab)."&lt;/page&gt;"."<br/>";
  //}
}

function printFunctionsDesktop($xmlDoc, $tab, $floor, $room, $name, $functions){
  echo printTab($tab)."&lt;page name=\"[DesktopHeader][".$floor.".".$room."]".$name."\" visible=\"false\"&gt;"."<br/>";
  $tab++;
  
  foreach ($functions as $key => $functionType){
    if($functionType[0]->GroupAddressRef["Id"] != null || $functionType->GroupAddressRef["Id"] != null){
      echo printTab($tab)."&lt;text flavour=\"headline\"&gt;<br/>";
      $tab++;
      echo printTab($tab)."&lt;layout colspan=\"12\"/&gt;<br/>";
      echo printTab($tab)."&lt;label&gt;";
      switch ($key){
        case "Licht":
          echo "&lt;icon name=\"lightbulb_on\" color=\"#888888\"/&gt;"; 
          break;
        case "Fenster":
          echo "&lt;icon name=\"shutter\" color=\"#888888\"/&gt;"; 
          break;
        case "Heizung":
          echo "&lt;icon name=\"temperature\" color=\"#888888\"/&gt;"; 
          break;
        case "Sicherheit":
          echo "&lt;icon name=\"alarm_on\" color=\"#888888\"/&gt;"; 
          break;
        case "Wetter":
          echo "&lt;icon name=\"windalarm_on\" color=\"#888888\"/&gt;"; 
          break;
        default:
          break;
      }
      echo $key."&lt;/label&gt;<br/>";
      $tab--;
      echo printTab($tab)."&lt;/text&gt;<br/>";
      
      echo printTab($tab)."&lt;break/&gt;<br/>";
      
      // echo printTab($tab)."&lt;group&gt;<br/>";
      // $tab++;
      // echo printTab($tab)."&lt;layout colspan=\"12\"/&gt;<br/>";
      
      $mobile = false;
      printFunctionTypes($xmlDoc, $tab, $functionType, $mobile);
      
      // $tab--;
      // echo printTab($tab)."&lt;/group&gt;<br/>";

    }
  }
  $tab--;
  echo printTab($tab)."&lt;/page&gt;"."<br/>";
}

function printFunctionTypes($xmlDoc, $tab, $functionType, $mobile){
  foreach($functionType as $function){
    if($function->GroupAddressRef["Id"] != null){
      $functionName = utf8_decode($function['Name']);
      
      if (strrpos($functionName, "]") > 0){
        $functionName = substr($functionName, strrpos($functionName, "]") + 2);
      }
      
      if(strpos($function['Description'], "[CV:hidden]") === false){
        switch ($function["Type"]){
          case "SwitchableLight":
            $GASwitchRead = null;
            $GASwitchWrite = null;
            
            foreach($function->GroupAddressRef as $GroupAddressRef){
              $GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
              $GroupAddressAttributes = $GroupAddress[0]->attributes();
              switch($GroupAddressAttributes['DatapointType']){
                case "DPST-1-1":
                  $GASwitchWrite = $GroupAddressAttributes;
                  break;
                case "DPST-1-11":
                  $GASwitchRead = $GroupAddressAttributes;
                  break;
                // //Sperren
                // case "DPST-1-3":
                  // break;
              }
            }
            
            
            echo printTab($tab)."&lt;group&gt;<br/>";
            $tab++;
            if ($mobile){
              echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
            }
            
            if ($GASwitchWrite['Central'] == "true"){
              echo printTab($tab)."&lt;group nowidget=\"true\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"3\" rowspan=\"13\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"4\" rowspan=\"13\"/&gt;<br/>";
              }
              
              echo printTab($tab)."&lt;text&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"2\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"2\"/&gt;<br/>";
              }
              //echo printTab($tab)."&lt;label&gt;&lt;icon name=\"light_light\" color=\"#FF8000\"/&gt;".$functionName."&lt;/label&gt;<br/>";
              echo printTab($tab)."&lt;label&gt;".$functionName."&lt;/label&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/text&gt;<br/>";
              
              echo printTab($tab)."&lt;trigger value=\"0\" mapping=\"OnOffStatus\" align=\"right\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"1\"/&gt;<br/>";
              }
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchWrite['DatapointType'])."\" variant=\"button\" mode=\"write\"&gt;".translateAddress($GASwitchWrite['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/trigger&gt;<br/>";
              
              echo printTab($tab)."&lt;trigger value=\"1\" mapping=\"OnOffStatus\" align=\"right\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"1\"/&gt;<br/>";
              }
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchWrite['DatapointType'])."\" mode=\"write\"&gt;".translateAddress($GASwitchWrite['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/trigger&gt;<br/>";
              
              echo printTab($tab)."&lt;text flavour=\"description\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
              }
              echo printTab($tab)."&lt;label&gt;zentral schalten&lt;/label&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/text&gt;<br/>";
              
              $tab--;
              echo printTab($tab)."&lt;/group&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;group nowidget=\"true\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"3\" rowspan=\"13\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"4\" rowspan=\"13\"/&gt;<br/>";
              }
              echo printTab($tab)."&lt;switch mapping=\"OnOff\" bind_click_to_widget=\"false\"&gt;<br/>";
              $tab++;
              //echo printTab($tab)."&lt;label&gt;&lt;icon name=\"light_light\" color=\"#FF8000\"/&gt;".$functionName."&lt;/label&gt;<br/>";
              echo printTab($tab)."&lt;label&gt;".$functionName."&lt;/label&gt;<br/>";
              if ($GASwitchRead != null){
                echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchWrite['DatapointType'])."\" mode=\"write\"&gt;".translateAddress($GASwitchWrite['Address'])."&lt;/address&gt;<br/>";
                echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchRead['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GASwitchRead['Address'])."&lt;/address&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchWrite['DatapointType'])."\" mode=\"readwrite\"&gt;".translateAddress($GASwitchWrite['Address'])."&lt;/address&gt;<br/>";
              }
              $tab--;
              echo printTab($tab)."&lt;/switch&gt;<br/>";
              
              echo printTab($tab)."&lt;info mapping=\"OnOffStatus\" flavour=\"description\" align=\"left\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
              }
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchRead['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GASwitchRead['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/info&gt;<br/>";
              
              $tab--;
              echo printTab($tab)."&lt;/group&gt;<br/>";
            }
            
            $tab--;
            echo printTab($tab)."&lt;/group&gt;<br/>";

            break;
          case "DimmableLight":
            $GASwitchRead = null;
            $GASwitchWrite = null;
            $GADimRead = null;
            $GADimWrite = null;
            $GADimStep = null;
            
            foreach($function->GroupAddressRef as $GroupAddressRef){
              $GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
              $GroupAddressAttributes = $GroupAddress[0]->attributes();
              switch($GroupAddressAttributes['DatapointType']){
                case "DPST-1-1":
                  $GASwitchWrite = $GroupAddressAttributes;
                  break;
                case "DPST-1-11":
                  $GASwitchRead = $GroupAddressAttributes;
                  break;
                case "DPST-3-7":
                  $GADimStep = $GroupAddressAttributes;
                  break;
                case "DPST-5-1":
                  if(strpos($GroupAddressAttributes['Description'], "read") > 0){
                    $GADimRead = $GroupAddressAttributes;
                  }
                  if(strpos($GroupAddressAttributes['Description'], "write") > 0){
                    $GADimWrite = $GroupAddressAttributes;
                  }
                  break;
                // //Sperren
                // case "DPST-1-3":
                  // break;
              }
            }
            
            echo printTab($tab)."&lt;group&gt;<br/>";
            $tab++;
            if ($mobile){
              echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
            }
            
            echo printTab($tab)."&lt;group nowidget=\"true\"&gt;<br/>";
            $tab++;
            if ($mobile){
              echo printTab($tab)."&lt;layout colspan=\"3\" rowspan=\"13\"/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;layout colspan=\"4\" rowspan=\"13\"/&gt;<br/>";
            }
            echo printTab($tab)."&lt;switch mapping=\"OnOff\" bind_click_to_widget=\"false\"&gt;<br/>";
            $tab++;
            if ($mobile){
              echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
            }
            //echo printTab($tab)."&lt;label&gt;&lt;icon name=\"light_control\" color=\"#FF8000\"/&gt;".$functionName."&lt;/label&gt;<br/>";
            echo printTab($tab)."&lt;label&gt;".$functionName."&lt;/label&gt;<br/>";
            if ($GASwitchRead != null){
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchRead['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GASwitchRead['Address'])."&lt;/address&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchWrite['DatapointType'])."\" mode=\"write\"&gt;".translateAddress($GASwitchWrite['Address'])."&lt;/address&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchWrite['DatapointType'])."\" mode=\"readwrite\"&gt;".translateAddress($GASwitchWrite['Address'])."&lt;/address&gt;<br/>";
            }
            $tab--;
            echo printTab($tab)."&lt;/switch&gt;<br/>";
            echo printTab($tab)."&lt;break/&gt;<br/>";
            
            echo printTab($tab)."&lt;trigger value=\"0\" shorttime=\"300\" shortvalue=\"6\" mapping=\"Licht\"&gt;<br/>";
            $tab++;
            echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
            echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchWrite['DatapointType'])."\" variant=\"button\" mode=\"write\"&gt;".translateAddress($GASwitchWrite['Address'])."&lt;/address&gt;<br/>";
            echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GADimStep['DatapointType'])."\" variant=\"short\" mode=\"write\"&gt;".translateAddress($GADimStep['Address'])."&lt;/address&gt;<br/>";
            $tab--;
            echo printTab($tab)."&lt;/trigger&gt;<br/>";

            echo printTab($tab)."&lt;slide min=\"0\" max=\"100\" step=\"5\" format=\"%d%%\" &gt;<br/>";
            $tab++;
            if ($mobile){
              echo printTab($tab)."&lt;layout colspan=\"2\"/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
            }
            if ($GADimRead != null){
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GADimRead['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GADimRead['Address'])."&lt;/address&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GADimWrite['DatapointType'])."\" mode=\"write\"&gt;".translateAddress($GADimWrite['Address'])."&lt;/address&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GADimWrite['DatapointType'])."\" mode=\"readwrite\"&gt;".translateAddress($GADimWrite['Address'])."&lt;/address&gt;<br/>";
            }
            $tab--;
            echo printTab($tab)."&lt;/slide&gt;<br/>";
            echo printTab($tab)."&lt;trigger value=\"1\" shorttime=\"300\" shortvalue=\"14\" mapping=\"Licht\" align=\"right\"&gt;<br/>";
            $tab++;
            echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
            echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GASwitchWrite['DatapointType'])."\" variant=\"button\" mode=\"write\"&gt;".translateAddress($GASwitchWrite['Address'])."&lt;/address&gt;<br/>";
            echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GADimStep['DatapointType'])."\" variant=\"short\" mode=\"write\"&gt;".translateAddress($GADimStep['Address'])."&lt;/address&gt;<br/>";
            $tab--;
            echo printTab($tab)."&lt;/trigger&gt;<br/>";
            $tab--;
            echo printTab($tab)."&lt;/group&gt;<br/>";
            
            
            $tab--;
            echo printTab($tab)."&lt;/group&gt;<br/>";
            
            break;
          case "SunProtection":
            $GADoorStatus = null;
            $GAUpDown = null;
            $GAStep = null;
            $GAPositionRead = null;
            $GAPositionWrite = null;
            $GABladeRead = null;
            $GABladeWrite = null;
          
            foreach($function->GroupAddressRef as $GroupAddressRef){
              $GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
              $GroupAddressAttributes = $GroupAddress[0]->attributes();
              
              switch($GroupAddressAttributes['DatapointType']){
                case "DPST-1-8":
                  $GAUpDown = $GroupAddressAttributes;
                  break;
                case "DPST-1-9":
                  $GAStep = $GroupAddressAttributes;
                  break;
                case "DPST-1-19":
                  $GADoorStatus = $GroupAddressAttributes;
                  break;
                case "DPST-1-11":
                  $GASwitchRead = $GroupAddressAttributes;
                  break;
                case "DPST-5-1":
                  if(strpos($GroupAddressAttributes['Description'], "position") > 0){
                    if(strpos($GroupAddressAttributes['Description'], "read") > 0){
                      $GAPositionRead = $GroupAddressAttributes;
                    }
                    if(strpos($GroupAddressAttributes['Description'], "write") > 0){
                      $GAPositionWrite = $GroupAddressAttributes;
                    }
                  }
                  if(strpos($GroupAddressAttributes['Description'], "blade") > 0){
                    if(strpos($GroupAddressAttributes['Description'], "read") > 0){
                      $GABladeRead = $GroupAddressAttributes;
                    }
                    if(strpos($GroupAddressAttributes['Description'], "write") > 0){
                      $GABladeWrite = $GroupAddressAttributes;
                    }
                  }
                  break;
              }
            }
            
            
            echo printTab($tab)."&lt;group&gt;<br/>";
            $tab++;
            if ($mobile){
              echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
            }

            //echo printTab($tab)."&lt;layout colspan=\"3\" /&gt;<br/>";
            echo printTab($tab)."&lt;group nowidget=\"true\" &gt;<br/>";
            $tab++;
            if ($mobile){
              echo printTab($tab)."&lt;layout colspan=\"3\" rowspan=\"20\"/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;layout colspan=\"4\" rowspan=\"20\"/&gt;<br/>";
            }
            
            if($GADoorStatus != null){
              echo printTab($tab)."&lt;info mapping=\"DoorOpenClose\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
              }
              //echo printTab($tab)."&lt;label&gt;&lt;icon name=\"fts_shutter\" color=\"#FF8000\"/&gt;".$functionName."&lt;/label&gt;<br/>";
              echo printTab($tab)."&lt;label&gt;".$functionName."&lt;/label&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"DPT:1.019\" mode=\"read\"&gt;".translateAddress($GADoorStatus['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/info&gt;<br/>";
              echo printTab($tab)."&lt;break/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;text&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
              }
              //echo printTab($tab)."&lt;label&gt;&lt;icon name=\"fts_shutter\" color=\"#FF8000\"/&gt;".$functionName."&lt;/label&gt;<br/>";
              echo printTab($tab)."&lt;label&gt;".$functionName."&lt;/label&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/text&gt;<br/>";
              echo printTab($tab)."&lt;break/&gt;<br/>";
            }
            
            if($GAPositionWrite != null){
              echo printTab($tab)."&lt;trigger value=\"0\" shorttime=\"300\" shortvalue=\"0\" mapping=\"Jalousien\"&gt;<br/>";
              $tab++;
              echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAUpDown['DatapointType'])."\" variant=\"button\" mode=\"write\"&gt;".translateAddress($GAUpDown['Address'])."&lt;/address&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAStep['DatapointType'])."\" variant=\"short\" mode=\"write\"&gt;".translateAddress($GAStep['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/trigger&gt;<br/>";
              
              echo printTab($tab)."&lt;slide min=\"0\" max=\"100\" step=\"10\" format=\"%d%%\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"2\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              }
              if($GAPositionRead != null){
                echo printTab($tab)."&lt;address transform=\"DPT:5.001\" mode=\"read\"&gt;".translateAddress($GAPositionRead['Address'])."&lt;/address&gt;<br/>";
                echo printTab($tab)."&lt;address transform=\"DPT:5.001\" mode=\"write\"&gt;".translateAddress($GAPositionWrite['Address'])."&lt;/address&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;address transform=\"DPT:5.001\" mode=\"readwrite\"&gt;".translateAddress($GAPositionWrite['Address'])."&lt;/address&gt;<br/>";
              }
              $tab--;
              echo printTab($tab)."&lt;/slide&gt;<br/>";
              
              echo printTab($tab)."&lt;trigger value=\"1\" shorttime=\"300\" shortvalue=\"1\" mapping=\"Jalousien\" align=\"right\"&gt;<br/>";
              $tab++;
              echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAUpDown['DatapointType'])."\" variant=\"button\" mode=\"write\"&gt;".translateAddress($GAUpDown['Address'])."&lt;/address&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAStep['DatapointType'])."\" variant=\"short\" mode=\"write\"&gt;".translateAddress($GAStep['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/trigger&gt;<br/>";
              
              echo printTab($tab)."&lt;break/&gt;<br/>";
            }
            
            if($GABladeWrite != null){
              echo printTab($tab)."&lt;trigger value=\"0\" mapping=\"Lamellen\"&gt;<br/>";
              $tab++;
              echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAStep['DatapointType'])."\" variant=\"button\" mode=\"write\"&gt;".translateAddress($GAStep['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/trigger&gt;<br/>";
              
              echo printTab($tab)."&lt;slide min=\"0\" max=\"100\" step=\"10\" format=\"%d%%\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"2\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              }
              if($GABladeRead != null){
                echo printTab($tab)."&lt;address transform=\"DPT:5.001\" mode=\"read\"&gt;".translateAddress($GABladeRead['Address'])."&lt;/address&gt;<br/>";
                echo printTab($tab)."&lt;address transform=\"DPT:5.001\" mode=\"write\"&gt;".translateAddress($GABladeWrite['Address'])."&lt;/address&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;address transform=\"DPT:5.001\" mode=\"readwrite\"&gt;".translateAddress($GABladeWrite['Address'])."&lt;/address&gt;<br/>";
              }
              $tab--;
              echo printTab($tab)."&lt;/slide&gt;<br/>";
              
              echo printTab($tab)."&lt;trigger value=\"1\" mapping=\"Lamellen\" align=\"right\"&gt;<br/>";
              $tab++;
              echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAStep['DatapointType'])."\" variant=\"button\" mode=\"write\"&gt;".translateAddress($GAStep['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/trigger&gt;<br/>";
              
              echo printTab($tab)."&lt;break/&gt;<br/>";
            }
            
            
            $tab--;
            echo printTab($tab)."&lt;/group&gt;<br/>";
            
            
            $tab--;
            echo printTab($tab)."&lt;/group&gt;<br/>";
            
            break;
          case "HeatingFloor":
            $GATemperatureRead = null;
            $GATemperatureWrite = null;
            $GAMode = null;
          
            foreach($function->GroupAddressRef as $GroupAddressRef){
              $GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
              $GroupAddressAttributes = $GroupAddress[0]->attributes();
              
              switch($GroupAddressAttributes['DatapointType']){
                case "DPST-9-1":
                  if(strpos($GroupAddressAttributes['Description'], "read") > 0){
                    $GATemperatureRead = $GroupAddressAttributes;
                  }
                  if(strpos($GroupAddressAttributes['Description'], "write") > 0){
                    $GATemperatureWrite = $GroupAddressAttributes;
                  }
                  break;
                case "DPST-20-102":
                  $GAMode = $GroupAddressAttributes;
                  break;
              }
            }
                        
            echo printTab($tab)."&lt;group&gt;<br/>";
            $tab++;
            if ($mobile){
              echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
            }
            
            echo printTab($tab)."&lt;group nowidget=\"true\" &gt;<br/>";
            $tab++;
            if ($mobile){
              echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
            } else {
              echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
            }
            
            if($GATemperatureRead != null){
              echo printTab($tab)."&lt;info format=\"%.1f ".utf8_decode(°)."C\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
              }
              //echo printTab($tab)."&lt;label&gt;&lt;icon name=\"temp_temperature\" color=\"#FF8000\"/&gt;Temperatur&lt;/label&gt;<br/>";
              echo printTab($tab)."&lt;label&gt;Temperatur&lt;/label&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"DPT:9.001\" mode=\"read\"&gt;".translateAddress($GATemperatureRead['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/info&gt;<br/>";
            }
            
            if($GATemperatureWrite != null){
              // echo printTab($tab)."&lt;infotrigger uplabel=\"+\" upvalue=\"0.5\" downlabel=\"-\" downvalue=\"-0.5\" styling=\"BluePurpleRedTemp\" infoposition=\"middle\" format=\"%.1f ".utf8_decode(°)."C\" change=\"absolute\" min=\"15\" max=\"25\"&gt;<br/>";
              // $tab++;
              // if ($mobile){
                // echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              // } else {
                // echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
              // }
              // echo printTab($tab)."&lt;label&gt;&lt;icon name=\"temperature\"/&gt;Soll&lt;/label&gt;<br/>";
              // echo printTab($tab)."&lt;address transform=\"DPT:9.001\" mode=\"readwrite\"&gt;".translateAddress($GATemperatureWrite['Address'])."&lt;/address&gt;<br/>";
              // $tab--;
              // echo printTab($tab)."&lt;/infotrigger&gt;<br/>";
              
              echo printTab($tab)."&lt;text align=\"left\"&gt;<br/>";
              $tab++;
              echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
              echo printTab($tab)."&lt;label&gt;&lt;icon name=\"temp_temperature_min\" color=\"#888888\"/&gt;&lt;/label&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/text&gt;<br/>";
              
              echo printTab($tab)."&lt;slide min=\"15\" max=\"25\" step=\"0.5\" format=\"%.1f".utf8_decode(°)."\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"2\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              }
              echo printTab($tab)."&lt;address transform=\"DPT:9.001\" mode=\"readwrite\"&gt;".translateAddress($GATemperatureWrite['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/slide&gt;<br/>";
              echo printTab($tab)."&lt;text align=\"right\"&gt;<br/>";
              $tab++;
              echo printTab($tab)."&lt;layout colspan=\"0.5\"/&gt;<br/>";
              echo printTab($tab)."&lt;label&gt;&lt;icon name=\"temp_temperature_max\" color=\"#888888\"/&gt;&lt;/label&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/text&gt;<br/>";
            }
            
            if($GAMode != null){
              echo printTab($tab)."&lt;break/&gt;<br/>";
              echo printTab($tab)."&lt;multitrigger button1label=\"Auto\" button1value=\"auto\" button2label=\"Komfort\" button2value=\"comfort\" button3label=\"Standy By\" button3value=\"standby\" button4label=\"Economy\" button4value=\"economy\" showstatus=\"true\"&gt;<br/>";
              $tab++;
              if ($mobile){
                echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
              } else {
                echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
              }
              //echo printTab($tab)."&lt;label&gt;Betriebsart&lt;/label&gt;<br/>";
              echo printTab($tab)."&lt;address transform=\"DPT:20.102\" mode=\"readwrite\"&gt;".translateAddress($GAMode['Address'])."&lt;/address&gt;<br/>";
              $tab--;
              echo printTab($tab)."&lt;/multitrigger&gt;<br/>";
            }
            
            $tab--;
            echo printTab($tab)."&lt;/group&gt;<br/>";
            
            $tab--;
            echo printTab($tab)."&lt;/group&gt;<br/>";

            break;
          case "HeatingRadiator":
            //tbd
            break;
          default:
            switch($function["Name"]){
              case "Wetter":
                $GAWindalarm = null;
                $GAWindspeed = null;
                $GABrightnessEast = null;
                $GABrightnessSouth = null;
                $GABrightnessWest = null;
                $GADawn = null;
                $GANight = null;
                $GATemperature = null;
                $GAFrostalarm = null;
              
                foreach($function->GroupAddressRef as $GroupAddressRef){
                  $GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
                  $GroupAddressAttributes = $GroupAddress[0]->attributes();
                  
                  switch(true){
                    case strstr($GroupAddressAttributes['Description'], "[CV:weather;windalarm]"):
                      $GAWindalarm = $GroupAddressAttributes;
                      break;
                    case strstr($GroupAddressAttributes['Description'], "[CV:weather;windspeed]"):
                      $GAWindspeed = $GroupAddressAttributes;
                      break;
                    case strstr($GroupAddressAttributes['Description'], "[CV:weather;brightness-east]"):
                      $GABrightnessEast = $GroupAddressAttributes;
                      break;
                    case strstr($GroupAddressAttributes['Description'], "[CV:weather;brightness-south]"):
                      $GABrightnessSouth = $GroupAddressAttributes;
                      break;
                    case strstr($GroupAddressAttributes['Description'], "[CV:weather;brightness-west]"):
                      $GABrightnessWest = $GroupAddressAttributes;
                      break;
                    case strstr($GroupAddressAttributes['Description'], "[CV:weather;dawn]"):
                      $GADawn = $GroupAddressAttributes;
                      break;
                    case strstr($GroupAddressAttributes['Description'], "[CV:weather;night]"):
                      $GANight = $GroupAddressAttributes;
                      break;
                    case strstr($GroupAddressAttributes['Description'], "[CV:weather;temperature]"):
                      $GATemperature = $GroupAddressAttributes;
                      break;
                    case strstr($GroupAddressAttributes['Description'], "[CV:weather;frostalarm]"):
                      $GAFrostalarm = $GroupAddressAttributes;
                      break;
                    default:
                      break;
                  }
                }
                
                if ($GAWindalarm != null || $GAWindspeed != null){
                  echo printTab($tab)."&lt;group&gt;<br/>";
                  $tab++;
                  
                  if ($mobile){
                    echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
                  } else {
                    echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
                  }
                  
                  echo printTab($tab)."&lt;text align=\"left\"&gt;<br/>";
                  $tab++;
                  echo printTab($tab)."&lt;label&gt;Wind&lt;/label&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/text&gt;<br/>";
                  
                  echo printTab($tab)."&lt;break/&gt;<br/>";

                  if ($GAWindalarm != null){
                    echo printTab($tab)."&lt;info mapping=\"AchtungSymbol\"&gt;<br/>";
                    $tab++;
                    echo printTab($tab)."&lt;label&gt;Windalarm&lt;/label&gt;<br/>";
                    echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAWindalarm['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GAWindalarm['Address'])."&lt;/address&gt;<br/>";
                    $tab--;
                    echo printTab($tab)."&lt;/info&gt;<br/>";
                  }
                  
                  if ($GAWindspeed != null){
                    echo printTab($tab)."&lt;info mapping=\"Windstärke_m/s_kurz\" &gt;<br/>";
                    $tab++;
                    echo printTab($tab)."&lt;label&gt;Windspeed&lt;/label&gt;<br/>";
                    echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAWindspeed['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GAWindspeed['Address'])."&lt;/address&gt;<br/>";
                    $tab--;
                    echo printTab($tab)."&lt;/info&gt;<br/>";
                  }
                  
                  $tab--;
                  echo printTab($tab)."&lt;/group&gt;<br/>";
                }
                
                echo printTab($tab)."&lt;group&gt;<br/>";
                $tab++;

                if ($mobile){
                  echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
                } else {
                  echo printTab($tab)."&lt;layout colspan=\"8\"/&gt;<br/>";
                }
                
                echo printTab($tab)."&lt;text align=\"left\"&gt;<br/>";
                $tab++;
                if (!$mobile){
                  echo printTab($tab)."&lt;layout colspan=\"8\"/&gt;<br/>";
                }
                echo printTab($tab)."&lt;label&gt;Helligkeit&lt;/label&gt;<br/>";
                $tab--;
                echo printTab($tab)."&lt;/text&gt;<br/>";
                
                echo printTab($tab)."&lt;break/&gt;<br/>";

                if ($GABrightnessEast != null){
                  echo printTab($tab)."&lt;info &gt;<br/>";
                  $tab++;
                  if (!$mobile){
                    echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
                  }
                  echo printTab($tab)."&lt;label&gt;Ost&lt;/label&gt;<br/>";
                  echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GABrightnessEast['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GABrightnessEast['Address'])."&lt;/address&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/info&gt;<br/>";
                }
                
                if ($GABrightnessSouth != null){
                  echo printTab($tab)."&lt;info &gt;<br/>";
                  $tab++;
                  if (!$mobile){
                    echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
                  }
                  echo printTab($tab)."&lt;label&gt;".utf8_decode("Süd")."&lt;/label&gt;<br/>";
                  echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GABrightnessSouth['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GABrightnessSouth['Address'])."&lt;/address&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/info&gt;<br/>";
                }
                
                if ($GABrightnessWest != null){
                  echo printTab($tab)."&lt;info &gt;<br/>";
                  $tab++;
                  if (!$mobile){
                    echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
                  }
                  echo printTab($tab)."&lt;label&gt;West&lt;/label&gt;<br/>";
                  echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GABrightnessWest['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GABrightnessWest['Address'])."&lt;/address&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/info&gt;<br/>";
                }
                
                if ($GADawn != null){
                  echo printTab($tab)."&lt;info &gt;<br/>";
                  $tab++;
                  if (!$mobile){
                    echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
                  }
                  echo printTab($tab)."&lt;label&gt;".utf8_decode("Dämmerungswert")."&lt;/label&gt;<br/>";
                  echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GADawn['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GADawn['Address'])."&lt;/address&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/info&gt;<br/>";
                }
                
                if ($GANight != null){
                  echo printTab($tab)."&lt;info &gt;<br/>";
                  $tab++;
                  if (!$mobile){
                    echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
                  }
                  echo printTab($tab)."&lt;label&gt;Nacht&lt;/label&gt;<br/>";
                  echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GANight['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GANight['Address'])."&lt;/address&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/info&gt;<br/>";
                }
                
                $tab--;
                echo printTab($tab)."&lt;/group&gt;<br/>";
                
                echo printTab($tab)."&lt;group&gt;<br/>";
                $tab++;
                
                if ($mobile){
                  echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
                } else {
                  echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
                }
                
                echo printTab($tab)."&lt;text align=\"left\"&gt;<br/>";
                $tab++;
                echo printTab($tab)."&lt;label&gt;Temperatur&lt;/label&gt;<br/>";
                $tab--;
                echo printTab($tab)."&lt;/text&gt;<br/>";
                
                echo printTab($tab)."&lt;break/&gt;<br/>";

                if ($GATemperature != null){
                  echo printTab($tab)."&lt;info format=\"%.1f ".utf8_decode(°)."C\" &gt;<br/>";
                  $tab++;
                  echo printTab($tab)."&lt;label&gt;".utf8_decode("Außentemperatur")."&lt;/label&gt;<br/>";
                  echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GATemperature['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GATemperature['Address'])."&lt;/address&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/info&gt;<br/>";
                }
                
                if ($GAFrostalarm != null){
                  echo printTab($tab)."&lt;info mapping=\"AchtungSymbol\" &gt;<br/>";
                  $tab++;
                  echo printTab($tab)."&lt;label&gt;Frostalarm&lt;/label&gt;<br/>";
                  echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAFrostalarm['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GAFrostalarm['Address'])."&lt;/address&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/info&gt;<br/>";
                }
                
                $tab--;
                echo printTab($tab)."&lt;/group&gt;<br/>";
                
                break;
              case "Sicherheit":
                $GAAlarmWrite = null;
                $GAAlarm = null;
                $GAWindows = null;
              
                foreach($function->GroupAddressRef as $GroupAddressRef){
                  $GroupAddress = $xmlDoc->xpath('//knx:GroupAddress[@Id="'.$GroupAddressRef["RefId"].'"]');
                  $GroupAddressAttributes = $GroupAddress[0]->attributes();
                  
                  switch($GroupAddressAttributes['DatapointType']){
                    case "DPST-1-1":
                      $GAAlarmWrite = $GroupAddressAttributes;
                      break;
                    case "DPST-1-5":
                      $GAAlarm = $GroupAddressAttributes;
                      break;
                    case "DPST-1-19":
                      $windowParams = explode(";", substr($GroupAddressAttributes['Description'], strpos($GroupAddressAttributes['Description'], "[CV:") + 4,  strpos($GroupAddressAttributes['Description'], "]") - strpos($GroupAddressAttributes['Description'], "[CV:") - 4));
                      $WindowStatus = null;
                      
                      $WindowStatus["Name"] = substr($windowParams[1], strpos($windowParams[1], "=") + 1);
                      $WindowStatus["Number"] = substr($windowParams[2], strpos($windowParams[2], "=") + 1);
                      $WindowStatus["Attributes"] = $GroupAddressAttributes;
                      $GAWindows[] = $WindowStatus;
                      break;
                  }
                }
                
                usort($GAWindows, "sortByNumber");
                
                if ($GAAlarmWrite != null){
                  echo printTab($tab)."&lt;group&gt;<br/>";
                  $tab++;
                  
                  if ($mobile){
                    echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
                  } else {
                    echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
                  }
                  
                  echo printTab($tab)."&lt;text align=\"left\"&gt;<br/>";
                  $tab++;
                  echo printTab($tab)."&lt;label&gt;Alarm&lt;/label&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/text&gt;<br/>";
                  
                  echo printTab($tab)."&lt;break/&gt;<br/>";
                  
                  echo printTab($tab)."&lt;switch mapping=\"OnOff\" bind_click_to_widget=\"false\"&gt;<br/>";
                  $tab++;
                  echo printTab($tab)."&lt;label&gt;Alarm schalten&lt;/label&gt;<br/>";
                  echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAAlarmWrite['DatapointType'])."\" mode=\"readwrite\"&gt;".translateAddress($GAAlarmWrite['Address'])."&lt;/address&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/switch&gt;<br/>";
                  
                  echo printTab($tab)."&lt;switch mapping=\"OnOff\" bind_click_to_widget=\"false\"&gt;<br/>";
                  $tab++;
                  echo printTab($tab)."&lt;label&gt;Alarm aktiv&lt;/label&gt;<br/>";
                  echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAAlarm['DatapointType'])."\" mode=\"readwrite\"&gt;".translateAddress($GAAlarm['Address'])."&lt;/address&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/switch&gt;<br/>";
                  
                  $tab--;
                  echo printTab($tab)."&lt;/group&gt;<br/>";
                }
                
                if (sizeof($GAWindows) > 0){
                  echo printTab($tab)."&lt;group&gt;<br/>";
                  $tab++;
                  
                  if ($mobile){
                    echo printTab($tab)."&lt;layout colspan=\"3\"/&gt;<br/>";
                  } else {
                    echo printTab($tab)."&lt;layout colspan=\"8\"/&gt;<br/>";
                  }
                  
                  echo printTab($tab)."&lt;text align=\"left\"&gt;<br/>";
                  $tab++;
                  if (!$mobile){
                    echo printTab($tab)."&lt;layout colspan=\"8\"/&gt;<br/>";
                  }
                  echo printTab($tab)."&lt;label&gt;Fensterstatus&lt;/label&gt;<br/>";
                  $tab--;
                  echo printTab($tab)."&lt;/text&gt;<br/>";
                  
                  foreach($GAWindows as $GAWindow){
                    echo printTab($tab)."&lt;info mapping=\"DoorOpenClose\" bind_click_to_widget=\"false\"&gt;<br/>";
                    $tab++;
                    if (!$mobile){
                      echo printTab($tab)."&lt;layout colspan=\"4\"/&gt;<br/>";
                    }
                    echo printTab($tab)."&lt;label&gt;".$GAWindow["Name"]."&lt;/label&gt;<br/>";
                    echo printTab($tab)."&lt;address transform=\"".translateDatapointType($GAWindow["Attributes"]['DatapointType'])."\" mode=\"read\"&gt;".translateAddress($GAWindow["Attributes"]['Address'])."&lt;/address&gt;<br/>";
                    $tab--;
                    echo printTab($tab)."&lt;/info&gt;<br/>";
                  }
                  
                  $tab--;
                  echo printTab($tab)."&lt;/group&gt;<br/>";
                }
                break;
              default:
                break;
            }
            break;
        }
      }
    }
  }
}

//----------------------------------------
$xmlDoc = simplexml_load_file("uploads/0.xml") or die("Error: Cannot create object");

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

echo printTab($tab)."&lt;page name=\"Start\" showtopnavigation=\"false\" showfooter=\"false\" shownavbar-left=\"true\"&gt;"."<br/>";
$tab++;

echo printTab($tab)."&lt;navbar position=\"top\"&gt;"."<br/>";
$tab++;
echo printTab($tab)."&lt;header/&gt;"."<br/>";
$tab--;
echo printTab($tab)."&lt;/navbar&gt;"."<br/>";
echo printTab($tab)."&lt;navbar position=\"left\" dynamic=\"true\" width=\"250\"&gt;"."<br/>";
$tab++;
echo printTab($tab)."&lt;menu/&gt;"."<br/>";

// echo printTab($tab)."&lt;line/&gt;"."<br/>";
// echo printTab($tab)."&lt;pagejump target=\"[Settings]Settings\"&gt;"."<br/>";
// $tab++;
// echo printTab($tab)."&lt;layout colspan=\"6\"/&gt;"."<br/>";
// echo printTab($tab)."&lt;label&gt;Settings&lt;/label&gt;"."<br/>";
// $tab--;
// echo printTab($tab)."&lt;/pagejump&gt;"."<br/>";
$tab--;
echo printTab($tab)."&lt;/navbar&gt;"."<br/>";

// echo printTab($tab)."&lt;page name=\"[Settings]Settings\" visible=\"false\"&gt;"."<br/>";
// $tab++;
// echo printTab($tab)."&lt;group name=\"setup\"&gt;"."<br/>";
// $tab++;
// echo printTab($tab)."&lt;layout colspan=\"12\"/&gt;"."<br/>";
// echo printTab($tab)."&lt;web src=\"designs/material/xmlparser/parser.html\" width=\"800px\" height=\"400px\" frameborder=\"false\" background=\"white\"&gt;"."<br/>";
// $tab++;
// echo printTab($tab)."&lt;layout colspan=\"12\"/&gt;"."<br/>";
// $tab--;
// echo printTab($tab)."&lt;/web&gt;"."<br/>";
// $tab--;
// echo printTab($tab)."&lt;/group&gt;"."<br/>";
// $tab--;
// echo printTab($tab)."&lt;/page&gt;"."<br/>";

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