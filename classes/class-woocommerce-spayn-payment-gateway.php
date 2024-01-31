<?php
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Spayn_Payment_Gateway' ) ) {

    /**
     * Spayn Payment method
     *
     * @class       WC_Spayn_Payment_Gateway
     * @extends     WC_Payment_Gateway
     * @version     2.1.0
     */
    class WC_Spayn_Payment_Gateway extends WC_Payment_Gateway {
        const IFRAME_ID = 'spayn_iframe';
        const AMOUNT_CENT_CONVERT_FOR_PAYMENT = 100.0;

        protected static $_instance = null;
        private $order_status;
        private $Environments=['PRODUCTION','TEST'];
        private $SecureTypes=['DEFINED_BY_RULES','SECURE','NOT SECURE'];
        private $Currency_Code= array(
            'AFA' => '971',
            'AWG' => '533',
            'AUD' => '036',
            'ARS' => '032',
            'AZN' => '944',
            'BSD' => '044',
            'BDT' => '050',
            'BBD' => '052',
            'BYR' => '974',
            'BOB' => '068',
            'BRL' => '986',
            'GBP' => '826',
            'BGN' => '975',
            'KHR' => '116',
            'CAD' => '124',
            'KYD' => '136',
            'CLP' => '152',
            'CNY' => '156',
            'COP' => '170',
            'CRC' => '188',
            'HRK' => '191',
            'CPY' => '196',
            'CZK' => '203',
            'DKK' => '208',
            'DOP' => '214',
            'XCD' => '951',
            'EGP' => '818',
            'ERN' => '232',
            'EEK' => '233',
            'EUR' => '978',
            'GEL' => '981',
            'GHC' => '288',
            'GIP' => '292',
            'GTQ' => '320',
            'HNL' => '340',
            'HKD' => '344',
            'HUF' => '348',
            'ISK' => '352',
            'INR' => '356',
            'IDR' => '360',
            'ILS' => '376',
            'JMD' => '388',
            'JPY' => '392',
            'KZT' => '368',
            'KES' => '404',
            'KWD' => '414',
            'LVL' => '428',
            'LBP' => '422',
            'LTL' => '440',
            'MOP' => '446',
            'MKD' => '807',
            'MGA' => '969',
            'MYR' => '458',
            'MTL' => '470',
            'BAM' => '977',
            'MUR' => '480',
            'MXN' => '484',
            'MZM' => '508',
            'NPR' => '524',
            'ANG' => '532',
            'TWD' => '901',
            'NZD' => '554',
            'NIO' => '558',
            'NGN' => '566',
            'KPW' => '408',
            'NOK' => '578',
            'OMR' => '512',
            'PKR' => '586',
            'PYG' => '600',
            'PEN' => '604',
            'PHP' => '608',
            'QAR' => '634',
            'RON' => '946',
            'RUB' => '643',
            'SAR' => '682',
            'CSD' => '891',
            'SCR' => '690',
            'SGD' => '702',
            'SKK' => '703',
            'SIT' => '705',
            'ZAR' => '710',
            'KRW' => '410',
            'LKR' => '144',
            'SRD' => '968',
            'SEK' => '752',
            'CHF' => '756',
            'TZS' => '834',
            'THB' => '764',
            'TTD' => '780',
            'TRY' => '949',
            'AED' => '784',
            'USD' => '840',
            'UGX' => '800',
            'UAH' => '980',
            'UYU' => '858',
            'UZS' => '860',
            'VEB' => '862',
            'VND' => '704',
            'AMK' => '894',
            'ZWD' => '716'
        );
        
        public function __construct(){
            $this->id = 'spayn_payment';
            $this->method_title = __('WooCommerce SPayN Payment','woocommerce-spayn-payment-gateway');
            $this->title = __('SPayN Payment Gateway','woocommerce-spayn-payment-gateway');
            $this->has_fields = true;
            $this->init_form_fields();
            $this->init_settings();
            $this->enabled = $this->get_option('enabled');
            $this->title = $this->get_option('title');
            $this->order_status = $this->get_option('order_status');
            $this->ENVIRONMENT = $this->get_option('ENVIRONMENT');
            $this->CURRENCY_CODE = $this->get_option('CURRENCY_CODE');
            $this->MERCHANT = $this->get_option('MERCHANT');
            $this->ACCOUNT = $this->get_option('ACCOUNT');
            $this->API_KEY = $this->get_option('API_KEY');
            $this->AUTO_REDIRECT = $this->get_option('AUTO_REDIRECT');
            $this->AUTO_SUBMIT = $this->get_option('AUTO_SUBMIT');
            $this->SECURE_TYPE = $this->get_option('SECURE_TYPE');
            $this->BASE_BACKGROUND_COLOR = $this->get_option('BASE_BACKGROUND_COLOR');
            $this->FRAME_BACKGROUND_COLOR =  $this->get_option('FRAME_BACKGROUND_COLOR');
            $this->FRAME_LABEL_COLOR = $this->get_option('FRAME_LABEL_COLOR');
            $this->BUTTON_BACKGROUND_COLOR = $this->get_option('BUTTON_BACKGROUND_COLOR');
            $this->BUTTON_LABEL_COLOR = $this->get_option('BUTTON_LABEL_COLOR');
            $this->TEXT_LABEL_AMOUNT = $this->get_option('TEXT_LABEL_AMOUNT');
            $this->TEXT_LABEL_CONCEPT = $this->get_option('TEXT_LABEL_CONCEPT');
            $this->TEXT_LABEL_ALIAS = $this->get_option('TEXT_LABEL_ALIAS');
            $this->TEXT_LABEL_CARD = $this->get_option('TEXT_LABEL_CARD');
            $this->TEXT_LABEL_EXP_MONTH = $this->get_option('TEXT_LABEL_EXP_MONTH');
            $this->TEXT_LABEL_EXP_YEAR = $this->get_option('TEXT_LABEL_EXP_YEAR');
            $this->TEXT_LABEL_PAYMENT_DATE = $this->get_option('TEXT_LABEL_PAYMENT_DATE');
            $this->TEXT_LABEL_PAN = $this->get_option('TEXT_LABEL_PAN');
            $this->TEXT_LABEL_AUTH_CODE = $this->get_option('TEXT_LABEL_AUTH_CODE');
            $this->TEXT_LABEL_OPERATION_CODE = $this->get_option('TEXT_LABEL_OPERATION_CODE');
            $this->TEXT_BUTTON_BACK = $this->get_option('TEXT_BUTTON_BACK');
 
            $this->includes();
            $this->hooks();
        }

        public function getInstance(){
            if ( is_null( self::$_instance ) ) {
                self::$_instance = new self();
            }

            return self::$_instance;
        }
   
        public function assets_url($uri){
            error_log('Asset loading ' .$uri);
            return $this->plugin_url('/assets/'.$uri);
        } 

        public function plugin_url($uri){
            if(function_exists('plugins_url')){return plugins_url('/woocommerce-spayn-payment-gateway-2/'.$uri);}
            if(function_exists('wpcf7_plugin_url')){return wpcf7_plugin_url('/woocommerce-spayn-payment-gateway-2/'.$uri);}
            else{return 'http://localhost/wp-content/plugins/woocommerce-spayn-payment-gateway-2/' . $uri;}
        } 


        function display_spayn_order_details($order){
            echo '<p><strong>'.__('SPayN Payment Reference').':</strong> ' . get_post_meta( $order->id, 'SPayN Merchant Operation', true ). '</p>';
        }

        function add_payment_field($checkout)
        {
            ob_start();
 
            $apiClient=new tokenRequest($this);
            $field_label=empty($this->TEXT_LABEL_OPERATION_CODE) ? __('SPayN Merchant Operation') : $this->TEXT_LABEL_OPERATION_CODE;
            $payment_ref=$apiClient->getMerchantOperation();
            printf('<div id="%1$s" class="spayn-payment-iframe"><p><h3>%2$s</h3><br><iframe sandbox="allow-same-origin allow-scripts allow-popups allow-forms" frameBorder="0" align="center" class="spayn-payment-iframe-contents" frameborder="no" src="%3$s"></iframe></div>',
                self::IFRAME_ID, $this->title, $apiClient->requestTokenAndReturnPaymentIframeContents());
                $payment_field = woocommerce_form_field('spayn_payment_ref', array(
                    'type' => 'text',
                    'class' => array(
                    'spayn-payment-field-class form-row-wide'
                ) ,
                'label' => $field_label,
                'placeholder' => __('Operation reference number') ,
                'required' => false,
            ) , $payment_ref);

            printf('<div id="spayn_payment_field">%1$s</div>', $payment_field);
        }

        function payment_ref_update_order_meta($order_id)
        {
            if (!empty($_POST['spayn_payment_ref'])) {
                update_post_meta($order_id, 'SPayN Merchant Operation', sanitize_text_field($_POST['spayn_payment_ref']));
            }
        }


        public function getLogedUser(){
            if(function_exists('wp_get_current_user')){
                $user=wp_get_current_user();
                return $user->user_login;}
            else{return '';}
        } 

        

        public function validate_fields() {
            return true;
        }
        public function generateRefNumber() {
            //$result=strtoupper(substr($this->MERCHANT,0,2));
            $result = substr(str_shuffle(str_repeat("ABCDEFGHIJKLMNOPQRSTUVWXYZ", 2)), 0, 2); //2 RANDOM CHARACTERS
            $mt = explode(' ', microtime());
            $result.=strval(((int)$mt[1]) * 1000 + ((int)round($mt[0] * 1000))); //EPOCH MILLISECONDS
            $result.=strval(rand(0,9)); //RANDOM NUM
            return $result;
        }
        public function process_payment( $order_id ) {
            global $woocommerce;
            $order = new WC_Order( $order_id );
            $order->update_status($this->order_status, __( 'Awaiting payment', 'woocommerce-spayn-payment-gateway' ));
            wc_reduce_stock_levels( $order_id );
            if(isset($_POST[ $this->id.'-admin-note']) && trim($_POST[ $this->id.'-admin-note'])!=''){
                $order->add_order_note(esc_html($_POST[ $this->id.'-admin-note']),1);
            }
            $woocommerce->cart->empty_cart();
            return array(
                'result' => 'success',
                'redirect' => $this->get_return_url( $order )
            );
        }
        public function clear_payment( $order_id ) {
            global $woocommerce;
            $order = new WC_Order( $order_id );
            // Mark as on-hold (we're awaiting the cheque)
            $order->update_status($this->order_status, __( 'Awaiting payment', 'woocommerce-spayn-payment-gateway' ));
            // Reduce stock levels
            wc_reduce_stock_levels( $order_id );
            if(isset($_POST[ $this->id.'-admin-note']) && trim($_POST[ $this->id.'-admin-note'])!=''){
                $order->add_order_note(esc_html($_POST[ $this->id.'-admin-note']),1);
            }
            // Remove cart
            $woocommerce->cart->empty_cart();
            // Return thankyou redirect
        }



        public function payment_fields(){
            ?>
                <script type="text/javascript">
                    var paybutton=document.getElementById('place_order');
                    if(paybutton!=null){
                        paybutton.style.visibility="hidden";
                        paybutton.value = '<?= __('Rellene los datos y continúe realice el pago') ?>';
                        paybutton.innerHTML = '<?= __('Rellene los datos y continúe realice el pago') ?>';
                        paybutton.onclick=function() {
                        console.log('Payment button clicked!');
                        document.getElementById('<?=self::IFRAME_ID?>').style.visibility="visible";
                            document.getElementById('<?=self::IFRAME_ID?>').scrollIntoView( false );
                            //window.document.getElementById('place_order').visibility="hidden";
                        };			
                    }
                </script>
            <?php
        }


        // Getters And Setters

        public function getApiKey(){return $this->API_KEY;}

        public function getAutoRedirect(){
            if ($this->AUTO_REDIRECT==1){return 'false';}
            else{return 'true';}
        }

        public function getAutoSubmit() {
            if ($this->AUTO_SUBMIT==1) {
                return 'true';
            }
            return 'false';
        }
        
        public function getMerchant(){return $this->MERCHANT;}
        public function getAccount(){return $this->ACCOUNT;}
        public function getCurrencyCode(){
            if($this->CURRENCY_CODE == NULL || $this->CURRENCY_CODE == '' ){
                return $this->Currency_Code['EUR'];}
            else{
                $currencies=array_keys($this->Currency_Code);
                return $this->Currency_Code[$currencies[$this->CURRENCY_CODE]];
            }
        }
        public function getSecureType(){
            if($this->SECURE_TYPE == NULL || $this->SECURE_TYPE == '' ){return $this->SecureTypes[0];}
            else{return $this->SecureTypes[$this->SECURE_TYPE];}
        }
        public function getAmount(){
            $amount=ceil(WC()->cart->total*self::AMOUNT_CENT_CONVERT_FOR_PAYMENT);
            error_log('Amount is ' . $amount);
            return $amount;
        }
        public function getUrl(){
            if(((int)$this->ENVIRONMENT) == 0) {
                return 'https://psp.spayn.es/client';
            } else {
                return 'https://test-psp.spayn.es/client';
            }
        }

        public function getEnvironment(){
            return $this->Environments[(int)$this->ENVIRONMENT];
        }


        // Private API 

        function spayn_payment_load_plugin_textdomain() {
            load_plugin_textdomain( 'woocommerce-spayn-payment-gateway-2', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
        }

        public function init_form_fields(){
            $this->form_fields = array(
                'enabled' => array(
                'title' 		=> __( 'Activo/Inactivo', 'woocommerce-spayn-payment-gateway' ),
                'type' 			=> 'checkbox',
                'label' 		=> __( 'Activar SPayN', 'woocommerce-spayn-payment-gateway' ),
                'default' 		=> 'yes'
                ),
                'title' => array(
                    'title' 		=> __( 'Nombre de pago', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Controla un titulo para el pago.', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( 'SPayN', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'order_status' => array(
                    'title' => __( 'Estado de pedido tras el pago.', 'woocommerce-spayn-payment-gateway' ),
                    'type' => 'select',
                    'options' => wc_get_order_statuses(),
                    'default' => 'wc-on-hold',
                    'description' 	=> __( 'El estado en que queda un pedido tras realizar el pago.', 'woocommerce-spayn-payment-gateway' ),
                ),
                'ENVIRONMENT' => array(
                    'title' => __( 'Entorno', 'woocommerce-spayn-payment-gateway' ),
                    'type' => 'select',
                    'options' => ['PRODUCTION','TEST'],
                    'default' => 'TEST',
                    'description' 	=> __( 'Entorno de trabajo.', 'woocommerce-spayn-payment-gateway' ),
                ),
                'CURRENCY_CODE' => array(
                    'title' => __( 'Divisa', 'woocommerce-spayn-payment-gateway' ),
                    'type' => 'select',
                    'options' => ['AFA','AWG','AUD','ARS','AZN','BSD','BDT','BBD','BYR','BOB','BRL','GBP','BGN','KHR','CAD','KYD','CLP','CNY','COP','CRC','HRK','CPY','CZK','DKK','DOP','XCD','EGP','ERN','EEK','EUR','GEL','GHC','GIP','GTQ','HNL','HKD','HUF','ISK','INR','IDR','ILS','JMD','JPY','KZT','KES','KWD','LVL','LBP','LTL','MOP','MKD','MGA','MYR','MTL','BAM','MUR','MXN','MZM','NPR','ANG','TWD','NZD','NIO','NGN','KPW','NOK','OMR','PKR','PYG','PEN','PHP','QAR','RON','RUB','SAR','CSD','SCR','SGD','SKK','SIT','ZAR','KRW','LKR','SRD','SEK','CHF','TZS','THB','TTD','TRY','AED','USD','UGX','UAH','UYU','UZS','VEB','VND','AMK','ZWD'],
                    'default' => 'EUR',
                    'description' 	=> __( 'Divisa de pago.', 'woocommerce-spayn-payment-gateway' ),
                ),
                'MERCHANT' => array(
                    'title' => __( 'Comercio', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Obligatorio. Codigo de comercio en la plataforma SPayN', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'ACCOUNT' => array(
                    'title' => __( 'Cuenta de pago', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Cuenta del comercio en la plataforma SPayN', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'API_KEY' => array(
                    'title' => __( 'Clave', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Obligatorio. API key del comercio en la plataforma SPayN', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'AUTO_REDIRECT' => array(
                    'title' => __( 'Fin de pago', 'woocommerce-spayn-payment-gateway' ),
                    'type' => 'select',
                    'options' => ['Realiza el pedido','Muestra pantalla resumen'],
                    'default' => 'TEST',
                    'description' 	=> __( 'Comportamiento tras el pago.', 'woocommerce-spayn-payment-gateway' ),
                ),
                'AUTO_SUBMIT' => array(
                    'title' => __( 'Enviar tarjeta automáticamente', 'woocommerce-spayn-payment-gateway' ),
                    'type' => 'select',
                    'options' => ['Dejar al usuario meter la tarjeta','Envia automáticamente'],
                    'default' => '0',
                    'description' 	=> __( 'Hace que si hay una tarjeta rellenada en el formulario de tarjetas, se envíe automáticamente para pago sin dejar que el usuario revise. Por defecto, deja al usuario elegir la tarjeta.', 'woocommerce-spayn-payment-gateway' ),
                ),
                'SECURE_TYPE' => array(
                    'title' => __( 'Modalidad de Pago', 'woocommerce-spayn-payment-gateway' ),
                    'type' => 'select',
                    'options' => ['DEFINED_BY_RULES','SECURE','NOT SECURE'],
                    'default' => 'DEFINED_BY_RULES',
                    'description' 	=> __( 'Comportamiento que tendrá el pago. Tendrá prioridad respecto a la configuración global del comercio.', 'woocommerce-spayn-payment-gateway' ),
                ),
                'BASE_BACKGROUND_COLOR' => array(
                    'title' 		=> __( 'Color de fondo', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Color base de fondo en hexadecimal (pej. #FFFFFF)', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'FRAME_BACKGROUND_COLOR' => array(
                    'title' 		=> __( 'Color del Frame', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Color de fondo del Frame en hexadecimal (pej. #FFFFFF)', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'FRAME_LABEL_COLOR' => array(
                    'title' 		=> __( 'Color textos', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Color de fuente de los textos en hexadecimal (pej. #FFFFFF)', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'BUTTON_BACKGROUND_COLOR' => array(
                    'title' 		=> __( 'Color de botón', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Color del fondo de los botones en hexadecimal (pej. #FFFFFF)', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'BUTTON_LABEL_COLOR' => array(
                    'title' 		=> __( 'Color texto botón', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Color del texto de los botones en hexadecimal (pej. #FFFFFF)', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_AMOUNT' => array(
                    'title' 		=> __( 'Texto Importe', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta importe', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_CONCEPT' => array(
                    'title' 		=> __( 'Texto Concepto', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta concepto', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_ALIAS' => array(
                    'title' 		=> __( 'Texto Alias', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta Alias', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_CARD' => array(
                    'title' 		=> __( 'Texto Tarjeta', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta Tarjeta', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_EXP_MONTH' => array(
                    'title' 		=> __( 'Texto Mes Caducidad', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta Mes de caducidad', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_EXP_YEAR' => array(
                    'title' 		=> __( 'Texto Año Caducidad', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta Año de caducidad', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_PAYMENT_DATE' => array(
                    'title' 		=> __( 'Texto Fecha de pago', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta Fecha de pago', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_PAN' => array(
                    'title' 		=> __( 'Texto PAN', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta PAN', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_AUTH_CODE' => array(
                    'title' 		=> __( 'Texto Código Autorización', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta Código de Autorización', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_LABEL_OPERATION_CODE' => array(
                    'title' 		=> __( 'Texto Código de Operación', 'woocommerce-spayn-paymtest-psp.spayn.esent-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para la etiqueta Código de Autorización', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
                'TEXT_BUTTON_BACK' => array(
                    'title' 		=> __( 'Texto Botón Volver', 'woocommerce-spayn-payment-gateway' ),
                    'type' 			=> 'text',
                    'description' 	=> __( 'Opcional. Texto a mostrar para el botón de volver', 'woocommerce-spayn-payment-gateway' ),
                    'default'		=> __( '', 'woocommerce-spayn-payment-gateway' ),
                    'desc_tip'		=> false,
                ),
         );
        }

        // Private API

        function enqueue_scripts() {
            wp_enqueue_style( 'woocommerce-spayn-default', $this->assets_url( "/css/spayn-default-style.css" ), array(), false );
        }

        function includes() {
            require_once dirname( WOOCOMMERCE_SPAYN_PLUGIN_FILE ) . '/utils/network-utils.php';
            require_once dirname( WOOCOMMERCE_SPAYN_PLUGIN_FILE ) . '/apis/api.php';
        }

        function hooks() {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
     
            add_action( 'woocommerce_update_options_payment_gateways_'.$this->id, array($this, 'process_admin_options'));

            // Register with hook
            add_action( 'plugins_loaded', array( $this, 'spayn_payment_load_plugin_textdomain' ) );
            add_action( 'woocommerce_proceed_to_checkout', function() {
                error_log('Spayn: Checkout');
            });
            add_action( 'woocommerce_before_checkout_form', function() {
                error_log('Spayn: Checkout Form');
                global $is_safari;
                if($is_safari){
                    $gw=(new WC_spayn_Payment_Gateway())->getInstance();
                    if(!isset($_GET['safari']) && ( $_COOKIE['SpayN_' . $gw->getEnvironment()]!='done')){
                        setcookie('SpayN_' . $gw->getEnvironment(),'done',time()+86400, ['samesite' => 'None', 'secure' => true] );
                        $uri=$gw->getUrl() . '/redirect?url=' . base64url_encode(wc_get_checkout_url());
                        wp_redirect($uri);
                    }
                }
            });
            add_action( 'woocommerce_after_order_notes', array( $this, 'add_payment_field' ) );
            add_action( 'woocommerce_order_details_after_order_table',  array( $this, 'display_spayn_order_details'), 10, 1 );
            add_action( 'woocommerce_admin_order_data_after_shipping_address',  array( $this, 'display_spayn_order_details'), 10, 1 );
                
        }

            
        /**
         * Admin Panel Options
         * - Options for bits like 'title' and availability on a country-by-country basis
         *
         * @since 1.0.0
         * @return void
         */
        function admin_options() {
            ?>
            <h3><?php _e( 'Custom Payment Settings', 'woocommerce-spayn-payment-gateway' ); ?></h3>
            <div id="poststuff">
                <div id="post-body" class="metabox-holder columns-2">
                    <div id="post-body-content">
                        <table class="form-table">
                            <?php $this->generate_settings_html();?>
                        </table><!--/.form-table-->
                    </div>
                    <div id="postbox-container-1" class="postbox-container">
                            <div id="side-sortables" class="meta-box-sortables ui-sortable"> 
                            </div>
                        </div>
                    </div>
                </div>
            <div class="clear"></div>
            <?php
        }
    }

}