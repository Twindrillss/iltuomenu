<?php



function visualizzapdfcorrente($wpdb,$etichetta) {


$query = "SELECT * FROM {$wpdb->prefix}listapdf";
$datacorrenteunix = strtotime(date('Y-m-d'));

$query = "SELECT * FROM {$wpdb->prefix}listapdf";

$results = $wpdb->get_results($query);


$controllo = false;
$selezionesenzafine = false;

foreach ($results as $result) {

 


$dataconfrontoiniziounix = strtotime($result->data_partenza);
$dataconfrontofineunix = strtotime($result->data_fine);

$hafine = $result->hafine;


if ($hafine){

  if (!$selezionesenzafine){
if ($datacorrenteunix <= $dataconfrontofineunix){
    if ($datacorrenteunix >= $dataconfrontoiniziounix){
        $controllo = true;
    $nomefileprescelto = $result->nome_file;
    $urlfileprescelto = $result->url_completo;
    }
}
  }
} else {
  $controllo = true;
  $selezionesenzafine = true;
  $nomefileprescelto = $result->nome_file;
  $urlfileprescelto = $result->url_completo;
  
}


}

if ($controllo){
    $cartelle = dirname(__FILE__);
    $ultimacartella = basename($cartelle);

    $urlcomp = site_url().'/wp-content/plugins/'.$ultimacartella.'/menu/';

    $finalurl = $urlcomp.$urlfileprescelto;

    if (!is_string($etichetta)){
    $contenutohtml = generahtmlmodal($finalurl);
  } else {
    $contenutohtml = generahtmlmodalridotto($finalurl,$etichetta);
  }
    return array("nome"=>$nomefileprescelto, "url"=>$finalurl,'html'=>$contenutohtml,'controllo'=>$controllo);
} else {
   return array ('controllo'=>$controllo);
}


}


function generacssmodal (){

    return '<style>

    .menu-pdf-container {

        max-height: 100%;
        width: 100%;
        margin-bottom: 20px;
		text-align:center;
      }
      
@media only screen and (max-width: 600px) {
canvas {
margin-top:50px;
}

.button-container {
    display: flex;
    justify-content: space-between;
    padding: 10px;
	
}

}

@media only screen and (min-width: 601px) {

.button-container {
text-align:center;
margin-top:15px;
}

}
        
    .menu-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.7);
        justify-content: center;
        align-items: center;
      }

      @media only screen and (max-width: 600px) {
      .menu-modal-content {
      width:100%;
      }
	}

   @media only screen and (min-width: 600px) {
   .menu-modal-content {
   width:80%;
   }
}
  
      /* Contenuto modal */
      .menu-modal-content {
        background-color: #fff;
        height: 100%;
        position: relative;
		
      }
  
      /* Tasto chiudi */
      .close {
      background-color:black;
      padding:10px;
        position: absolute;
        top: 10px;
        right: 0px;
        font-size: 20px;
        cursor: pointer;
        color: white;
      }

      .bottonebello {
        background-color: #04AA6D; /* Green */
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        -webkit-transition-duration: 0.4s; /* Safari */
        transition-duration: 0.4s;
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
      }
	  
	   #prev {
	   padding: 10px;
        background-color: #04AA6D; /* Green */
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        -webkit-transition-duration: 0.4s; /* Safari */
        transition-duration: 0.4s;
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
      }
	  
	  	   #next {
	  padding: 10px;
        background-color: #04AA6D; /* Green */
        border: none;
        color: white;
        padding: 15px 32px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        -webkit-transition-duration: 0.4s; /* Safari */
        transition-duration: 0.4s;
        box-shadow: 0 8px 16px 0 rgba(0,0,0,0.2), 0 6px 20px 0 rgba(0,0,0,0.19);
      }
      
      .bottonebello:hover {
        box-shadow: 0 12px 16px 0 rgba(0,0,0,0.24),0 17px 50px 0 rgba(0,0,0,0.19);
      }
        
        
        
        </style>
        
';

}

function generajsmodal() {


    return '
    <script>
    // Open the modal
    function openModal() {
      document.getElementById("pdfModal").style.display = "flex";
    }
  
    // Close the modal
    function closeModal() {
      document.getElementById("pdfModal").style.display = "none";
    }
  </script>

  <script>
  
  document.addEventListener("DOMContentLoaded", function() {
     
      var elements = document.querySelectorAll(".is-layout-constrained");

      elements.forEach(function(element) {
          element.classList.remove("is-layout-constrained");
      });
  });
  
  
    </script>
	
	<script>
	
	 function stopScrollPropagation(event) {
    // Questo evita che lo scroll sul modale vada sulla pagina
    event.stopPropagation();
  }
	
	</script>
  
  ';

}

function generahtmlmodal($filepdf){

    $cartelle = dirname(__FILE__);
    $ultimacartella = basename($cartelle);

    $urlcomp = site_url().'/wp-content/plugins/'.$ultimacartella.'/';
    
return '
<div style="text-align:center;z-index:100;">
<button class="bottonebello" onclick="openModal()">Apri il Menu</button>
</div>
<!-- Modale PDF -->
  <div style="background-color:white;" id="pdfModal" class="menu-modal" onscroll="stopScrollPropagation(event)">
    <div class="menu-modal-content">
      <!-- Close button -->
      <span class="close" onclick="closeModal()">&times;</span>
      
      <!-- METTERE QUI IL VISUALIZZATORE -->

      <script src="'.$urlcomp.'pdf.js" type="module"></script>
      
      <script type="module">
        // If absolute URL from the remote server is provided, configure the CORS
        // header on that server.
        var url = "'.$filepdf.'";
      
        // Loaded via <script> tag, create shortcut to access PDF.js exports.
        var { pdfjsLib } = globalThis;
      
        // The workerSrc property shall be specified.
        pdfjsLib.GlobalWorkerOptions.workerSrc = "'.$urlcomp.'pdf.worker.js";
      
        var pdfDoc = null,
            pageNum = 1,
            pageRendering = false,
            pageNumPending = null,
            scale = 3.0,
            canvas = document.getElementById("the-canvas"),
            ctx = canvas.getContext("2d");
      
        /**
         * Get page info from document, resize canvas accordingly, and render page.
         * @param num Page number.
         */
        function renderPage(num) {
          pageRendering = true;
          // Using promise to fetch the page
          pdfDoc.getPage(num).then(function(page) {
            var viewport = page.getViewport({scale: scale});
            canvas.height = viewport.height;
            canvas.width = viewport.width;
      
            // Render PDF page into canvas context
            var renderContext = {
              canvasContext: ctx,
              viewport: viewport
            };
            var renderTask = page.render(renderContext);
      
            // Wait for rendering to finish
            renderTask.promise.then(function() {
              pageRendering = false;
              if (pageNumPending !== null) {
                // New page rendering is pending
                renderPage(pageNumPending);
                pageNumPending = null;
              }
            });
          });
      
          // Update page counters
          document.getElementById("page_num").textContent = num;
        }
      
        /**
         * If another page rendering in progress, waits until the rendering is
         * finised. Otherwise, executes rendering immediately.
         */
        function queueRenderPage(num) {
          if (pageRendering) {
            pageNumPending = num;
          } else {
            renderPage(num);
          }
        }
      
        /**
         * Displays previous page.
         */
        function onPrevPage() {
          if (pageNum <= 1) {
            return;
          }
          pageNum--;
          queueRenderPage(pageNum);
        }
        document.getElementById("prev").addEventListener("click", onPrevPage);
      
        /**
         * Displays next page.
         */
        function onNextPage() {
          if (pageNum >= pdfDoc.numPages) {
            return;
          }
          pageNum++;
          queueRenderPage(pageNum);
        }
        document.getElementById("next").addEventListener("click", onNextPage);
      
        /**
         * Asynchronously downloads PDF.
         */
        pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
          pdfDoc = pdfDoc_;
          document.getElementById("page_count").textContent = pdfDoc.numPages;
      
          // Initial/first page rendering
          renderPage(pageNum);
        });
      </script>
      
      <div class="menu-pdf-container" style="overflow:scroll;">
      <canvas style="width:100%;" id="the-canvas"></canvas>
      
      <div class="button-container">
        <button id="prev">Indietro</button>
        <button id="next">Avanti</button>
        </div>
        
        <br>
        <div style="text-align:center;">
        <span>Pagina: <span id="page_num"></span> / <span id="page_count"></span></span>
      </div>
            
          </div>  <!-- FINE VISUALIZZATORE -->
      
      
  
    </div>
  </div>

';

}

function generahtmlmodalridotto($filepdf,$etichetta){

    $cartelle = dirname(__FILE__);
    $ultimacartella = basename($cartelle);

    $urlcomp = site_url().'/wp-content/plugins/'.$ultimacartella.'/';
    
  return '
  <div style="z-index:5000;">
  <div style="text-align:center;margin-bottom:12px;"><a style="font-size:20px;text-transform: uppercase;cursor: pointer;" onclick="openModal()">'.$etichetta.'</a></div>
  <!-- Modale PDF -->
  <div style="background-color:white;" id="pdfModal" class="menu-modal" onscroll="stopScrollPropagation(event)">
    <div class="menu-modal-content">
      <!-- Close button -->
      <span class="close" onclick="closeModal()">&times;</span>
      
	  <!-- METTERE QUI IL VISUALIZZATORE -->

<script src="'.$urlcomp.'pdf.js" type="module"></script>

<script type="module">
  // If absolute URL from the remote server is provided, configure the CORS
  // header on that server.
  var url = "'.$filepdf.'";

  // Loaded via <script> tag, create shortcut to access PDF.js exports.
  var { pdfjsLib } = globalThis;

  // The workerSrc property shall be specified.
  pdfjsLib.GlobalWorkerOptions.workerSrc = "'.$urlcomp.'pdf.worker.js";

  var pdfDoc = null,
      pageNum = 1,
      pageRendering = false,
      pageNumPending = null,
      scale = 3.0,
      canvas = document.getElementById("the-canvas"),
      ctx = canvas.getContext("2d");

  /**
   * Get page info from document, resize canvas accordingly, and render page.
   * @param num Page number.
   */
  function renderPage(num) {
    pageRendering = true;
    // Using promise to fetch the page
    pdfDoc.getPage(num).then(function(page) {
      var viewport = page.getViewport({scale: scale});
      canvas.height = viewport.height;
      canvas.width = viewport.width;

      // Render PDF page into canvas context
      var renderContext = {
        canvasContext: ctx,
        viewport: viewport
      };
      var renderTask = page.render(renderContext);

      // Wait for rendering to finish
      renderTask.promise.then(function() {
        pageRendering = false;
        if (pageNumPending !== null) {
          // New page rendering is pending
          renderPage(pageNumPending);
          pageNumPending = null;
        }
      });
    });

    // Update page counters
    document.getElementById("page_num").textContent = num;
  }

  /**
   * If another page rendering in progress, waits until the rendering is
   * finised. Otherwise, executes rendering immediately.
   */
  function queueRenderPage(num) {
    if (pageRendering) {
      pageNumPending = num;
    } else {
      renderPage(num);
    }
  }

  /**
   * Displays previous page.
   */
  function onPrevPage() {
    if (pageNum <= 1) {
      return;
    }
    pageNum--;
    queueRenderPage(pageNum);
  }
  document.getElementById("prev").addEventListener("click", onPrevPage);

  /**
   * Displays next page.
   */
  function onNextPage() {
    if (pageNum >= pdfDoc.numPages) {
      return;
    }
    pageNum++;
    queueRenderPage(pageNum);
  }
  document.getElementById("next").addEventListener("click", onNextPage);

  /**
   * Asynchronously downloads PDF.
   */
  pdfjsLib.getDocument(url).promise.then(function(pdfDoc_) {
    pdfDoc = pdfDoc_;
    document.getElementById("page_count").textContent = pdfDoc.numPages;

    // Initial/first page rendering
    renderPage(pageNum);
  });
</script>

<div class="menu-pdf-container" style="overflow-y:scroll;overflow-z:hidden;">
<canvas style="width:100%;" id="the-canvas"></canvas>

<div class="button-container">
  <button id="prev">Indietro</button>
  <button id="next">Avanti</button>
  </div>
  
  <br>
  <div style="text-align:center;">
  <span>Pagina: <span id="page_num"></span> / <span id="page_count"></span></span>
</div>
	  
	</div>  <!-- FINE VISUALIZZATORE -->
      
      
  
    </div>
  </div>
  </div>
  ';
  
  }
