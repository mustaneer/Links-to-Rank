<?php
/**
 * Plugin Name:	   Custom Links Estimation
 * Plugin URI:		https://mustaneerabdullah.com
 * Description:	   Plugin will get user data from wpforms entry and calculate using MOZ API. [wc_links_estimation] shortcode will be used to show results.
 * Version:		   1.0
 * Author:			Mustaneer Abdullah
 * Author URI:		https://mustaneerabdullah.com
 * License:		   GPL-2.0+
 * License URI:	   http://www.gnu.org/licenses/gpl-2.0.txt
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Links_Estimation' ) ) {
	/**
	 * Main Plugin Class
	 *
	 * @since 1.0
	 */
	/**
	 * Class Links_Estimation
	 */
	class Links_Estimation {

		/**
		 * Plugin name for text domains.
		 *
		 * @var
		 */
		public static $plugin_name='links-estimation';

		/**
		 * Plugin base name
		 *
		 * @var string
		 */
		public static $plugin_basename;

		/**
		 * Plugin file path
		 *
		 * @var string
		 */
		public static $plugin_path;

		/**
		 * Plugin url
		 *
		 * @var string
		 */
		public static $plugin_url;

		/**
		 * MOZ Access ID
		 *
		 * @var string
		 */
		public static $moz_access_id;

		/**
		 * MOZ Secret Key
		 *
		 * @var string
		 */
		public static $moz_secret_key;

		public function __construct() {
			self::$plugin_basename=plugin_basename( __FILE__ );
			self::$plugin_path=plugin_dir_path( __FILE__ );
			self::$plugin_url=plugin_dir_url( __FILE__ );
			self::$moz_access_id='mozscape-931bcb3f41';
			self::$moz_secret_key='38e12880025604740dae14f058c5813f';

			//Enqueue javascript and style
			add_action( 'wp_enqueue_scripts', [ $this, 'links_estimation_resources' ] );

			add_filter( 'wpforms_process_redirect_url', [ $this, 'wc_form_id_in_redirect' ], 10, 3 );
			add_filter( 'wpforms_smart_tags', [ $this, 'wc_register_custom_smarttag' ] );
			add_filter( 'wpforms_smart_tag_process', [ $this, 'wc_dev_custom_smarttags_function' ], 10, 2 );

			// add frontend feedback form shortcodes
			add_shortcode( 'wc_links_estimation', [ $this, 'wc_links_estimation_function' ] );
		}

		/**
		 * Loads plugin resources
		 */
		public function links_estimation_resources() {
			// Frontend style
			wp_enqueue_style(
				'wc-links-frontend-style',
				self::$plugin_url . 'assets/css/wc-links-style.css',
				[],
				strtotime( 'now' )
			);

			wp_enqueue_script(
				'wc-links-script',
				self::$plugin_url . 'assets/js/wc-links-script.js',
				[ 'jquery' ],
				strtotime( 'now' ),
				true
			);
		}

		/**
		 * Register the Entry ID Smart Tag so it will be available to select in the form builder.
		 *
		 * @param array $tags
		 *
		 * @return array
		 */
		public function wc_register_custom_smarttag( $tags ) {

			// Key is the tag, item is the tag name.
			$tags['links-estimation-result']='Link Estimation Result';

			return $tags;
		}

		/**
		 * Process the Entry ID Smart Tag.
		 *
		 * @see
		 *
		 * @param string $content
		 * @param string $tag
		 *
		 * @return string
		 */
		public function wc_dev_custom_smarttags_function( $content, $tag ) {
			if ( $tag === 'links-estimation-result' ) {
				//$content = str_replace( '{' . $tag . '}', 'Your entry ID is ' . absint( wpforms()->process->entry_id ), $content );
				ob_start();
				include 'shortcode/links-estimation-shortcode.php';
				$output=ob_get_clean();
				$content=str_replace( '{' . $tag . '}', $output, $content );

				return $content;
			}
			//return $content;
		}

		/**
		 * WPForms, Pass Entry ID on form completion
		 */
		public function wc_form_id_in_redirect( $url, $form_id, $fields ) {
			if ( $form_id != 38838 ) {
				return;
			}
			$entry_id=$_POST['wpforms']['entry_id'];

			return $this->wc_url_with_entry( $url, $entry_id );
		}

		/**
		 * URL with Entry
		 */
		public function wc_url_with_entry( $url=false, $entry_id=false, $scheme='auth' ) {
			$url=add_query_arg(
				[
					'entry_id'   => $entry_id,
					'entry_hash' => $this->wc_entry_hash( $entry_id, $scheme ),
				],
				esc_url( $url )
			);

			return $url;
		}

		/**
		 * Entry Hash
		 */
		public function wc_entry_hash( $entry_id=false, $scheme='auth' ) {
			return sha1( 'entry' . $entry_id . wp_salt( $scheme ) );
		}

		/**
		 * Verify Entry
		 */
		public function wc_verify_entry() {
			$return=[ 'type' => 'error', 'message' => 'Unknown Error' ];
			$entry_id=isset( $_GET['entry_id'] ) ? esc_attr( $_GET['entry_id'] ) : false;
			$entry_hash=isset( $_GET['entry_hash'] ) ? esc_attr( $_GET['entry_hash'] ) : false;
			$verified_hash=[ $this->wc_entry_hash( $entry_id ) ];

			if ( is_user_logged_in() ) {
				$verified_hash[]=$this->wc_entry_hash( $entry_id, 'logged_in' );
			}

			if ( ! $entry_id ) {
				$return['message']='No entry specified';
			} elseif ( ! $entry_hash ) {
				$return['message']='Security hash not provided';
			} elseif ( !in_array( $entry_hash, $verified_hash ) ) {
				$return['message']='Security hash incorrect';
			} else {
				$entry=wpforms()->entry->get( $entry_id );

				if ( empty( $entry ) ) {
					$return['message']='Entry cannot be found';
				} else {
					$return=[ 'type' => 'success', 'message' => 'Success', 'entry' => $entry ];
				}
			}

			return $return;
		}

		/**
		 * @param array $atts
		 *
		 * @return false|string
		 */
		public function wc_links_estimation_function( $atts=[] ) {
			ob_start();
			$atts=shortcode_atts( [
				'show' => 'no',
			], $atts );

			$verified_entry=( !empty( $this->wc_verify_entry() ) ) ? $this->wc_verify_entry() : ['type'=>'error'];

			if ( !empty( $verified_entry ) && 'error' != $verified_entry['type'] ) {
				if ( 'error' == $verified_entry['type'] ) {
					echo '<p class="error">Error: ' . $verified_entry['message'] . '</p><p>To receive an estimate, please complete <a href="' . home_url( 'test-form' ) . '">this form</a>.</p>';
				} else {
					if ( 'success' == $verified_entry['type'] ) {
						include 'shortcode/links-estimation-shortcode.php';
					}
				}
			}

			return ob_get_clean();
		}

		public function moz_api_url_metrics( $accessID, $secretKey, $batchedDomains ) {

			// Set your expires times for several minutes into the future.
			// An expires time excessively far in the future will not be honored by the Mozscape API.
			$expires=time() + 300;

			// Put each parameter on a new line.
			$stringToSign=$accessID . "\n" . $expires;

			// Get the "raw" or binary output of the hmac hash.
			$binarySignature=hash_hmac( 'sha1', $stringToSign, $secretKey, true );

			// Base64-encode it and then url-encode that.
			$urlSafeSignature=urlencode( base64_encode( $binarySignature ) );

			// Add up all the bit flags you want returned.
			$cols='103079215140';

			// Put it all together and you get your request URL.
			$requestUrl='http://lsapi.seomoz.com/linkscape/url-metrics/?Cols=' . $cols . '&AccessID=' . $accessID . '&Expires=' . $expires . '&Signature=' . $urlSafeSignature;

			// Put your URLS into an array and json_encode them.
			//$batchedDomains = array('www.clickintelligence.dev', 'www.mustaneerabdullah.com', 'www.facebook.com');
			$encodedDomains=json_encode( $batchedDomains );

			// Send your encoded list of domains through Curl's POSTFIELDS.
			$options=[
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POSTFIELDS	 => $encodedDomains,
			];

			$ch=curl_init( $requestUrl );
			curl_setopt_array( $ch, $options );
			$content=curl_exec( $ch );
			curl_close( $ch );

			$contents=json_decode( $content );

			return  $contents;
		}
	}
}

add_action( 'plugins_loaded', 'wc_links_estimation_init' );
/**
 * @return Links_Estimation
 */
function wc_links_estimation_init() {

	//Plugin global variable
	return $GLOBALS['links_estimation_obj']=new Links_Estimation();
}
