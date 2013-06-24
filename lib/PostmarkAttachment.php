<?php
class PostmarkAttachment
{
	protected
		$filename,
		$data,
		$mimetype;
		
	public function __construct($filename, $data, $mimetype){
		$this->filename = $filename;
		$this->data = $data;
		$this->mimetype = $mimetype;
	}
	
	public function getFilename(){
		return $this->filename;
	}
	
	public function getData(){
		return $this->data;
	}
	
	public function getMimeType(){
		return $this->mimetype;
	}
	
	public function getSize(){
		return strlen($this->getData());
	}
}