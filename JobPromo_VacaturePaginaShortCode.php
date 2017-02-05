<?php
include_once('JobPromo_ShortCodeLoader.php');

class JobPromo_VacaturePaginaShortCode extends JobPromo_ShortCodeLoader {

    /**
     * @param  $atts shortcode inputs
     * @return string shortcode content
     */
    public function handleShortcode($atts) {

        global $wpdb;

        $options = $wpdb->get_results("
             SELECT * FROM `wp_jp_options`;
             ");

        foreach ($options as $options) {
            $pagina_breedte = $options->pagina_breedte;
            $pagina_links = $options->pagina_links;
            $pagina_rechts = $options->pagina_rechts;
            $pagina_titelgrote = $options->pagina_titelgrote;
            $pagina_tekstgrote = $options->pagina_tekstgrote;
            $pagina_logo = $options->pagina_logo;
            $pagina_map = $options->pagina_map;
            $pagina_background = $options->pagina_background;
            $pagina_hoek = $options->pagina_hoek;
            $pagina_padding = $options->pagina_padding;
            $pagina_trans = $options->pagina_trans;
            $pagina_design = $options->pagina_design;

            $tabel = $options->tabel;
            $tabel_referentie = $options->tabel_referentie;
            $tabel_dienstverband = $options->tabel_dienstverband;
            $tabel_werklocatie = $options->tabel_werklocatie;
            $tabel_branche = $options->tabel_branche;
            $tabel_beroep = $options->tabel_beroep;
            $tabel_salaris = $options->tabel_salaris;
            $tabel_startdatum = $options->tabel_startdatum;
            $tabel_niveau = $options->tabel_niveau;
        }

        $result = $wpdb->get_results("
             SELECT * FROM `wp_jp_vacatures` WHERE referentie='" . $_GET['ref'] . "';
             ");

        if ($pagina_trans == 1) {
            $trans = 0.5;
        } else {
            $trans = 1;
        }

        foreach ($result as $result) {
            
            
            
            echo '<img src="' . $result->url_pixel . '" style="height:1px; width:1px; " />';

            $beschikbaar_vanaf = date('d-m-y', $result->beschikbaar_vanaf);
            $geplaatst_op = date('d-m-y', $result->geplaatst_op);
            if ($result->marktconform = 1) {
                $salaris = "Marktconform";
            } else {
                $salaris = "Minimaal " . $result->minimale_vergoeding . " tot maximaal " . $result->minimale_vergoeding;
            }

            if ($result->per_direct = 1) {
                $beschikbaar_vanaf = "Per direct";
            } else {
                $beschikbaar_vanaf = date('d-m-y', $result->beschikbaar_vanaf);
            }

            if ($result->uren_van == $result->uren_tot) {
                $dienstverband = $result->uren_van . " uur per week";
            } else {
                $dienstverband = $result->uren_van . " tot " . $result->uren_tot . " uur per week";
            }
            
            if ($tabel_referentie == 1 && !empty($result->referentie)) {
                $referentie = '<b>  Referentie: </b>' . $result->referentie. '&nbsp&nbsp';
            }
            
            if ($tabel_dienstverband == 1 && !empty($dienstverband)) {
                $dienstverbandstring = '<b>  Dienstverband: </b>' . $dienstverband. '&nbsp&nbsp';
            }
            
            if ($tabel_werklocatie == 1 && !empty($result->plaats)) {
                $werklocatie = '<b>  Werklocatie: </b>' . $result->plaats. '&nbsp&nbsp';
            }
            
            if ($tabel_branche == 1 && !empty($result->branche)) {
                $branche = '<b>  Branche: </b>' . $result->branche. '&nbsp&nbsp';
            }
            
            if ($tabel_beroep == 1 && !empty($result->vakgebied)) {
                $beroep = '<b>  Vakgebied: </b>' . $result->vakgebied. '&nbsp&nbsp';
            }
            
            if ($tabel_salaris == 1 && !empty($salaris)) {
                $salarisstring = '<b>  Salaris: </b>' . $salaris. '&nbsp&nbsp';
            }
            
            if ($tabel_startdatum == 1 && !empty($beschikbaar_vanaf)) {
                $beschikbaar = '<b>  Startdatum: </b>' . $beschikbaar_vanaf. '&nbsp&nbsp';
            }
            
            if ($tabel_niveau == 1 && !empty($result->niveau)) {
                $niveau = '<b>  Niveau: </b>' . $result->niveau . '&nbsp&nbsp';
            }
            
            
            echo '<img height="1px" width="1px" src="url_pixel">';

            if ($pagina_design == 1) {

                echo '<table style="' . (!empty($pagina_background) ? 'background-color:rgba(' . $pagina_background . ',' . $trans . ') !important; ' . ($pagina_hoek == 1 ? 'border-radius: 15px;' : '') . '  padding:' . $pagina_padding . 'px;' : '') . '; width:' . $pagina_breedte . 'px; margin-left:-' . $pagina_links . 'px; margin-right:-' . $pagina_rechts . 'px;"><tr><td>';
                echo '<div style=" width:' . ($pagina_breedte - 250) . 'px; ' . (!empty($pagina_background) ? 'background-color:transparent !important; padding:15px;' : '') . '">'; //WRAPPER
                if ($pagina_logo == 1)
                    echo '<img src="' . $result->url_logo . '" style="width:; "><br /><br /> ';
                echo '<span style="font-size:15px; float:right; margin-top:10px;"><b>' . $geplaatst_op . '</b></span>';
                echo '<span style="font-weight: bold; font-size:' . $pagina_titelgrote . 'px;">' . $result->functietitel . '</span><br /><br />';
                echo '<span style="font-size:' . $pagina_tekstgrote . 'px;">' . $result->omschrijving . '</span><br />';
                echo '<span style="font-size:' . $pagina_tekstgrote . 'px;">' . $result->contact_omschrijving . '</span>';
                echo '<br/><br/><a style="margin-left:40%;" href="' . $result->url_sollicitatie . '" class="myButton">Solliciteer</a>';
                echo '</div>'; //Sluit wrapper af
                echo '</td><td><div style="width:1px; height:650px; background-color:grey; margin-top:20px; margin-right:10px;"></div></td><td>';
                echo '<div style="width:190px;">';
                echo '<br/><br/><a href="' . $result->url_sollicitatie . '" class="myButton">Solliciteer</a><br/><br/>';


                if ($pagina_map == 1){
                echo '<span style="font-size:' . $pagina_titelgrote . 'px";><b>Standplaats<b/></span><div id="map_canvas" style="border:solid 1px black; width:150px; height:225px; margin-left:0px;"></div>';}

                if ($tabel == 1) {
                    echo '<br/><br/><table style="font-size:' . $pagina_tekstgrote . 'px;' . (!empty($pagina_background) ? 'background-color: transparent  !important; ' : '') . ' width:150px;">';
                }
                if ($tabel_referentie == 1 && !empty($result->referentie)) {
                    echo '<tr><td><b>Referentie<b/></td></tr>';
                    echo '<tr><td>' . $result->referentie . '<br /><br /></td></tr>';
                }
                if ($tabel_dienstverband == 1 && !empty($dienstverband)) {
                    echo '<tr><td><b>Dienstverband<b/></td></tr>';
                    echo '<tr><td>' . $dienstverband . '<br /><br /></td></tr>';
                }
                if ($tabel_werklocatie == 1 && !empty($result->plaats)) {
                    echo '<tr><td><b>Werklocatie<b/></td></tr>';
                    echo '<tr><td>' . $result->plaats . '<br /><br /></td></tr><br />';
                }
                if ($tabel_branche == 1 && !empty($result->branche)) {
                    echo '<tr><td><b>Branche<b/></td></tr>';
                    echo '<tr><td>' . $result->branche . '<br /><br /></td></tr>';
                }
                if ($tabel_beroep == 1 && !empty($result->vakgebied)) {
                    echo '<tr><td><b>Beroep<b/></td></tr>';
                    echo '<tr><td>' . $result->vakgebied . '<br /><br /></td></tr>';
                }
                if ($tabel_salaris == 1 && !empty($salaris)) {
                    echo '<tr><td><b>Salaris indicatie<b/></td></tr>';
                    echo '<tr><td>' . $salaris . '<br /><br /></td></tr>'; 
                }
                if ($tabel_startdatum == 1 && !empty($beschikbaar_vanaf)) {
                    echo '<tr><td><b>Startdatum<b/></td></tr>';
                    echo '<tr><td>' . $beschikbaar_vanaf . '<br /><br /></td></tr>';
                }
                if ($tabel_niveau == 1 && !empty($result->niveau)) {
                    echo '<tr><td><b>Niveau<b/></td></tr>';
                    echo '<tr><td>' . $result->niveau . '<br /><br /></td></tr>';
                }
                if ($tabel == 1) {
                    echo '</table>';
                }
                echo '</div>';
                echo '<span style="font-size:' . $pagina_titelgrote . 'px";><b>Deel vacature<b/></span><br/><a rel="Deel deze vacature!" href="http://www.facebook.com/share.php?u=' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="tips" target="_blank"><img class="share_img" src="http://www.eenvacaturebij.nl/images/icon-facebook.gif" alt="" title=""></a>
                <a rel="Deel deze vacature!" href="https://www.linkedin.com/shareArticle?mini=true&url=' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&title=' . utf8_encode($result->functietitel) . '&summary=&source=" class="tips" target="_blank"><img class="share_img" src="http://www.eenvacaturebij.nl/images/icon-linkedin.gif" alt="" title=""></a>
                <a rel="Deel deze vacature!" href="http://twitter.com/home/?source=eenvacaturebij.nl&amp;status=Vacature:+' . utf8_encode($result->functietitel) . ', ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="tips" target="_blank"><img class="share_img" src="http://www.eenvacaturebij.nl/images/icon-twitter.gif" alt="" title=""></a>';
                echo '</td></tr></table>';
            }

            if ($pagina_design == 0) {
                echo '<div style="' . (!empty($pagina_background) ? 'background-color:rgba(' . $pagina_background . ',' . $trans . ') !important; ' . ($pagina_hoek == 1 ? 'border-radius: 15px;' : '') . '  padding:' . $pagina_padding . 'px;' : '') . '; width:' . $pagina_breedte . 'px;">'; //main wrapper
                
                echo '<span style="font-size:15px; margin-top:10px;">' . $geplaatst_op . '</span><br/>';
                echo '<a rel="Deel deze vacature!" href="http://www.facebook.com/share.php?u=' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="tips" target="_blank"><img class="share_img" src="http://www.eenvacaturebij.nl/images/icon-facebook.gif" alt="" title=""></a>
                <a style="margin-left:-5px;" rel="Deel deze vacature!" href="https://www.linkedin.com/shareArticle?mini=true&url=' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&title=' . utf8_encode($result->functietitel) . '&summary=&source=" class="tips" target="_blank"><img class="share_img" src="http://www.eenvacaturebij.nl/images/icon-linkedin.gif" alt="" title=""></a>
                <a style="margin-left:-5px;" rel="Deel deze vacature!" href="http://twitter.com/home/?source=eenvacaturebij.nl&amp;status=Vacature:+' . utf8_encode($result->functietitel) . ', ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="tips" target="_blank"><img class="share_img" src="http://www.eenvacaturebij.nl/images/icon-twitter.gif" alt="" title=""></a><br/>';
                if ($pagina_logo == 1){
                echo '<img style="float:right;" src="' . $result->url_logo . '"><br/>';}
                echo '<div width="100%" height="100px"><span style="font-weight: bold; font-size:' . $pagina_titelgrote . 'px; line-height:' . $pagina_titelgrote . 'px;">' . $result->functietitel . '</span><br/>';
                
                echo '<br/></div>';
                echo $referentie . $dienstverbandstring . $werklocatie . $branche . $beroep . $salarisstring . $beschikbaar . $niveau . '<br/><br/><br/>';//1e blok
                if ($pagina_map == 1){
                echo '<div id="map_canvas" style="width:auto; height:150px; margin-left:0px;"></div>';} //2e blok
                echo '<div>'; //3e blok begin
                echo '<br/><br/><a style="margin-left:;" href="' . $result->url_sollicitatie . '" class="myButton">Solliciteer</a><br/><br/><br/>';
                echo '<span style="font-size:' . $pagina_tekstgrote . 'px;">' . $result->omschrijving . '</span><br />';
                echo '<span style="font-size:' . $pagina_tekstgrote . 'px;">' . $result->contact_omschrijving . '</span><br/><br/>';
                echo '<br/><br/><a style="margin-left:;" href="' . $result->url_sollicitatie . '" class="myButton" style="padding:6px 288px; height:6px; width:288px;">Solliciteer</a><br/><br/>';
                echo '</div>'; //3e blok eind
                echo '<div>  </div>'; //4e blok
                echo '</div>';// einde wrapper
            }
        }
        ?>
        <style>
            .myButton {
                -moz-box-shadow:inset 0px 1px 0px 0px #ffffff;
                -webkit-box-shadow:inset 0px 1px 0px 0px #ffffff;
                box-shadow:inset 0px 1px 0px 0px #ffffff;
                background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #ededed), color-stop(1, #dfdfdf));
                background:-moz-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
                background:-webkit-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
                background:-o-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
                background:-ms-linear-gradient(top, #ededed 5%, #dfdfdf 100%);
                background:linear-gradient(to bottom, #ededed 5%, #dfdfdf 100%);
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#ededed', endColorstr='#dfdfdf',GradientType=0);
                background-color:#ededed;
                -moz-border-radius:6px;
                -webkit-border-radius:6px;
                border-radius:6px;
                border:1px solid #dcdcdc;
                display:inline-block;
                cursor:pointer;
                color:#777777;
                font-family:arial;
                font-size:15px;
                font-weight:bold;
                padding:6px 44%;
                text-decoration:none;
                text-shadow:0px 1px 0px #ffffff;
                //margin-left:1%;
            }
            .myButton:hover {
                background:-webkit-gradient(linear, left top, left bottom, color-stop(0.05, #dfdfdf), color-stop(1, #ededed));
                background:-moz-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
                background:-webkit-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
                background:-o-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
                background:-ms-linear-gradient(top, #dfdfdf 5%, #ededed 100%);
                background:linear-gradient(to bottom, #dfdfdf 5%, #ededed 100%);
                filter:progid:DXImageTransform.Microsoft.gradient(startColorstr='#dfdfdf', endColorstr='#ededed',GradientType=0);
                background-color:#dfdfdf;
            }
            .myButton:active {
                position:relative;
                top:1px;
            }


        </style>
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false&key=AIzaSyCHVBDUI7nmSyUq5a7HgGfYZxLCUmOW-ms"></script>
            <script type="text/javascript">
                var geocoder;
                var map;

                function initialize()
                {
                    geocoder = new google.maps.Geocoder();
                    var myOptions = {zoom: 9, mapTypeId: google.maps.MapTypeId.ROADMAP};
                    map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

                    loc = new google.maps.LatLng(<?php echo $result->latitude_coordinatie . ',' . $result->longitude_coordinatie; ?>);
                    map.setCenter(loc);
                    var image = 'http://www.eenvacaturebij.nl/images/job.png';

                    var marker = new google.maps.Marker({
                        map: map,
                        position: loc,
                        icon: image
                    });
                }
                initialize();
            </script>
        <?php
    }

}
?>