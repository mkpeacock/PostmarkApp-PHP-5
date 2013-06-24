<?php
class PostmarkMessageRecipient
{
	const
		TYPETO	= 0x1,
		TYPECC	= 0x2,
		TYPEBCC	= 0x4;
	
	protected
		$type,
		$email,
		$name;
	
	public function __construct($type, $email, $name = null){
		$this->type = $type;
		$this->email = $email;
		$this->name = $name;
	}
	
	public function getType(){
		return (int)$this->type;
	}
	
	public function getEmail(){
		return $this->email;
	}
	
	public function getName(){
		return $this->name;
	}
	
	public function __toString(){
		if(null !== $this->name){
			return sprintf('%s <%s>', $this->name, $this->email);
		}
		return $this->email;
	}
}