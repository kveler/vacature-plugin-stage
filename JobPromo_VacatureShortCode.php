<?php

include_once('JobPromo_ShortCodeLoader.php');

class JobPromo_VacatureShortCode extends JobPromo_ShortCodeLoader {

    /**
     * @param  $atts shortcode inputs
     * @return string shortcode content
     */
    public function handleShortcode($atts) {
        global $wpdb;

        

        $result = $wpdb->get_results("
             SELECT * FROM `wp_jp_vacatures` WHERE online='1' ORDER BY topvacature desc;
             ");

        $options = $wpdb->get_results("
             SELECT * FROM `wp_jp_options`;
             ");

        foreach ($options as $options) {
            $lijst_breedte = $options->lijst_breedte;
            $lijst_afstand = $options->lijst_afstand;
            $characters = $options->lijst_characters;
            $lijst_opmaak = $options->lijst_opmaak; // 1 of 0, standaard 0
            $lijst_titelgrote = $options->lijst_titelgrote;
            $lijst_lettergrote = $options->lijst_lettergrote;
            $lijst_links = $options->lijst_links;
            $lijst_rechts = $options->lijst_rechts;
            $pagina_url = $options->pagina_url;
            $lijst_background = $options->lijst_background;
            $lijst_hoek = $options->lijst_hoek;
            $lijst_trans = $options->lijst_trans;
            $lijst_backwidth = $options->lijst_backwidth;
            $lijst_backheight = $options->lijst_backheight;
        }
        
        //WEBSERVICE
        
        if (isset($_POST['webservice']) && $_POST['webservice'] == $_SERVER['HTTP_HOST']) {
            unset($_POST['webservice']);

            $values = array();
            $keys = array();
            $update = array();
            foreach ($_POST AS $k => $v) {
                $values[] = '\''.$v.'\'';
                $keys[] = $k;
                $update[] = $k . '=\'' . $v.'\'';
            }
            $wpdb->query('INSERT INTO wp_jp_vacatures(' . implode(',', $keys) . ') VALUES(' . implode(',', $values) . ') ON DUPLICATE KEY UPDATE ' . implode(',', $update));
            //echo 'INSERT INTO wp_jp_vacatures(' . implode(',', $keys) . ') VALUES(' . implode(',', $values) . ') ON DUPLICATE KEY UPDATE ' . implode(',', $update);
            echo 'jobpromo[' . $_SERVER['HTTP_HOST'] . '/' . $pagina_url . '?ref=' . $_POST['referentie'] . ']jobpromo';
            //exit(0);
        }
        
               
        if($lijst_trans == 1){
            $trans = 0.5;
        }
        else{
            $trans = 1;
        }

        foreach ($result as $result) {

            $kort = substr(strip_tags($result->intro), 0, $characters) . "...";
            $geplaatst_op = date('d-m-y', $result->geplaatst_op);


            echo '<div style="' . (!empty($lijst_background) ? 'background-color:rgba(' . $lijst_background . ',' . $trans . ') !important; ' . ($lijst_hoek == 1 ? 'border-radius: 15px;' : '') . ' padding:20px;' : '') . ' height:' . $lijst_backheight . 'px; width:' . $lijst_backwidth . 'px; margin-left:-' . $lijst_links . 'px; margin-right:-' . $lijst_rechts . 'px; margin-bottom:' . $lijst_afstand . 'px;">'; //wrapper div
            echo '<div style="float:left; width:' . $lijst_breedte . 'px;">'; // linker div
            echo '<a href="/' . $pagina_url . '?ref=' . $result->referentie . '"><span style="font-weight: bold; margin-bottom:-5px; font-size:' . $lijst_titelgrote . 'px; line-height:' . $lijst_titelgrote . 'px;">' . $result->functietitel . '</span></a><br /><br />'; //titel vacature
            echo '<span style="margin-bottom:-5px; font-size:' . $lijst_lettergrote . 'px;">' . $kort . '<br />';  //tekst vacature
            echo '<a href="/' . $pagina_url . '?ref=' . $result->referentie . '" >Lees verder...</a>'; //lees verder naar vacature pagina
            if ($lijst_opmaak == 1) {
                echo '<br /><br /><b>Plaatsdatum: </b>' . $geplaatst_op . '<b> Uren: </b>' . $result->uren_tot . '<b> Plaats: </b>' . $result->plaats . '<b> Niveau: </b>' . $result->niveau . '</span></div>';
            }

            if ($lijst_opmaak == 0) {
                echo '</span></div>';    //sluit linker div af
                echo '<div style="float:right; width:100px; height:auto; margin-right:90px;">'; // rechter div
                echo '<br/><br/><table style="' . (!empty($lijst_background) ? 'background-color:transparent !important; ' : '') . '">';
                if (!empty($geplaatst_op)){ echo '<tr><td style="padding-right:10px;"><b>Plaatsdatum</b></td><td style="min-width:75px;">' . $geplaatst_op . '</td></tr>';} //datum van plaatsen
                if (!empty($result->uren_tot)){ echo '<tr><td><b>Aantal uren</b></td><td>' . $result->uren_tot . '</td></tr>';} //Aantal uren
                if (!empty($result->plaats)){ echo '<tr><td><b>Werklocatie</b></td><td>' . $result->plaats . '</td></tr>';}   //Locatie
                if (!empty($result->niveau)){ echo '<tr><td><b>Niveau</b></td><td>' . $result->niveau . '</td></tr>';}    //werkniveau
                echo '</table></div>'; //sluit rechter div en tabel af
            }

            echo '</div><br />';  //sluit wrapper af
            
            
        }
        if (empty($result)){
                echo 'Geen vacatures beschikbaar';
        }
    }

}

?>