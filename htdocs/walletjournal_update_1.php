<?php

set_time_limit(0);

function XmlToBD($xml,$conexao)
{
    global $a;
    global $affected;
    global $accountKey;

    $a = 0;

    foreach ($xml->result->rowset->row as $linha)
    {
        $atrib[] = $linha->attributes();

        $query = "INSERT walletjournal ";
        $query .= "(accountKey,date,refID,refTypeID,ownerName1,ownerID1,ownerName2,ownerID2,argName1,argID1,amount,balance,reason) ";
        $query .= "VALUES (";
        $query .= "'" . $accountKey . "',";
        $query .= "'" . $linha['date'] . "',";
        $query .= "'" . $linha['refID'] . "',";
        $query .= "'" . $linha['refTypeID'] . "',";
        $query .= "'" . $linha['ownerName1'] . "',";
        $query .= "'" . $linha['ownerID1'] . "',";
        $query .= "'" . $linha['ownerName2'] . "',";
        $query .= "'" . $linha['ownerID2'] . "',";
        $query .= "'" . $linha['argName1'] . "',";
        $query .= "'" . $linha['argID1'] . "',";
        $query .= "'" . $linha['amount'] . "',";
        $query .= "'" . $linha['balance'] . "',";
        $query .= "'" . $linha['reason'] . "'";

        $query .= ") ON DUPLICATE KEY UPDATE ";
        $query .= "accountKey = '" . $accountKey . "',";
        $query .= "date = '" . $linha['date'] . "',";
        $query .= "refTypeID = '" . $linha['refTypeID'] . "',";
        $query .= "ownerName1 = '" . $linha['ownerName1'] . "',";
        $query .= "ownerID1 = '" . $linha['ownerID1'] . "',";
        $query .= "ownerName2 = '" . $linha['ownerName2'] . "',";
        $query .= "ownerID2 = '" . $linha['ownerID2'] . "',";
        $query .= "argName1 = '" . $linha['argName1'] . "',";
        $query .= "argID1 = '" . $linha['argID1'] . "',";
        $query .= "amount = '" . $linha['amount'] . "',";
        $query .= "balance = '" . $linha['balance'] . "',";
        $query .= "reason = '" . $linha['reason'] . "';";
        
        if(!mysql_query($query, $conexao))
        {
            die(mysql_error($conexao));
        }
        
        if(mysql_affected_rows($conexao)>0) $affected++;

        $a++;
    }
    return($linha['refID']);
}

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

$keyID = "1551088";
$vCode = "VSkl9LTuwvE9G1WzP4HQJ6oUdNyDJ9DbC9YNUXF6YZHVmIDoNZLR7gML3Qc2dCth";
$rowCount = 1500;

$accountKey = 1000;
if(isset($_GET['accountKey']) && $_GET['accountKey']>=1000 && $_GET['accountKey']<=1006)
    $accountKey = $_GET['accountKey'];

$url_online = "http://api.eveonline.com/corp/WalletJournal.xml.aspx?rowCount=$rowCount&keyID=$keyID&vCode=$vCode&accountKey=$accountKey";
$url_offline = "xml/WalletJournal_" . $accountKey . ".xml";

$url_usada = $url_offline;
$url_usada = $url_online;

$affected = 0;
$query = "";
$last_refID = 0;

$a=0;

$steps = 0; $loop = 0;
$last_refID = 0;

if(isset($_GET['steps']) && $_GET['steps']>0) $steps = $_GET['steps'];

do{
    $url = $url_usada;
    if(isset($retorno)) $url=$url_usada . "&fromID=" . $retorno;
    
    echo "fetching info de $url <br>";
    flush();
    
    $xml = simplexml_load_file($url);
    $retorno = XmlToBD($xml,$conn);
} while ($a==$rowCount);

echo "data desta consulta: " . $xml->currentTime;
echo "<br>";
echo "$a registros encontrados na API <br>";
echo "$affected registros criados/alterados<br>";
echo "registro mais antigo encontrado: $retorno <br>";
echo "proximo update de api: " . $xml->cachedUntil;













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
?>