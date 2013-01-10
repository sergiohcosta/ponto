<?php

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

$url_online = "http://api.eveonline.com/eve/RefTypes.xml.aspx";
$url_offline = "RefTypes.xml";

 if(!$xml = simplexml_load_file($url_online))
    $xml = simplexml_load_file($url_offline);


// So processar a API caso o cache ja tenha expirado
// esta logica precisa ser acertada, nao funciona deste jeito

/*

$cache = $xml->cachedUntil;
$objCache = new DateTime($cache);
$objData = new DateTime();
$diff = $objData->diff($objCache);
$tot = ($diff->s) + ($diff->i*60) + ($diff->h*3600) + ($diff->d*86400) + ($diff->m*2592000) + ($diff->y*31104000);

 if($tot>=0) die("Sem necessidade de atualizacao da base. Prox atualizacao: $cache");
 
 */

$a=0;$affected=0;

foreach ($xml->result->rowset->row as $linha)
{
    $atrib[] = $linha->attributes();
    
    $query = "INSERT reftypes ";
    $query .= "(refTypeID,refTypeName) ";
    $query .= "VALUES (";
    $query .= "'" . $linha['refTypeID'] . "',";
    $query .= "'" . $linha['refTypeName'] . "'";
        
    $query .= ") ON DUPLICATE KEY UPDATE ";
    $query .= "refTypeName = '" . $linha['refTypeName'] . "';";
    
    if(!mysql_query($query, $conn))
    {
           die(mysql_error($conn));
    }
    
    if(mysql_affected_rows()>0) $affected++;
    
    $a++;
}

echo "$a registros encontrados na API <br>";
echo "$affected registros criados/alterados";

?>
