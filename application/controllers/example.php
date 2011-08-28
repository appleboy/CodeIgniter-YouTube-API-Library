<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Example extends CI_Controller
{
    //CALL THIS METHOD FIRST BY GOING TO
    //www.your_url.com/index.php/request_youtube

    public function __construct()
    {
        parent::__construct();
        $this->load->config('youtube');
        $this->load->library('session');
    }

    public function index()
    {
        $this->youtube_no_auth();
    }

    public function request_youtube()
    {
        $params['key'] = $this->config->item('google_consumer_key');
        $params['secret'] = $this->config->item('google_consumer_secret');
        $params['algorithm'] = $this->config->item('google_signing_algo');

        $this->load->library('google_oauth', $params);
        $data = $this->google_oauth->get_request_token(site_url('example/access_youtube'));
        $this->session->set_userdata('token_secret', $data['token_secret']);
        redirect($data['redirect']);
    }

    //This method will be redirected to automatically
    //once the user approves access of your application
    public function access_youtube()
    {
        $params['key'] = $this->config->item('google_consumer_key');
        $params['secret'] = $this->config->item('google_consumer_secret');
        $params['algorithm'] = $this->config->item('google_signing_algo');

        $this->load->library('google_oauth', $params);

        $oauth = $this->google_oauth->get_access_token(false, $this->session->userdata('token_secret'));

        $this->session->set_userdata('oauth_token', $oauth['oauth_token']);
        $this->session->set_userdata('oauth_token_secret', $oauth['oauth_token_secret']);
    }

    //This method can be called without having
    //done the oauth steps
    public function youtube_no_auth()
    {
        $params['apikey'] = $this->config->item('youtube_api_key');

        $this->load->library('youtube', $params);
        echo $this->youtube->getKeywordVideoFeed('pac man');
    }

    //This method can be called after you executed
    //the oauth steps
    public function youtube_auth()
    {
        $params['apikey'] = $this->config->item('youtube_api_key');
        $params['oauth']['key'] = $this->config->item('google_consumer_key');
        $params['oauth']['secret'] = $this->config->item('google_consumer_secret');
        $params['oauth']['algorithm'] = $this->config->item('google_signing_algo');
        $params['oauth']['access_token'] = array('oauth_token'=>urlencode($this->session->userdata('oauth_token')),
                                                 'oauth_token_secret'=>urlencode($this->session->userdata('oauth_token_secret')));

        $this->load->library('youtube', $params);
        echo $this->youtube->getUserUploads();
    }
}

/* End of file example.php */
/* Location: ./application/controllers/example.php */