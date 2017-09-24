<?php


namespace calderawp\calderaforms\pro\admin;


/**
 * Class menu
 * @package calderawp\calderaforms\pro\admin
 */
class menu {

	protected $view_dir;

	/**
	 * @var scripts
	 */
	protected $scripts;

	protected  $slug;
	public function __construct( $view_dir, $slug, scripts $scripts ){
		$this->view_dir = $view_dir;
		$this->slug = $slug;
		$this->scripts = $scripts;
	}

	/**
	 * Create admin page view
	 *
	 * @since 0.1.0
	 */
	public function display() {
		add_submenu_page(
			\Caldera_Forms::PLUGIN_SLUG,
			__( 'Caldera Forms Pro', 'caldera-forms-pro'),
			'<span class="caldera-forms-menu-dashicon"><span class="dashicons dashicons-star-filled"></span>' .__( 'Caldera Forms Pro', 'caldera-forms-pro') . '</span>',
			'manage_options',
			$this->slug,
			[ $this, 'render' ]
		);
	}

	/**
	 * Redner admin page view
	 *
	 * @since 0.1.0
	 */
	public function render() {
		$inline = \Caldera_Forms_Render_Util::create_cdata('var CF_PRO_ADMIN= ' . wp_json_encode( $this->scripts->data() ) . ';' );
		wp_enqueue_style( \Caldera_Forms_Admin_Assets::slug( 'admin', false ), \Caldera_Forms_Render_Assets::make_url( 'admin', false ) );
		ob_start();
		include $this->view_dir . '/index.html';
		$str = ob_get_clean();
		foreach ( [
			'styles',
			'manifest',
			'vendor',
			'client'
		] as $thing ){
			$str = str_replace( '/' . $thing, $this->scripts->get_assets_url() . $thing, $str );
		}
		echo $inline .str_replace([
			'<head>',
			'</head>'
		], '', $str );

	}

}