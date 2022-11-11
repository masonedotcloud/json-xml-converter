<?php
//formattazione dper XML
function prettyPrintXmlToBrowser(SimpleXMLElement $xml)
{
    $domXml = new DOMDocument('1.0');
    $domXml->preserveWhiteSpace = false;
    $domXml->formatOutput = true;
    $domXml->loadXML($xml->asXML());
    $xmlString = $domXml->saveXML();
    return $xmlString;
}

//conversione da array a XML
function array_to_xml($data, &$xml_data)
{
    foreach ($data as $key => $value) {
        if (is_array($value)) {
            if (is_numeric($key)) {
                $key = 'item' . $key;
            }
            $subnode = $xml_data->addChild($key);
            array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key", htmlspecialchars("$value"));
        }
    }
}

//variabili si ritorno
$stato = '';
$codice_update = '';
$codice = '';

//presa del codice dalla pagina principale
try {
    $codice = $_POST['codice'];
} catch (\Throwable $th) {
    $stato = 'errore';
}

//XML -> JSON
try {
    if ($_POST['inizio'] == 'xml') {
        $xml = simplexml_load_string($codice, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $array = json_decode($json, TRUE);
        $out = array_values($array);
        $codice_update = json_encode($out, JSON_PRETTY_PRINT);
        $stato = 'successo';
    }
} catch (\Throwable $th) {
    $stato = 'errore';
}

//JSON -> XML
try {
    if ($_POST['inizio'] == 'json') {
        $array = json_decode($codice, TRUE);
        $xml_data = new SimpleXMLElement('<?xml version="1.0"?><data></data>');
        array_to_xml($array, $xml_data);
        $codice_update = str_replace(' ', '  ', prettyPrintXmlToBrowser($xml_data));
        $stato = 'successo';
    }
} catch (\Throwable $th) {
    $stato = 'errore';
}

//ritorno chiamata ajax
$ajax_return = array(
    "stato" => $stato,
    "codice_update" => $codice_update,
);
echo json_encode($ajax_return);
exit();
