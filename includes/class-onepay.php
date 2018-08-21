<?php
if (! defined('ABSPATH')) {
    exit;
}

use Transbank\Onepay\OnepayBase;
use Transbank\Onepay\ShoppingCart;
use Transbank\Onepay\Item;
use Transbank\Onepay\Transaction;
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/TransbankDevelopers/transbank-plugin-woocommerce-onepay
 * @since      1.0.0
 *
 * @package    Onepay
 * @subpackage Onepay/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Onepay
 * @subpackage Onepay/includes
 * @author     Onepay <transbankdevelopers@continuum.cl>
 */
class Onepay extends WC_Payment_Gateway {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Onepay_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $plugin_name    The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string    $version    The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */

    public static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new Onepay();
            return self::$instance;
        }
        return self::$instance;
    }



    public function __construct() {
        if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
            $this->version = PLUGIN_NAME_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        $this->plugin_name = 'onepay';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
        $this->define_public_hooks();

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        $this->id                 = 'onepay';
        $this->title              = __( 'Onepay', 'onepay' );
        $this->description        = __( 'This is the payment gateway description', 'onepay' );
		$this->icon               = apply_filters('woocommerce_Onepay_icon', plugin_dir_url( dirname( __FILE__ ) ) . 'public/images/logo_onepay.png');
		$this->has_fields         = false;
		$this->method_title       = __( 'Onepay', 'onepay' );
		$this->method_description = __( 'This is the payment gateway description', 'onepay' );
		$this->supports = array(
			'products'
		  );


        // Define user set variables
        $this->apikey         = $this->get_option( 'apikey' );
        $this->shared_secret   = $this->get_option( 'shared_secret' );

        // Actions
        add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
        add_action( 'woocommerce_api_'.strtolower(get_class($this)), array($this, 'callback_handler'));

        add_action( 'rest_api_init', function () {
            register_rest_route( 'onepay/v1', '/transaction', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_transaction'),
            ));

            register_rest_route( 'onepay/v1', '/commit', array(
                'methods' => 'GET',
                'callback' => array($this, 'commit_transaction'),
            ));
        });

        self::$instance = $this;
    }

    function commit_transaction($data) {

        $order_id = WC()->session->get('order_id');
        $externalUniqueNumber = $data['externalUniqueNumber'];
        $transactionCommitResponse = Transaction::commit($data['occ'], $externalUniqueNumber);
        $order = new WC_Order($order_id);

        if($transactionCommitResponse->getResponseCode() == 'OK') {
            $order->update_status('completed');
            $order->payment_complete();
            update_post_meta($order_id, 'occ', $transactionCommitResponse->getOcc());
            update_post_meta($order_id, 'externalUniqueNumber', $externalUniqueNumber);
            update_post_meta($order_id, 'buyOrder', $transactionCommitResponse->getBuyOrder());
            update_post_meta($order_id, 'description', $transactionCommitResponse->getDescription());
            update_post_meta($order_id, 'amount', $transactionCommitResponse->getAmount());
            update_post_meta($order_id, 'installmentsNumber', $transactionCommitResponse->getInstallmentsNumber());
            update_post_meta($order_id, 'installmentsAmount', $transactionCommitResponse->getInstallmentsAmount());
            update_post_meta($order_id, 'issuedAt', $transactionCommitResponse->getIssuedAt());
            update_post_meta($order_id, 'authorizationCode', $transactionCommitResponse->getAuthorizationCode());
            update_post_meta($order_id, 'signature', $transactionCommitResponse->getSignature());

            } else {
                $order->update_status('cancelled');
            }

        WC()->session->set('order_id', null);

        if ( wp_redirect($order->get_checkout_order_received_url()) ) {
            exit;
        }
    }

    function create_transaction($data) {
        $carro = new ShoppingCart();

        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $nombre = $cart_item['data']->get_title();
            $cantidad = $cart_item['quantity'];
            $precio = intval($cart_item['data']->get_price());

            $item = new Item($nombre, $cantidad, $precio);
            $carro->add($item);
        }

        if (WC()->cart->get_shipping_total() != 0) {
            $item = new Item("Costo por envio", 1, intval(WC()->cart->get_shipping_total()));
            $carro->add($item);
        }

        $transaction = Transaction::create($carro);
        $response = [];

        $response['occ'] = $transaction->getOcc();
        $response['ott'] = $transaction->getOtt();
        $response['externalUniqueNumber'] = $transaction->getExternalUniqueNumber();
        $response['qrCodeAsBase64'] = $transaction->getQrCodeAsBase64();
        $response['issuedAt'] = $transaction->getIssuedAt();
        $response['signature'] = $transaction->getSignature();
        $response['amount'] = $carro->getTotal();

        return $response;
    }

	function callback_handler() {
		//Handle the thing here!
		global $woocommerce;
		@ob_clean();

		wp_redirect($order->get_shipping_first_name());
		error_log('handle');

	  }

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Onepay_Loader. Orchestrates the hooks of the plugin.
	 * - Onepay_i18n. Defines internationalization functionality.
	 * - Onepay_Admin. Defines all hooks for the admin area.
	 * - Onepay_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

        /**
         * The class responsible for loading external dependencies of the
         * core plugin.
         */

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'vendor/autoload.php';
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-onepay-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-onepay-i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-onepay-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-onepay-public.php';

        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-onepay-api.php';

        $this->loader = new Onepay_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Onepay_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale() {

        $plugin_i18n = new Onepay_i18n();

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks() {

        $plugin_admin = new Onepay_Admin( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action('plugin_action_links_onepay',$plugin_admin, 'plugin_action_links');
    }

    /**
     * Register all of the hooks related to the publsic-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks() {

        $plugin_public = new Onepay_Public( $this->get_plugin_name(), $this->get_version() );

        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

    }

    /**
         * Create form fields for the payment gateway
         *
         * @return void
         */
        public function init_form_fields() {
            $this->form_fields = array(
                'enabled' => array(
                    'title' => __( 'Activa/Desactiva', 'onepay' ),
                    'type' => 'checkbox',
                    'label' => __( 'Activar Onepay', 'onepay' ),
                    'default' => 'no'
                ),
                'apikey' => array(
                    'title' => __( 'APIKey', 'onepay' ),
                    'type' => 'text'
                ),
                'shared_secret' => array(
                    'title' => __( 'Shared Secret', 'onepay' ),
                    'type' => 'text'
                ),
                'endpoint' => array(
                    'title' => __('Endpoint', 'onepay'),
                    'type' => 'select',
                    'default' => 0,
                    'options' => array(
                      'Test' => __( 'Test', 'onepay' ),
                      'Integration' => __( 'Integración', 'onepay' ),
                      'Production' => __( 'Producción', 'onepay' )
                    )
                )
            );
        }


    function admin_options() {


        parent::admin_options();
        ?>
        <style type="text/css">
            .generate-pdf-button {
                height: 2em;
                font-weight:  bold;
                font-size: 1rem;
            }
            .generate-pdf-link {
                text-decoration: none;
                color: black;
            }
        </style>
        <div>
            <button class="generate-pdf-button">
                <a class="generate-pdf-link"
                   href=<?php echo plugin_dir_url( dirname( __FILE__ ) ) . 'admin/diagnostic_pdf.php' ?>
                   target="_blank"
                   rel="noopener"
                   >Generar PDF de Diagnóstico</a>
            </button>
        </div>

        <?php
    }

    function payment_fields(){
        echo wpautop( wptexturize( "¡Paga con Onepay! En la siguiente pantalla podrás escanear el código QR, o ingresar el código de compra." ) );
    }

    function validate_fields() {
        $is_valid = parent::validate_fields();
        return $is_valid;
    }

    public function process_payment( $order_id ) {
        global $woocommerce, $post;

        $shared_secret = $this->get_option( 'shared_secret' );
        $api_key = $this->get_option( 'apikey' );

        OnepayBase::setSharedSecret($shared_secret);
        OnepayBase::setApiKey($api_key);


        $order = new WC_Order( $order_id );

        WC()->session->set('order_id', $order_id);
        // TODO missing correct URL redirection
        return array(
            'result'    => 'success',
            'redirect'  => plugin_dir_url( dirname( __FILE__ ) ) . 'public/pago/pagar.php'
        );
    }


    public function plugin_action_links( $links, $file) {
        if ($file == 'onepay/onepay.php') {
            $plugin_links = array(
                '<a href="admin.php?page=wc-settings&tab=checkout&section=onepay">' . esc_html__( 'Settings', 'woocommerce-gateway-onepay' ) . '</a>'
            );


            return array_merge( $plugin_links, $links );
        }
        return $links;
    }
    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run() {
        add_filter( 'plugin_action_links', array($this, 'plugin_action_links'), 10, 2 );
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Onepay_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

}
