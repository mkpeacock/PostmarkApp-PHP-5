# PHP 5 Library for PostmarkApp

PHP 5 Library for sending emails using PostmarkApp.  There were a number of other available libraries, either for specific frameworks, or which didn't work out of the box due to errors. So to ensure I had a fully working library, and to help me understand the API better, I thought I'd write my own library.

## Usage

    require_once( 'postmark.php' );
    $postmark = new Postmark( 'Your-API-Key leave blank to use class defined key' );
    $postmark->setSender( 'test@test.com', 'Test Sender' );
    $postmark->addRecipient( 'test2@test.com', 'Test Recipient' );
    $postmark->setSubject('Subject');
    $postmark->setMessage( 'Plain text message' );
    if( $postmark->send() )
    {
    	// do something
    }
    else
    {
    	// do something else 
    }
