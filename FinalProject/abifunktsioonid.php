<?php
require_once("SRVconf.php");

function kysiLaudadeAndmed() {
    global $yhendus;
    $paring = $yhendus->prepare("SELECT laud_id, istekohtade_arv, asukoht FROM laud");
    $paring->execute();
    $tulemus = $paring->get_result();
    $laudadeAndmed = [];
    while ($rida = $tulemus->fetch_object()) {
        $laudadeAndmed[] = $rida;
    }
    $paring->close();
    return $laudadeAndmed;
}

function lisaBroneering($nimi, $laud_id, $kuupaev, $kellaaeg, $inimeste_arv) {
    global $yhendus;
    $paring = $yhendus->prepare("INSERT INTO broneering (kliendi_nimi, laud_id, kuupaev, kellaaeg, inimiste_arv) VALUES (?, ?, ?, ?, ?)");
    $paring->bind_param("sissi", $nimi, $laud_id, $kuupaev, $kellaaeg, $inimeste_arv);
    $paring->execute();
    $paring->close();
}

function kysiBroneeringud() {
    global $yhendus;
    $stmt = $yhendus->prepare("SELECT b.*, l.istekohtade_arv FROM broneering b JOIN laud l ON b.laud_id = l.laud_id ORDER BY b.kuupaev, b.kellaaeg");
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_all(MYSQLI_ASSOC);
}

function kustutaBroneering($id) {
    global $yhendus;
    $stmt = $yhendus->prepare("DELETE FROM broneering WHERE broneering_id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

function looLaudRippMenyy($sql, $name) {
    global $yhendus;
    $stmt = $yhendus->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    $html = "<select name='$name' required>";
    while ($rida = $result->fetch_assoc()) {
        $html .= "<option value='{$rida['laud_id']}'>Laud {$rida['laud_id']} ({$rida['istekohtade_arv']} kohta)</option>";
    }
    $html .= "</select>";
    return $html;
}

?>
