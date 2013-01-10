<?php

include_once 'skill_arrays.php';

if(isset($_GET['sid'])) {
    if(is_numeric($_GET['sid']))
    {
        if(isset($arrSkills[$_GET['sid']]))
        {
            echo json_encode($arrSkills[$_GET['sid']]);
            exit();
        }
    }
    else if($_GET['sid']=="all")
    {
        echo json_encode($arrSkills);
        exit();
    }
}
echo null;
?>