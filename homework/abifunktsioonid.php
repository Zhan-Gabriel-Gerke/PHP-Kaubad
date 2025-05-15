<?php
require('SRVconf.php');

// Получить все инфо про темп
function kysiTemperatuuriAndmed($sorttulp="kuupaev_kellaaeg", $otsisona=''){
    global $yhendus;
    $lubatudtulbad=array("kuupaev_kellaaeg", "temperatuur", "maakonnanimi");
    if(!in_array($sorttulp, $lubatudtulbad)){
        return "Недопустимый столбец для сортировки";
    }
    $otsisona=addslashes(stripslashes($otsisona));
    $kask=$yhendus->prepare("SELECT ilmatemperatuurid.id, temperatuur, kuupaev_kellaaeg, maakonnanimi
        FROM ilmatemperatuurid, maakonnad
        WHERE ilmatemperatuurid.maakonna_id=maakonnad.id
        AND (maakonnanimi LIKE '%$otsisona%' OR temperatuur LIKE '%$otsisona%')
        ORDER BY $sorttulp");
    $kask->bind_result($id, $temperatuur, $kuupaev_kellaaeg, $maakonnanimi);
    $kask->execute();
    $hoidla=array();
    while($kask->fetch()){
        $temp=new stdClass();
        $temp->id=$id;
        $temp->temperatuur=$temperatuur;
        $temp->kuupaev_kellaaeg=$kuupaev_kellaaeg;
        $temp->maakonnanimi=htmlspecialchars($maakonnanimi);
        array_push($hoidla, $temp);
    }
    return $hoidla;
}

// Добавить уезд
function lisaMaakond($maakonnanimi, $maakonnakeskus){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO maakonnad (maakonnanimi, maakonnakeskus) VALUES (?, ?)");
    $kask->bind_param("ss", $maakonnanimi, $maakonnakeskus);
    $kask->execute();
}

// Добавить запись про темп
function lisaTemperatuur($temperatuur, $kuupaev_kellaaeg, $maakonna_id){
    global $yhendus;
    $kask=$yhendus->prepare("INSERT INTO ilmatemperatuurid (temperatuur, kuupaev_kellaaeg, maakonna_id)
        VALUES (?, ?, ?)");
    $kask->bind_param("dsi", $temperatuur, $kuupaev_kellaaeg, $maakonna_id);
    $kask->execute();
}

// создает рипп меню
function looRippMenyy($sqllause, $valikunimi){
    global $yhendus;
    $kask=$yhendus->prepare($sqllause);
    $kask->bind_result($id, $sisu);
    $kask->execute();
    $tulemus="<select name='$valikunimi'>";
    while($kask->fetch()){
        $tulemus.="<option value='$id'>$sisu</option>";
    }
    $tulemus.="</select>";
    return $tulemus;
}
?>
