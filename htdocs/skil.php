<?php
include_once 'krumo/class.krumo.php';

function pre_pr($param){
    echo "<pre>";
    print_r($param);
    echo "</pre>";
}
function pre_vd($param){
    echo "<pre>";
    var_dump($param);
    echo "</pre>";
}

function echobr($param){
    echo "<br>";
    echo $param;
    echo "<br>";
}


$url_offline = "xml/skilltree.xml";
$url_usada = $url_offline;

$xml = simplexml_load_file($url_usada);

$arrSkillTree = array();
$arrSkills = array();

// loop que itera pelos grupos de skills
foreach ($xml->result->rowset->row as $group)
{
    
    $atGroup = $group->attributes();
    $groupID = (int)$atGroup->groupID;
    $groupName = (string) $atGroup->groupName;

    $arrSkillTree[$groupID]['groupName'] = $groupName;
    
    // loop que itera pelas skills dentro do grupo
    foreach($group->rowset->row as $type)
    {
        $atType = $type->attributes();
        $typeID = (int)$atType->typeID;
        $typeName = (string) $atType->typeName;
        $arrSkillTree[$groupID]['skills'][$typeID]['typeName'] = $typeName;
        $arrSkillTree[$groupID]['skills'][$typeID]['description'] = (string) $type->description;
        $arrSkillTree[$groupID]['skills'][$typeID]['rank'] = (int) $type->rank;
        $arrSkillTree[$groupID]['skills'][$typeID]['rank'] = (int) $type->rank;
        
        // aproveita essa iteracao para fazer um array de index de skills
        $arrSkills[$typeID]['typeName'] = $typeName;
        $arrSkills[$typeID]['groupID'] = $groupID;
        
        // loop que itera pelos pre-requisitos da skill
        $reqSkills = $type->xpath('rowset[@name="requiredSkills"]');
        foreach($reqSkills[0] as $rs)
        {
            $rsAtrib = $rs->attributes();
            $rsTypeID = (int) $rsAtrib->typeID;
            $rsSkillLevel = (int) $rsAtrib->skillLevel;
            $arrSkillTree[$groupID]['skills'][$typeID]['requiredSkills'][$rsTypeID] = (int) $rsSkillLevel;
            
            $arrSkills[$typeID]['requiredSkills'][$rsTypeID] = (int) $rsSkillLevel;
        }
        
        // preenche os atributos requeridos da skill
        $reqAttr = $type->requiredAttributes;
        $arrSkillTree[$groupID]['skills'][$typeID]['requiredAttributes']['primaryAttribute'] = (string) $reqAttr->primaryAttribute;
        $arrSkillTree[$groupID]['skills'][$typeID]['requiredAttributes']['secondaryAttribute'] = (string) $reqAttr->secondaryAttribute;
        
        $arrSkills[$typeID]['requiredAttributes']['primaryAttribute'] = (string) $reqAttr->primaryAttribute;
        $arrSkills[$typeID]['requiredAttributes']['secondaryAttribute'] = (string) $reqAttr->secondaryAttribute;
    }
   
}

?>

<div id="myTree">
    
<?
foreach($arrSkillTree as $group)
{
    krumo($group);
}
?>
    
    
    
</div>
