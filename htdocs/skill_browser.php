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

function echoh3a($param){
    echo "<h3><a href='#'>";
    echo $param;
    echo "</a></h3>\n";
}

function echodiv($param){
    echo "<div>";
    echo $param;
    echo "</div>\n";
}

function echodivp($param){
    echo "<div><p>";
    echo $param;
    echo "</p></div>\n";
}

function echoli($param){
    echo "<li>";
    echo $param;
    echo "</li>\n";
}

include_once 'skill_arrays.php';

$rankSP = array();
$rankSP[1] = 250; $rankSP[2] = 1415; $rankSP[3] = 8000; $rankSP[4] = 45255; $rankSP[5] = 256000;

//krumo($arrSkillTree);
//krumo($arrSkills);

?>

<script src="jquery/jquery.js"></script>
<script src="jquery/jsTree/jquery.jstree.js"></script>

<script>

$(document).ready(function(){
    $("#skillTree").jstree({
        "plugins" : [
            "themes",
            "sort",
            "html_data" ],
        "themes" : {
            "theme" : "ponto"
        }
        // ,"sort": function(a,b) {
        //    alert(this.get_id(a) + "<>" + this.get_id(b));
        //    return this.get_text(a) < this.get_text(b) ? -1 : 1;
        //}
    });
    
    $("#colapseAll").click( function(){
        $("#skillTree").jstree('close_all');
    });
    
    $("#expandAll").click( function(){
        $("#skillTree").jstree('open_all');
    });
    
    $("#containerSkillGeral, #containerSkillTraining").hide();
    
    
    // o que fazer ao clicar em um node da arvore
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
                        
                        $rankSP= [0 , 250 , 1415 , 8000 , 45255 , 256000];
                        
                        $("#skillLvl1").html(json.level1);
                        $("#skillLvl2").html(json.level2);
                        $("#skillLvl3").html(json.level3);
                        $("#skillLvl4").html(json.level4);
                        $("#skillLvl5").html(json.level5);
                        
                        $("#skillReqSkills").html("");
                        //alert(json.requiredSkills);
                        
                        if(typeof json.requiredSkills !== 'undefined'){
                            $("#skillReqSkills").append("Required skills:<BR>");
                            jQuery.each(json.requiredSkills,function(i,val){
                                jQuery.ajax({
                                    url:"skill_details.php",
                                    data:"sid=" + i,
                                    dataType:"json",
                                    success:function(json2){
                                        $("#skillReqSkills").append(json2.typeName + " " + val + "<BR>");
                                    }
                                });
                            });
                        }
                        
                        
                    }
                });
            }
    });

});
</script>

<style>

</style>

<div id="containerSkillTree" style="position: relative; float:left; width:28%; height: 720px; overflow: auto; border: 1px solid red;">

<input type="button" value=" + " id="expandAll">
<input type="button" value=" - " id="colapseAll">

<br><br>

<div id="skillTree">
<ul>

<?php foreach($arrSkillTree as $group) { if($group['groupName']=="Fake Skills") continue; ?>
    <li><a><?php echo $group['groupName']?></a>
        
        <ul>
<?php foreach($group['skills'] as $skill) {  // echobr("-----------"); pre_pr($skill); echobr("*************"); 
        if($skill['published']==1) {
?>
            <li><a class="aSkill" id="skill_<?php echo $skill['typeID']?>"><?php echo $skill['typeName']?></a></li>
<?php } // if $skill['published']
} // foreach $group['skills']
?>
        </ul>
    </li>
<?php } // foreach $arrSkillTree ?>

</ul>
    
</div>

</div>

<div id="containerSkillDetails" style="position: relative; float:left; width:700px; height: 700px; overflow: auto; border: 1px solid red; padding: 10px; ">

<span id="skillName"></span>

<div id="containerSkillGeral" style="border: 1px solid red;">
<span id="skillDesc"></span>
</div>

<div id="containerSkillTraining" style="border: 1px solid red;">
Rank: <span id="skillRank"></span>
<br>
Attributes: <span id="skillPriAttrib"></span>, <span id="skillSecAttrib"></span>
<br>
Level 1: <span id="skillLvl1"></span>
<br>
Level 2: <span id="skillLvl2"></span>
<br>
Level 3: <span id="skillLvl3"></span>
<br>
Level 4: <span id="skillLvl4"></span>
<br>
Level 5: <span id="skillLvl5"></span>
<br>
<span id="skillReqSkills"></span>

</div>


</div>
    
</div>
    
</div>