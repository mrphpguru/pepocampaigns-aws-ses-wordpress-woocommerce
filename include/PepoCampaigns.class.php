<?php
class PepoCampaigns
{
    private $pepo_key;
    private $pepo_secret;
    private $pepo_url;

    function __construct($keys){
        $this->pepo_key = $keys['key'];
        $this->pepo_secret = $keys['secret'];
        $this->pepo_url = 'https://pepocampaigns.com';
    }

    /**
     * Creating a new list
     * @link https://know.pepocampaigns.com/articles/managing-lists-api/#creating-a-new-list
     *
     * @param array $data Associative array with 'name' and 'source' keys  
     * @return string Raw response from API
     */
    public function create_list($data){
        $api_url = '/api/v1/list/create/';
        $request_time = $this->get_request_time();
        $delimiter = '::';
        $signature = $this->generate_signature($this->pepo_secret, $api_url.$delimiter.$request_time);
        $fields = array(
            'request-time' => $request_time,
            'signature' => $signature,
            'api-key' => $this->pepo_key,
            'name' => $data['name'],
            'source' => $data['source']
        );
        $result = $this->execute_curl($api_url, $fields);
        return $result;
    }

    /**
     * Adding a contact to a List
     * @link https://know.pepocampaigns.com/articles/managing-lists-api/#adding-a-contact-to-a-list
     *
     * @param int $list_id List ID to add the contact 
     * @param array $data Associative array with 'email','first_name' and 'last_name' keys 
     * @return string Raw response from API
     */    
    public function add_contact_to_list($list_id, $data){
        $api_url = '/api/v1/list/'.$list_id.'/add-contact/';
        $request_time = $this->get_request_time();
        $delimiter = '::';
        $signature = $this->generate_signature($this->pepo_secret, $api_url.$delimiter.$request_time);
        $fields = array(
            'request-time' => $request_time,
            'signature' => $signature,
            'api-key' => $this->pepo_key,
            'email' => $data['email'],
            'attributes[First Name]' => $data['first_name'],
            'attributes[Last Name]' => $data['last_name']
        );
        $result = $this->execute_curl($api_url, $fields);
        return $result;
    }

    /**
     * Removing a contact from a list
     * @link https://know.pepocampaigns.com/articles/managing-lists-api/#removing-a-contact-from-a-list
     *
     * @param int $list_id List ID to add the contact 
     * @param array $data Associative array with 'email' key 
     * @return string Raw response from API
     */     
    public function remove_contact_from_list($list_id, $data){
        $api_url = '/api/v1/list/'.$list_id.'/remove-contact/';
        $request_time = $this->get_request_time();
        $delimiter = '::';
        $signature = $this->generate_signature($this->pepo_secret, $api_url.$delimiter.$request_time);
        $fields = array(
            'request-time' => $request_time,
            'signature' => $signature,
            'api-key' => $this->pepo_key,
            'email' => $data['email']
        );
        $result = $this->execute_curl($api_url, $fields);
        return $result;
    }

    /**
     * Changing User Status
     * @link https://know.pepocampaigns.com/articles/managing-contacts-api/#changing-user-status
     *
     * @param array $data Associative array with 'email' and 'type' key 
     * @return string Raw response from API
     */     
    public function change_user_status($data){
        $api_url = '/api/v1/list/'.$this->list.'/remove-contact/';
        $request_time = $this->get_request_time();
        $delimiter = '::';
        $signature = $this->generate_signature($this->pepo_secret, $api_url.$delimiter.$request_time);
        $fields = array(
            'request-time' => $request_time,
            'signature' => $signature,
            'api-key' => $this->pepo_key,
            'email' => $data['email'],
            'type' => $data['type']
        );
        $result = $this->execute_curl($api_url, $fields);
        return $result;
    }

    /**
     * Send Transactional Email
     * @link https://know.pepocampaigns.com/articles/managing-transactional/#send-transactional-email
     *
     * @param string $email Email
     * @param string $template Template          
     * @param array $email_vars Associative array of email variables
     * @return string Raw response from API
     */ 
    public function send_transactional_email($email, $template, $email_vars){
        $api_url = '/api/v1/send/';
        $request_time = $this->get_request_time();
        $delimiter = '::';
        $signature = $this->generate_signature($this->pepo_secret, $api_url.$delimiter.$request_time);
        $fields = array(
            'request-time' => $request_time,
            'signature' => $signature,
            'api-key' => $this->pepo_key,
            'email' => $email,
            'template' => $template,
            'email_vars' => json_encode($email_vars)
        );
        $result = $this->execute_curl($api_url, $fields);
        return $result;       
    }

    private function execute_curl($api_url, $fields){
        $fields_string = http_build_query($fields);
        try {
            $ch = curl_init();
            $url= $this->pepo_url.$api_url;
            $curl_options = array(
                                  CURLOPT_URL => $url,
                                  CURLOPT_RETURNTRANSFER => true,
                                  CURLOPT_SSL_VERIFYPEER => false,
                                  CURLOPT_SSL_VERIFYHOST => false,
                                  CURLOPT_SSLVERSION => 6,
                                  CURLOPT_POST => true,
                                  CURLOPT_POSTFIELDS => $fields_string,
                                  );
            curl_setopt_array($ch, $curl_options);
            $result = curl_exec($ch);
            if ($result === FALSE)
                throw new Exception(curl_error($ch), curl_errno($ch));
        } catch (Exception $e){
           trigger_error(sprintf(
                'Curl failed with error #%d: %s',
                $e->getCode(), $e->getMessage()),
                E_USER_ERROR);
        }
        curl_close( $ch );
        return $result;
    }

    private function generate_signature($api_secret, $signature){
        return hash_hmac('sha256', $signature, $api_secret, false);
    }

    private function get_request_time(){
        return date("Y-m-d\TH:i:sP");
    }
}