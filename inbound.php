<?php
/*
    Copyright (c) 2011 D. Keith Casey Jr. "caseysoftware"
*/

include 'Zendesk.lib.php';

ini_set('display_errors', 0);
error_reporting(0);

define("ZD_SITE", 'yourzendesksite');
define("ZD_USER", 'youruser@test.com');
define("ZD_PASS", 'yourpassword');
define("ZD_FIELD",'fieldid');
define("PLACEHOLDEREMAIL", ZD_USER);

if (isset($_POST)){
    $from    = isset($_POST['From']) ?
                $_POST['From'] : 'bad phone number';
    $body    = isset($_POST['Body']) ?
                $_POST['Body'] : 'An error occured from this number: '.$from;

    $zd = new Zendesk(ZD_SITE, ZD_USER, ZD_PASS);
    $zd->set_output(ZENDESK_OUTPUT_XML);

    $result = $zd->get(ZENDESK_SEARCH, array(
                    'query' => "query=type:ticket+status:new+status:open+" .
                                "status:pending+order_by:updated_at+sort:desc+" .
                                "ticket_field_".ZD_FIELD.":$from"
                )
            );

    $xml = simplexml_load_string($result);
    $attr = $xml->attributes();

    if ((int) $attr['count']) {
        $ticket_id = $xml->xpath('/records/record/nice-id');
        $ticket_id = (string) $ticket_id[0][0];
        // incoming sms has the same From as an open ticket, just append
        $result = $zd->update(ZENDESK_TICKETS, array(
                'details' => array(
                        'is-public' => true,
                        'value' => $body
                        ),
                'id' => $ticket_id,
                ));
    } else {
        // incoming sms has a new From, so create a new ticket
        $result = $zd->create(ZENDESK_TICKETS, array(
                'details' => array(
                        'description' => $body,
                        'subject' => substr($body, 0, 80),
                        'requester_email' => PLACEHOLDEREMAIL,
                        'ticket-field-entries' => array (
                            array(
                                'ticket-field-id' => ZD_FIELD,
                                'value' => $from
                            )
                        )
                )));
    }
}

header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
?>
<Response></Response>