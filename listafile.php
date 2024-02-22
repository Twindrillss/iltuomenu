<style>

table {
    margin-top:50px;
    border-collapse: collapse !important;
    border-spacing: 0 !important;
    width: 100% !important;
    border: 1px solid #ddd !important;
    background-color:white;
  }
  
  th, td {
    text-align: left !important;
    padding: 8px !important;
  }
  
  tr:nth-child(even){background-color: #f2f2f2 !important}


  @media only screen and (min-width: 600px) {

  .dacapospec {
display:none;
  }

  .selettore {
    display:inline-block;
  }
  .pos {
    display:inline-block;
  }

}


@media only screen and (max-width: 600px) { 
    .selettore {
    display:block;
  }

  .pos {
    text-align:center;
    display:block;
  }
    
}

  </style>

<?php


//caricamento globale per db wordpress per tutte le operazioni
global $wpdb;

//impostazione variabile per tutte le operazioni di lettura/scrittura su directory

$cartelle = dirname(__FILE__);
$ultimacartella = basename($cartelle);

$target_dir = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/".$ultimacartella."/menu/";




?>


<?php

// QUI MI CONTROLLO L'ELIMINAZIONE

if (isset($_GET['elimina'])){
    $datoelimina = $_GET['elimina'];

//CARICO E CONTROLLO DAL DATABASE

// QUERY SUL DATABASE
$query = "SELECT * FROM {$wpdb->prefix}listapdf WHERE id='$datoelimina'";

// ESECUZIONE QUERY
$results = $wpdb->get_results($query);

foreach ($results as $result) {

  $urlfile = $result->url_completo;

}


//ELIMINO IL FILE DALLA DIRECTORY
unlink($target_dir.$urlfile);

// QUERY SUL DATABASE PER ELIMINAZIONE RIFERIMENTO FILE
$query = "DELETE FROM {$wpdb->prefix}listapdf WHERE id='$datoelimina'";

// ESECUZIONE QUERY
$results = $wpdb->get_results($query);



}

// QUI MI CONTROLLO L'ELIMINAZIONE

?>


<?php
// QUI AVVIENE IL CARICAMENTO DI UN FILE
$esitofile = "";

if(isset($_POST["invia"])) {
  
    

    if (isset($_POST['fineindicata'])){
        $fineindicata = true;
        $campoinizio = $_POST['datainizio'];
        $campofine = $_POST['datafine'];
    } else {
        $fineindicata = false;
        $campoinizio = '0000-00-00';
        $campofine = '0000-00-00';
    }

  if (!empty($campoinizio)&& !empty($campofine)) {
    
   
    
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    $uploadOk = 1;


    // Controlla se il file esiste
if (file_exists($target_file)) {
  $esitofile = 'Esiste già un file con questo nome. Scegliere un altro nome oppure eliminare quello esistente dalla lista.';
  $uploadOk = 0;
}

if ($uploadOk){
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $nomefile = htmlspecialchars( basename( $_FILES["fileToUpload"]["name"]));
        $esitofile = "Il file ". $nomefile . " è stato caricato.";

        // QUERY SUL DATABASE PER INSERIMENTO FILE

        

$query = "INSERT INTO {$wpdb->prefix}listapdf (nome_file, url_completo, hafine, data_partenza, data_fine) VALUES ('$nomefile','$nomefile','$fineindicata','$campoinizio','$campofine')";

// ESECUZIONE QUERY
$results = $wpdb->get_results($query);

    }else {
        $esitofile = 'file non caricato';
    }
}
  } else {
    $esitofile = 'Assicurati di aver compilato tutti i campi';
  }
}

// QUI AVVIENE IL CARICAMENTO DI UN FILE
?>



<?php 

// QUERY SUL DATABASE
$query = "SELECT * FROM {$wpdb->prefix}listapdf";

// ESECUZIONE QUERY
$results = $wpdb->get_results($query);

?>

<hr>

<h2>Carica un nuovo menu</h2>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=iei-menu-dashboard" method="post" enctype="multipart/form-data">

<div class="pos">
  <label for="pdfFile">Scegli file PDF:</label>
  <input type="file" id="fileToUpload" name="fileToUpload" accept="application/pdf">
</div>
  
  <div class="selettore" style="border-width:2px; border-style:solid; border-color:#dcdcde; border-radius:10px; padding:10px; text-align:center;">
  <input onclick="statocheckbox()" type="checkbox" id="fineindicata" name="fineindicata" value="1">
  <label for="fineindicata"> Questo menu ha una scadenza</label><br>
<p style="margin-top:15px;">
  <label for="datainizio">Imposta data inizio</label>
  <input type="date" id="datainizio" name="datainizio">
<br class="dacapospec">
  <label for="datafine">Imposta data fine</label>
  <input type="date" id="datafine" name="datafine">
</p>
</div>
<div class="pos">
  <input type="submit" name="invia" value="Carica">
</div>
</form>

<p style="color:red;font-weight:bold;"><?php echo $esitofile; ?></p>

<hr>


<?php 


$label_stato_menu = '';

if (isset($_POST['invia2'])){
    if (isset($_POST['stato'])){

        scrivisujson ('yes');
        
        $label_stato_menu = 'menu attivo';
    } else {
       
        scrivisujson ('no');
         $label_stato_menu = 'menu disattivato';
    }
    
}


?>






<h2>Attiva/Disattiva</h2>
<div class="selettore" style="border-width:2px; border-style:solid; border-color:#dcdcde; border-radius:10px; padding:10px; text-align:center;">
<form action="<?php echo $_SERVER['PHP_SELF']; ?>?page=iei-menu-dashboard" method="post" enctype="multipart/form-data">




<?php
if (controllamenu()){
?>
<input type="checkbox" id="stato" name="stato" value="1" checked>
<?php } else { ?>
<input type="checkbox" id="stato" name="stato" value="1">
<?php } ?>

  <label for="stato"> ATTIVA IL MENU E RELATIVE POSIZIONI</label><br>
  <input style="display:none" type="submit" id="invia2" name="invia2" value="Cambia">
</form>
</div>

<p style="color:red;font-weight:bold;"><?php echo $label_stato_menu; ?></p>

<hr>

<div style="overflow-x:auto;">

<table>

<tr>
    <th>Nome del File</th>
    <th>Data Partenza</th>
    <th>Data Fine</th>
    <th>/</th>
</tr>

<?php

foreach ($results as $result) {

    echo '<tr><td>'.$result->nome_file.'</td>';


    if ($result->hafine){
        echo '<td>'.$result->data_partenza.'</td>';

        echo '<td>'.$result->data_fine.'</td>';
    } else {


        echo '<td> -- </td>';

        echo '<td> -- </td>';

    }


    

    echo '<td><button onclick="elimina('.$result->id.')">elimina</button></td></tr>';

}

?>


</table>



</div>
<div id="infobox" style="margin-top:30px;">
<?php
$etichetta = 0;
$arrayrisultati = visualizzapdfcorrente($wpdb,$etichetta);

if ($arrayrisultati['controllo']){

echo '<b>In questo momento il menu attivo è: '.$arrayrisultati['nome'].'</b><br>';
echo '<b>Per vederlo nel browser clicca qui: <a target="_blank" href="'.$arrayrisultati['url'].'">VEDI MENU PDF</a>';
} else {
  echo '<b>Non esiste un menu attivo in questo momento, controlla le date dei file caricati oppure carica un nuovo file.</b>';
}
?>
<br><br>
<i>Nota: se si inserisce un menu SENZA scadenza, quelli CON scadenza non verranno mai presi in condiserazione.</i><br>
<i>Nota sul comportamento: in caso di conflitto, verrà preferito sempre il menu caricato più recentemente.</i>
</div>
<script>

    statocheckbox();

function elimina(elemento){
    updateURLParameter('elimina', elemento);
}


function updateURLParameter(key, value) {
  // Get the current URL
  var url = window.location.href;

  // Check if the parameter already exists in the URL
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  var separator = url.indexOf('?') !== -1 ? "&" : "?";

  if (url.match(re)) {
    // If the parameter exists, remove it
    url = url.replace(re, '$1' + key + "="+ value +'$2');
  } else {
    // If the parameter doesn't exist, add it
    url = url + separator + key + '=' + value;
  }

  // Refresh the page with the updated URL
  window.location.href = url;
}


function statocheckbox () {


    if (document.getElementById("fineindicata").checked){
        // alert ("checkata");
        document.getElementById('datainizio').disabled = false;
        document.getElementById('datafine').disabled = false;
    } else {
        document.getElementById('datainizio').disabled = true;
        document.getElementById('datafine').disabled = true;
        // alert ("non checkata");
    }

}


    // Questa funzione manda il form di controllo attivazione
    function submitForm() {
        document.getElementById('invia2').click();
    }

    // Add event listener to the checkbox
    document.getElementById('stato').addEventListener('change', function() {
        
            // If checkbox is checked, submit the form
            submitForm();
        
    });

</script>




