<?php

include("funzioni.php");

/**
 * Plugin Name: Iei MenuPdf
 * Description: Crea uno shortcode con il pdf del tuo Menu
 * Version: 0.0.1
 * Text Domain: options-plugin
 */

if (!defined("ABSPATH")) {
    exit; // esci se questo file viene letto direttamente
}

if (!class_exists("ieimenupdf")) {

    class ieimenupdf
    {

        public function __construct()
        {
            add_action('admin_menu', array($this, 'crea_voci'));

            //questo si triggera quando il plugin viene attivato ed aggiunge una nuova tabella nel database

            register_activation_hook(__FILE__, array($this, 'plugin_activation'));

            //questo aggiunge la possibilità di avere uno shortcode

            add_action('init', array($this, 'register_shortcodes'));
            add_action('init', array($this, 'register_shortcodes_a'));
        }


        public function crea_voci()
        {
            // Add main menu entry
            add_menu_page(
                'IEI Menu',
                'Il tuo menu',
                'manage_options',
                'iei-menu-dashboard',
                array($this, 'iei_menu_dashboard_page'),
                'dashicons-food', // Icon for the menu entry (replace with your preferred icon)
                20 // Position on the menu
            );

            add_submenu_page(
                'iei-menu-dashboard',
                'Internet & Idee',
                'Internet & Idee',
                'manage_options',
                'iei-menu-external',
                array($this, 'iei_menu_external_page')
            );
        }

        // Callback functions per menu e submenu del plugin
        public function iei_menu_dashboard_page()
        {
            // Main dashboard page content
            echo '<h2>Il Tuo Menu</h2><br>';
            echo '<br>Per visualizzare il tuo menu in una pagina, usa questo shortcode: <br> <input style="margin-top:10px;" type="text" value="[ieimenupdf_content]"/>';
            include("listafile.php");
        }


        public function iei_menu_external_page()
        {
            echo '<h2>Internet & Idee</h2>';
            echo '<p>Questo plugin è stato sviluppato da <a target="_blank" href="https://internet-idee.net">Internet & Idee</a></p>';
            echo '<img style="width:250px;" src="https://www.internet-idee.net/assets/img/colore.svg"/>';
            // Contenuto
        }


        // LAVORAZIONE PER CREAZIONE TABELLA NEL DATABASE ALL'ATTIVAZIONE DEL PLUGIN


        public function plugin_activation()
        {
            $this->create_database_table();
        }


        public function create_database_table()
        {
            global $wpdb;

            $table_name = $wpdb->prefix . 'listapdf';

            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                $charset_collate = $wpdb->get_charset_collate();

                $sql = "CREATE TABLE $table_name (
                    id mediumint(9) NOT NULL AUTO_INCREMENT,
                    nome_file varchar(255) NOT NULL,
                    url_completo varchar(255) NOT NULL,
                    data_partenza date NOT NULL,
                    data_fine date NOT NULL,
                    PRIMARY KEY (id)
                ) $charset_collate;";

                require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                dbDelta($sql);
            }
        }

        // FINE LAVORAZIONE PER CREAZIONE TABELLA NEL DATABASE


        //GESTIONE SHORTCODE
        public function ieimenupdf_shortcode() {
            // Qui mettere lo shortcode
            global $wpdb;
            $etichetta = 0;
            $arrayrisultati = visualizzapdfcorrente($wpdb,$etichetta);
            if (isset($arrayrisultati)){
            return $arrayrisultati['html'];} else {
                return 'Nessun menu abilitato';
            }
        }

        public function register_shortcodes() {
            add_shortcode('ieimenupdf_content', array($this, 'ieimenupdf_shortcode'));
        }

        //GESTIONE SHORTCODE


            //GESTIONE SHORTCODE TAG A
            public function ieimenupdf_shortcode_a($atts) {

            //Accesso ad array attributi per prendermi il nome etichetta


            $atts = shortcode_atts(
                array(
                    'etichetta' => 'Menu',
                    // Add more attributes if needed
                ),
                $atts,
                'ieimenupdf_content_a'  // Shortcode di riferimento
            );



                // Qui mettere lo shortcode
                global $wpdb;
                $etichetta = $atts['etichetta'];
                $arrayrisultati = visualizzapdfcorrente($wpdb,$etichetta);

                if (isset($arrayrisultati)){
                return $arrayrisultati['html'];} else {
                    return 'Nessun menu abilitato';
                }
            }
    
            public function register_shortcodes_a() {
                add_shortcode('ieimenupdf_content_a', array($this, 'ieimenupdf_shortcode_a'));
            }
    
            //GESTIONE SHORTCODE TAG A
    


    }//FINE CLASSE

    new ieimenupdf();

        // AGGIUNGO CSS A HEADER DI WORDPRESS
function custom_header_content() {
    // Add your custom content here
    $urlcomp = get_home_url().'/wp-content/plugins/ieimenupdf/';
    echo '<script src="'.$urlcomp.'pdf.js"></script>';
    
   
    echo generacssmodal();
   echo generajsmodal();
}
        
    add_action('wp_head', 'custom_header_content');

}
