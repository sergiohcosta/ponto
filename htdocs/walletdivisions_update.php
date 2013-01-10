<?php

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

$url_online = "http://api.eveonline.com/eve/CorporationSheet.xml.aspx";
$url_offline = "xml/CorporationSheet.xml";

//if(!$xml = simplexml_load_file($url_online))
//    $xml = simplexml_load_file($url_offline);

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

$wd = $xml->xpath('/eveapi/result/rowset[@name="walletDivisions"]');

foreach ($wd[0] as $linha)
{
    //$atrib[] = $linha->attributes();
    
    $atrib = $linha->attributes();
    
    $query = "INSERT walletdivisions ";
    $query .= "(accountKey,description) ";
    $query .= "VALUES (";
    $query .= "'" . $atrib['accountKey'] . "',";
    $query .= "'" . $atrib['description'] . "'";
        
    $query .= ") ON DUPLICATE KEY UPDATE ";
    $query .= "description = '" . $linha['description'] . "';";
    
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
