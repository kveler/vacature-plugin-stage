<?php
include_once('JobPromo_ShortCodeLoader.php');

class JobPromo_SolicitatieShortCode extends JobPromo_ShortCodeLoader {

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

            echo '<img height="1px" width="1px" src="url_pixel">';
            echo '<table style="' . (!empty($pagina_background) ? 'background-color:rgba(' . $pagina_background . ',' . $trans . ') !important; ' . ($pagina_hoek == 1 ? 'border-radius: 15px;' : '') . '  padding:' . $pagina_padding . 'px;' : '') . '; width:' . $pagina_breedte . 'px; margin-left:-' . $pagina_links . 'px; margin-right:-' . $pagina_rechts . 'px;"><tr><td>';
            echo '<div style=" width:' . ($pagina_breedte - 250) . 'px; ' . (!empty($pagina_background) ? 'background-color:transparent !important; padding:15px;' : '') . '">'; //WRAPPER
            //if ($pagina_logo == 1)
            //    echo '<img src="' . $result->url_logo . '" style="width:; "><br /><br /> ';
            //echo '<span style="font-size:15px; float:right; margin-top:10px;"><b>' . $geplaatst_op . '</b></span>';
            //echo '<span style="font-weight: bold; font-size:' . $pagina_titelgrote . 'px;">' . $result->functietitel . '</span><br />';
            //echo '<span style="font-size:' . $pagina_tekstgrote . 'px;">' . $result->omschrijving . '</span><br />';
            //echo '<span style="font-size:' . $pagina_tekstgrote . 'px;">' . $result->contact_omschrijving . '</span>';
            //echo '<br/><br/><a style="margin-left:40%;" href="' . $result->url_sollicitatie . '" class="myButton">Solliciteer</a>';

            

            if (isset($_GET['s']))
                echo 'Succes!';



            $return = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];   // Huidige pagina locatie   (Instellingen voor Opzet)
            $success = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'].'?s=1';  // Success pagina    (Instellingen voor Opzet)
            // Formulier test url (zodra de app live gaat krijgen jullie van mij een uiteindelijke url)
            $url = $result->url_sollicitatie; // (Instelling Job Promo)

if( ! session_id() ) session_start();
            $ch = curl_init($url . 'formhtml=' . session_id() . '&success=' . urlencode($success) . '&return=' . urlencode($return));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            $data = curl_exec($ch);
            echo curl_error($ch);
            curl_close($ch);

            echo $data;
            //echo $url . '?formhtml=' . session_id() . '&success=' . urlencode($success) . '&return=' . urlencode($return);
            

            echo '</div>'; //Sluit wrapper af
            echo '</td><td><div style="width:1px; height:650px; background-color:black; margin-top:20px; margin-right:10px;"></div></td><td>';
                echo '<div style="width:190px;">';
                    //echo '<br/><br/><a href="' . $result->url_sollicitatie . '" class="myButton">Solliciteer</a><br/><br/>';


                    if ($pagina_map == 1)
                    echo '<span style="font-size:' . $pagina_titelgrote . 'px";><b>Standplaats<b/></span><div id="map_canvas" style="border:solid 1px black; width:150px; height:225px; margin-left:0px;"></div>';
                    ?>
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
                        echo '<tr><td>' . $salaris . '<br /><br /></td></tr>'; //NOG NIET GOED
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
                    //echo '<span style="font-size:' . $pagina_titelgrote . 'px";><b>Deel vacature<b/></span><br/><a rel="Deel deze vacature!" href="http://www.facebook.com/share.php?u=' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="tips" target="_blank"><img class="share_img" src="http://www.eenvacaturebij.nl/images/icon-facebook.gif" alt="" title=""></a>
                    //    <a rel="Deel deze vacature!" href="https://www.linkedin.com/shareArticle?mini=true&url=' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&title=' . utf8_encode($result->functietitel) . '&summary=&source=" class="tips" target="_blank"><img class="share_img" src="http://www.eenvacaturebij.nl/images/icon-linkedin.gif" alt="" title=""></a>
                    //    <a rel="Deel deze vacature!" href="http://twitter.com/home/?source=eenvacaturebij.nl&amp;status=Vacature:+' . utf8_encode($result->functietitel) . ', ' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '" class="tips" target="_blank"><img class="share_img" src="http://www.eenvacaturebij.nl/images/icon-twitter.gif" alt="" title=""></a>';
                    echo '</td></tr></table>';
                }
                ?>
                    <style>
                        .cvinvoer{
                            width:175px;
                        }
                    </style>
                <?php
            }

        }
        ?>