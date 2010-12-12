<?php
class PostmarkHttpTransport
{
	protected
		$key,
		$endpoint,
		$ssl;
	
	public function __construct($key, $endpoint = 'api.postmarkapp.com/email', $ssl = false){
		$this->key = $key;
		$this->endpoint = $endpoint;
		$this->ssl = (bool)$ssl;
	}
	
	public function send(PostmarkMessage $message){
		
	}
	
	protected function getEndpoint(){
		return sprintf(
			'http%s://%s',
			$this->ssl ? 's' : '',
			$this->endpoint
		);
	}
}