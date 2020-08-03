<?php
defined( 'ABSPATH' ) || die( 'Something went wrong.' );

function bawnm_get_icons() {
	$list = [
		 'freemium' => __( 'also exists as a Pro/Premium/Paid version?', 'bawnm' ),
		 'email'    => __( 'asks for your email address?', 'bawnm' ),
		 'notices'  => __( 'displays untimely admin notices?', 'bawnm' ),
		 'ad'       => __( 'displays ads for other products/services than theirs?', 'bawnm' ),
		 'file'     => __( 'creates or modifies any file?', 'bawnm' ),
		 'database' => __( 'creates a new table or add bulky entries in the database?', 'bawnm' ),
		 'dev'      => __( 'requires some development skills?', 'bawnm' ),
		 'i18n'     => __( 'is ready for internationalization?', 'bawnm' ),
		 'licence'  => __( 'need a (free or paid) licence key to be functionnal?', 'bawnm' ),
		 'support'  => __( 'offers (free or paid) support?', 'bawnm' ),
		 'doc'      => __( 'offers a free documentation?', 'bawnm' ),
		 'addons'   => __( 'offers (free or paid) addons?', 'bawnm' ),
		 'api'      => __( 'calls an external API or Website?', 'bawnm' ),
		 'github'   => __( 'has a Github repository?', 'bawnm' ),
		 'services' => __( 'offers (free or paid) services?', 'bawnm' ),
		 'wpstyle'  => __( 'displays its settings with the default WordPress style?', 'bawnm' ),
		 'donation' => __( 'asks for a donation?', 'bawnm' ),
	 ];
	return apply_filters( 'bawnm_icon_list', $list );
}
function bawnm_get_uniqid() {
	return md5( home_url() );
}

function bawnm_get_label( $index ) {
	$labels = [
		'0' => __( 'No', 'bawnm' ),
		'1' => __( 'Yes', 'bawnm' )
	];
	if ( isset( $labels[ $index ] ) ) {
		return $labels[ $index ];
	}
	return __( 'I don’t know', 'bawnm' );
}
