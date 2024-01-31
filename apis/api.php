<?php




class tokenRequest {
    private $MERCHANT;
    private $ACCOUNT;
    private $CLIENT;
    private $AMOUNT;
    private $CURRENCY;
    private $MERCHANT_OPERATION;
    private $REDIRECT_URL;
    
    private $SECURE_TYPE;
    private $BASE_BACKGROUND_COLOR;
    private $FRAME_BACKGROUND_COLOR;
    private $FRAME_LABEL_COLOR;
    private $BUTTON_BACKGROUND_COLOR;
    private $BUTTON_LABEL_COLOR;
    private $AUTO_REDIRECT;
    private $AUTO_SUBMIT;
    private $TEXT_LABEL_AMOUNT;
    private $TEXT_LABEL_CONCEPT;
    private $TEXT_LABEL_ALIAS;
    private $TEXT_LABEL_CARD;
    private $TEXT_LABEL_EXP_MONTH;
    private $TEXT_LABEL_EXP_YEAR;
    private $TEXT_LABEL_PAYMENT_DATE;
    private $TEXT_LABEL_PAN;
    private $TEXT_LABEL_AUTH_CODE;
    private $TEXT_BUTTON_BACK;
    private $SPAYN;
    public function __construct($gateway){
        $this->MERCHANT = $gateway->MERCHANT;
        $this->CLIENT = $gateway->getLogedUser();
        $this->AMOUNT = $gateway->getAmount();
        $this->CURRENCY = $gateway->getCurrencyCode();
        $this->MERCHANT_OPERATION = $gateway->generateRefNumber();
        $this->REDIRECT_URL = $gateway->plugin_url('paymentcallback.php');
        $this->SIGNATURE = hash_hmac('sha256', $this->MERCHANT . $this->ACCOUNT . $this->CLIENT . $this->AMOUNT . $this->CURRENCY . $this->MERCHANT_OPERATION . $this->REDIRECT_URL, hexToStr($gateway->API_KEY));   
        $this->SPAYN=$gateway;
        if($gateway!=NULL){
            $this->SECURE_TYPE=$gateway->getSecureType();
            $this->AUTO_REDIRECT=$gateway->getAutoRedirect();
            $this->AUTO_SUBMIT=$gateway->getAutoSubmit();
            $this->BASE_BACKGROUND_COLOR=$gateway->BASE_BACKGROUND_COLOR;
            $this->FRAME_BACKGROUND_COLOR=$gateway->FRAME_BACKGROUND_COLOR;
            $this->FRAME_LABEL_COLOR=$gateway->FRAME_LABEL_COLOR;
            $this->BUTTON_BACKGROUND_COLOR=$gateway->BUTTON_BACKGROUND_COLOR;
            $this->BUTTON_LABEL_COLOR=$gateway->BUTTON_LABEL_COLOR;
            $this->TEXT_LABEL_AMOUNT=$gateway->TEXT_LABEL_AMOUNT;
            $this->TEXT_LABEL_CONCEPT=$gateway->TEXT_LABEL_CONCEPT;
            $this->TEXT_LABEL_ALIAS=$gateway->TEXT_LABEL_ALIAS;
            $this->TEXT_LABEL_CARD=$gateway->TEXT_LABEL_CARD;
            $this->TEXT_LABEL_EXP_MONTH=$gateway->TEXT_LABEL_EXP_MONTH;
            $this->TEXT_LABEL_EXP_YEAR=$gateway->TEXT_LABEL_EXP_YEAR;
            $this->TEXT_LABEL_PAYMENT_DATE=$gateway->TEXT_LABEL_PAYMENT_DATE;
            $this->TEXT_LABEL_PAN=$gateway->TEXT_LABEL_PAN;
            $this->TEXT_LABEL_AUTH_CODE=$gateway->TEXT_LABEL_AUTH_CODE;
            $this->TEXT_BUTTON_BACK=$gateway->TEXT_BUTTON_BACK;     
        }
    }
    private function getJSon(){
        $result='{';
            $result.=$this->addField('MERCHANT',$this->MERCHANT);
            $result.=$this->addField('ACCOUNT',$this->ACCOUNT);
            $result.=$this->addField('CLIENT',$this->CLIENT);
            $result.=$this->addField('AMOUNT',$this->AMOUNT);
            $result.=$this->addField('CURRENCY',$this->CURRENCY);
            $result.=$this->addField('MERCHANT_OPERATION',$this->MERCHANT_OPERATION);
            $result.=$this->addField('REDIRECT_URL',$this->REDIRECT_URL);
            if(($this->SPAYN)!=NULL){
                $result.='"PARAMS":{';
                $result.=$this->addField('SECURE_TYPE',$this->SECURE_TYPE);
                $result.=$this->addField('BASE_BACKGROUND_COLOR',$this->BASE_BACKGROUND_COLOR);
                $result.=$this->addField('FRAME_BACKGROUND_COLOR',$this->FRAME_BACKGROUND_COLOR);
                $result.=$this->addField('FRAME_LABEL_COLOR',$this->FRAME_LABEL_COLOR);
                $result.=$this->addField('BUTTON_BACKGROUND_COLOR',$this->BUTTON_BACKGROUND_COLOR);
                $result.=$this->addField('BUTTON_LABEL_COLOR',$this->BUTTON_LABEL_COLOR);
                $result.=$this->addField('AUTO_REDIRECT',$this->AUTO_REDIRECT);
                $result.=$this->addField('AUTO_SUBMIT',$this->AUTO_SUBMIT);
                $result.=$this->addField('TEXT_LABEL_AMOUNT',$this->TEXT_LABEL_AMOUNT);
                $result.=$this->addField('TEXT_LABEL_CONCEPT',$this->TEXT_LABEL_CONCEPT);
                $result.=$this->addField('TEXT_LABEL_ALIAS',$this->TEXT_LABEL_ALIAS);
                $result.=$this->addField('TEXT_LABEL_CARD',$this->TEXT_LABEL_CARD);
                $result.=$this->addField('TEXT_LABEL_EXP_MONTH',$this->TEXT_LABEL_EXP_MONTH);
                $result.=$this->addField('TEXT_LABEL_EXP_YEAR',$this->TEXT_LABEL_EXP_YEAR);
                $result.=$this->addField('TEXT_LABEL_PAYMENT_DATE',$this->TEXT_LABEL_PAYMENT_DATE);
                $result.=$this->addField('TEXT_LABEL_PAN',$this->TEXT_LABEL_PAN);
                $result.=$this->addField('TEXT_LABEL_AUTH_CODE',$this->TEXT_LABEL_AUTH_CODE);
                $result.=$this->addField('TEXT_BUTTON_BACK',$this->TEXT_BUTTON_BACK);
                $result=substr($result,0,-1) . "},";
            }
            $result.=$this->addField('SIGNATURE',$this->SIGNATURE);
            $result=substr($result,0,-1) . "}";
            return $result;
        }
    private function addField($name,$value){
        if($value != NULL && $value!=NULL){return '"' . $name . '":"' . $value . '",'; }
    }
    public function getMerchantOperation(){return $this->MERCHANT_OPERATION;}    
    
    public function requestTokenAndReturnPaymentIframeContents() {
        $message=$this->getJSon();
        // Safe defaults
        $url='https://test-psp.spayn.es/client'; 
        $is_test_env=false;

        if($this->SPAYN!=NULL){
            $url=$this->SPAYN->getUrl();
            $environment=$this->SPAYN->getEnvironment();
            $is_test_env=($environment=='TEST') ? true : false;
        } 

        error_log('Sending a login request to [' . $url . '/brw/token/request] in mode '. $this->SPAYN->getEnvironment());
        $response = $this->post($url . '/brw/token/request', $message, !$is_test_env);

        if($response!=NULL) {
            $json = json_decode($response, TRUE);
            if(isset($json['CODE']) && $json['CODE']=='PARAM') {
                error_log('We had an error while sending the request: [' . $json['DESCRIPTION'] . '] Track code: ' . $json['DEBUG_ID']); 
                $error_iframe=$this->SPAYN->plugin_url('payment-error-iframe.php') . '?MESSAGE=' . $json['DESCRIPTION'] . '&TRACE_ID=' . $json['DEBUG_ID'];
                return $error_iframe;
            } 
            if(isset($json['TOKEN']) && str_contains($json['URL'], 'spayn.es/client')) {
                $destination_url=urlencode($json['URL']);
                $token=urlencode($json['TOKEN']);
                $traking_code=urlencode($json['MERCHANT_OPERATION']);
                $ongoing_iframe=$this->SPAYN->plugin_url('payment-ongoing-iframe.php').'?URL='.$destination_url.'&TOKEN='.$token.'&TRACKING_CODE='.$traking_code;
                return $ongoing_iframe;
            }
        }       
        return "";
    }

    function post($url,$postdata, $verifycert = true){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verifycert);
        $result = curl_exec($ch);
        return $result;
    }
    
}
function strToHex($string){
    $hex = '';
    for ($i=0; $i<strlen($string); $i++){
        $ord = ord($string[$i]);
        $hexCode = dechex($ord);
        $hex .= substr('0'.$hexCode, -2);
    }
    return strToUpper($hex);
}
function hexToStr($hex){
    $string='';
    for ($i=0; $i < strlen($hex)-1; $i+=2){
        $string .= chr(hexdec($hex[$i].$hex[$i+1]));
    }
    return $string;
}

?>