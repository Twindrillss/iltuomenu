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

  </style>

<?php

//caricamento globale per db wordpress per tutte le operazioni
global $wpdb;

//impostazione variabile per tutte le operazioni di lettura/scrittura su directory
$target_dir = $_SERVER['DOCUMENT_ROOT']."/wp-content/plugins/ieimenupdf/menu/";
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
  $campoinizio = $_POST['datainizio'];
    $campofine = $_POST['datafine'];

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
$query = "INSERT INTO {$wpdb->prefix}listapdf (nome_file, url_completo, data_partenza, data_fine) VALUES ('$nomefile','$nomefile','$campoinizio','$campofine')";

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
$urlcomp = get_home_url();
echo '<link rel="stylesheet" href="'.$urlcomp.'/wp-content/plugins/ieimenupdf/elementi/style.css">';

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
  <label for="pdfFile">Scegli file PDF:</label>
  <input type="file" id="fileToUpload" name="fileToUpload" accept="application/pdf">
  
  <label for="datainizio">Imposta data inizio</label>
  <input type="date" id="datainizio" name="datainizio">

  <label for="datafine">Imposta data fine</label>
  <input type="date" id="datafine" name="datafine">

  <input type="submit" name="invia" value="Carica">
</form>

<p style="color:red;font-weight:bold;"><?php echo $esitofile; ?></p>

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

    echo '<td>'.$result->data_partenza.'</td>';

    echo '<td>'.$result->data_fine.'</td>';

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
</div>
<script>

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

</script>




