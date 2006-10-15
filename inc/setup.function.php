<?php
/*
 * @version $Id$
 -------------------------------------------------------------------------
 GLPI - Gestionnaire Libre de Parc Informatique
 Copyright (C) 2003-2006 by the INDEPNET Development Team.

 http://indepnet.net/   http://glpi-project.org
 -------------------------------------------------------------------------

 LICENSE

 This file is part of GLPI.

 GLPI is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation; either version 2 of the License, or
 (at your option) any later version.

 GLPI is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with GLPI; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 --------------------------------------------------------------------------
 */

// ----------------------------------------------------------------------
// Original Author of file:
// Purpose of file:
// ----------------------------------------------------------------------

if (!defined('GLPI_ROOT')){
	die("Sorry. You can't access directly to this file");
	}

// FUNCTIONS Setup

function showFormTreeDown ($target,$name,$human,$ID,$value2='',$where='',$tomove='',$type='') {

	global $CFG_GLPI, $LANG;

	if (!haveRight("dropdown","w")) return false;

	echo "<div align='center'>&nbsp;\n";
	echo "<form method='post' action=\"$target\">";

	echo "<table class='tab_cadre_fixe'  cellpadding='1'>\n";
	echo "<tr><th colspan='3'>$human:</th></tr>";
	if (countElementsInTable("glpi_dropdown_".$name)>0){
		echo "<tr><td  align='center' valign='middle' class='tab_bg_1'>";
		echo "<input type='hidden' name='which' value='$name'>";


		$value=getTreeLeafValueName("glpi_dropdown_".$name,$ID,1);

		dropdownValue("glpi_dropdown_".$name, "ID",$ID,0);
		// on ajoute un input text pour entrer la valeur modifier
		echo "&nbsp;&nbsp<input type='image' class='calendrier' src=\"".$CFG_GLPI["root_doc"]."/pics/puce.gif\" alt='' title='' name='fillright' value='fillright'>&nbsp";


		echo "<input type='text' maxlength='100' size='20' name='value' value=\"".$value["name"]."\"><br>";
		echo "<textarea rows='2' cols='50' name='comments' title='".$LANG["common"][25]."' >".$value["comments"]."</textarea>";

		echo "</td><td align='center' class='tab_bg_2' width='99'>";
		echo "<input type='hidden' name='tablename' value='glpi_dropdown_".$name."'>";
		//  on ajoute un bouton modifier
		echo "<input type='submit' name='update' value='".$LANG["buttons"][14]."' class='submit'>";
		echo "</td><td align='center' class='tab_bg_2' width='99'>";
		//
		echo "<input type='submit' name='delete' value=\"".$LANG["buttons"][6]."\" class='submit'>";
		echo "</td></tr></table></form>";

		echo "<form method='post' action=\"$target\">";

		echo "<input type='hidden' name='which' value='$name'>";
		echo "<table class='tab_cadre_fixe' cellpadding='1'>\n";

		echo "<tr><td align='center' class='tab_bg_1'>";

		dropdownValue("glpi_dropdown_".$name, "value_to_move",$tomove,0);
		echo "&nbsp;&nbsp;&nbsp;".$LANG["setup"][75]." :&nbsp;&nbsp;&nbsp;";

		dropdownValue("glpi_dropdown_".$name, "value_where",$where,0);
		echo "</td><td align='center' colspan='2' class='tab_bg_2' width='202'>";
		echo "<input type='hidden' name='tablename' value='glpi_dropdown_".$name."' >";
		echo "<input type='submit' name='move' value=\"".$LANG["buttons"][20]."\" class='submit'>";

		echo "</td></tr>";	

	}
		echo "</table></form>";	

	echo "<form action=\"$target\" method='post'>";
	echo "<input type='hidden' name='which' value='$name'>";

	echo "<table class='tab_cadre_fixe' cellpadding='1'>\n";
	echo "<tr><td  align='center'  class='tab_bg_1'>";
	echo "<input type='text' maxlength='100' size='15' name='value'>&nbsp;&nbsp;&nbsp;";


	if (countElementsInTable("glpi_dropdown_".$name)>0){
		echo "<select name='type'>";
		echo "<option value='under' ".($type=='under'?" selected ":"").">".$LANG["setup"][75]."</option>";
		echo "<option value='same' ".($type=='same'?" selected ":"").">".$LANG["setup"][76]."</option>";
		echo "</select>&nbsp;&nbsp;&nbsp;";
		;
		dropdownValue("glpi_dropdown_".$name, "value2",$value2,0);
	}		
	else echo "<input type='hidden' name='type' value='first'>";

	echo "<br><textarea rows='2' cols='50' name='comments' title='".$LANG["common"][25]."' ></textarea>";

	echo "</td><td align='center' colspan='2' class='tab_bg_2'  width='202'>";
	echo "<input type='hidden' name='tablename' value='glpi_dropdown_".$name."' >";
	echo "<input type='submit' name='add' value=\"".$LANG["buttons"][8]."\" class='submit'>";
	echo "</td></tr>";



	echo "</table></form></div>";
}


function showFormDropDown ($target,$name,$human,$ID,$value2='') {

	global $DB,$CFG_GLPI, $LANG;

	if (!haveRight("dropdown","w")) return false;

	echo "<div align='center'>&nbsp;";
	echo "<form method='post' action=\"$target\">";

	echo "<table class='tab_cadre_fixe' cellpadding='1'>";
	echo "<tr><th colspan='3'>$human:</th></tr>";
	if (countElementsInTable("glpi_dropdown_".$name)>0){
		echo "<tr><td class='tab_bg_1' align='center' valign='top'>";
		echo "<input type='hidden' name='which' value='$name'>";

		dropdownValue("glpi_dropdown_".$name, "ID",$ID,0);
		// on ajoute un input text pour entrer la valeur modifier
		echo "&nbsp;&nbsp;<input type='image' class='calendrier'  src=\"".$CFG_GLPI["root_doc"]."/pics/puce.gif\" alt='' title='' name='fillright' value='fillright'>&nbsp;";

		if ($name != "netpoint"){
			if (!empty($ID)){
				$value=getDropdownName("glpi_dropdown_".$name,$ID,1);
			}
			else $value=array("name"=>"","comments"=>"");
		} else {$value="";$loc="";}

		if($name == "netpoint") {
			$query = "select * from glpi_dropdown_netpoint where ID = '". $ID ."'";
			$result = $DB->query($query);
			$value=$loc=$comments="";
			if($DB->numrows($result) == 1) {
				$value = $DB->result($result,0,"name");
				$loc = $DB->result($result,0,"location");
				$comments = $DB->result($result,0,"comments");
			}
			echo "<br>";
			echo $LANG["common"][15].": ";		

			dropdownValue("glpi_dropdown_locations", "value2",$loc,0);
			echo $LANG["networking"][52].": ";
			echo "<input type='text' maxlength='100' size='10' name='value' value=\"".$value."\"><br>";
			echo "<textarea rows='2' cols='50' name='comments' title='".$LANG["common"][25]."' >".$comments."</textarea>";

		} 
		else {

			echo "<input type='text' maxlength='100' size='20' name='value' value=\"".$value["name"]."\"><br>";
			echo "<textarea rows='2' cols='50' name='comments' title='".$LANG["common"][25]."' >".$value["comments"]."</textarea>";
		}
		//
		echo "</td><td align='center' class='tab_bg_2' width='99'>";
		echo "<input type='hidden' name='tablename' value='glpi_dropdown_".$name."'>";
		//  on ajoute un bouton modifier
		echo "<input type='submit' name='update' value='".$LANG["buttons"][14]."' class='submit'>";
		echo "</td><td align='center' class='tab_bg_2' width='99'>";
		//
		echo "<input type='submit' name='delete' value=\"".$LANG["buttons"][6]."\" class='submit'>";
		echo "</td></tr>";

	}
	echo "</table></form>";
	echo "<form action=\"$target\" method='post'>";
	echo "<input type='hidden' name='which' value='$name'>";
	echo "<table class='tab_cadre_fixe' cellpadding='1'>";
	echo "<tr><td align='center'  class='tab_bg_1'>";
	if($name == "netpoint") {
		echo $LANG["common"][15].": ";		
		dropdownValue("glpi_dropdown_locations", "value2",$value2,0);
		echo $LANG["networking"][52].": ";
		echo "<input type='text' maxlength='100' size='10' name='value'><br>";
		echo "<textarea rows='2' cols='50' name='comments' title='".$LANG["common"][25]."'></textarea>";
	}
	else {
		echo "<input type='text' maxlength='100' size='20' name='value'><br>";
		echo "<textarea rows='2' cols='50' name='comments' title='".$LANG["common"][25]."'></textarea>";
	}
	echo "</td><td align='center' colspan='2' class='tab_bg_2' width='202'>";
	echo "<input type='hidden' name='tablename' value='glpi_dropdown_".$name."' >";
	echo "<input type='submit' name='add' value=\"".$LANG["buttons"][8]."\" class='submit'>";
	echo "</td></tr>";

	// Multiple Add for Netpoint
	if($name == "netpoint") {
		echo "</table></form>";

		echo "<form action=\"$target\" method='post'>";
		echo "<input type='hidden' name='which' value='$name'>";
		echo "<table class='tab_cadre_fixe' cellpadding='1'>";
		echo "<tr><td align='center'  class='tab_bg_1'>";

		echo $LANG["common"][15].": ";		
		dropdownValue("glpi_dropdown_locations", "value2",$value2,0);
		echo $LANG["networking"][52].": ";
		echo "<input type='text' maxlength='100' size='5' name='before'>";
		echo "<select name='from'>";
		for ($i=0;$i<400;$i++) echo "<option value='$i'>$i</option>";
		echo "</select>";
		echo "-->";
		echo "<select name='to'>";
		for ($i=0;$i<400;$i++) echo "<option value='$i'>$i</option>";
		echo "</select>";

		echo "<input type='text' maxlength='100' size='5' name='after'><br>";	
		echo "<textarea rows='2' cols='50' name='comments' title='".$LANG["common"][25]."'></textarea>";
		echo "</td><td align='center' colspan='2' class='tab_bg_2' width='202'>";
		echo "<input type='hidden' name='tablename' value='glpi_dropdown_".$name."' >";
		echo "<input type='submit' name='several_add' value=\"".$LANG["buttons"][8]."\" class='submit'>";
		echo "</td></tr>";
	}

	echo "</table></form></div>";
}

function showFormTypeDown ($target,$name,$human,$ID) {

	global $CFG_GLPI, $LANG;

	if (!haveRight("dropdown","w")) return false;	

	echo "<div align='center'>&nbsp;";

	echo "<form action=\"$target\" method='post'>";

	echo "<table class='tab_cadre_fixe'>";
	echo "<tr><th colspan='3'>$human:</th></tr>";

	if (countElementsInTable("glpi_type_".$name)>0){
		echo "<tr><td align='center' valign='middle' class='tab_bg_1'>";

		dropdownValue("glpi_type_".$name, "ID",$ID,0);
		// on ajoute un input text pour entrer la valeur modifier
		echo "&nbsp;&nbsp;<input type='image' class='calendrier' src=\"".$CFG_GLPI["root_doc"]."/pics/puce.gif\" alt='' title='' name='fillright' value='fillright'>&nbsp;";

		if (!empty($ID))
			$value=getDropdownName("glpi_type_".$name,$ID,1);
		else $value=array("name"=>"","comments"=>"");

		echo "<input type='text' maxlength='100' size='20' name='value'  value=\"".$value["name"]."\"><br>";
		echo "<textarea rows='2' cols='50' name='comments' title='".$LANG["common"][25]."'>".$value["comments"]."</textarea>";

		echo "</td><td align='center' class='tab_bg_2'>";
		echo "<input type='hidden' name='tablename' value='glpi_type_".$name."'>";
		echo "<input type='hidden' name='which' value='$name'>";

		//  on ajoute un bouton modifier
		echo "<input type='submit' name='update' value='".$LANG["buttons"][14]."' class='submit'>";
		echo "</td><td align='center' class='tab_bg_2'>";
		echo "<input type='submit' name='delete' value=\"".$LANG["buttons"][6]."\" class='submit'>";
		echo "</td></tr>";
	}
	echo "</table></form>";

	echo "<form action=\"$target\" method='post'>";
	echo "<table class='tab_cadre_fixe'>";
	echo "<tr><td align='center' class='tab_bg_1'>";
	echo "<input type='text' maxlength='100' size='20' name='value'><br>";
	echo "<textarea rows='2' cols='50' name='comments' title='".$LANG["common"][25]."'></textarea>";

	echo "</td><td align='center' colspan='2' class='tab_bg_2'>";
	echo "<input type='hidden' name='tablename' value='glpi_type_".$name."'>";
	echo "<input type='hidden' name='which' value='$name'>";
	echo "<input type='submit' name='add' value=\"".$LANG["buttons"][8]."\" class='submit'>";
	echo "</td></tr>";
	echo "</table></form></div>";
}
function moveTreeUnder($table,$to_move,$where){
	global $DB;
	if ($where!=$to_move){
		// Is the $where location under the to move ???
		$impossible_move=false;

		$current_ID=$where;
		while ($current_ID!=0&&$impossible_move==false){

			$query="select * from $table WHERE ID='$current_ID'";
			$result = $DB->query($query);
			$current_ID=$DB->result($result,0,"parentID");
			if ($current_ID==$to_move) $impossible_move=true;

		}
		if (!$impossible_move){

			// Move Location
			$query = "UPDATE $table SET parentID='$where' where ID='$to_move'";
			$result = $DB->query($query);
			regenerateTreeCompleteNameUnderID($table,$to_move);
		}	

	}	
}

function updateDropdown($input) {
	global $DB,$CFG_GLPI;


	if($input["tablename"] == "glpi_dropdown_netpoint") {
		$query = "update ".$input["tablename"]." SET name = '".$input["value"]."', location = '".$input["value2"]."', comments='".$input["comments"]."' where ID = '".$input["ID"]."'";

	}
	else {
		$query = "update ".$input["tablename"]." SET name = '".$input["value"]."', comments='".$input["comments"]."' where ID = '".$input["ID"]."'";
	}

	if ($result=$DB->query($query)) {
		if (in_array($input["tablename"],$CFG_GLPI["dropdowntree_tables"])){
			regenerateTreeCompleteNameUnderID($input["tablename"],$input["ID"]);
		}
		cleanRelationCache($input["tablename"]);
		return true;
	} else {
		return false;
	}
}


function addDropdown($input) {
	global $DB,$CFG_GLPI;

	if (!empty($input["value"])){

		if($input["tablename"] == "glpi_dropdown_netpoint") {
			$query = "INSERT INTO ".$input["tablename"]." (name,location,comments) VALUES ('".$input["value"]."', '".$input["value2"]."', '".$input["comments"]."')";
		}
		else if (in_array($input["tablename"],$CFG_GLPI["dropdowntree_tables"])){
			if ($input['type']=="first"){
				$query = "INSERT INTO ".$input["tablename"]." (name,parentID,completename,comments) VALUES ('".$input["value"]."', '0','','".$input["comments"]."')";		
			} else {
				$query="SELECT * from ".$input["tablename"]." where ID='".$input["value2"]."'";
				$result=$DB->query($query);
				if ($DB->numrows($result)>0){
					$data=$DB->fetch_array($result);
					$level_up=$data["parentID"];
					if ($input["type"]=="under") {
						$level_up=$data["ID"];
					} 
					$query = "INSERT INTO ".$input["tablename"]." (name,parentID,completename,comments) VALUES ('".$input["value"]."', '$level_up','','".$input["comments"]."')";		
				} else $query = "INSERT INTO ".$input["tablename"]." (name,parentID,completename,comments) VALUES ('".$input["value"]."', '0','','".$input["comments"]."')";				
			}
		}
		else {
			$query = "INSERT INTO ".$input["tablename"]." (name,comments) VALUES ('".$input["value"]."','".$input["comments"]."')";
		}

		if ($result=$DB->query($query)) {
			$ID=$DB->insert_id();
			if (in_array($input["tablename"],$CFG_GLPI["dropdowntree_tables"])){
				regenerateTreeCompleteNameUnderID($input["tablename"],$ID);		
			}
			cleanRelationCache($input["tablename"]);
			return $ID;
		} else {
			return false;
		}
	}
}

function deleteDropdown($input) {

	global $DB;
	$send = array();
	$send["tablename"] = $input["tablename"];
	$send["oldID"] = $input["ID"];
	$send["newID"] = "NULL";
	replaceDropDropDown($send);
	cleanRelationCache($input["tablename"]);
}

//replace all entries for a dropdown in each items
function replaceDropDropDown($input) {
	global $DB;
	$name = getDropdownNameFromTable($input["tablename"]);
	$RELATION=getDbRelations();

	if (isset($RELATION[$input["tablename"]]))
	foreach ($RELATION[$input["tablename"]] as $table => $field){
		$query = "update $table set $field = '". $input["newID"] ."'  where $field = '".$input["oldID"]."'";
		$DB->query($query);
	}

	$query = "delete from ". $input["tablename"] ." where ID = '". $input["oldID"] ."'";
	$DB->query($query);
}

function showDeleteConfirmForm($target,$table, $ID) {
	global $DB,$LANG;

	if (!haveRight("dropdown","w")) return false;

	if ($table=="glpi_dropdown_locations"){

		$query = "Select count(*) as cpt FROM $table where parentID = '".$ID."'";
		$result = $DB->query($query);
		if($DB->result($result,0,"cpt") > 0)  {
			echo "<div align='center'><p style='color:red'>".$LANG["setup"][74]."</p></div>";
			return;
		}
	}	

	if ($table=="glpi_dropdown_kbcategories"){
		$query = "Select count(*) as cpt FROM $table where parentID = '".$ID."'";
		$result = $DB->query($query);
		if($DB->result($result,0,"cpt") > 0)  {	
			echo "<div align='center'><p style='color:red'>".$LANG["setup"][74]."</p></div>";
			return;
		} else {
			$query = "Select count(*) as cpt FROM glpi_kbitems where categoryID = '".$ID."'";
			$result = $DB->query($query);
			if($DB->result($result,0,"cpt") > 0)  {
				echo "<div align='center'><p style='color:red'>".$LANG["setup"][74]."</p></div>";
				return;
			}
		}
	}

	echo "<div align='center'>";
	echo "<p style='color:red'>".$LANG["setup"][63]."</p>";
	echo "<p>".$LANG["setup"][64]."</p>";

	echo "<form action=\"". $target ."\" method=\"post\">";
	echo "<input type=\"hidden\" name=\"tablename\" value=\"". $table ."\"  />";
	echo "<input type=\"hidden\" name=\"ID\" value=\"". $ID ."\"  />";
	echo "<input type=\"hidden\" name=\"which\" value=\"". str_replace("glpi_type_","",str_replace("glpi_dropdown_","",$table)) ."\"  />";
	echo "<input type=\"hidden\" name=\"forcedelete\" value=\"1\" />";

	echo "<table class='tab_cadre'><tr><td>";
	echo "<input class='button' type=\"submit\" name=\"delete\" value=\"".$LANG["buttons"][2]."\" /></td>";

	echo "<form action=\" ". $target ."\" method=\"post\">";
	echo "<td><input class='button' type=\"submit\" name=\"annuler\" value=\"".$LANG["buttons"][34]."\" /></td></tr></table>";
	echo "</form>";
	echo "<p>". $LANG["setup"][65]."</p>";
	echo "<form action=\" ". $target ."\" method=\"post\">";
	echo "<input type=\"hidden\" name=\"which\" value=\"". str_replace("glpi_type_","",str_replace("glpi_dropdown_","",$table)) ."\"  />";
	echo "<table class='tab_cadre'><tr><td>";
	dropdownNoValue($table,"newID",$ID);
	echo "<input type=\"hidden\" name=\"tablename\" value=\"". $table ."\"  />";
	echo "<input type=\"hidden\" name=\"oldID\" value=\"". $ID ."\"  />";
	echo "</td><td><input class='button' type=\"submit\" name=\"replace\" value=\"".$LANG["buttons"][39]."\" /></td></tr></table>";
	echo "</form>";

	echo "</div>";
}


function getDropdownNameFromTable($table) {

	if(ereg("glpi_type_",$table)){
		$name = ereg_replace("glpi_type_","",$table);
	}
	else {
		if($table == "glpi_dropdown_locations") $name = "location";
		else {
			$name = ereg_replace("glpi_dropdown_","",$table);
		}
	}
	return $name;
}

function getDropdownNameFromTableForStats($table) {

	if(ereg("glpi_type_",$table)){
		$name = "type";
	}
	else {
		if($table == "glpi_dropdown_locations") $name = "location";
		else {
			$name = ereg_replace("glpi_dropdown_","",$table);
		}
	}
	return $name;
}


//check if the dropdown $ID is used into item tables
function dropdownUsed($table, $ID) {

	global $DB;
	$name = getDropdownNameFromTable($table);

	$var1 = true;

	$RELATION=getDbRelations();
	if (isset($RELATION[$table]))
	foreach ($RELATION[$table] as $tablename => $field){
		$query = "Select count(*) as cpt FROM $tablename where $field = '".$ID."'";
		$result = $DB->query($query);
		if($DB->result($result,0,"cpt") > 0)  $var1 = false;
	}

	return $var1;

}



function listTemplates($type,$target,$add=0) {

	global $DB,$CFG_GLPI, $LANG;

	if (!haveTypeRight($type,"w")) return false;

	switch ($type){
		case COMPUTER_TYPE :
			$title=$LANG["Menu"][0];
			$query = "SELECT * FROM glpi_computers where is_template = '1' ORDER by tplname";
			break;
		case NETWORKING_TYPE :
			$title=$LANG["Menu"][1];
			$query = "SELECT * FROM glpi_networking where is_template = '1' ORDER by tplname";
			break;
		case MONITOR_TYPE :
			$title=$LANG["Menu"][3];
			$query = "SELECT * FROM glpi_monitors where is_template = '1' ORDER by tplname";
			break;	
		case PRINTER_TYPE :
			$title=$LANG["Menu"][2];
			$query = "SELECT * FROM glpi_printers where is_template = '1' ORDER by tplname";
			break;	
		case PERIPHERAL_TYPE :
			$title=$LANG["Menu"][16];
			$query = "SELECT * FROM glpi_peripherals where is_template = '1' ORDER by tplname";
			break;
		case SOFTWARE_TYPE :
			$title=$LANG["Menu"][4];
			$query = "SELECT * FROM glpi_software where is_template = '1' ORDER by tplname";
			break;
		case PHONE_TYPE :
			$title=$LANG["Menu"][34];
			$query = "SELECT * FROM glpi_phones where is_template = '1' ORDER by tplname";
			break;

	}
	if ($result = $DB->query($query)) {

		echo "<div align='center'><table class='tab_cadre' width='50%'>";
		if ($add)
			echo "<tr><th>".$LANG["common"][7]." - $title:</th></tr>";
		else 
			echo "<tr><th colspan='2'>".$LANG["common"][14]." - $title:</th></tr>";

		while ($data= $DB->fetch_array($result)) {

			$templname = $data["tplname"];
			if ($templname=="Blank Template")
				$templname=$LANG["common"][31];

			echo "<tr>";
			echo "<td align='center' class='tab_bg_1'>";
			if (!$add){
				echo "<a href=\"$target?ID=".$data["ID"]."&amp;withtemplate=1\">&nbsp;&nbsp;&nbsp;$templname&nbsp;&nbsp;&nbsp;</a></td>";

				echo "<td align='center' class='tab_bg_2'>";
				if ($data["tplname"]!="Blank Template")
					echo "<b><a href=\"$target?ID=".$data["ID"]."&amp;purge=purge&amp;withtemplate=1\">".$LANG["buttons"][6]."</a></b>";
				else echo "&nbsp;";
				echo "</td>";
			} else {
				echo "<a href=\"$target?ID=".$data["ID"]."&amp;withtemplate=2\">&nbsp;&nbsp;&nbsp;$templname&nbsp;&nbsp;&nbsp;</a></td>";
			}

			echo "</tr>";		

		}

		if (!$add){
			echo "<tr>";
			echo "<td colspan='2' align='center' class='tab_bg_2'>";
			echo "<b><a href=\"$target?withtemplate=1\">".$LANG["common"][9]."</a></b>";
			echo "</td>";
			echo "</tr>";
		}

		echo "</table></div>";
	}


}





function titleConfigGen(){

	global  $LANG,$CFG_GLPI;

	echo "<div align='center'><table border='0'><tr><td>";
	echo "<img src=\"".$CFG_GLPI["root_doc"]."/pics/configuration.png\" alt='' title=''></td><td><b><span class='icon_sous_nav'>".$LANG["setup"][70]."</span>";
	echo "</b></td></tr></table>&nbsp;</div>";


}

function titleConfigDisplay(){

	global  $LANG,$CFG_GLPI;

	echo "<div align='center'><table border='0'><tr><td>";
	echo "<img src=\"".$CFG_GLPI["root_doc"]."/pics/configuration.png\" alt='' title=''></td><td><b><span class='icon_sous_nav'>".$LANG["setup"][119]."</span>";
	echo "</b></td></tr></table>&nbsp;</div>";


}

function showFormConfigGen($target){

	global  $DB,$LANG,$CFG_GLPI;

	if (!haveRight("config","w")) return false;	

	echo "<form name='form' action=\"$target\" method=\"post\">";
	echo "<input type='hidden' name='ID' value='".$CFG_GLPI["ID"]."'>";
	echo "<div align='center'><table class='tab_cadre_fixe'>";
	echo "<tr><th colspan='4'>".$LANG["setup"][70]."</th></tr>";

	$default_language=$CFG_GLPI["default_language"];
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][113]." </td><td><select name=\"default_language\">";
	foreach ($CFG_GLPI["languages"] as $key => $val){
		echo "<option value=\"".$key."\"";
		if($default_language==$key){ echo " selected";}
		echo ">".$val[0]. " (".$key.")";
	}

	echo "</select></td>";

	echo "<td align='center'> ".$LANG["setup"][133]." </td><td>";
	dropdownYesNoInt("ocs_mode",$CFG_GLPI["ocs_mode"]);
	echo "</td></tr>";


	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][102]." </td><td><select name=\"event_loglevel\">";
	$level=$CFG_GLPI["event_loglevel"];
	echo "<option value=\"1\"";  if($level==1){ echo " selected";} echo ">".$LANG["setup"][103]." </option>";
	echo "<option value=\"2\"";  if($level==2){ echo " selected";} echo ">".$LANG["setup"][104]."</option>";
	echo "<option value=\"3\"";  if($level==3){ echo " selected";} echo ">".$LANG["setup"][105]."</option>";
	echo "<option value=\"4\"";  if($level==4){ echo " selected";} echo ">".$LANG["setup"][106]." </option>";
	echo "<option value=\"5\"";  if($level==5){ echo " selected";} echo ">".$LANG["setup"][107]."</option>";
	echo "</select></td>";

	echo "<td align='center'>".$LANG["setup"][109]." </td><td><input type=\"text\" name=\"expire_events\" value=\"". $CFG_GLPI["expire_events"] ."\"></td></tr>";


	echo "<tr class='tab_bg_2'><td align='center'> ".$LANG["setup"][124]." </td><td>";
	dropdownYesNoInt("auto_add_users",$CFG_GLPI["auto_add_users"]);
	echo "</td>";

	echo "<td align='center'>".$LANG["setup"][138]." </td><td><select name=\"debug\">";
	$check=$CFG_GLPI["debug"];
	echo "<option value=\"".NORMAL_MODE."\" ".($CFG_GLPI["debug"]==NORMAL_MODE?" selected ":"")." >".$LANG["setup"][135]." </option>";
	echo "<option value=\"".TRANSLATION_MODE."\" ".($CFG_GLPI["debug"]==TRANSLATION_MODE?" selected ":"")." >".$LANG["setup"][136]." </option>";
	echo "<option value=\"".DEBUG_MODE."\" ".($CFG_GLPI["debug"]==DEBUG_MODE?" selected ":"")." >".$LANG["setup"][137]." </option>";
	echo "<option value=\"".DEMO_MODE."\" ".($CFG_GLPI["debug"]==DEMO_MODE?" selected ":"")." >".$LANG["setup"][141]." </option>";
	echo "</select></td></tr>";


	echo "<tr class='tab_bg_1'><td colspan='4' align='center'><strong>".$LANG["setup"][10]."</strong></td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][115]."</td><td><select name='cartridges_alarm'>";
	for ($i=-1;$i<=100;$i++)
		echo "<option value='$i' ".($i==$CFG_GLPI["cartridges_alarm"]?" selected ":"").">$i</option>";
	echo "</select></td>";

	echo "<td align='center'>".$LANG["setup"][221]."</td><td>";
	showCalendarForm("form","date_fiscale",$CFG_GLPI["date_fiscale"],0);	
	echo "</td></tr>";


	echo "<tr class='tab_bg_1'><td colspan='4' align='center'><strong>".$LANG["title"][24]."</strong></td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][219]."</td><td>";
	dropdownYesNoInt("permit_helpdesk",$CFG_GLPI["permit_helpdesk"]);
	echo "</td>";

	echo "<td align='center'> ".$LANG["setup"][116]." </td><td>";
	dropdownYesNoInt("auto_assign",$CFG_GLPI["auto_assign"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][405]."</td><td>";
	dropdownYesNoInt("followup_on_update_ticket",$CFG_GLPI["followup_on_update_ticket"]);
	echo "</td><td align='center'>".$LANG["tracking"][37]."</td><td>";
	dropdownYesNoInt("keep_tracking_on_delete",$CFG_GLPI["keep_tracking_on_delete"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_1'><td colspan='4' align='center'><strong>".$LANG["common"][41]."</strong></td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][246]." (".$LANG["common"][44].")</td><td>";
	dropdownContractAlerting("contract_alerts",$CFG_GLPI["contract_alerts"]);
	echo "</td>";

	echo "<td align='center'>".$LANG["setup"][247]." (".$LANG["common"][44].")</td><td>";
	echo "<select name=\"infocom_alerts\">";
	echo "<option value=\"0\" ".($CFG_GLPI["infocom_alerts"]==0?" selected ":"")." >-----</option>";
	echo "<option value=\"".pow(2,ALERT_END)."\" ".($CFG_GLPI["infocom_alerts"]==pow(2,ALERT_END)?" selected ":"")." >".$LANG["financial"][80]." </option>";
	echo "</select>";
	echo "</td></tr>";


	echo "<tr class='tab_bg_1'><td colspan='4' align='center'><strong>".$LANG["setup"][306]."</strong></td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][306]." </td><td><select name=\"auto_update_check\">";
	$check=$CFG_GLPI["auto_update_check"];
	echo "<option value=\"0\" ".($check==0?" selected":"").">".$LANG["setup"][307]." </option>";
	echo "<option value=\"7\" ".($check==7?" selected":"").">".$LANG["setup"][308]." </option>";
	echo "<option value=\"30\" ".($check==30?" selected":"").">".$LANG["setup"][309]." </option>";
	echo "</select></td><td colspan='2'>&nbsp;</td></tr>";



	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][401]." </td><td><input type=\"text\" name=\"proxy_name\" value=\"". $CFG_GLPI["proxy_name"] ."\"></td>";
	echo "<td align='center'>".$LANG["setup"][402]." </td><td><input type=\"text\" name=\"proxy_port\" value=\"". $CFG_GLPI["proxy_port"] ."\"></td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][403]." </td><td><input type=\"text\" name=\"proxy_user\" value=\"". $CFG_GLPI["proxy_user"] ."\"></td>";
	echo "<td align='center'>".$LANG["setup"][404]." </td><td><input type=\"text\" name=\"proxy_password\" value=\"". $CFG_GLPI["proxy_password"] ."\"></td></tr>";

	echo "<tr class='tab_bg_2'><td colspan='4' align='center'><input type=\"submit\" name=\"update_confgen\" class=\"submit\" value=\"".$LANG["buttons"][2]."\" ></td></tr>";

	echo "</table></div>";	


	echo "</form>";
}

function showFormConfigDisplay($target){

	global $DB, $LANG,$CFG_GLPI;

	if (!haveRight("config","w")) return false;	

	// Needed for list_limit
	$cfg=new Config();
	$cfg->getFromDB(1);
	echo "<form name='form' action=\"$target\" method=\"post\">";
	echo "<input type='hidden' name='ID' value='".$CFG_GLPI["ID"]."'>";
	echo "<div align='center'><table class='tab_cadre'>";
	echo "<tr><th colspan='4'>".$LANG["setup"][70]."</th></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][108]."</td><td> <input type=\"text\" name=\"num_of_events\" value=\"". $CFG_GLPI["num_of_events"] ."\"></td>";
	echo "<td align='center'>".$LANG["setup"][111]."</td><td> <input type=\"text\" name=\"list_limit\" value=\"". $cfg->fields["list_limit"] ."\"></td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][112]."</td><td><input type=\"text\" name=\"cut\" value=\"". $CFG_GLPI["cut"] ."\"></td>";

	$dp_limit=$CFG_GLPI["dropdown_limit"];
	echo "<td align='center'>".$LANG["setup"][131]."</td><td>";
	echo "<select name='dropdown_limit'>";
	for ($i=20;$i<=100;$i++) echo "<option value='$i'".($dp_limit==$i?" selected ":"").">$i</option>";
	echo "</select>";	

	echo "</td></tr>";


	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][128]." </td><td><select name=\"dateformat\">";
	echo "<option value=\"0\"";  if($CFG_GLPI["dateformat"]==0){ echo " selected";} echo ">YYYY-MM-DD</option>";
	echo "<option value=\"1\"";  if($CFG_GLPI["dateformat"]==1){ echo " selected";} echo ">DD-MM-YYYY</option>";
	echo "</select></td>";

	echo "<td align='center'> ".$LANG["setup"][117]." </td><td>";
	dropdownYesNoInt("public_faq",$CFG_GLPI["public_faq"]);
	echo " </td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][129]." </td><td>";
	dropdownYesNoInt("view_ID",$CFG_GLPI["view_ID"]);
	echo "</td>";

	echo "<td align='center'>".$LANG["setup"][130]." </td><td><select name=\"nextprev_item\">";
	$nextprev_item=$CFG_GLPI["nextprev_item"];
	echo "<option value=\"ID\"";  if($nextprev_item=="ID"){ echo " selected";} echo ">".$LANG["common"][2]." </option>";
	echo "<option value=\"name\"";  if($nextprev_item=="name"){ echo " selected";} echo ">".$LANG["common"][16]."</option>";
	echo "</select></td></tr>";

	$plan_begin=split(":",$CFG_GLPI["planning_begin"]);
	$plan_end=split(":",$CFG_GLPI["planning_end"]);
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][223]."</td><td>";
	echo "<select name='planning_begin'>";
	for ($i=0;$i<=24;$i++) echo "<option value='$i'".($plan_begin[0]==$i?" selected ":"").">$i</option>";
	echo "</select>";
	echo "&nbsp;->&nbsp;";
	echo "<select name='planning_end'>";
	for ($i=0;$i<=24;$i++) echo "<option value='$i' ".($plan_end[0]==$i?" selected ":"").">$i</option>";
	echo "</select>";


	echo "</td><td align='center'>".$LANG["setup"][148]."</td><td>";
	echo "<select name='time_step'>";
	$steps=array(5,10,15,20,30,60);
	foreach ($steps as $step){
		echo "<option value='$step'".($CFG_GLPI["time_step"]==$step?" selected ":"").">$step</option>";
	}
	echo "</select>&nbsp;".$LANG["job"][22];
	echo "</td></tr>";


	echo "<tr class='tab_bg_2'><td align='center'> ".$LANG["setup"][118]." </td><td colspan='3' align='center'>";
	echo "<textarea cols='70' rows='4' name='text_login' >";
	echo $CFG_GLPI["text_login"];
	echo "</textarea>";
	echo "</td></tr>";



	echo "<tr class='tab_bg_1'><td colspan='4' align='center'><strong>".$LANG["title"][24]."</strong></td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'> ".$LANG["setup"][110]." </td><td>";
	dropdownYesNoInt("jobs_at_login",$CFG_GLPI["jobs_at_login"]);
	echo " </td><td colspan='2'>&nbsp;</td></tr>";


	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][114]."</td><td colspan='3'>";
	echo "<table><tr>";
	echo "<td bgcolor='".$CFG_GLPI["priority_1"]."'>1:<input type=\"text\" name=\"priority_1\" size='7' value=\"".$CFG_GLPI["priority_1"]."\"></td>";
	echo "<td bgcolor='".$CFG_GLPI["priority_2"]."'>2:<input type=\"text\" name=\"priority_2\" size='7' value=\"".$CFG_GLPI["priority_2"]."\"></td>";
	echo "<td bgcolor='".$CFG_GLPI["priority_3"]."'>3:<input type=\"text\" name=\"priority_3\" size='7' value=\"".$CFG_GLPI["priority_3"]."\"></td>";
	echo "<td bgcolor='".$CFG_GLPI["priority_4"]."'>4:<input type=\"text\" name=\"priority_4\" size='7' value=\"".$CFG_GLPI["priority_4"]."\"></td>";
	echo "<td bgcolor='".$CFG_GLPI["priority_5"]."'>5:<input type=\"text\" name=\"priority_5\" size='7' value=\"".$CFG_GLPI["priority_5"]."\"></td>";
	echo "</tr></table>";
	echo "</td></tr>";

	echo "<tr class='tab_bg_1'><td colspan='4' align='center'><strong>".$LANG["setup"][147]."</strong></td></tr>";	


	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][120]." </td><td>";
	dropdownYesNoInt("use_ajax",$CFG_GLPI["use_ajax"]);
	echo "</td>";

	echo "<td align='center'>".$LANG["setup"][127]." </td><td>";
	dropdownYesNoInt("ajax_autocompletion",$CFG_GLPI["ajax_autocompletion"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][121]."</td><td><input type=\"text\" size='1' name=\"ajax_wildcard\" value=\"". $CFG_GLPI["ajax_wildcard"] ."\"></td>";

	echo "<td align='center'>".$LANG["setup"][122]."</td><td>";
	echo "<select name='dropdown_max'>";
	$dropdown_max=$CFG_GLPI["dropdown_max"];
	for ($i=0;$i<=200;$i++) echo "<option value='$i'".($dropdown_max==$i?" selected ":"").">$i</option>";
	echo "</select>";
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][123]."</td><td>";
	echo "<select name='ajax_limit_count'>";
	$ajax_limit_count=$CFG_GLPI["ajax_limit_count"];
	for ($i=0;$i<=200;$i++) echo "<option value='$i'".($ajax_limit_count==$i?" selected ":"").">$i</option>";
	echo "</select>";
	echo "</td><td colspan='2'>&nbsp;</td></tr>";



	echo "</table>&nbsp;</div>";	
	echo "<p class=\"submit\"><input type=\"submit\" name=\"update_confdisplay\" class=\"submit\" value=\"".$LANG["buttons"][2]."\" ></p>";


	echo "</form>";
}




function titleExtAuth(){
	// Un titre pour la gestion des sources externes

	global  $LANG,$CFG_GLPI;
	echo "<div align='center'><table border='0'><tr><td>";
	echo "<img src=\"".$CFG_GLPI["root_doc"]."/pics/authentification.png\" alt='' title=''></td><td><span class='icon_sous_nav'>".$LANG["setup"][150]."</span>";
	echo "</td></tr></table>&nbsp;</div>";

}





function showMailServerConfig($value){
	global $LANG;

	if (!haveRight("config","w")) return false;	

	if (ereg(":",$value)){
		$addr=ereg_replace("{","",preg_replace("/:.*/","",$value));
		$port=preg_replace("/.*:/","",preg_replace("/\/.*/","",$value));
	}
	else {
		if (ereg("/",$value))
			$addr=ereg_replace("{","",preg_replace("/\/.*/","",$value));
		else $addr=ereg_replace("{","",preg_replace("/}.*/","",$value));
		$port="";
	}
	$mailbox=preg_replace("/.*}/","",$value);

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][163]."</td><td><input size='30' type=\"text\" name=\"mail_server\" value=\"". $addr."\" ></td></tr>";	
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][168]."</td><td>";
	echo "<select name='server_type'>";
	echo "<option value=''>&nbsp;</option>";
	echo "<option value='/imap' ".(ereg("/imap",$value)?" selected ":"").">IMAP</option>";
	echo "<option value='/pop' ".(ereg("/pop",$value)?" selected ":"").">POP</option>";
	echo "</select>";
	echo "<select name='server_ssl'>";
	echo "<option value=''>&nbsp;</option>";
	echo "<option value='/ssl' ".(ereg("/ssl",$value)?" selected ":"").">SSL</option>";
	echo "</select>";
	echo "<select name='server_cert'>";
	echo "<option value=''>&nbsp;</option>";
	echo "<option value='/novalidate-cert' ".(ereg("/novalidate-cert",$value)?" selected ":"").">NO-VALIDATE-CERT</option>";
	echo "<option value='/validate-cert' ".(ereg("/validate-cert",$value)?" selected ":"").">VALIDATE-CERT</option>";
	echo "</select>";
	echo "<select name='server_tls'>";
	echo "<option value=''>&nbsp;</option>";
	echo "<option value='/tls' ".(ereg("/tls",$value)?" selected ":"").">TLS</option>";
	echo "<option value='/notls' ".(ereg("/notls",$value)?" selected ":"").">NO-TLS</option>";
	echo "</select>";

	echo "</td></tr>";	

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][169]."</td><td><input size='30' type=\"text\" name=\"server_mailbox\" value=\"". $mailbox."\" ></td></tr>";	
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][171]."</td><td><input size='10' type=\"text\" name=\"server_port\" value=\"". $port."\" ></td></tr>";	
	if (empty($value)) $value="&nbsp;";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][170]."</td><td><b>$value</b></td></tr>";	

}	

function constructIMAPAuthServer($input){

	$out="";
	if (isset($input['mail_server'])&&!empty($input['mail_server'])) $out.="{".$input['mail_server'];
	else return $out;
	if (isset($input['server_port'])&&!empty($input['server_port'])) $out.=":".$input['server_port'];
	if (isset($input['server_type'])) $out.=$input['server_type'];
	if (isset($input['server_ssl'])) $out.=$input['server_ssl'];
	if (isset($input['server_cert'])) $out.=$input['server_cert'];
	if (isset($input['server_tls'])) $out.=$input['server_tls'];

	$out.="}";
	if (isset($input['server_mailbox'])) $out.=$input['server_mailbox'];

	return $out;

}

function showFormExtAuth($target) {

	global  $DB,$LANG,$CFG_GLPI;

	if (!haveRight("config","w")) return false;	

	echo "<form action=\"$target\" method=\"post\">";
	echo "<input type='hidden' name='ID' value='".$CFG_GLPI["ID"]."'>";
	if(function_exists('imap_open')) {

		echo "<div align='center'>";
		echo "<p >".$LANG["setup"][160]."</p>";
		//		echo "<p>".$LANG["setup"][161]."</p>";
		echo "<table class='tab_cadre_fixe'>";
		echo "<tr><th colspan='2'>".$LANG["setup"][162]."</th></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][164]."</td><td><input size='30' type=\"text\" name=\"imap_host\" value=\"". $CFG_GLPI["imap_host"] ."\" ></td></tr>";

		showMailServerConfig($CFG_GLPI["imap_auth_server"]);
		echo "</table>&nbsp;</div>";
	}
	else {
		echo "<input type=\"hidden\" name=\"IMAP_Test\" value=\"1\" >";

		echo "<div align='center'>&nbsp;<table class='tab_cadre_fixe'>";
		echo "<tr><th colspan='2'>".$LANG["setup"][162]."</th></tr>";
		echo "<tr class='tab_bg_2'><td align='center'><p class='red'>".$LANG["setup"][165]."</p><p>".$LANG["setup"][166]."</p></td></tr></table></div>";
	}
	if(extension_loaded('ldap'))
	{
		echo "<div align='center'><p > ".$LANG["setup"][151]."</p>";

		echo "<table class='tab_cadre_fixe'>";
		echo "<tr><th colspan='4'>".$LANG["setup"][152]."</th></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][153]."</td><td><input type=\"text\" name=\"ldap_host\" value=\"". $CFG_GLPI["ldap_host"] ."\"></td>";
		echo "<td align='center'>".$LANG["setup"][172]."</td><td><input type=\"text\" name=\"ldap_port\" value=\"". $CFG_GLPI["ldap_port"] ."\"></td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][154]."</td><td><input type=\"text\" name=\"ldap_basedn\" value=\"". $CFG_GLPI["ldap_basedn"] ."\" ></td>";
		echo "<td align='center'>".$LANG["setup"][155]."</td><td><input type=\"text\" name=\"ldap_rootdn\" value=\"". $CFG_GLPI["ldap_rootdn"] ."\" ></td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][156]."</td><td><input type=\"password\" name=\"ldap_pass\" value=\"". $CFG_GLPI["ldap_pass"] ."\" ></td>";
		echo "<td align='center'>".$LANG["setup"][159]."</td><td><input type=\"text\" name=\"ldap_condition\" value=\"". $CFG_GLPI["ldap_condition"] ."\" ></td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][228]."</td><td><input type=\"text\" name=\"ldap_login\" value=\"". $CFG_GLPI["ldap_login"] ."\" ></td>";
		echo "<td align='center'>".$LANG["setup"][180]."</td><td>";
		if (function_exists("ldap_start_tls")){
			$ldap_use_tls=$CFG_GLPI["ldap_use_tls"];
			echo "<select name='ldap_use_tls'>\n";
			echo "<option value='0' ".(!$ldap_use_tls?" selected ":"").">".$LANG["choice"][0]."</option>\n";
			echo "<option value='1' ".($ldap_use_tls?" selected ":"").">".$LANG["choice"][1]."</option>\n";
			echo "</select>\n";	
		} else {
			echo "<input type='hidden' name='ldap_use_tls' value='0'>";
			echo $LANG["setup"][181];

		}
		echo "</td></tr>";

		echo "<tr class='tab_bg_1'><td align='center' colspan='4'>".$LANG["setup"][259]."</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][254]."</td><td>";
		$ldap_search_for_groups=$CFG_GLPI["ldap_search_for_groups"];

		echo "<select name='ldap_search_for_groups'>\n";
		echo "<option value='0' ".(($ldap_search_for_groups==0)?" selected ":"").">".$LANG["setup"][256]."</option>\n";
		echo "<option value='1' ".(($ldap_search_for_groups==1)?" selected ":"").">".$LANG["setup"][257]."</option>\n";
		echo "<option value='2' ".(($ldap_search_for_groups==2)?" selected ":"").">".$LANG["setup"][258]."</option>\n";
		echo "</select>\n";
		echo "</td>";
		echo "<td align='center'>".$LANG["setup"][260]."</td><td><input type=\"text\" name=\"ldap_field_group\" value=\"". $CFG_GLPI["ldap_field_group"] ."\" ></td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][253]."</td><td>";
		echo "<input type=\"text\" name=\"ldap_group_condition\" value=\"". $CFG_GLPI["ldap_group_condition"] ."\" ></td>";
		echo "<td align='center'>".$LANG["setup"][255]."</td><td><input type=\"text\" name=\"ldap_field_group_member\" value=\"". $CFG_GLPI["ldap_field_group_member"] ."\" ></td></tr>";


		echo "<tr class='tab_bg_1'><td align='center' colspan='4'>".$LANG["setup"][167]."</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][48]."</td><td><input type=\"text\" name=\"ldap_field_realname\" value=\"". $CFG_GLPI["ldap_field_realname"] ."\" ></td>";
		echo "<td align='center'>".$LANG["common"][43]."</td><td><input type=\"text\" name=\"ldap_field_firstname\" value=\"". $CFG_GLPI["ldap_field_firstname"] ."\" ></td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][15]."</td><td><input type=\"text\" name=\"ldap_field_location\" value=\"". $CFG_GLPI["ldap_field_location"] ."\" ></td>";
		echo "<td align='center'>".$LANG["setup"][14]."</td><td><input type=\"text\" name=\"ldap_field_email\" value=\"". $CFG_GLPI["ldap_field_email"] ."\" ></td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["financial"][29]."</td><td><input type=\"text\" name=\"ldap_field_phone\" value=\"". $CFG_GLPI["ldap_field_phone"] ."\" ></td>";
		echo "<td align='center'>".$LANG["financial"][29]." 2</td><td><input type=\"text\" name=\"ldap_field_phone2\" value=\"". $CFG_GLPI["ldap_field_phone2"] ."\" ></td></tr>";

		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][42]."</td><td><input type=\"text\" name=\"ldap_field_mobile\" value=\"". $CFG_GLPI["ldap_field_mobile"] ."\" ></td>";
		echo "<td align='center' colspan='2'>&nbsp;</td></tr>";

		echo "</table>&nbsp;</div>";
	}
	else {
		echo "<input type=\"hidden\" name=\"LDAP_Test\" value=\"1\" >";
		echo "<div align='center'><table class='tab_cadre_fixe'>";
		echo "<tr><th colspan='2'>".$LANG["setup"][152]."</th></tr>";
		echo "<tr class='tab_bg_2'><td align='center'><p class='red'>".$LANG["setup"][157]."</p><p>".$LANG["setup"][158]."</p></td></th></table></div>";
	}

	if(function_exists('curl_init')&&(version_compare(PHP_VERSION,'5','>=')||(function_exists("domxml_open_mem")&&function_exists("utf8_decode"))))
	{
		echo "<div align='center'><p > ".$LANG["setup"][173]."</p>";

		echo "<table class='tab_cadre'>";
		echo "<tr><th colspan='2'>".$LANG["setup"][177]."</th></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][174]."</td><td><input type=\"text\" name=\"cas_host\" value=\"". $CFG_GLPI["cas_host"] ."\"></td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][175]."</td><td><input type=\"text\" name=\"cas_port\" value=\"". $CFG_GLPI["cas_port"] ."\"></td></tr>";
		echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][176]."</td><td><input type=\"text\" name=\"cas_uri\" value=\"". $CFG_GLPI["cas_uri"] ."\" ></td></tr>";

		echo "</table>&nbsp;</div>";
	}
	else {
		echo "<input type=\"hidden\" name=\"CAS_Test\" value=\"1\" >";
		echo "<div align='center'><table class='tab_cadre_fixe'>";
		echo "<tr><th colspan='2'>".$LANG["setup"][177]."</th></tr>";
		echo "<tr class='tab_bg_2'><td align='center'><p class='red'>".$LANG["setup"][178]."</p><p>".$LANG["setup"][179]."</p></td></th></table></div>";
	}

	echo "<p class=\"submit\"><input type=\"submit\" name=\"update_ext\" class=\"submit\" value=\"".$LANG["buttons"][2]."\" ></p>";
	echo "</form>";
}


function titleMailing(){
	// Un titre pour la gestion du suivi par mail

	global  $LANG,$CFG_GLPI;
	echo "<div align='center'><table border='0'><tr><td>";
	echo "<img src=\"".$CFG_GLPI["root_doc"]."/pics/mail.png\" alt='' title=''></td><td><span class='icon_sous_nav'>".$LANG["setup"][200]."</span>";
	echo "</td></tr></table></div>";
}


function showFormMailing($target) {

	global $DB,$LANG,$CFG_GLPI;

	if (!haveRight("config","w")) return false;	

	echo "<form action=\"$target\" method=\"post\">";
	echo "<input type='hidden' name='ID' value='".$CFG_GLPI["ID"]."'>";

	echo "<div id='barre_onglets'><ul id='onglet'>";
	echo "<li "; if ($_SESSION['glpi_mailconfig']==1){ echo "class='actif'";} echo  "><a href='$target?next=mailing&amp;onglet=1'>".$LANG["Menu"][10]."</a></li>";
	echo "<li "; if ($_SESSION['glpi_mailconfig']==2){ echo "class='actif'";} echo  "><a href='$target?next=mailing&amp;onglet=2'>".$LANG["setup"][240]."</a></li>";
	echo "<li "; if ($_SESSION['glpi_mailconfig']==3){ echo "class='actif'";} echo  "><a href='$target?next=mailing&amp;onglet=3'>".$LANG["setup"][242]."</a></li>";
	echo "</ul></div>";

	if ($_SESSION['glpi_mailconfig']==1){
		echo "<div align='center'><table class='tab_cadre_fixe'><tr><th colspan='2'>".$LANG["setup"][201]."</th></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][202]."</td><td>";
		dropdownYesNoInt("mailing",$CFG_GLPI["mailing"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][203]."</td><td> <input type=\"text\" name=\"admin_email\" size='40' value=\"".$CFG_GLPI["admin_email"]."\"> </td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][204]."</td><td><input type=\"text\" name=\"mailing_signature\" size='40' value=\"".$CFG_GLPI["mailing_signature"]."\" ></td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][226]."</td><td>";
		dropdownYesNoInt("url_in_mail",$CFG_GLPI["url_in_mail"]);
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][227]."</td><td> <input type=\"text\" name=\"url_base\" size='40' value=\"".$CFG_GLPI["url_base"]."\"> </td></tr>";

		if (!function_exists('mail')) {
			echo "<tr class='tab_bg_2'><td align='center' colspan='2'><span class='red'>".$LANG["setup"][217]." : </span><span>".$LANG["setup"][218]."</span></td></tr>";
		}

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][231]."</td><td>&nbsp; ";

		if (!function_exists('mail')) { // if mail php disabled we forced SMTP usage 
			echo $LANG["choice"][1]."  &nbsp;<input type=\"radio\" name=\"smtp_mode\" value=\"1\" checked >";
		}else{
			dropdownYesNoInt("smtp_mode",$CFG_GLPI["smtp_mode"]);
		}
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][232]."</td><td> <input type=\"text\" name=\"smtp_host\" size='40' value=\"".$CFG_GLPI["smtp_host"]."\"> </td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][233]."</td><td> <input type=\"text\" name=\"smtp_port\" size='40' value=\"".$CFG_GLPI["smtp_port"]."\"> </td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][234]."</td><td> <input type=\"text\" name=\"smtp_username\" size='40' value=\"".$CFG_GLPI["smtp_username"]."\"> </td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][235]."</td><td> <input type=\"password\" name=\"smtp_password\" size='40' value=\"".$CFG_GLPI["smtp_password"]."\"> </td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][245]." ".$LANG["setup"][244]."</td><td>";
		echo "<select name='cartridges_alert'> ";
		echo "<option value='0' ".($CFG_GLPI["cartridges_alert"]==0?"selected":"")." >".$LANG["setup"][307]."</option>";
		echo "<option value='".WEEK_TIMESTAMP."' ".($CFG_GLPI["cartridges_alert"]==WEEK_TIMESTAMP?"selected":"")." >".$LANG["setup"][308]."</option>";
		echo "<option value='".MONTH_TIMESTAMP."' ".($CFG_GLPI["cartridges_alert"]==MONTH_TIMESTAMP?"selected":"")." >".$LANG["setup"][309]."</option>";
		echo "</select>";
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td >".$LANG["setup"][245]." ".$LANG["setup"][243]."</td><td>";
		echo "<select name='consumables_alert'> ";
		echo "<option value='0' ".($CFG_GLPI["consumables_alert"]==0?"selected":"")." >".$LANG["setup"][307]."</option>";
		echo "<option value='".WEEK_TIMESTAMP."' ".($CFG_GLPI["consumables_alert"]==WEEK_TIMESTAMP?"selected":"")." >".$LANG["setup"][308]."</option>";
		echo "<option value='".MONTH_TIMESTAMP."' ".($CFG_GLPI["consumables_alert"]==MONTH_TIMESTAMP?"selected":"")." >".$LANG["setup"][309]."</option>";
		echo "</select>";
		echo "</td></tr>";

		echo "<tr class='tab_bg_2'><td align='center' colspan='2'>";
		echo "<input type=\"submit\" name=\"update_mailing\" class=\"submit\" value=\"".$LANG["buttons"][2]."\" >";
		echo "</td></tr>";

		echo "</table>";
		echo "</div>";
		echo "</form>";
		echo "<form action=\"$target\" method=\"post\">";
		echo "<div align='center'><table class='tab_cadre_fixe'><tr><th colspan='2'>".$LANG["setup"][229]."</th></tr>";
		echo "<tr class='tab_bg_2'>";
		echo "<td align='center'>";
		echo "<input class=\"submit\" type=\"submit\" name=\"test_smtp_send\" value=\"".$LANG["buttons"][2]."\">";
		echo " </td></tr></table></div>";

	} else if ($_SESSION['glpi_mailconfig']==2)	{

		$profiles[USER_MAILING_TYPE."_".ADMIN_MAILING]=$LANG["setup"][237];
		$profiles[USER_MAILING_TYPE."_".TECH_MAILING]=$LANG["common"][10];
		$profiles[USER_MAILING_TYPE."_".USER_MAILING]=$LANG["common"][34]." ".$LANG["common"][1];
		$profiles[USER_MAILING_TYPE."_".AUTHOR_MAILING]=$LANG["setup"][238];
		$profiles[USER_MAILING_TYPE."_".ASSIGN_MAILING]=$LANG["setup"][239];
		

		$query="SELECT ID, name FROM glpi_profiles order by name";
		$result=$DB->query($query);
		while ($data=$DB->fetch_assoc($result))
			$profiles[PROFILE_MAILING_TYPE."_".$data["ID"]]=$LANG["profiles"][22]." ".$data["name"];

		$query="SELECT ID, name FROM glpi_groups order by name";
		$result=$DB->query($query);
		while ($data=$DB->fetch_assoc($result))
			$profiles[GROUP_MAILING_TYPE."_".$data["ID"]]=$LANG["common"][35]." ".$data["name"];


		ksort($profiles);
		echo "<div align='center'>";
		echo "<input type='hidden' name='update_notifications' value='1'>";
		// ADMIN
		echo "<table class='tab_cadre_fixe'>";
		echo "<tr><th colspan='3'>".$LANG["setup"][211]."</th></tr>";
		echo "<tr class='tab_bg_2'>";
		showFormMailingType("new",$profiles);
		echo "</tr>";
		echo "<tr><th colspan='3'>".$LANG["setup"][212]."</th></tr>";
		echo "<tr class='tab_bg_1'>";
		showFormMailingType("followup",$profiles);
		echo "</tr>";
		echo "<tr class='tab_bg_2'><th colspan='3'>".$LANG["setup"][213]."</th></tr>";
		echo "<tr class='tab_bg_2'>";
		showFormMailingType("finish",$profiles);
		echo "</tr>";
		echo "<tr class='tab_bg_2'><th colspan='3'>".$LANG["setup"][230]."</th></tr>";
		echo "<tr class='tab_bg_1'>";
		$profiles[USER_MAILING_TYPE."_".OLD_ASSIGN_MAILING]=$LANG["setup"][236];
		ksort($profiles);
		showFormMailingType("update",$profiles);
		unset($profiles[USER_MAILING_TYPE."_".OLD_ASSIGN_MAILING]);
		echo "</tr>";

		echo "<tr class='tab_bg_2'><th colspan='3'>".$LANG["setup"][225]."</th></tr>";
		echo "<tr class='tab_bg_2'>";
		unset($profiles[USER_MAILING_TYPE."_".ASSIGN_MAILING]);
		showFormMailingType("resa",$profiles);
		echo "</tr>";

		echo "</table>";
		echo "</div>";
	} else if ($_SESSION['glpi_mailconfig']==3)	{
		$profiles[USER_MAILING_TYPE."_".ADMIN_MAILING]=$LANG["setup"][237];
		$query="SELECT ID, name FROM glpi_profiles order by name";
		$result=$DB->query($query);
		while ($data=$DB->fetch_assoc($result))
			$profiles[PROFILE_MAILING_TYPE."_".$data["ID"]]=$LANG["profiles"][22]." ".$data["name"];

		$query="SELECT ID, name FROM glpi_groups order by name";
		$result=$DB->query($query);
		while ($data=$DB->fetch_assoc($result))
			$profiles[GROUP_MAILING_TYPE."_".$data["ID"]]=$LANG["common"][35]." ".$data["name"];


		ksort($profiles);
		echo "<div align='center'>";
		echo "<input type='hidden' name='update_notifications' value='1'>";
		// ADMIN
		echo "<table class='tab_cadre_fixe'>";
		echo "<tr><th colspan='3'>".$LANG["setup"][243]."</th></tr>";
		echo "<tr class='tab_bg_2'>";
		showFormMailingType("alertconsumable",$profiles);
		echo "</tr>";
		echo "<tr><th colspan='3'>".$LANG["setup"][244]."</th></tr>";
		echo "<tr class='tab_bg_1'>";
		showFormMailingType("alertcartridge",$profiles);
		echo "</tr>";
		echo "<tr><th colspan='3'>".$LANG["setup"][246]."</th></tr>";
		echo "<tr class='tab_bg_2'>";
		showFormMailingType("alertcontract",$profiles);
		echo "</tr>";
		echo "<tr><th colspan='3'>".$LANG["setup"][247]."</th></tr>";
		echo "<tr class='tab_bg_1'>";
		showFormMailingType("alertinfocom",$profiles);
		echo "</tr>";
		echo "</table>";
		echo "</div>";

	}
	echo "</form>";

}

function showFormMailingType($type,$profiles){
	global $LANG,$DB;

	echo "<td align='right'>";

	echo "<select name='mailing_to_add_".$type."[]' multiple size='5'>";

	foreach ($profiles as $key => $val){
		list($item_type,$item)=split("_",$key);
		echo "<option value='$key'>".$val."</option>";
	}
	echo "</select>";
	echo "</td>";
	echo "<td align='center'>";
	echo "<input type='submit'  class=\"submit\" name='mailing_add_$type' value='".$LANG["buttons"][8]." >>'><br><br>";
	echo "<input type='submit'  class=\"submit\" name='mailing_delete_$type' value='<< ".$LANG["buttons"][6]."'>";
	echo "</td>";
	echo "<td>";
	echo "<select name='mailing_to_delete_".$type."[]' multiple size='5'>";
	// Get User mailing
	$query="SELECT glpi_mailing.FK_item as item, glpi_mailing.ID as ID FROM glpi_mailing WHERE glpi_mailing.type='$type' AND glpi_mailing.item_type='".USER_MAILING_TYPE."' ORDER BY glpi_mailing.FK_item;";
	$result=$DB->query($query);
	if ($DB->numrows($result))
		while ($data=$DB->fetch_assoc($result)){
			switch ($data["item"]){
				case ADMIN_MAILING: $name=$LANG["setup"][237];break;
				case ASSIGN_MAILING: $name=$LANG["setup"][239];break;
				case AUTHOR_MAILING: $name=$LANG["setup"][238];break;
				case USER_MAILING: $name=$LANG["common"][34]." ".$LANG["common"][1];break;
				case OLD_ASSIGN_MAILING: $name=$LANG["setup"][236];break;
				case TECH_MAILING: $name=$LANG["common"][10];break;
			}
			echo "<option value='".$data["ID"]."'>".$name."</option>";
		}
	// Get Profile mailing
	$query="SELECT glpi_mailing.FK_item as item, glpi_mailing.ID as ID, glpi_profiles.name as prof FROM glpi_mailing LEFT JOIN glpi_profiles ON (glpi_mailing.FK_item = glpi_profiles.ID) WHERE glpi_mailing.type='$type' AND glpi_mailing.item_type='".PROFILE_MAILING_TYPE."' ORDER BY glpi_profiles.name;";
	$result=$DB->query($query);
	if ($DB->numrows($result))
		while ($data=$DB->fetch_assoc($result)){
			echo "<option value='".$data["ID"]."'>".$LANG["profiles"][22]." ".$data["prof"]."</option>";
		}

	// Get Group mailing
	$query="SELECT glpi_mailing.FK_item as item, glpi_mailing.ID as ID, glpi_groups.name as name FROM glpi_mailing LEFT JOIN glpi_groups ON (glpi_mailing.FK_item = glpi_groups.ID) WHERE glpi_mailing.type='$type' AND glpi_mailing.item_type='".GROUP_MAILING_TYPE."' ORDER BY glpi_groups.name;";
	$result=$DB->query($query);
	if ($DB->numrows($result))
		while ($data=$DB->fetch_assoc($result)){
			echo "<option value='".$data["ID"]."'>".$LANG["common"][35]." ".$data["name"]."</option>";
		}

	echo "</select>";
	echo "</td>";

}

function updateMailNotifications($input){
	global $DB;
	$type="";
	$action="";


	foreach ($input as $key => $val){
		if (!ereg("mailing_to_",$key)&&ereg("mailing_",$key)){
			if (preg_match("/mailing_([a-z]+)_([a-z]+)/",$key,$matches)){
				$type=$matches[2];
				$action=$matches[1];
			}
		}
	}

	if (count($input["mailing_to_".$action."_".$type])>0){
		foreach ($input["mailing_to_".$action."_".$type] as $val){
			switch ($action){
				case "add":
					list($item_type,$item)=split("_",$val);
				$query="INSERT INTO glpi_mailing (type,FK_item,item_type) VALUES ('$type','$item','$item_type')";
				$DB->query($query);
				break;
				case "delete":
					$query="DELETE FROM glpi_mailing WHERE ID='$val'";
				$DB->query($query);
				break;
			} 
		}
	}


}


function ocsFormDBConfig($target, $id) {


	global  $DB,$DBocs,$LANG,$CFG_GLPI;

	if (!haveRight("ocsng","w")) return false;	

	$data=getOcsConf($id);

	echo "<form name='formdbconfig' action=\"$target\" method=\"post\">";
	echo "<input type='hidden' name='ID' value='".$CFG_GLPI["ID"]."'>";
	echo "<input type='hidden' name='update_ocs_dbconfig' value='1'>";
	echo "<div align='center'><table class='tab_cadre'>";
	echo "<tr><th colspan='2'>".$LANG["ocsconfig"][0]."</th></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][2]." </td><td> <input type=\"text\" name=\"ocs_db_host\" value=\"".$data["ocs_db_host"]."\"></td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][4]." </td><td> <input type=\"text\" name=\"ocs_db_name\" value=\"".$data["ocs_db_name"]."\"></td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][1]." </td><td> <input type=\"text\" name=\"ocs_db_user\" value=\"".$data["ocs_db_user"]."\"></td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][3]." </td><td> <input type=\"password\" name=\"ocs_db_passwd\" value=\"".$data["ocs_db_passwd"]."\"></td></tr>";
	echo "</table></div>";
	echo "<p class=\"submit\"><input type=\"submit\" name=\"update_conf_ocs\" class=\"submit\" value=\"".$LANG["buttons"][2]."\" ></p>";
	echo "</form>";


	echo "<div align='center'>";
	if (!$DBocs->error){
		echo $LANG["ocsng"][18]."<br>";
		$result=$DBocs->query("SELECT TVALUE FROM config WHERE NAME='GUI_VERSION'");
		if ($DBocs->numrows($result)==1&&$DBocs->result($result,0,0)>=4020) {
			$query = "UPDATE config SET IVALUE='1' WHERE NAME='TRACE_DELETED'";
			$DBocs->query($query);

			echo $LANG["ocsng"][19]."</div>";
			ocsFormConfig($target, $id);
		} else echo $LANG["ocsng"][20]."</div>";
	} else echo $LANG["ocsng"][21]."</div>";

}

function ocsFormConfig($target, $id) {


	global  $DB,$LANG,$CFG_GLPI;

	if (!haveRight("ocsng","w")) return false;	

	$data=getOcsConf($id);

	echo "<form name='formconfig' action=\"$target\" method=\"post\">";
	echo "<input type='hidden' name='ID' value='".$CFG_GLPI["ID"]."'>";
	echo "<input type='hidden' name='update_ocs_config' value='1'>";
	echo "<div align='center'><table class='tab_cadre'>";
	echo "<tr><th colspan='2'>".$LANG["ocsconfig"][5]."</th></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][17]." </td><td> <input type=\"text\" size='30' name=\"tag_limit\" value=\"".$data["tag_limit"]."\"></td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][16]." </td><td>";
	dropdownValue("glpi_dropdown_state","default_state",$data["default_state"]);
	echo "</td></tr>";

	$periph=$data["import_periph"];
	$monitor=$data["import_monitor"];
	$printer=$data["import_printer"];
	$software=$data["import_software"];
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][8]." </td><td>";
	echo "<select name='import_periph'>";
	echo "<option value='0' ".($periph==0?" selected ":"").">".$LANG["ocsconfig"][11]."</option>";
	echo "<option value='1' ".($periph==1?" selected ":"").">".$LANG["ocsconfig"][10]."</option>";
	echo "<option value='2' ".($periph==2?" selected ":"").">".$LANG["ocsconfig"][12]."</option>";
	echo "</select>";
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][7]." </td><td>";
	echo "<select name='import_monitor'>";
	echo "<option value='0' ".($monitor==0?" selected ":"").">".$LANG["ocsconfig"][11]."</option>";
	echo "<option value='1' ".($monitor==1?" selected ":"").">".$LANG["ocsconfig"][10]."</option>";
	echo "<option value='2' ".($monitor==2?" selected ":"").">".$LANG["ocsconfig"][12]."</option>";
	echo "</select>";
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][9]." </td><td>";

	echo "<select name='import_printer'>";
	echo "<option value='0' ".($printer==0?" selected ":"").">".$LANG["ocsconfig"][11]."</option>";
	echo "<option value='1' ".($printer==1?" selected ":"").">".$LANG["ocsconfig"][10]."</option>";
	echo "<option value='2' ".($printer==2?" selected ":"").">".$LANG["ocsconfig"][12]."</option>";
	echo "</select>";
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][6]." </td><td>";
	echo "<select name='import_software'>";
	echo "<option value='0' ".($software==0?" selected ":"").">".$LANG["ocsconfig"][11]."</option>";
	echo "<option value='1' ".($software==1?" selected ":"").">".$LANG["ocsconfig"][12]."</option>";
	echo "</select>";

	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][38]." </td><td>";
	dropdownYesNoInt("use_soft_dict",$data["use_soft_dict"]);
	echo "</td></tr>";
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][40]." </td><td>";
	echo "<select name='cron_sync_number'>";
	for ($i=0;$i<100;$i++){
		echo "<option value='$i' ".($i==$data["cron_sync_number"]?" selected":"").">$i</option>";
	}
	echo "</select>";

	echo "</td></tr>";

	echo "</table></div>";

	echo "<div align='center'>".$LANG["ocsconfig"][15]."</div>";
	echo "<div align='center'>".$LANG["ocsconfig"][14]."</div>";
	echo "<div align='center'>".$LANG["ocsconfig"][13]."</div>";

	echo "<br />";

	echo "<div align='center'><table class='tab_cadre'>";
	echo "<tr><th>".$LANG["ocsconfig"][27]."</th><th>".$LANG["ocsconfig"][28]."</th></tr>";
	echo "<tr><td class='tab_bg_2' valign='top'><table width='100%' cellpadding='1' cellspacing='0' border='0'>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][39]." </td><td>";
	echo "<select name='import_tag_field'>";
	echo "<option value=''>".$LANG["ocsconfig"][11]."</option>";
	echo "<option value='otherserial' ".($data["import_tag_field"]=="otherserial"?"selected":"").">".$LANG["common"][20]."</option>";
	echo "<option value='contact_num' ".($data["import_tag_field"]=="contact_num"?"selected":"").">".$LANG["common"][21]."</option>";
	echo "<option value='location' ".($data["import_tag_field"]=="location"?"selected":"").">".$LANG["common"][15]."</option>";
	echo "<option value='network' ".($data["import_tag_field"]=="network"?"selected":"").">".$LANG["setup"][88]."</option>";
	echo "</select>";
	echo "</td></tr>";


	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][16]." </td><td>";
	dropdownYesNoInt("import_general_name",$data["import_general_name"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["computers"][9]." </td><td>";
	dropdownYesNoInt("import_general_os",$data["import_general_os"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][19]." </td><td>";
	dropdownYesNoInt("import_general_serial",$data["import_general_serial"]);
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][22]." </td><td>";
	dropdownYesNoInt("import_general_model",$data["import_general_model"]);
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][5]." </td><td>";
	dropdownYesNoInt("import_general_enterprise",$data["import_general_enterprise"]);
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][17]." </td><td>";
	dropdownYesNoInt("import_general_type",$data["import_general_type"]);
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["setup"][89]." </td><td>";
	dropdownYesNoInt("import_general_domain",$data["import_general_domain"]);
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][18]." </td><td>";
	dropdownYesNoInt("import_general_contact",$data["import_general_contact"]);
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["common"][25]." </td><td>";
	dropdownYesNoInt("import_general_comments",$data["import_general_comments"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td colspan='2'>&nbsp;";
	echo "</td></tr>";
	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["networking"][14]." </td><td>";
	dropdownYesNoInt("import_ip",$data["import_ip"]);
	echo "</td></tr>";

	echo "</table></td>";
	echo "<td class='tab_bg_2' valign='top'><table width='100%' cellpadding='1' cellspacing='0' border='0'>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["devices"][4]." </td><td>";
	dropdownYesNoInt("import_device_processor",$data["import_device_processor"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["devices"][6]." </td><td>";
	dropdownYesNoInt("import_device_memory",$data["import_device_memory"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["devices"][1]." </td><td>";
	dropdownYesNoInt("import_device_hdd",$data["import_device_hdd"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["devices"][3]." </td><td>";
	dropdownYesNoInt("import_device_iface",$data["import_device_iface"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["devices"][2]." </td><td>";
	dropdownYesNoInt("import_device_gfxcard",$data["import_device_gfxcard"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["devices"][7]." </td><td>";
	dropdownYesNoInt("import_device_sound",$data["import_device_sound"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["devices"][19]." </td><td>";
	dropdownYesNoInt("import_device_drives",$data["import_device_drives"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][36]." </td><td>";
	dropdownYesNoInt("import_device_modems",$data["import_device_modems"]);
	echo "</td></tr>";

	echo "<tr class='tab_bg_2'><td align='center'>".$LANG["ocsconfig"][37]." </td><td>";
	dropdownYesNoInt("import_device_ports",$data["import_device_ports"]);
	echo "</td></tr>";

	echo "</table></td></tr>";
	echo "</table></div>";
	echo "<p class=\"submit\"><input type=\"submit\" name=\"update_conf_ocs\" class=\"submit\" value=\"".$LANG["buttons"][2]."\" ></p>";
	echo "</form>";

}

?>
