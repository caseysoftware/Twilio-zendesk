<?php
/**
 * Twilio+Zendesk Example
 *
 * PHP Version 5 
 *
 * @category Examples
 * @package  Twilio-Zendesk
 * @author   Devin Rader <devin@twilio.com>
 * @license  MIT <http://opensource.org/licenses/MIT>
 * @link     http://twilio.com
 *
 */
include "vendor/autoload.php";

use Zendesk\API\Client as ZendeskAPI;

$ZD_FIELD = getenv('ZD_FIELD');

function getZendeskClient() 
{
    $ZD_SUBDOMAIN = getenv('ZD_SUBDOMAIN');
    $ZD_USERNAME = getenv('ZD_USERNAME');
    $ZD_APITOKEN = getenv('ZD_APITOKEN');

    $client = new ZendeskAPI($ZD_SUBDOMAIN, $ZD_USERNAME);
    $client->setAuth('token', $ZD_APITOKEN);

    return $client;    
}

if (isset($_REQUEST)) {
    
    $response = new Services_Twilio_Twiml();

    $from    = isset($_REQUEST['From']) ?
                $_REQUEST['From'] : 'bad phone number';
    $body    = isset($_REQUEST['Body']) ?
                $_REQUEST['Body'] : 'An error occurred from this number: '.$from;

    $commandDelimiterPosition = strpos($body, ' ');
    if ($commandDelimiterPosition===false) {
        $command = $body;
    } else {
        $command = substr($body, 0, $commandDelimiterPosition);
        $remainder = substr($body, $commandDelimiterPosition, strlen($body));
    }

    if ( $command == "menu" ) {
        $response->message(
            "The available commands are:\r\new - create a new ticket\r\n" .
            "[id] - update this ticket ID\r\n" .
            "menu - show this menu"
        );
    } else if ( $command == "new" ) {

        $client = getZendeskClient();
        $result = $client->tickets()->create(
            array ('description' => $remainder,
                'subject' => substr($remainder, 0, 80), 
                'requester_email' => $username,
                'custom_fields' => array(
                    'id' => $ZD_FIELD,
                    'value' => $from                    
                )
            )
        );
        
        $response->message(
            "A new ticket has been created.  To update, " .
            "reply with the command '" . $result->ticket->id . " [message]'"
        );

    } else if (is_numeric($command)) {        
        
        $client = getZendeskClient();
        try {
            
            $result = $client->tickets()->find(array('id'=>$command));
            $ticket = $result->ticket;
            $ticket_id = (string) $ticket->id;

            $client->ticket($ticket_id)->update(
                array('comment' => array(
                        'public' => true,
                        'body' => $remainder
                        )
                )
            );
                
            $response->message(
                "Your ticket has been updated." .
                "We'll get on it as soon as we can."
            );

        } catch (Exception $e) {
            $response->message(
                "I could not find a ticket with the ID '" . $command . "'.  " .
                "Are you sure that's the right ID?"
            );
        }       
    } else {
        $response->message(
            "Hello and thanks for the message.  " .
            "Unfortunately I did not quite understand what you needed.  " .
            "Try sending the word 'menu' for the list of commands."
        );
    }
}

header("content-type: text/xml");
print $response;
?>