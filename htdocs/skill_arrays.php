<?php

$url_offline = "xml/skilltree.xml";
$url_usada = $url_offline;

$xml = simplexml_load_file($url_usada);

$arrSkillTree = array();
$arrSkills = array();

$rankSP= array(0 , 250 , 1415 , 8000 , 45255 , 256000);

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
        $published = (int) $atType->published;
        $rank = (int) $type->rank;
        $description = (string) $type->description;
        
        $arrSkillTree[$groupID]['skills'][$typeID]['typeID'] = $typeID;
        $arrSkillTree[$groupID]['skills'][$typeID]['typeName'] = $typeName;
        $arrSkillTree[$groupID]['skills'][$typeID]['description'] = $description;
        $arrSkillTree[$groupID]['skills'][$typeID]['rank'] = $rank;
        $arrSkillTree[$groupID]['skills'][$typeID]['level1'] = $rank * $rankSP[1];
        $arrSkillTree[$groupID]['skills'][$typeID]['level2'] = $rank * $rankSP[2];
        $arrSkillTree[$groupID]['skills'][$typeID]['level3'] = $rank * $rankSP[3];
        $arrSkillTree[$groupID]['skills'][$typeID]['level4'] = $rank * $rankSP[4];
        $arrSkillTree[$groupID]['skills'][$typeID]['level5'] = $rank * $rankSP[5];
        $arrSkillTree[$groupID]['skills'][$typeID]['published'] = $published;
        
        // aproveita essa iteracao para fazer um array de index de skills
        $arrSkills[$typeID]['typeID'] = $typeID;
        $arrSkills[$typeID]['typeName'] = $typeName;
        $arrSkills[$typeID]['groupID'] = $groupID;
        $arrSkills[$typeID]['description'] = $description;
        $arrSkills[$typeID]['rank'] = $rank;
        $arrSkills[$typeID]['level1'] = $rank * $rankSP[1];
        $arrSkills[$typeID]['level2'] = $rank * $rankSP[2];
        $arrSkills[$typeID]['level3'] = $rank * $rankSP[3];
        $arrSkills[$typeID]['level4'] = $rank * $rankSP[4];
        $arrSkills[$typeID]['level5'] = $rank * $rankSP[5];
        $arrSkills[$typeID]['published'] = $published;
        
        // loop que itera pelos pre-requisitos da skill
        $reqSkills = $type->xpath('rowset[@name="requiredSkills"]');
        foreach($reqSkills[0] as $rs)
        {
            $rsAtrib = $rs->attributes();
            $rsTypeID = (int) $rsAtrib->typeID;
            $rsSkillLevel = (int) $rsAtrib->skillLevel;
            
            //$arrSkillTree[$groupID]['skills'][$typeID]['requiredSkills'][$rsTypeID]['typeID'] = $rsTypeID;
            //$arrSkillTree[$groupID]['skills'][$typeID]['requiredSkills'][$rsTypeID]['level'] = (int) $rsSkillLevel;
            $arrSkillTree[$groupID]['skills'][$typeID]['requiredSkills'][$rsTypeID] = $rsSkillLevel;
            
            //$arrSkills[$typeID]['requiredSkills']['typeID'] = $rsTypeID;
            //$arrSkills[$typeID]['requiredSkills']['level'] = (int) $rsSkillLevel;
            $arrSkills[$typeID]['requiredSkills'][$rsTypeID] = $rsSkillLevel;

        }
        
        // preenche os atributos requeridos da skill
        $reqAttr = $type->requiredAttributes;
        $arrSkillTree[$groupID]['skills'][$typeID]['requiredAttributes']['primaryAttribute'] = (string) $reqAttr->primaryAttribute;
        $arrSkillTree[$groupID]['skills'][$typeID]['requiredAttributes']['secondaryAttribute'] = (string) $reqAttr->secondaryAttribute;
        
        $arrSkills[$typeID]['requiredAttributes']['primaryAttribute'] = (string) $reqAttr->primaryAttribute;
        $arrSkills[$typeID]['requiredAttributes']['secondaryAttribute'] = (string) $reqAttr->secondaryAttribute;
    }
   
}

//krumo($arrSkillTree);
//krumo($arrSkills);
?>
