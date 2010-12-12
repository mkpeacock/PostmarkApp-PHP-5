<?php
class PostmarkResponse
{
	protected
		$code,
		$message,
		$id,
		$submission,
		$to;
	
	public function __construct($code, $message, $id, $submission, $to){
		$this->code = $code;
		$this->message = $message;
		$this->id = $id;
		$this->submission = $submission;
		$this->to = $to;
	}
	
	public function getCode(){
		return (int)$this->code;
	}
	
	public function getMessage(){
		return $this->message;
	}
	
	public function getId(){
		return $this->id;
	}
	
	public function getSubmissionTime(){
		return strtotime($this->submission);
	}
	
	public function getTo(){
		return $this->to;
	}
}