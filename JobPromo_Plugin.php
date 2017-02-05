<?php


include_once('JobPromo_LifeCycle.php');

class JobPromo_Plugin extends JobPromo_LifeCycle {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'ATextInput' => array(__('Enter in some text', 'my-awesome-plugin')),
            'Donated' => array(__('I have donated to this plugin', 'my-awesome-plugin'), 'false', 'true'),
            'CanSeeSubmitData' => array(__('Can See Submission data', 'my-awesome-plugin'),
                                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone')
        );
    }

//    protected function getOptionValueI18nString($optionValue) {
//        $i18nValue = parent::getOptionValueI18nString($optionValue);
//        return $i18nValue;
//    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'Job Promo';
    }

    protected function getMainPluginFileName() {
        return 'job-promo.php';
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Called by install() to create any database tables if needed.
     * Best Practice:
     * (1) Prefix all table names with $wpdb->prefix
     * (2) make table names lower case only
     * @return void
     */
    protected function installDatabaseTables() {
        global $wpdb;
        $tableName = $this->prefixTableName('wp_jp_vacatures');
        $wpdb->query("CREATE TABLE IF NOT EXISTS `$tableName` (
        `vacature_id` int(1) NOT NULL,
        `online` int(1) NOT NULL,
        `topvacature` int(1) NOT NULL,
        `referentie` varchar(16) NOT NULL,
        `functietitel` varchar(128) NOT NULL,
        `uren_van` int(11) NOT NULL,
        `uren_tot` int(11) NOT NULL,
        `geplaatst_op` int(11) NOT NULL,
        `vervalt_op` int(11) NOT NULL,
        `beschikbaar_vanaf` int(11) NOT NULL,
        `per_direct` int(1) NOT NULL,
        `niveau` varchar(64) NOT NULL,
        `vakgebied` varchar(64) NOT NULL,
        `branche` varchar(64) NOT NULL,
        `minimale_vergoeding` int(11) NOT NULL,
        `maximale_vergoeding` int(11) NOT NULL,
        `marktconform` int(1) NOT NULL,
        `omschrijving` mediumtext NOT NULL,
        `contact_omschrijving` varchar(32768) NOT NULL,
        `latitude_coordinatie` float NOT NULL,
        `longitude_coordinatie` float NOT NULL,
        `provincie` varchar(64) NOT NULL,
        `plaats` varchar(64) NOT NULL,
        `straatnaam` varchar(64) NOT NULL,
        `postcode` varchar(16) NOT NULL,
        `aanhef` tinyint(4) NOT NULL,
        `contactpersoon` varchar(128) NOT NULL,
        `functie_contactpersoon` varchar(64) NOT NULL,
        `telefoon_contactpersoon` varchar(64) NOT NULL,
        `email_contactpersoon` varchar(64) NOT NULL,
        `website_contact` varchar(128) NOT NULL,
        `trefwoorden` text NOT NULL,
        `url_vacature` varchar(128) NOT NULL,
        `url_solicitatie` varchar(128) NOT NULL,
        `url_logo` varchar(128) NOT NULL,
        `meta_keywords` varchar(1024) NOT NULL,
        `meta_description` varchar(1024) NOT NULL
        PRIMARY KEY (vacature_id)
      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
    }

    /**
     * See: http://plugin.michael-simpson.com/?page_id=101
     * Drop plugin-created tables on uninstall.
     * @return void
     */
    protected function unInstallDatabaseTables() {
        //        global $wpdb;
        //        $tableName = $this->prefixTableName('mytable');
        //        $wpdb->query("DROP TABLE IF EXISTS `$tableName`");
    }


    /**
     * Perform actions when upgrading from version X to version Y
     * See: http://plugin.michael-simpson.com/?page_id=35
     * @return void
     */
    public function upgrade() {
    }

    public function addActionsAndFilters() {

        // Add options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        // Example adding a script & style just for the options administration page
        // http://plugin.michael-simpson.com/?page_id=47
        //        if (strpos($_SERVER['REQUEST_URI'], $this->getSettingsSlug()) !== false) {
        //            wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        //            wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        }


        // Add Actions & Filters
        // http://plugin.michael-simpson.com/?page_id=37


        // Adding scripts & styles to all pages
        // Examples:
        //        wp_enqueue_script('jquery');
        //        wp_enqueue_style('my-style', plugins_url('/css/my-style.css', __FILE__));
        //        wp_enqueue_script('my-script', plugins_url('/js/my-script.js', __FILE__));
        
        //wp_enqueue_script('jQuery', plugins_url('wordpress_stage/wp-content/plugins/job-promo/js/jquery-1.11.1.min', __FILE__));
        
 


        // Register short codes
        // http://plugin.michael-simpson.com/?page_id=39
        
        include_once('JobPromo_VacatureShortCode.php');
	$sc = new JobPromo_VacatureShortCode();
	$sc->register('jp_vacatures');
        
        include_once('JobPromo_VacaturePaginaShortCode.php');
	$sc = new JobPromo_VacaturePaginaShortCode();
	$sc->register('jp_vacature_pagina');
        
        include_once('JobPromo_SolicitatieShortCode.php');
	$sc = new JobPromo_SolicitatieShortCode();
	$sc->register('jp_solicitatie_pagina');


        // Register AJAX hooks
        // http://plugin.michael-simpson.com/?page_id=41

    }


}
