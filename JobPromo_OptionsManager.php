<?php
/*
  "WordPress Plugin Template" Copyright (C) 2014 Michael Simpson  (email : michael.d.simpson@gmail.com)

  This file is part of WordPress Plugin Template for WordPress.

  WordPress Plugin Template is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  WordPress Plugin Template is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with Contact Form to Database Extension.
  If not, see http://www.gnu.org/licenses/gpl-3.0.html
 */

class JobPromo_OptionsManager {

    public function getOptionNamePrefix() {
        return get_class($this) . '_';
    }

    /**
     * Define your options meta data here as an array, where each element in the array
     * @return array of key=>display-name and/or key=>array(display-name, choice1, choice2, ...)
     * key: an option name for the key (this name will be given a prefix when stored in
     * the database to ensure it does not conflict with other plugin options)
     * value: can be one of two things:
     *   (1) string display name for displaying the name of the option to the user on a web page
     *   (2) array where the first element is a display name (as above) and the rest of
     *       the elements are choices of values that the user can select
     * e.g.
     * array(
     *   'item' => 'Item:',             // key => display-name
     *   'rating' => array(             // key => array ( display-name, choice1, choice2, ...)
     *       'CanDoOperationX' => array('Can do Operation X', 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber'),
     *       'Rating:', 'Excellent', 'Good', 'Fair', 'Poor')
     */
    public function getOptionMetaData() {
        return array();
    }

    /**
     * @return array of string name of options
     */
    public function getOptionNames() {
        return array_keys($this->getOptionMetaData());
    }

    /**
     * Override this method to initialize options to default values and save to the database with add_option
     * @return void
     */
    protected function initOptions() {
        
    }

    /**
     * Cleanup: remove all options from the DB
     * @return void
     */
    protected function deleteSavedOptions() {
        $optionMetaData = $this->getOptionMetaData();
        if (is_array($optionMetaData)) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                $prefixedOptionName = $this->prefix($aOptionKey); // how it is stored in DB
                delete_option($prefixedOptionName);
            }
        }
    }

    /**
     * @return string display name of the plugin to show as a name/title in HTML.
     * Just returns the class name. Override this method to return something more readable
     */
    public function getPluginDisplayName() {
        return get_class($this);
    }

    /**
     * Get the prefixed version input $name suitable for storing in WP options
     * Idempotent: if $optionName is already prefixed, it is not prefixed again, it is returned without change
     * @param  $name string option name to prefix. Defined in settings.php and set as keys of $this->optionMetaData
     * @return string
     */
    public function prefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) { // 0 but not false
            return $name; // already prefixed
        }
        return $optionNamePrefix . $name;
    }

    /**
     * Remove the prefix from the input $name.
     * Idempotent: If no prefix found, just returns what was input.
     * @param  $name string
     * @return string $optionName without the prefix.
     */
    public function &unPrefix($name) {
        $optionNamePrefix = $this->getOptionNamePrefix();
        if (strpos($name, $optionNamePrefix) === 0) {
            return substr($name, strlen($optionNamePrefix));
        }
        return $name;
    }

    /**
     * A wrapper function delegating to WP get_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param $default string default value to return if the option is not set
     * @return string the value from delegated call to get_option(), or optional default value
     * if option is not set.
     */
    public function getOption($optionName, $default = null) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        $retVal = get_option($prefixedOptionName);
        if (!$retVal && $default) {
            $retVal = $default;
        }
        return $retVal;
    }

    /**
     * A wrapper function delegating to WP delete_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @return bool from delegated call to delete_option()
     */
    public function deleteOption($optionName) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return delete_option($prefixedOptionName);
    }

    /**
     * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function addOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return add_option($prefixedOptionName, $value);
    }

    /**
     * A wrapper function delegating to WP add_option() but it prefixes the input $optionName
     * to enforce "scoping" the options in the WP options table thereby avoiding name conflicts
     * @param  $optionName string defined in settings.php and set as keys of $this->optionMetaData
     * @param  $value mixed the new value
     * @return null from delegated call to delete_option()
     */
    public function updateOption($optionName, $value) {
        $prefixedOptionName = $this->prefix($optionName); // how it is stored in DB
        return update_option($prefixedOptionName, $value);
    }

    /**
     * A Role Option is an option defined in getOptionMetaData() as a choice of WP standard roles, e.g.
     * 'CanDoOperationX' => array('Can do Operation X', 'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber')
     * The idea is use an option to indicate what role level a user must minimally have in order to do some operation.
     * So if a Role Option 'CanDoOperationX' is set to 'Editor' then users which role 'Editor' or above should be
     * able to do Operation X.
     * Also see: canUserDoRoleOption()
     * @param  $optionName
     * @return string role name
     */
    public function getRoleOption($optionName) {
        $roleAllowed = $this->getOption($optionName);
        if (!$roleAllowed || $roleAllowed == '') {
            $roleAllowed = 'Administrator';
        }
        return $roleAllowed;
    }

    /**
     * Given a WP role name, return a WP capability which only that role and roles above it have
     * http://codex.wordpress.org/Roles_and_Capabilities
     * @param  $roleName
     * @return string a WP capability or '' if unknown input role
     */
    protected function roleToCapability($roleName) {
        switch ($roleName) {
            case 'Super Admin':
                return 'manage_options';
            case 'Administrator':
                return 'manage_options';
            case 'Editor':
                return 'publish_pages';
            case 'Author':
                return 'publish_posts';
            case 'Contributor':
                return 'edit_posts';
            case 'Subscriber':
                return 'read';
            case 'Anyone':
                return 'read';
        }
        return '';
    }

    /**
     * @param $roleName string a standard WP role name like 'Administrator'
     * @return bool
     */
    public function isUserRoleEqualOrBetterThan($roleName) {
        if ('Anyone' == $roleName) {
            return true;
        }
        $capability = $this->roleToCapability($roleName);
        return current_user_can($capability);
    }

    /**
     * @param  $optionName string name of a Role option (see comments in getRoleOption())
     * @return bool indicates if the user has adequate permissions
     */
    public function canUserDoRoleOption($optionName) {
        $roleAllowed = $this->getRoleOption($optionName);
        if ('Anyone' == $roleAllowed) {
            return true;
        }
        return $this->isUserRoleEqualOrBetterThan($roleAllowed);
    }

    /**
     * see: http://codex.wordpress.org/Creating_Options_Pages
     * @return void
     */
    public function createSettingsMenu() {
        $pluginName = $this->getPluginDisplayName();
        //create new top-level menu
        add_menu_page($pluginName . ' Plugin Settings', $pluginName, 'administrator', get_class($this), array(&$this, 'settingsPage')
        /* ,plugins_url('/images/icon.png', __FILE__) */); // if you call 'plugins_url; be sure to "require_once" it
        //call register settings function
        add_action('admin_init', array(&$this, 'registerSettings'));
    }

    public function registerSettings() {
        $settingsGroup = get_class($this) . '-settings-group';
        $optionMetaData = $this->getOptionMetaData();
        foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
            register_setting($settingsGroup, $aOptionMeta);
        }
    }

    /**
     * Creates HTML for the Administration page to set options for this plugin.
     * Override this method to create a customized page.
     * @return void
     */
    public function settingsPage() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.', 'job-promo'));
        }

        $optionMetaData = $this->getOptionMetaData();

        // Save Posted Options
        if ($optionMetaData != null) {
            foreach ($optionMetaData as $aOptionKey => $aOptionMeta) {
                if (isset($_POST[$aOptionKey])) {
                    $this->updateOption($aOptionKey, $_POST[$aOptionKey]);
                }
            }
        }

        global $wpdb;
        
        

        //VARIABELEN UIT DATABASE
        $result = $wpdb->get_results("
             SELECT * FROM `wp_jp_vacatures`;
             ");

        $options = $wpdb->get_results("
             SELECT * FROM `wp_jp_options`;
             ");

        foreach ($options as $options) {
            $lijst_breedte = $options->lijst_breedte;
            $lijst_afstand = $options->lijst_afstand;
            $characters = $options->lijst_characters;
            $lijst_opmaak = $options->lijst_opmaak;
            $lijst_titelgrote = $options->lijst_titelgrote;
            $lijst_lettergrote = $options->lijst_lettergrote;
            $lijst_links = $options->lijst_links;
            $lijst_rechts = $options->lijst_rechts;
            $lijst_background = $options->lijst_background;
            $lijst_hoek = $options->lijst_hoek;
            $lijst_trans = $options->lijst_trans;
            $lijst_backwidth = $options->lijst_backwidth;
            $lijst_backheight = $options->lijst_backheight;

            $pagina_breedte = $options->pagina_breedte;
            $pagina_links = $options->pagina_links;
            $pagina_rechts = $options->pagina_rechts;
            $pagina_titelgrote = $options->pagina_titelgrote;
            $pagina_tekstgrote = $options->pagina_tekstgrote;
            $pagina_logo = $options->pagina_logo;
            $pagina_map = $options->pagina_map;
            $pagina_url = $options->pagina_url;
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
        
        //DATABASE VERNIEUWEN (button voor maken)
        
        if (isset($_POST['drop_vacatures'])) {
            $wpdb->query("DROP TABLE wp_jp_vacatures");
            
            $wpdb->query("CREATE TABLE IF NOT EXISTS `wp_jp_vacatures` (
                        `vacature_id` int(11) NOT NULL,
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
                        `intro` text NOT NULL,
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
                        `url_vacature` varchar(256) NOT NULL,
                        `url_sollicitatie` varchar(256) NOT NULL,
                        `url_logo` varchar(256) NOT NULL,
                        `meta_keywords` varchar(1024) NOT NULL,
                        `meta_description` varchar(1024) NOT NULL,
                        `url_pixel` varchar(128) NOT NULL
                      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

            $wpdb->query("ALTER TABLE `wp_jp_vacatures`
                             ADD PRIMARY KEY (`vacature_id`), ADD UNIQUE KEY `vacature_id` (`vacature_id`);");
            
        }
        
        
        if (isset($_POST['drop_options'])) {
            $wpdb->query("DROP TABLE wp_jp_options");
            
            $wpdb->query("CREATE TABLE IF NOT EXISTS `wp_jp_options` (
                            `id` int(11) NOT NULL,
                              `lijst_breedte` int(11) NOT NULL,
                              `lijst_afstand` int(11) NOT NULL,
                              `lijst_opmaak` int(1) NOT NULL,
                              `lijst_characters` int(11) NOT NULL,
                              `lijst_titelgrote` int(11) NOT NULL,
                              `lijst_lettergrote` int(11) NOT NULL,
                              `lijst_links` int(11) NOT NULL,
                              `lijst_rechts` int(11) NOT NULL,
                              `lijst_background` varchar(255) NOT NULL,
                              `lijst_hoek` int(1) NOT NULL,
                              `lijst_trans` int(1) NOT NULL,
                              `lijst_backheight` int(11) NOT NULL,
                              `lijst_backwidth` int(11) NOT NULL,
                              `pagina_design` int(1) NOT NULL,
                              `pagina_breedte` int(11) NOT NULL,
                              `pagina_links` int(11) NOT NULL,
                              `pagina_rechts` int(11) NOT NULL,
                              `pagina_opmaak` int(1) NOT NULL,
                              `pagina_titelgrote` int(11) NOT NULL,
                              `pagina_tekstgrote` int(11) NOT NULL,
                              `pagina_logo` int(1) NOT NULL,
                              `pagina_map` int(1) NOT NULL,
                              `pagina_url` varchar(255) NOT NULL,
                              `pagina_background` varchar(255) NOT NULL,
                              `pagina_hoek` int(1) NOT NULL,
                              `pagina_padding` int(11) NOT NULL,
                              `pagina_trans` int(1) NOT NULL,
                              `tabel` int(1) NOT NULL,
                              `tabel_referentie` int(1) NOT NULL,
                              `tabel_dienstverband` int(1) NOT NULL,
                              `tabel_werklocatie` int(1) NOT NULL,
                              `tabel_branche` int(1) NOT NULL,
                              `tabel_beroep` int(1) NOT NULL,
                              `tabel_salaris` int(1) NOT NULL,
                              `tabel_startdatum` int(1) NOT NULL,
                              `tabel_niveau` int(1) NOT NULL
                            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
                            ");

            $wpdb->query("INSERT INTO `wp_jp_options` (`id`, `lijst_breedte`, `lijst_afstand`, `lijst_opmaak`, `lijst_characters`, `lijst_titelgrote`, `lijst_lettergrote`, `lijst_links`, `lijst_rechts`, `lijst_background`, `lijst_hoek`, `lijst_trans`, `lijst_backheight`, `lijst_backwidth`, `pagina_breedte`, `pagina_links`, `pagina_rechts`, `pagina_opmaak`, `pagina_titelgrote`, `pagina_tekstgrote`, `pagina_logo`, `pagina_map`, `pagina_url`, `pagina_background`, `pagina_hoek`, `pagina_padding`, `pagina_trans`, `tabel`, `tabel_referentie`, `tabel_dienstverband`, `tabel_werklocatie`, `tabel_branche`, `tabel_beroep`, `tabel_salaris`, `tabel_startdatum`, `tabel_niveau`, `pagina_design`) VALUES
                        (1, 450, 190, 0, 300, 30, 15, 0, 0, '', 1, 1, 170, 650, 650, 0, 0, 0, 25, 15, 1, 1, '', '', 1, 20, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0);
                        ");

            $wpdb->query("ALTER TABLE `wp_jp_options`
                            ADD PRIMARY KEY (`id`);");

            $wpdb->query("ALTER TABLE `wp_jp_options`
                            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;");
        }    


        //DATABASES AANMAKEN

        if (isset($_POST['databases'])) {
            $wpdb->query("CREATE TABLE IF NOT EXISTS `wp_jp_vacatures` (
                        `vacature_id` int(11) NOT NULL,
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
                        `intro` text NOT NULL,
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
                        `url_vacature` varchar(256) NOT NULL,
                        `url_sollicitatie` varchar(256) NOT NULL,
                        `url_logo` varchar(256) NOT NULL,
                        `meta_keywords` varchar(1024) NOT NULL,
                        `meta_description` varchar(1024) NOT NULL,
                        `url_pixel` varchar(128) NOT NULL
                      ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

            $wpdb->query("ALTER TABLE `wp_jp_vacatures`
                             ADD PRIMARY KEY (`vacature_id`), ADD UNIQUE KEY `vacature_id` (`vacature_id`);");

            $wpdb->query("CREATE TABLE IF NOT EXISTS `wp_jp_options` (
                            `id` int(11) NOT NULL,
                              `lijst_breedte` int(11) NOT NULL,
                              `lijst_afstand` int(11) NOT NULL,
                              `lijst_opmaak` int(1) NOT NULL,
                              `lijst_characters` int(11) NOT NULL,
                              `lijst_titelgrote` int(11) NOT NULL,
                              `lijst_lettergrote` int(11) NOT NULL,
                              `lijst_links` int(11) NOT NULL,
                              `lijst_rechts` int(11) NOT NULL,
                              `lijst_background` varchar(255) NOT NULL,
                              `lijst_hoek` int(1) NOT NULL,
                              `lijst_trans` int(1) NOT NULL,
                              `lijst_backheight` int(11) NOT NULL,
                              `lijst_backwidth` int(11) NOT NULL,
                              `pagina_design` int(1) NOT NULL,
                              `pagina_breedte` int(11) NOT NULL,
                              `pagina_links` int(11) NOT NULL,
                              `pagina_rechts` int(11) NOT NULL,
                              `pagina_opmaak` int(1) NOT NULL,
                              `pagina_titelgrote` int(11) NOT NULL,
                              `pagina_tekstgrote` int(11) NOT NULL,
                              `pagina_logo` int(1) NOT NULL,
                              `pagina_map` int(1) NOT NULL,
                              `pagina_url` varchar(255) NOT NULL,
                              `pagina_background` varchar(255) NOT NULL,
                              `pagina_hoek` int(1) NOT NULL,
                              `pagina_padding` int(11) NOT NULL,
                              `pagina_trans` int(1) NOT NULL,
                              `tabel` int(1) NOT NULL,
                              `tabel_referentie` int(1) NOT NULL,
                              `tabel_dienstverband` int(1) NOT NULL,
                              `tabel_werklocatie` int(1) NOT NULL,
                              `tabel_branche` int(1) NOT NULL,
                              `tabel_beroep` int(1) NOT NULL,
                              `tabel_salaris` int(1) NOT NULL,
                              `tabel_startdatum` int(1) NOT NULL,
                              `tabel_niveau` int(1) NOT NULL
                            ) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
                            ");

            $wpdb->query("INSERT INTO `wp_jp_options` (`id`, `lijst_breedte`, `lijst_afstand`, `lijst_opmaak`, `lijst_characters`, `lijst_titelgrote`, `lijst_lettergrote`, `lijst_links`, `lijst_rechts`, `lijst_background`, `lijst_hoek`, `lijst_trans`, `lijst_backheight`, `lijst_backwidth`, `pagina_breedte`, `pagina_links`, `pagina_rechts`, `pagina_opmaak`, `pagina_titelgrote`, `pagina_tekstgrote`, `pagina_logo`, `pagina_map`, `pagina_url`, `pagina_background`, `pagina_hoek`, `pagina_padding`, `pagina_trans`, `tabel`, `tabel_referentie`, `tabel_dienstverband`, `tabel_werklocatie`, `tabel_branche`, `tabel_beroep`, `tabel_salaris`, `tabel_startdatum`, `tabel_niveau`, `pagina_design`) VALUES
                        (1, 450, 190, 0, 300, 30, 15, 0, 0, '', 1, 1, 170, 650, 650, 0, 0, 0, 25, 15, 1, 1, '', '', 1, 20, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0);
                        ");

            $wpdb->query("ALTER TABLE `wp_jp_options`
                            ADD PRIMARY KEY (`id`);");

            $wpdb->query("ALTER TABLE `wp_jp_options`
                            MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;");

            $wpdb->query("INSERT INTO `wp_jp_vacatures` (`vacature_id`, `online`, `topvacature`, `referentie`, `functietitel`, `uren_van`, `uren_tot`, `geplaatst_op`, `vervalt_op`, `beschikbaar_vanaf`, `per_direct`, `niveau`, `vakgebied`, `branche`, `minimale_vergoeding`, `maximale_vergoeding`, `marktconform`, `omschrijving`, `contact_omschrijving`, `latitude_coordinatie`, `longitude_coordinatie`, `provincie`, `plaats`, `straatnaam`, `postcode`, `aanhef`, `contactpersoon`, `functie_contactpersoon`, `telefoon_contactpersoon`, `email_contactpersoon`, `website_contact`, `trefwoorden`, `url_vacature`, `url_sollicitatie`, `url_logo`, `meta_keywords`, `meta_description`, `intro`) VALUES ('100', '1', '1', 'ABC100', 'Testvacature', '40', '40', '1414402159', '1514402159', '0', '1', 'HBO', 'Marketing', 'Groothandel/Handel', '0', '0', '1', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent interdum feugiat quam, vitae suscipit libero dignissim in. In hac habitasse platea dictumst. Proin in vestibulum purus, a tempus nulla. Etiam consectetur est vitae tempor tristique. Curabitur eget mollis justo, sed posuere nisi. Nunc ac enim ut erat euismod finibus nec quis erat. Nunc tincidunt rhoncus eros, nec sagittis augue consequat sit amet. In hac habitasse platea dictumst. Integer malesuada fermentum dignissim. Nulla nec efficitur justo, id suscipit est. Vestibulum vehicula blandit nunc a porttitor. In non blandit odio. Vestibulum commodo tristique dolor, vel pellentesque ligula tristique non. Integer eleifend convallis ligula.

            Mauris molestie suscipit tincidunt. Ut tempus diam nisl, quis imperdiet purus consequat vehicula. Mauris vitae elit laoreet, dapibus ipsum ultricies, ultrices urna. Etiam facilisis justo id placerat pellentesque. Fusce at tincidunt ex. Cras eget arcu in enim luctus pellentesque eget ut orci. Donec ac neque vitae massa aliquet porta. Morbi varius justo ut nisi viverra ultrices. Fusce erat augue, lobortis sed nisi quis, dictum aliquam ipsum. Suspendisse vitae tellus accumsan, mattis risus ac, porttitor sapien. Vestibulum et cursus neque.

            Fusce bibendum pharetra egestas. Nulla ultrices eleifend mauris, id mollis ex congue eu. Fusce in nunc nisi. Aenean placerat quis dolor eu scelerisque. Duis eget interdum ante, vestibulum maximus enim. Mauris elementum ullamcorper sem quis sodales. Nulla arcu elit, venenatis in libero ut, elementum blandit turpis. Phasellus mattis leo sit amet mi dignissim, nec faucibus urna vestibulum. Proin massa tortor, faucibus a scelerisque ac, pharetra egestas mi. Integer libero felis, finibus a lacinia at, pellentesque a velit.

            Nulla eu augue a neque aliquet accumsan id in nunc. Fusce in neque sed ex elementum bibendum. Phasellus gravida lorem quis felis ultricies fermentum. Sed nulla neque, molestie ac velit eget, tincidunt congue tellus. Praesent leo justo, fringilla et feugiat in, sagittis eu libero. In scelerisque est in mi aliquam, finibus vulputate nunc ultricies. Fusce nec lacus eu ligula eleifend faucibus non et erat. Vivamus volutpat elementum nulla, ut rhoncus lacus facilisis sit amet. Aenean vestibulum ante id turpis ornare, nec iaculis diam posuere. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Suspendisse placerat feugiat malesuada. Mauris pellentesque lacus nec rutrum elementum. Donec sem enim, sagittis sit amet libero quis, aliquam eleifend turpis. Ut a magna eu ligula interdum consectetur non sed mi.', 'Phasellus at urna tincidunt, pharetra nulla a, varius arcu. Suspendisse gravida consequat porta. Nullam porttitor placerat gravida. Proin efficitur varius arcu, at fringilla tortor laoreet in. Phasellus fringilla tincidunt elementum. Sed vel dui in magna rutrum porta vel non neque. Sed placerat vel eros quis tristique. Nullam condimentum arcu eu faucibus dapibus. In vitae dui bibendum, rutrum nulla nec, tempus odio. Pellentesque vitae volutpat est, nec ullamcorper est. Suspendisse ut urna laoreet, elementum nisi quis, vulputate sapien. Donec ullamcorper libero in ultricies volutpat.', '52.1263', '4.66358', 'Zuid-Holland', 'Alphen aan den Rijn', 'Teststraat 22', '1234AB', 'Mevr', 'Testpersoon', 'Testen', '123456789', 'test@test.nl', 'www.testsite.com', 'test', 'www.testvacature.nl', 'www.testsollicitatie.nl', 'http://account.jobpromo.nl/images/jobpromo.gif', 'testen', 'test test', 'Dit is de intro tekst van de test vacature.');");

            echo "<script>  alert ('Databases zijn aangemaakt!'); window.location.href=window.location.href;</script>";
        }

        //VACATURE OPTIES

        if (isset($_POST['vacature_submit'])) {
            foreach ($_POST AS $k => $v) {
                if (!strncmp($k, 'online', 6)) {
                    $x = explode('_', $k);
                    $x[1];
                    $v;

                    $wpdb->query('UPDATE wp_jp_vacatures SET online = "' . $v . '" WHERE vacature_id = "' . $x[1] . '" ');
                }
            }

            foreach ($_POST AS $k => $v) {
                if (!strncmp($k, 'topvacature', 11)) {
                    $x = explode('_', $k);
                    $x[1];
                    $v;

                    $wpdb->query('UPDATE wp_jp_vacatures SET topvacature = "' . $v . '" WHERE vacature_id = "' . $x[1] . '" ');
                }
            }
            echo "<script>  alert ('Wijzigingen zijn opgeslagen.'); window.location.href=window.location.href;</script>";
        }

        if (isset($_POST['verwijder'])) {




            $wpdb->query('DELETE FROM wp_jp_vacatures WHERE vacature_id = "' . mysql_real_escape_string($_POST['verwijder']) . '" ');


            echo "<script>  alert ('Vacature is verwijderd.'); window.location.href=window.location.href;</script>";
        }

        //LIJST OPTIES

        if (isset($_POST['lijst_submit'])) {

            $nieuwcharacters = mysql_real_escape_string($_POST['characters']);
            $nieuwafstand = mysql_real_escape_string($_POST['lijst_afstand']);
            $nieuwbreedte = mysql_real_escape_string($_POST['lijst_breedte']);
            $nieuwtitelgrote = mysql_real_escape_string($_POST['lijst_titelgrote']);
            $nieuwlettergrote = mysql_real_escape_string($_POST['lijst_lettergrote']);
            $nieuwopmaak = mysql_real_escape_string($_POST['lijst_opmaak']);
            $nieuwlijstlinks = mysql_real_escape_string($_POST['lijst_links']);
            $nieuwlijstrechts = mysql_real_escape_string($_POST['lijst_rechts']);
            $nieuwlijstbackground = mysql_real_escape_string($_POST['lijst_background']);
            $nieuwlijsthoek = mysql_real_escape_string($_POST['lijst_hoek']);
            $nieuwlijsttrans = mysql_real_escape_string($_POST['lijst_trans']);
            $nieuwwidth = mysql_real_escape_string($_POST['lijst_backwidth']);
            $nieuwheight = mysql_real_escape_string($_POST['lijst_backheight']);



            $wpdb->query('UPDATE wp_jp_options SET lijst_breedte = "' . $nieuwbreedte . '",'
                    . ' lijst_characters = "' . $nieuwcharacters . '",'
                    . ' lijst_afstand = "' . $nieuwafstand . '",'
                    . ' lijst_titelgrote = "' . $nieuwtitelgrote . '",'
                    . ' lijst_lettergrote = "' . $nieuwlettergrote . '",'
                    . ' lijst_links = "' . $nieuwlijstlinks . '",'
                    . ' lijst_rechts = "' . $nieuwlijstrechts . '",'
                    . ' lijst_background = "' . $nieuwlijstbackground . '",'
                    . ' lijst_hoek = "' . $nieuwlijsthoek . '",'
                    . ' lijst_trans = "' . $nieuwlijsttrans . '",'
                    . ' lijst_backwidth = "' . $nieuwwidth . '",'
                    . ' lijst_backheight = "' . $nieuwheight . '",'
                    . ' lijst_opmaak = "' . $nieuwopmaak . '" ');

            echo "<script>  alert ('Wijzigingen zijn opgeslagen.'); window.location.href=window.location.href;</script>";
        }
        if (isset($_POST['lijst_reset'])) {
            $wpdb->query('UPDATE wp_jp_options SET lijst_breedte = "450",'
                    . ' lijst_characters = "300",'
                    . ' lijst_afstand = "190",'
                    . ' lijst_titelgrote = "30",'
                    . ' lijst_lettergrote = "15",'
                    . ' lijst_links = "0",'
                    . ' lijst_rechts = "0",'
                    . ' lijst_background = "",'
                    . ' lijst_hoek = "1",'
                    . ' lijst_trans = "1",'
                    . ' lijst_backwidth = "650",'
                    . ' lijst_backheight = "170",'
                    . ' lijst_opmaak = "0" ');

            echo "<script>  alert ('Wijzigingen zijn opgeslagen.'); window.location.href=window.location.href;</script>";
        }

        //PAGINA OPTIES

        if (isset($_POST['pagina_submit'])) {

            $nieuwpaginabreedte = mysql_real_escape_string($_POST['pagina_breedte']);
            $nieuwpaginalinks = mysql_real_escape_string($_POST['pagina_links']);
            $nieuwpaginarechts = mysql_real_escape_string($_POST['pagina_rechts']);
            $nieuwtitelgrote = mysql_real_escape_string($_POST['pagina_titelgrote']);
            $nieuwtekstgrote = mysql_real_escape_string($_POST['pagina_tekstgrote']);
            $nieuwlogo = mysql_real_escape_string($_POST['pagina_logo']);
            $nieuwmap = mysql_real_escape_string($_POST['pagina_map']);
            $nieuwurl = mysql_real_escape_string($_POST['pagina_url']);
            $nieuwpaginabackground = mysql_real_escape_string($_POST['pagina_background']);
            $nieuwpaginapadding = mysql_real_escape_string($_POST['pagina_padding']);
            $nieuwpaginahoek = mysql_real_escape_string($_POST['pagina_hoek']);
            $nieuwpaginatrans = mysql_real_escape_string($_POST['pagina_trans']);
            $nieuwpaginadesign = mysql_real_escape_string($_POST['pagina_design']);


            $wpdb->query('UPDATE wp_jp_options SET pagina_breedte = "' . $nieuwpaginabreedte . '",'
                    . ' pagina_links = "' . $nieuwpaginalinks . '",'
                    . ' pagina_titelgrote = "' . $nieuwtitelgrote . '",'
                    . ' pagina_tekstgrote = "' . $nieuwtekstgrote . '",'
                    . ' pagina_logo = "' . $nieuwlogo . '",'
                    . ' pagina_map = "' . $nieuwmap . '",'
                    . ' pagina_url = "' . $nieuwurl . '",'
                    . ' pagina_background = "' . $nieuwpaginabackground . '",'
                    . ' pagina_hoek = "' . $nieuwpaginahoek . '",'
                    . ' pagina_padding = "' . $nieuwpaginapadding . '",'
                    . ' pagina_trans = "' . $nieuwpaginatrans . '",'
                    . ' pagina_design = "' . $nieuwpaginadesign . '",'
                    . ' pagina_rechts = "' . $nieuwpaginarechts . '" ');


            echo "<script>  alert ('Wijzigingen zijn opgeslagen.'); window.location.href=window.location.href;</script>";
        }

        if (isset($_POST['pagina_reset'])) {
            $wpdb->query('UPDATE wp_jp_options SET pagina_breedte = "650",'
                    . ' pagina_links = "0",'
                    . ' pagina_titelgrote = "25",'
                    . ' pagina_tekstgrote = "15",'
                    . ' pagina_logo = "1",'
                    . ' pagina_map = "1",'
                    . ' pagina_background = "",'
                    . ' pagina_hoek = "1",'
                    . ' pagina_padding = "20",'
                    . ' pagina_trans = "1",'
                    . ' pagina_design = "0",'
                    . ' pagina_rechts = "0" ');

            echo "<script>  alert ('Wijzigingen zijn opgeslagen.'); window.location.href=window.location.href;</script>";
        }

        //TABEL OPTIES

        if (isset($_POST['tabel_submit'])) {

            $nieuwtabelreferentie = mysql_real_escape_string($_POST['referentie']);
            $nieuwtabeldienstverband = mysql_real_escape_string($_POST['dienstverband']);
            $nieuwtabelwerklocatie = mysql_real_escape_string($_POST['werklocatie']);
            $nieuwtabelbranche = mysql_real_escape_string($_POST['branche']);
            $nieuwtabelberoep = mysql_real_escape_string($_POST['beroep']);
            $nieuwtabelsalaris = mysql_real_escape_string($_POST['salaris']);
            $nieuwtabelstartdatum = mysql_real_escape_string($_POST['startdatum']);
            $nieuwtabelniveau = mysql_real_escape_string($_POST['niveau']);
            $nieuwtabel = mysql_real_escape_string($_POST['tabel']);


            $wpdb->query('UPDATE wp_jp_options SET tabel_referentie = "' . $nieuwtabelreferentie . '",'
                    . ' tabel_dienstverband = "' . $nieuwtabeldienstverband . '",'
                    . ' tabel_werklocatie = "' . $nieuwtabelwerklocatie . '",'
                    . ' tabel_branche = "' . $nieuwtabelbranche . '",'
                    . ' tabel_beroep = "' . $nieuwtabelberoep . '",'
                    . ' tabel_salaris = "' . $nieuwtabelsalaris . '",'
                    . ' tabel_startdatum = "' . $nieuwtabelstartdatum . '",'
                    . ' tabel_niveau = "' . $nieuwtabelniveau . '",'
                    . ' tabel = "' . $nieuwtabel . '" ');

            echo "<script>  alert ('Wijzigingen zijn opgeslagen.'); window.location.href=window.location.href;</script>";
        }



        // HTML for the page
        $settingsGroup = get_class($this) . '-settings-group';
        ?>
        <div class="wrap">
            <h1>Settings Job Promo Plugin</h1>
            <p>Pas de getallen aan en kijk wat het met de tekst doet. Als je een fout maakt kan je op de reset knop klikken en is alles weer zoals eerst</p>

            <h2>Plugin opties</h2>
            <form action="<?php htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                <input class="button-primary" type="submit" name="databases" value="Tabellen aanmaken"> KIJK UIT! RESET OOK ALLE WAARDES! <br /><br />
                <!-- <input type="submit" name="drop_vacatures" value="Verwijder vacature tabel"> -->
                <!-- <input type="submit" name="drop_options" value="Verwijder options tabel"> -->
            </form>

            <div>
                <table>
                    <h2>Vacature opties</h2>

                    <tr><th>Vacature</th><th>Online</th><th>Topvacature</th><th>Verwijderen</th></tr>
                    <form action="<?php htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">

                        <?php
                        foreach ($result as $result) {
                            echo "<tr><td>" . $result->functietitel . "</td>";

                            echo "<input type='hidden' name='online_" . $result->vacature_id . "' value='0'>";
                            echo "<td> <input value='1' name='online_" . $result->vacature_id . "' type='checkbox'";
                            if ($result->online == 1)
                                echo ' checked';
                            echo "> </td>";

                            echo "<input type='hidden' name='topvacature_" . $result->vacature_id . "' value='0'>";
                            echo "<td> <input value='1' name='topvacature_" . $result->vacature_id . "' type='checkbox'";
                            if ($result->topvacature == 1)
                                echo ' checked';
                            echo "> </td>";


                            echo '<td><input type="submit" value="verwijderen" class="button-primary" onclick="this.value = \'' . $result->vacature_id . '\';" name="verwijder" ></td>';
                            echo "</tr>";
                        }
                        ?>
                        <tr>
                            <td><b>Wijzigingen opslaan</b></td>
                            <td><input class="button-primary" type="submit" name="vacature_submit" value="Opslaan"></td>
                        </tr>
                    </form>
                </table>
            </div>

            <div style="float:left;">
                <h2>Vacature lijst opties</h2>
                <form action="<?php htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                    <table>
                        <tr>
                            <td><p><b>Aantal characters in beschrijving</b></p></td>
                            <td><input type="number" name="characters" value="<?php echo $characters ?>"></td>
                        </tr>
                        <tr>
                            <td><p><b>Afstand tussen de vacatures</b></p></td>
                            <td><input type="number" name="lijst_afstand" value="<?php echo $lijst_afstand ?>"></td>
                        </tr>
                        <tr>
                            <td><p><b>Breedte tekst</b></p></td>
                            <td><input type="number" name="lijst_breedte" value="<?php echo $lijst_breedte ?>"></td>
                        </tr>
                        <tr>
                            <td><p><b>Grote van de titel</b></p></td>
                            <td><input type="number" name="lijst_titelgrote" value="<?php echo $lijst_titelgrote ?>"></td>
                        </tr>                                                <tr>
                            <td><p><b>Grote van de letters in de tekst</b></p></td>
                            <td><input type="number" name="lijst_lettergrote" value="<?php echo $lijst_lettergrote ?>"></td>
                        </tr>                        
                        <tr>
                            <td><p><b>Lijst naar links schuiven</b></p></td>
                            <td><input type="number" name="lijst_links" value="<?php echo $lijst_links ?>"></td>
                        </tr>
                        </tr>
                        <tr>
                            <td><p><b>Lijst naar rechts schuiven</b></p></td>
                            <td><input type="number" name="lijst_rechts" value="<?php echo $lijst_rechts ?>"></td>
                        </tr>
                        <tr>
                            <td><p><b>Opmaak steekwoorden</b></p></td>
                            <td><select name="lijst_opmaak">
                                    <option value="0"<?php if ($lijst_opmaak == 0) echo ' selected="SELECTED"'; ?>>Tabel</option>
                                    <option value="1"<?php if ($lijst_opmaak == 1) echo ' selected="SELECTED"'; ?>>Tekst</option>
                                </select></td>
                        </tr>
                        <tr>
                            <td><p><b>Achtergrond (RGB CODE scheiden door komma's)</b></p></td>
                            <td><input type="text" name="lijst_background" value="<?php echo $lijst_background ?>"></td>
                        </tr>
                        <tr>
                            <td><p><b>Breedte achtergrond</b></p></td>
                            <td><input type="number" name="lijst_backwidth" value="<?php echo $lijst_backwidth ?>"></td>
                        </tr>
                        <tr>
                            <td><p><b>Hoogte achtergrond</b></p></td>
                            <td><input type="number" name="lijst_backheight" value="<?php echo $lijst_backheight ?>"></td>
                        </tr>
                        <tr> 
                            <td><p><b>Doorzichtige achtergrond</b></p></td>
                        <input type="hidden" name="lijst_trans" value="0">
                        <td><input type="checkbox" <?php if ($lijst_trans == 1) echo ' checked="checked" '; ?> name="lijst_trans" value="1"></td>            
                        </tr> 
                        <tr> 
                            <td><p><b>Ronde hoeken in achtergrond</b></p></td>
                        <input type="hidden" name="lijst_hoek" value="0">
                        <td><input type="checkbox" <?php if ($lijst_hoek == 1) echo ' checked="checked" '; ?> name="lijst_hoek" value="1"></td>            
                        </tr> 
                        <tr>
                            <td><p><b>Reset naar standaard waarden</b></p></td>
                            <td><input class="button-primary" type='submit' name="lijst_reset" value="RESET"></td>
                        </tr>
                        <tr>
                            <td><p><b>Wijzigingen opslaan</b></p></td>
                            <td><input class="button-primary" type="submit" name="lijst_submit" value="Opslaan"></td>
                        </tr>
                    </table>
                </form>
            </div>
        <div style="float:left; margin-left:20px;">
            <h2>Vacature pagina opties</h2>
            <form action="<?php htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                <table>
                    <tr>
                        <td><p><b>URL van de pagina</b></p></td>
                        <td><input type="text" name="pagina_url" value="<?php echo $pagina_url ?>"></td>
                    </tr>
                    <tr>
                            <td><p><b>Vacature pagina design</b></p></td>
                            <td><select name="pagina_design">
                                    <option value="0"<?php if ($pagina_design == 0) echo ' selected="SELECTED"'; ?>>Design 1</option>
                                    <option value="1"<?php if ($pagina_design == 1) echo ' selected="SELECTED"'; ?>>Design 2</option>
                                </select></td>
                    </tr>
                    <tr>
                        <td><p><b>Breedte van de tekst</b></p></td>
                        <td><input type="number" name="pagina_breedte" value="<?php echo $pagina_breedte ?>"></td>
                    </tr>
                    <tr>
                        <td><p><b>Tekst naar links verplaatsen</b></p></td>
                        <td><input type="number" name="pagina_links" value="<?php echo $pagina_links ?>"></td>
                    </tr>
                    <tr>
                        <td><p><b>Tekst naar rechts verplaatsen</b></p></td>
                        <td><input type="number" name="pagina_rechts" value="<?php echo $pagina_rechts ?>"></td>
                    </tr>
                    <tr>
                        <td><p><b>Grote van de letters in de titel</b></p></td>
                        <td><input type="number" name="pagina_titelgrote" value="<?php echo $pagina_titelgrote ?>"></td>
                    </tr>
                    <tr>
                        <td><p><b>Grote van de letters in de tekst</b></p></td>
                        <td><input type="number" name="pagina_tekstgrote" value="<?php echo $pagina_tekstgrote ?>"></td>
                    </tr>
                    <tr>
                        <td><p><b>Achtergrond (RGB CODE scheiden door komma's)</b></p></td>
                        <td><input type="text" name="pagina_background" value="<?php echo $pagina_background ?>"></td>
                    </tr>
                    <tr> 
                            <td><p><b>Doorzichtige achtergrond</b></p></td>
                        <input type="hidden" name="pagina_trans" value="0">
                        <td><input type="checkbox" <?php if ($pagina_trans == 1) echo ' checked="checked" '; ?> name="pagina_trans" value="1"></td>            
                        </tr>
                    <tr>
                        <td><p><b>Padding</b></p></td>
                        <td><input type="number" name="pagina_padding" value="<?php echo $pagina_padding ?>"></td>
                    </tr>
                    <tr> 
                        <td><p><b>Ronde hoeken in achtergrond</b></p></td>
                    <input type="hidden" name="pagina_hoek" value="0">
                    <td><input type="checkbox" <?php if ($pagina_hoek == 1) echo ' checked="checked" '; ?> name="pagina_hoek" value="1"></td>            
                    </tr> 
                    <tr> 
                        <td><p><b>Logo bovenaan pagina</b></p></td>
                    <input type="hidden" name="pagina_logo" value="0">
                    <td><input type="checkbox" <?php if ($pagina_logo == 1) echo ' checked="checked" '; ?> name="pagina_logo" value="1"></td>            
                    </tr>
                    <tr> 
                        <td><p><b>Google Maps kaartje</b></p></td>
                    <input type="hidden" name="pagina_map" value="0">
                    <td><input type="checkbox" <?php if ($pagina_map == 1) echo ' checked="checked" '; ?> name="pagina_map" value="1"></td>            
                    </tr>
                    <tr>
                        <td><p><b>Reset naar standaard waarden</b></p></td>
                        <td><input class="button-primary" type='submit' name="pagina_reset" value="RESET"></td>
                    </tr>
                    <tr>
                        <td><p><b>Wijzigingen opslaan</b></p></td>
                        <td><input class="button-primary" type="submit" name="pagina_submit" value="Opslaan"></td>
                    </tr>

                </table>
            </form>
        </div>
        <div style="float:left; margin-left:30px;">
            <h2>Tabel opties</h2>
            <form action="<?php htmlentities($_SERVER['PHP_SELF']); ?>" method="POST">
                <table>
                    <tr> <input type="hidden" name="referentie" value="0">    
                    <td><input type="checkbox" <?php if ($tabel_referentie == 1) echo ' checked '; ?> name="referentie" value="1">Referentie</td>
                    </tr>
                    <tr> <input type="hidden" name="dienstverband" value="0">   
                    <td><input type="checkbox" <?php if ($tabel_dienstverband == 1) echo ' checked '; ?> name="dienstverband" value="1">Dienstverband</td>
                    </tr>
                    <tr> <input type="hidden" name="werklocatie" value="0">
                    <td><input type="checkbox" <?php if ($tabel_werklocatie == 1) echo ' checked '; ?> name="werklocatie" value="1">Werklocatie</td>
                    </tr>
                    <tr> <input type="hidden" name="branche" value="0">
                    <td><input type="checkbox" <?php if ($tabel_branche == 1) echo ' checked '; ?> name="branche" value="1">Branche</td>
                    </tr>
                    <tr> <input type="hidden" name="beroep" value="0">
                    <td><input type="checkbox" <?php if ($tabel_beroep == 1) echo ' checked '; ?> name="beroep" value="1">Beroep</td>
                    </tr>
                    <tr> <input type="hidden" name="salaris" value="0">
                    <td><input type="checkbox" <?php if ($tabel_salaris == 1) echo ' checked '; ?> name="salaris" value="1">Salaris indicatie</td>
                    </tr>
                    <tr> <input type="hidden" name="startdatum" value="0">
                    <td><input type="checkbox" <?php if ($tabel_startdatum == 1) echo ' checked '; ?> name="startdatum" value="1">Startdatum</td>
                    </tr>
                    <tr> <input type="hidden" name="niveau" value="0">
                    <td><input type="checkbox" <?php if ($tabel_niveau == 1) echo ' checked '; ?> name="niveau" value="1">Niveau</td>
                    </tr>
                    <tr> <input type="hidden" name="tabel" value="0">
                    <td><input type="checkbox" <?php if ($tabel == 1) echo ' checked="checked" '; ?> name="tabel" value="1">Tabel aan of uit zetten <br /> (Alleen als de rest ook uit is!)</td>
                    </tr>
                    <tr>
                        <td><p><b>Wijzigingen opslaan</b></p></td>
                        <td><input class="button-primary" type="submit" name="tabel_submit" value="Opslaan"></td>
                    </tr>
                </table>
            </form>
        </div>

        </div>
        <?php
    }

    /**
     * Helper-function outputs the correct form element (input tag, select tag) for the given item
     * @param  $aOptionKey string name of the option (un-prefixed)
     * @param  $aOptionMeta mixed meta-data for $aOptionKey (either a string display-name or an array(display-name, option1, option2, ...)
     * @param  $savedOptionValue string current value for $aOptionKey
     * @return void
     */
    protected function createFormControl($aOptionKey, $aOptionMeta, $savedOptionValue) {
        if (is_array($aOptionMeta) && count($aOptionMeta) >= 2) { // Drop-down list
            $choices = array_slice($aOptionMeta, 1);
            ?>
            <p><select name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>">
                    <?php
                    foreach ($choices as $aChoice) {
                        $selected = ($aChoice == $savedOptionValue) ? 'selected' : '';
                        ?>
                        <option value="<?php echo $aChoice ?>" <?php echo $selected ?>><?php echo $this->getOptionValueI18nString($aChoice) ?></option>
                        <?php
                    }
                    ?>
                </select></p>
            <?php
        } else { // Simple input field
            ?>
            <p><input type="text" name="<?php echo $aOptionKey ?>" id="<?php echo $aOptionKey ?>"
                      value="<?php echo esc_attr($savedOptionValue) ?>" size="50"/></p>
            <?php
        }
    }

    /**
     * Override this method and follow its format.
     * The purpose of this method is to provide i18n display strings for the values of options.
     * For example, you may create a options with values 'true' or 'false'.
     * In the options page, this will show as a drop down list with these choices.
     * But when the the language is not English, you would like to display different strings
     * for 'true' and 'false' while still keeping the value of that option that is actually saved in
     * the DB as 'true' or 'false'.
     * To do this, follow the convention of defining option values in getOptionMetaData() as canonical names
     * (what you want them to literally be, like 'true') and then add each one to the switch statement in this
     * function, returning the "__()" i18n name of that string.
     * @param  $optionValue string
     * @return string __($optionValue) if it is listed in this method, otherwise just returns $optionValue
     */
    protected function getOptionValueI18nString($optionValue) {
        switch ($optionValue) {
            case 'true':
                return __('true', 'job-promo');
            case 'false':
                return __('false', 'job-promo');

            case 'Administrator':
                return __('Administrator', 'job-promo');
            case 'Editor':
                return __('Editor', 'job-promo');
            case 'Author':
                return __('Author', 'job-promo');
            case 'Contributor':
                return __('Contributor', 'job-promo');
            case 'Subscriber':
                return __('Subscriber', 'job-promo');
            case 'Anyone':
                return __('Anyone', 'job-promo');
        }
        return $optionValue;
    }

    /**
     * Query MySQL DB for its version
     * @return string|false
     */
    protected function getMySqlVersion() {
        global $wpdb;
        $rows = $wpdb->get_results('select version() as mysqlversion');
        if (!empty($rows)) {
            return $rows[0]->mysqlversion;
        }
        return false;
    }

    /**
     * If you want to generate an email address like "no-reply@your-site.com" then
     * you can use this to get the domain name part.
     * E.g.  'no-reply@' . $this->getEmailDomain();
     * This code was stolen from the wp_mail function, where it generates a default
     * from "wordpress@your-site.com"
     * @return string domain name
     */
    public function getEmailDomain() {
        // Get the site domain and get rid of www.
        $sitename = strtolower($_SERVER['SERVER_NAME']);
        if (substr($sitename, 0, 4) == 'www.') {
            $sitename = substr($sitename, 4);
        }
        return $sitename;
    }

}
