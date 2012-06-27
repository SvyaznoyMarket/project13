<?php
/**
 * Build Network Administration Menu.
 *
 * @package WordPress
 * @subpackage Multisite
 * @since 3.1.0
 */

/* translators: Network menu item */
$menu[2] = array(wp__('Dashboard'), 'manage_network', 'index.php', '', 'menu-top menu-top-first menu-icon-dashboard', 'menu-dashboard', 'div');

$menu[4] = array( '', 'read', 'separator1', '', 'wp-menu-separator' );

/* translators: Sites menu item */
$menu[5] = array(wp__('Sites'), 'manage_sites', 'sites.php', '', 'menu-top menu-icon-site', 'menu-site', 'div');
$submenu['sites.php'][5]  = array( wp__('All Sites'), 'manage_sites', 'sites.php' );
$submenu['sites.php'][10]  = array( _x('Add New', 'site'), 'create_sites', 'site-new.php' );

$menu[10] = array(wp__('Users'), 'manage_network_users', 'users.php', '', 'menu-top menu-icon-users', 'menu-users', 'div');
$submenu['users.php'][5]  = array( wp__('All Users'), 'manage_network_users', 'users.php' );
$submenu['users.php'][10]  = array( _x('Add New', 'user'), 'create_users', 'user-new.php' );

$update_data = wp_get_update_data();

if ( $update_data['counts']['themes'] ) {
	$menu[15] = array(sprintf( wp__( 'Themes %s' ), "<span class='update-plugins count-{$update_data['counts']['themes']}'><span class='theme-count'>" . number_format_i18n( $update_data['counts']['themes'] ) . "</span></span>" ), 'manage_network_themes', 'themes.php', '', 'menu-top menu-icon-appearance', 'menu-appearance', 'div' );
} else {
	$menu[15] = array( wp__( 'Themes' ), 'manage_network_themes', 'themes.php', '', 'menu-top menu-icon-appearance', 'menu-appearance', 'div' );
}
$submenu['themes.php'][5]  = array( wp__('Installed Themes'), 'manage_network_themes', 'themes.php' );
$submenu['themes.php'][10] = array( _x('Add New', 'theme'), 'install_themes', 'theme-install.php' );
$submenu['themes.php'][15] = array( _x('Editor', 'theme editor'), 'edit_themes', 'theme-editor.php' );

if ( current_user_can( 'update_plugins' ) ) {
	$menu[20] = array( sprintf( wp__( 'Plugins %s' ), "<span class='update-plugins count-{$update_data['counts']['plugins']}'><span class='plugin-count'>" . number_format_i18n( $update_data['counts']['plugins'] ) . "</span></span>" ), 'manage_network_plugins', 'plugins.php', '', 'menu-top menu-icon-plugins', 'menu-plugins', 'div');
} else {
	$menu[20] = array( wp__('Plugins'), 'manage_network_plugins', 'plugins.php', '', 'menu-top menu-icon-plugins', 'menu-plugins', 'div' );
}
$submenu['plugins.php'][5]  = array( wp__('Installed Plugins'), 'manage_network_plugins', 'plugins.php' );
$submenu['plugins.php'][10] = array( _x('Add New', 'plugin editor'), 'install_plugins', 'plugin-install.php' );
$submenu['plugins.php'][15] = array( _x('Editor', 'plugin editor'), 'edit_plugins', 'plugin-editor.php' );


$menu[25] = array(wp__('Settings'), 'manage_network_options', 'settings.php', '', 'menu-top menu-icon-settings', 'menu-settings', 'div');
if ( defined( 'MULTISITE' ) && defined( 'WP_ALLOW_MULTISITE' ) && WP_ALLOW_MULTISITE ) {
	$submenu['settings.php'][5]  = array( wp__('Network Settings'), 'manage_network_options', 'settings.php' );
	$submenu['settings.php'][10] = array( wp__('Network Setup'), 'manage_network_options', 'setup.php' );
}

if ( $update_data['counts']['total'] ) {
	$menu[30] = array( sprintf( wp__( 'Updates %s' ), "<span class='update-plugins count-{$update_data['counts']['total']}' title='{$update_data['title']}'><span class='update-count'>" . number_format_i18n($update_data['counts']['total']) . "</span></span>" ), 'manage_network', 'upgrade.php', '', 'menu-top menu-icon-tools', 'menu-update', 'div' );
} else {
	$menu[30] = array( wp__( 'Updates' ), 'manage_network', 'upgrade.php', '', 'menu-top menu-icon-tools', 'menu-update', 'div' );
}

unset($update_data);

$submenu[ 'upgrade.php' ][10] = array( wp__( 'Available Updates' ), 'update_core',  'update-core.php' );
$submenu[ 'upgrade.php' ][15] = array( wp__( 'Update Network' ), 'manage_network', 'upgrade.php' );

$menu[99] = array( '', 'read', 'separator-last', '', 'wp-menu-separator-last' );

require_once(ABSPATH . 'wp-admin/includes/menu.php');

?>
