<?php
class PostmarkMessage
{
	protected
		$sender,
		$subject;
		
	public function getSender(){
		return $this->sender;
	}
	
	public function setSender(PostmarkMessageSender $sender){
		$this->sender = $sender;
	}
	
	public function getSubject(){
		return $this->subject;
	}
	
	public function setSubject($subject){
		$this->subject = $subject;
	}
}