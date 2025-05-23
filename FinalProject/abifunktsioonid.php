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
    $kask = "SELECT broneering_id, kliendi_nimi, laud_id, kuupaev, kellaaeg, inimiste_arv FROM broneering";
    $tulemus = $yhendus->query($kask);
    return $tulemus->fetch_all(MYSQLI_ASSOC);
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
function looRippMenyy($sql, $name) {
    global $yhendus;
    $stmt = $yhendus->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $html = "<select name='$name' required>";
    while ($row = $result->fetch_assoc()) {
        $html .= "<option value='" . $row[array_keys($row)[0]] . "'>" . $row[array_keys($row)[1]] . "</option>";
    }
    $html .= "</select>";

    $stmt->close();
    return $html;
}
?>
