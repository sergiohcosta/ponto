<?php


?>


<script src="jquery/jquery.js"></script>
<script src="jquery/jsTree/jquery.jstree.js"></script>

<script>

$(document).ready(function(){
    
    
    $("#skillTree").jstree({
        "plugins" : [
            "themes",
            "sort",
            "json_data" ],
        "themes" : {
            "theme" : "ponto"
        },
        "json_data" : {
            "ajax" : {
                
            }
        }
    });
    
    $("#colapseAll").click( function(){
        $("#skillTree").jstree('close_all');
    });
    
    $("#expandAll").click( function(){
        $("#skillTree").jstree('open_all');
    });
    
    $("#containerSkillGeral, #containerSkillTraining").hide();
    
    $(".jstree a").live("click", function(e) {
        var $strSplit = this.id.split("_");
        if(!isNaN($strSplit[1]))
            {
                $skillID = $strSplit[1];
                jQuery.ajax({
                    url:"skill_details.php",
                    data:"sid=" + $skillID,
                    dataType:"json",
                    success:function(json)
                    {
                        $("#skillName").html(json.typeName);
                        $("#skillRank").html(json.rank);
                        $("#skillDesc").html(json.description);
                        $("#skillPriAttrib").html(json.requiredAttributes.primaryAttribute);
                        $("#skillSecAttrib").html(json.requiredAttributes.secondaryAttribute);
                        
                        
                        $("#containerSkillGeral").show();
                        $("#containerSkillTraining").show();

                    }
                });
            }
    })
});
</script>

<div id="skillTree"></div>