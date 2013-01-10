<?php
/*
 * 
 * walletdivisions_update.php
 * 
 * funcao: consultar a API da corporacao para atualizar o nome das abas da wallet da referida corporacao
 * 
 */

$keyID = "1551088";
$vCode = "VSkl9LTuwvE9G1WzP4HQJ6oUdNyDJ9DbC9YNUXF6YZHVmIDoNZLR7gML3Qc2dCth";

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

$url_online = "http://api.eveonline.com/corp/CorporationSheet.xml.aspx&keyID=$keyID&vCode=$vCode";
$url_offline = "xml/CorporationSheet.xml";

$url = $url_offline;
$url = $url_online;

//if(!$xml = simplexml_load_file($url_online))
//    $xml = simplexml_load_file($url_offline);

$xml = simplexml_load_file($url);

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
