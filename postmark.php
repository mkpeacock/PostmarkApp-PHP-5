<?php
/**
 * PostmarkApp PHP5 Library
 * 
 * @author Michael Peacock
 * @copyright Michael Peacock
 * @version 0.8
 */
class Postmark{
	
	/**
	 * API Endpoint
	 * @var String
	 */
	private $apiEndPoint = 'http://api.postmarkapp.com/email';
	
	/**
	 * API Key [default is the test API key]
	 * @var String
	 */
	private $apiKey = 'POSTMARK_API_TEST';
	
	/**
	 * Default sender email address
	 * @var String
	 */
	private $defaultSenderEmail = 'email@test.com';
	
	/**
	 * Default sender name
	 * @var String
	 */
	private $defaultSenderName = 'Your Name';
	
	/**
	 * Reply-to address
	 * @var String
	 */
	private $replyTo='';
	
	/**
	 * The sender
	 * @var String
	 */
	private $sender='';
	
	/**
	 * Recipient email address
	 * @var array [to|cc|bcc]
	 */
	private $recipients = array( 'to' => array(), 'cc' => array(), 'bcc' => array() );
	
	/**
	 * Recipient name
	 * @var String
	 */
	private $recipientName = '';
	
	/**
	 * Message subject
	 * @var String
	 */
	private $subject;
	
	/**
	 * A tag to categorise the email within PostmarkApp
	 * @var String
	 */
	private $tag = '';
	
	/**
	 * Should we fork the sending via popen?
	 * The idea behind this is to fork the cURL request off, if it is likely to take a while to send, and is not being run in the background
	 * Limitation: Setting attachments etc would be front-end task, taking up alot of time too
	 * @var bool
	 */
	private $fork = false;
	
	/**
	 * Allowed attachment file types see: http://developer.postmarkapp.com/developer-build.html#attachments
	 * - and where appropriate, their corresponding mimetypes
	 * @var array
	 */
	private $allowedAttachmentTypes = array( 'gif' => 'image/gif', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'swf' => 'application/x-shockwave-flash', 'flv' => 'video/x-flv', 'avi' => 'video/x-msvideo', 'mpg' => 'video/mpeg', 'mp3' => 'audio/mpeg', 'rm' => 'application/vnd.rn-realmedia', 'mov' => 'video/quicktime', 'psd' => 'image/psd', 'ai' => 'application/postscript', 'tif' => 'image/tiff', 'tiff' => 'image/tiff', 'txt' => 'text/plain', 'rtf' => 'text/richtext', 'htm' => 'text/html', 'html' => 'text/html', 'pdf' => 'application/pdf', 'doc' => 'application/msword', 'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'ppt' => 'application/vnd.ms-powerpoint', 'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'xls' => 'application/vnd.ms-excel', 'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'ps' => 'image/psd', 'eps' => 'application/postscript', 'log' => 'text/plain', 'csv' => 'text/csv', 'ics' => 'text/calendar', 'xml' => 'text/xml');
	
	/**
	 * Maximum attachment size, 10mb, see: http://developer.postmarkapp.com/developer-build.html#attachments
	 * @var int
	 */
	private $maxAttachmentSize = 10485760;
	
	/**
	 * Array of attachments
	 * @var array
	 */
	private $attachments = array();
	
	/**
	 * Total size of the attachments
	 * @var int
	 */
	private $totalAttachmentsSize = 0;
	
	/**
	 * The message itself, in plain and HTML formats
	 * @var array
	 */
	private $message = array( 'plain' => '', 'html' => '' );
	
	/**
	 * Any custom headers
	 * @var array
	 */
	private $headers = array();
	
	/**
	 * The response from PostmarkApp
	 * @var array
	 */
	private $response;
	
	
	/**
	 * Postmark Library Constructor
	 * @param String $apiKey - if we wish to set the API Key at run-time
	 * @return void
	 */
	public function __construct( $apiKey='' )
	{
		if( $apiKey != '' )
		{
			$this->apiKey = $apiKey;
		}
	}
	
	/**
	 * Set the sender
	 * @param String $email
	 * @param String $name
	 * @return void
	 */
	public function setSender( $email, $name='' )
	{
		$this->sender = ( $name == '' ) ? $email : "{$name} <{$email}>";
	}	
	
	/**
	 * Set the subject of the email
	 * @param String $subject
	 * @return void
	 */
	public function setSubject( $subject )
	{
		$this->subject = $subject;
	}
	
	/**
	 * Add a recipient
	 * @param String $email their email address
	 * @param String $name their name
	 * @return void
	 */
	public function addRecipient( $email, $name='', $type='to' )
	{
		if( array_key_exists( $type, $this->recipients ) )
		{
			if( $this->verifyEmail( $email ) )
			{
				$this->recipients[ $type ][] = $name == '' ? $email : "{$name} <{$email}>";
			}
		}
	}
	
	/**
	 * Set the reply to address
	 * @param String $email the email address
	 * @param String $name the name
	 * @return void
	 */
	public function setReplyTo( $email, $name='' )
	{
		if( $this->verifyEmail( $email ) )
		{
			$this->replyTo = $name == '' ? $email : "{$name} <{$email}>";
		}
		
	}
	
	/**
	 * Set the message content
	 * @param String $message the message itself
	 * @param String $type the type of message [plain|html]
	 * @return void
	 */
	public function setMessage( $message, $type='plain' )
	{
		if( array_key_exists( $type, $this->message ) )
		{
			$this->message[ $type ] = $message;
		}
	}
	
	/**
	 * Set the tag for categorising the email within PostmarkApp
	 * @param String $tag
	 */
	public function setTag( $tag )
	{
		$this->tag = $tag;
	}
	
	public function addHeader( $name, $value )
	{
		$this->headers[] = array( 'Name' => $name, 'Value' => $value );
	}
	
	/**
	 * Add an attachment
	 * @param String $fileName the name of the file
	 * @param String $fileContents the contents of the file
	 * @param String $mimeType the mime-type of the file
	 * @return bool
	 */
	public function addAttachment( $fileName, $fileContents, $mimeType='' )
	{
		$ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
		if( array_key_exists( $ext, $this->allowedAttachmentTypes ) )
		{
			$size = strlen( $fileContents );
			if( ( $this->totalAttachmentsSize +  $size ) < $this->maxAttachmentSize )
			{
				$this->totalAttachmentsSize +=  $size;
				if( $mimeType == '' )
				{
					if( $this->allowedAttachmentTypes[ $ext ] != '' )
					{
						$mimeType = $this->allowedAttachmentTypes[ $ext ];
					}
					else
					{
						$mimeType = 'application/octet-stream';
					}
				}
				$this->attachments[] = array( 'Name' => $fileName, 'Content' => base64_encode( $fileContents ), 'ContentType' => $mimeType );
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			return false;
		}
	}
	
	/**
	 * Send the message
	 * @return mixed
	 */
	public function send()
	{
		if( $this->sender == '' )
		{
			$this->sender = ( $this->defaultSenderName == '' ) ? $this->defaultSenderEmail : "{$this->defaultSenderName} <{$this->defaultSenderEmail}>";
		}
		
		$email = array();
		$email['Subject'] = $this->subject;
		$email['From'] = $this->sender;
		$email['To'] = implode( ', ', $this->recipients['to'] );
		
		if( ! empty( $this->recipients['cc'] ) )
		{
			$email['Cc'] = implode( ', ', $this->recipients['cc'] );
		}
		
		if( ! empty( $this->recipients['bcc'] ) )
		{
			$email['Bcc'] = implode( ', ', $this->recipients['bcc'] );
		}
		
		if( $this->replyTo != '' )
		{
			$email['ReplyTo'] = $this->replyTo;
		}
		
		if( $this->tag != '' )
		{
			$email['Tag'] = $this->tag;
		}
		
		if( ! empty( $this->headers ) )
		{
			$email['Headers'] = $this->headers;
		}
		
		if( ! empty( $this->attachments ) )
		{
			$email['Attachments'] = $this->attachments;
		}
		
		if( $this->message['html'] != '' ) 
		{
			$email['HtmlBody'] = $this->message['html'];
		}
		
		if( $this->message['plain'] != '' ) 
		{
			$email['TextBody'] = $this->message['plain'];
		}
		
		
		$email = json_encode( $email );
			
		if( ! $this->fork )
		{
			$headers = array(
				'Accept: application/json',
				'Content-Type: application/json',
				'X-Postmark-Server-Token: ' . $this->apiKey
			);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $this->apiEndPoint );
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_POSTFIELDS, $email);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$response = curl_exec($ch);
			$error = curl_error($ch);
			$this->response = json_decode( $response );
			if( curl_getinfo($ch, CURLINFO_HTTP_CODE) == 200 )
			{
				return true;
			}
			else
			{
				return false;
			}
			
		}
		else
		{
			// this method is currently untested
			$request = "curl -X POST \"{$this->apiEndPoint}\" -H \"Accept: application/json\" -H \"Content-Type: application/json\" -H \"X-Postmark-Server-Token: {$this->apiKey}\" -v -d \"{$email}\"";
			$handle = popen( $request, 'r');
			$response = fread($handle, 2096);
			pclose($handle);
		}
	}
	
	/**
	 * Get the response from PostmarkApp
	 * @return Object
	 */
	public function getResponse()
	{
		return $this->response;
	}
	
	/**
	 * Verify the format of an email address
	 * @param String $email
	 * @return bool
	 */
	private function verifyEmail( $email )
	{
		return false !== filter_var($email, FILTER_VALIDATE_EMAIL);
	}
	
	
}


?>