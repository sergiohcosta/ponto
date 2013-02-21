<?php

require_once 'config.php';

set_time_limit(0);

function XmlToBD($xml,$conexao)
{
    global $a;
    global $registros;
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
        $registros++;
    }
    return($linha['refID']);
}

$conn = mysql_connect("localhost","root") or die(mysql_error());
mysql_select_db('eve',$conn) or die(mysql_error());

$query = "SELECT last,next FROM api_cache WHERE nome = 'walletjournal'";
$result = mysql_query($query, $conn);
$datas = mysql_fetch_assoc($result);

$dataNow = new DateTime();
$dataNow->setTimezone(new DateTimeZone('Europe/London'));

$expDate = '/\\A(?:^((\\d{2}(([02468][048])|([13579][26]))[\\-\\/\\s]?((((0?[13578])|(1[02]))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])))))|(\\d{2}(([02468][1235679])|([13579][01345789]))[\\-\\/\\s]?((((0?[13578])|(1[02]))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(3[01])))|(((0?[469])|(11))[\\-\\/\\s]?((0?[1-9])|([1-2][0-9])|(30)))|(0?2[\\-\\/\\s]?((0?[1-9])|(1[0-9])|(2[0-8]))))))(\\s(((0?[0-9])|(1[0-9])|(2[0-3]))\\:([0-5][0-9])((\\s)|(\\:([0-5][0-9])))?))?$)\\z/';

if (preg_match($expDate, $datas["next"])) // se a data puxada do banco for valida ...
{
    $dataLast = new DateTime($datas["last"],new DateTimeZone('Europe/London'));
    $dataNext = new DateTime($datas["next"],new DateTimeZone('Europe/London'));
    $dataDiff = $dataNow->diff($dataNext);

    if( ($dataNow < $dataNext) ) // se 'agora' for antes de 'next' ...
    {
        echo "<br> Horario do servidor: " . $dataNow->format('d-m-Y H:i:s');
        echo "<br> Horario da proxima atualizacao de API: " . $dataNext->format('d-m-Y H:i:s');
        echo "<br> " . $dataDiff->format("%y anos, %m meses, %d dias, %h horas, %i minutos, %s segundos restando.");
        echo "<br> Nenhuma alteracao a ser feita.";
        echo "<br> Encerrando.";

        exit();
    }
}

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

do{
    $url = $url_usada;
    if(isset($retorno)) $url=$url_usada . "&fromID=" . $retorno;
    
    echo $url . "<br>";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    
    $result = curl_exec($ch);
    curl_close($ch);
    
    if($result!=FALSE){
        $xml = new SimpleXMLElement($result);
        // $xml = simplexml_load_file($url) or die("<br>Erro ao carregar a API usando a url $url. Encerrando.<br>");
        
        $retorno = XmlToBD($xml,$conn);
    }

} while ($a==$rowCount && $result != FALSE);

if($result != FALSE)
{
    echo "<br> data desta consulta: " . $xml->currentTime;
    echo "<br> $registros registros encontrados na API";
    echo "<br> $affected registros criados/alterados";
    echo "<br> proximo update de api: " . $xml->cachedUntil;

    $query = "UPDATE api_cache SET last='" . $xml->currentTime . "',next='" . $xml->cachedUntil . "' WHERE nome='walletjournal'";
    echo $query . "<br>";
    
    if(mysql_query($query,$conn)) echo "<br>Informacoes de cache de API atualizadas.";
}
else
{
    echo "Ocorreram erros na consulta a API.";
}

echo "<br>Encerrando";
?>