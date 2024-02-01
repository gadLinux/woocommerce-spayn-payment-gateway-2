<?php
class spainApiClient {
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
    private $GATEWAY;
    public function __construct($gateway){
        $this->MERCHANT = $gateway->MERCHANT;
        $this->CLIENT = $gateway->getLogedUser();
        $this->AMOUNT = $gateway->getAmount();
        $this->CURRENCY = $gateway->getCurrencyCode();
        $this->MERCHANT_OPERATION = $gateway->generateRefNumber();
        $this->REDIRECT_URL = $gateway->plugin_url('paymentcallback.php');
        $this->GATEWAY=$gateway;
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

   

    public function getMerchantOperation(){return $this->MERCHANT_OPERATION;}    
    public function setMerchantOperation($merchantOperation){$this->MERCHANT_OPERATION=$merchantOperation;}    

    public function requestToken() {
        $message=$this->getTokenRequest();
        $endpoint = '/brw/token/request';
        $url = $this->get_endpoint($endpoint);

        error_log('Sending a iframe content request [' . $url . ']['.$this->MERCHANT_OPERATION.'] in mode ' . $this->GATEWAY->getEnvironment());
        $response = $this->post($url, $message, !$this->is_test_environment());
        if($response!=NULL) {
            $json = json_decode($response, TRUE);
            if(isset($json['CODE']) && $json['CODE']=='PARAM') {
                error_log('We had an error while sending the request: [' . $json['DESCRIPTION'] . '] Track code: ' . $json['DEBUG_ID']); 
                $result = array();
                $result['DESCRIPTION']=$json['DESCRIPTION'];
                $result['DEBUG_ID']=$json['DEBUG_ID'];
                return $result;
            } 
            if(isset($json['TOKEN']) && str_contains($json['URL'], 'spayn.es/client')) {
                $result = array();
                $result['DESTINATION_URL']=$json['URL'];
                $result['TOKEN']=$json['TOKEN'];
                $result['MERCHANT_OPERATION']=$json['MERCHANT_OPERATION'];
                return $result;
            }
        }       
        return array();
    }

 

    private function get_endpoint($path) {
        // Safe defaults
        $url='https://test-psp.spayn.es/client'; 

        if($this->GATEWAY!=NULL){
            $url=$this->GATEWAY->getUrl();
        } 
        return $url.$path;
    }

    private function is_test_environment() {
        if($this->GATEWAY!=NULL){
            $environment=$this->GATEWAY->getEnvironment();
            return ($environment=='TEST') ? true : false;
        }
        return true;
    }

    public function requestPaymentStatus() {
        $message=$this->getPaymentStatusRequest($this->MERCHANT_OPERATION);
        $endpoint = '/server/conciliation';
        $url = $this->get_endpoint($endpoint);

        $not_available = array();
        $not_available['PAYMENT']['STATUS']="403";
        $not_available['PAYMENT']['STATUS_DESCRIPTION']="NOT_FOUND";

        if(!isset($this->MERCHANT_OPERATION)){
            return $not_available;
        }

        error_log('Sending payment status request [' . $url . ']['.$this->MERCHANT_OPERATION.'] in mode ' . $this->GATEWAY->getEnvironment());
        $response = $this->post($url, $message, !$this->is_test_environment());
        if($response==NULL) {
            error_log("No response");
            return $not_available;
        }
        $json = json_decode($response, TRUE);
        if(isset($json['CODE']) && $json['CODE']=='PARAM') {
            error_log('We had an error while sending the request: [' . $json['DESCRIPTION'] . '] Track code: ' . $json['DEBUG_ID'].'\n'.$message); 
            return $not_available;
        }

        if(isset($json['TXS']) && isset($json['TXS'][$this->MERCHANT_OPERATION])) {
            error_log('We found the payment: ' . $response); 
            return $json['TXS'][$this->MERCHANT_OPERATION];
        } 
        
        error_log("Payment is not registered");
        return $not_available;     
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

    // Private methods
    private function getTokenRequest(){
        $request = array();
        $request['MERCHANT'] = $this->MERCHANT;
        $request['ACCOUNT'] = $this->ACCOUNT;
        $request['CLIENT'] = $this->CLIENT;
        $request['AMOUNT'] = $this->AMOUNT;
        $request['CURRENCY'] = $this->CURRENCY;
        $request['MERCHANT_OPERATION'] = $this->MERCHANT_OPERATION;
        $request['REDIRECT_URL'] = $this->REDIRECT_URL;
        if(($this->GATEWAY)!=NULL){
            $request['PARAMS']['SECURE_TYPE'] = $this->SECURE_TYPE;
            $request['PARAMS']['BASE_BACKGROUND_COLOR'] = $this->BASE_BACKGROUND_COLOR;
            $request['PARAMS']['FRAME_BACKGROUND_COLOR'] = $this->FRAME_BACKGROUND_COLOR;
            $request['PARAMS']['FRAME_LABEL_COLOR'] = $this->FRAME_LABEL_COLOR;
            $request['PARAMS']['BUTTON_BACKGROUND_COLOR'] = $this->BUTTON_BACKGROUND_COLOR;
            $request['PARAMS']['BUTTON_LABEL_COLOR'] = $this->BUTTON_LABEL_COLOR;
            $request['PARAMS']['AUTO_REDIRECT'] = $this->AUTO_REDIRECT;
            $request['PARAMS']['AUTO_SUBMIT'] = $this->AUTO_SUBMIT;
            $request['PARAMS']['TEXT_LABEL_AMOUNT'] = $this->TEXT_LABEL_AMOUNT;
            $request['PARAMS']['TEXT_LABEL_CONCEPT'] = $this->TEXT_LABEL_CONCEPT;
            $request['PARAMS']['TEXT_LABEL_ALIAS'] = $this->TEXT_LABEL_ALIAS;
            $request['PARAMS']['TEXT_LABEL_CARD'] = $this->TEXT_LABEL_CARD;
            $request['PARAMS']['TEXT_LABEL_EXP_MONTH'] = $this->TEXT_LABEL_EXP_MONTH;
            $request['PARAMS']['TEXT_LABEL_EXP_YEAR'] = $this->TEXT_LABEL_EXP_YEAR;
            $request['PARAMS']['TEXT_LABEL_PAYMENT_DATE'] = $this->TEXT_LABEL_PAYMENT_DATE;
            $request['PARAMS']['TEXT_LABEL_PAN'] = $this->TEXT_LABEL_PAN;
            $request['PARAMS']['TEXT_LABEL_AUTH_CODE'] = $this->TEXT_LABEL_AUTH_CODE;
            $request['PARAMS']['TEXT_BUTTON_BACK'] = $this->TEXT_BUTTON_BACK;
        }
        $request['SIGNATURE'] = hash_hmac('sha256', 
            $this->MERCHANT . 
            $this->ACCOUNT . 
            $this->CLIENT . 
            $this->AMOUNT . 
            $this->CURRENCY . 
            $this->MERCHANT_OPERATION . 
            $this->REDIRECT_URL, 
            hexToStr($this->GATEWAY->API_KEY));
        return json_encode(array_filter($request));
    }

    private function getPaymentStatusRequest($operation){
        $request = array();
        $request['MERCHANT'] = $this->MERCHANT;
        $request['MERCHANT_OPERATION'] = $this->MERCHANT_OPERATION;
        $request['DETAIL_LEVEL'] = "PAYMENT";
        $request['SIGNATURE'] = hash_hmac('sha256', 
            $this->MERCHANT . 
            $this->ACCOUNT . 
            $this->MERCHANT_OPERATION, 
            hexToStr($this->GATEWAY->API_KEY));
        return json_encode(array_filter($request));
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