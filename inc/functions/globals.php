<?php
/**
 * Set Up Globals Calls
 *
 * These files provide plugin-like functionality embedded within BC Douglas Fir Theme.
 *
 * @package BC Douglas Fir Theme
 */

/**
 * Globals class used to access Globals data
 */
class Globals {

	/**
	 * Settings
	 *
	 * @var string $settings Settings.
	 */
	public $settings;

	/**
	 * Globals Path
	 *
	 * @var string $path Globals Path.
	 */
	public $path;

	/**
	 * Globals URL
	 *
	 * @var string $url Globals URL.
	 */
	public $url;

	/**
	 * Globals Version Param
	 *
	 * @var string $version Version number.
	 */
	public $version;

	/**
	 * Analytics ID
	 *
	 * @var string $analytics Analytics ID.
	 */
	public $analytics;


	/**
	 * HTML File Path
	 *
	 * @var string $html_filepath File Path..
	 */
	public $html_filepath;

	/**
	 * Lite Head Filename
	 *
	 * @var string $lhead_filename Lite Head Filename.
	 */
	public $lhead_filename = 'lhead.html';

	/**
	 * Branded Head Filename
	 *
	 * @var string $bhead_filename Branded Head Filename.
	 */
	public $bhead_filename = 'bhead.html';

	/**
	 * Branded Footer Filename
	 *
	 * @var string $bfoot_filename Branded Footer Filename.
	 */
	public $bfoot_filename = 'bfoot.html';

	/**
	 * Legal Footer Filename
	 *
	 * @var string $legal_filename Legal Footer Filename.
	 */
	public $legal_filename = 'legal.html';

	/**
	 * Lite Analytics Script Filename
	 *
	 * @var string $galite_filename Filename.
	 */
	public $galite_filename = 'galite.html';

	/**
	 * Branded Analytics Script Filename
	 *
	 * @var string $gabranded_filename Filename.
	 */
	public $gabranded_filename = 'gabranded.html';


	/**
	 * Constructor
	 */
	public function __construct() {

		/**
		 * Load Settings Array
		 */
		$this->settings = bc_douglas_fir_get_options();

		/**
		 * Globals Path (local filesystem)
		 *
		 * When append is enabled, prepend DOCUMENT_ROOT to the configured path
		 * (e.g. /var/www/html + /g/4/). Accepts boolean true (Customizer) and
		 * legacy "1" values; the old `( "1" || true ) ===` check only matched
		 * boolean true because PHP's || returns a boolean.
		 */
		$globals_path        = $this->get_globals_option( 'globals_path' );
		$should_append_path  = ! empty( $this->get_globals_option( 'globals_append_path' ) );
		$this->path          = ( ! empty( $globals_path ) && $should_append_path )
			? $_SERVER['DOCUMENT_ROOT'] . $globals_path
			: $globals_path;

		$this->path = apply_filters( 'bc_douglas_fir_globals_path', $this->path );

		/**
		 * Filenames
		 */
		$this->html_filepath = $this->path . 'h/';

		/**
		 * Globals URL
		 */
		$this->url = $this->get_globals_option( 'globals_url' ) ?? '/g/4';
		$this->url = apply_filters( 'bc_douglas_fir_globals_url', $this->url );

		/**
		 * Globals Version
		 */
		$this->version = $this->get_globals_option( 'globals_version' );


		/**
		 * Actions
		 */
		add_action( 'bc_douglas_fir_header', array( $this, 'tophead' ) );
		add_action( 'bc_douglas_fir_header', array( $this, 'tophead_big' ) );
		add_action( 'bc_douglas_fir_footer', array( $this, 'footer' ), 50 );

	}

	/**
	 * Get an option from globals
	 *
	 * @param string $option Option Name.
	 * @return string Option.
	 */
	private function get_globals_option( $option ) {
		return $this->settings[ $option ];
	}

	/**
	 * Lite Header
	 */
	public function tophead() {
		$header_top = $this->html_filepath . $this->lhead_filename;
		if ( file_exists( $header_top ) ) {
			include_once $header_top;
		}
	}

	/**
	 * Branded Header
	 */
	public function tophead_big() {
		$header_top_big = $this->html_filepath . $this->bhead_filename;
		if ( file_exists( $header_top_big ) ) {
			include_once $header_top_big;
		}
	}

	/**
	 * Footer
	 */
	public function footer() {
		$this->big_footer();

		$this->footer_legal();
	}

	/**
	 * Footer
	 */
	public function big_footer() {
		$footer = $this->html_filepath . $this->bfoot_filename;
		if ( file_exists( $footer ) ) {
			include_once $footer;
		}
	}

	/**
	 * Legal Footer
	 */
	public function footer_legal() {
		$footerlegal = $this->html_filepath . $this->legal_filename;
		if ( file_exists( $footerlegal ) ) {
			include_once $footerlegal;
		}
	}

	/**
	 * Analytics
	 */
	public function analytics() {
		$ga_code = $this->html_filepath . $this->galite_filename;
		if ( file_exists( $ga_code ) ) {
			include_once $ga_code;
		}

		if ( bc_douglas_fir_get_option( 'ga_code' ) ) :

			if ( substr( bc_douglas_fir_get_option('ga_code'), 0, 3 ) === 'UA-' ) :
				// Format reference https://developers.google.com/analytics/devguides/collection/gajs/?hl=nl&csw=1#MultipleCommands.

				?><script type="text/javascript">
					/* Load google analytics scripts (may be duplicate) */
					(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
					(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
					m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
					})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
					/*Site-Specific GA code*/
					ga('create','<?php echo esc_attr( bc_douglas_fir_get_option('ga_code') ); ?>','bellevuecollege.edu',{'name':'singlesite'});
					ga('singlesite.send','pageview');
				</script>
				<?php
			elseif ( substr( bc_douglas_fir_get_option('ga_code'), 0, 2 ) === 'U-' || substr( bc_douglas_fir_get_option('ga_code'), 0, 2 ) === 'G-' ) :
				?><!-- Global site tag (gtag.js) - Google Analytics -->
				<script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr( bc_douglas_fir_get_option('ga_code') ); ?>"></script>
				<script>
				  window.dataLayer = window.dataLayer || [];
				  function gtag(){dataLayer.push(arguments);}
				  gtag('js', new Date());

				  gtag('config', '<?php echo esc_attr( bc_douglas_fir_get_option('ga_code') ); ?>');
				</script>
				<?php
			endif;
		endif;

	}

	/**
	 * Hook Analytics into Header
	 */
	public function hook_analytics() {
		add_action( 'wp_head', array( $this, 'analytics' ), 30 );
	}

}

