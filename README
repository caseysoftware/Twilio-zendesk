
## Twilio / Zendesk Integration Example

Copyright (c) 2011 D. Keith Casey Jr. "caseysoftware"

If you find this code useful, please let me know.

    Permission is hereby granted, free of charge, to any person
    obtaining a copy of this software and associated documentation
    files (the "Software"), to deal in the Software without
    restriction, including without limitation the rights to use,
    copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the
    Software is furnished to do so, subject to the following
    conditions:

    The above copyright notice and this permission notice shall be
    included in all copies or substantial portions of the Software.

    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
    EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
    OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
    NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
    HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
    WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
    FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
    OTHER DEALINGS IN THE SOFTWARE.

### Background

Zendesk is a Helpdesk system for receiving and responding to trouble tickets,
support requests, etc. Each customer service people can jump into particlar
tickets/threads as they need. All exchanges are via email.

In this scenario, I plug into Twilio to allow SMS to replace email completely
and transparency. The flow is as follows:

 *  A customer sends an SMS to a short/long code requesting support;
 *  This script receives the incoming SMS and checks for an open ticket from this phone number:
 **  If there is not a match, a new ticket is created using the body of the SMS and attaching the phone number as an attribute;
 **  If there is a match, the SMS is appended to the existing ticket;
 *  When a customer service representative responds to the ticket, an SMS is sent back out through Twilio to the original requestor;
 *  Goto 1.

### Configuration

 #  Create a Twilio account;
 #  Create a Zendesk account;
 #  Within Zendesk, visit Manage->Ticket Fields, select "Text" field, name it, and save it;
 #  Edit the field and note the Custom field ID on the right side of the screen;
 #  Within Zendesk, visit Settings->Extensions->Targets and click "add target":
 ##  Give the Target a useful name such as "Twilio Notification";
 ##  Use your Twilio Account SID & Phone Number with your Zendesk Custom field ID to fill in the AC1234, NNNN, and CCCC fields respectively in this URL: https://api.twilio.com/2010-04-01/Accounts/AC1234/SMS/Messages?From=+NNNN&To={{ticket.ticket_field_CCCC}}&Body={{ticket.latest_public_comment}}
 ##  Enter the completed URL in the URL box;
 ##  Enter your Account SID and Auth Token in the Username and Password fields;
 ##  Click save;
 #  Edit inbound.php filling in your Zendesk site, username, password, and the Custom field ID noted as CCCC above;
 #  If you want all tickets to come in as a specific user, you can set the PLACEHOLDER constant also;
 #  Upload the inbound.php file to your server, to get a URL such as http://example.org/inbound.php;
 #  Within Twilio, update the SMS URL for one of your phone numbers to point at the above URL;
 #  Within Zendesk, visit Manage->Triggers & mail notifications and click "add trigger":
 ##  Edit "Notify requestor of comment update" and change the "Perform these actions" dropdown to the Target name specified above.

Going forward, any incoming SMS to this number should generate a new ticket.
Any responses on that ticket will be sent to the original requestor via SMS.

### Dependencies

This example code is based on two independent libraries which are included here
for completeness, not because there's any implied ownership, endorsement, etc.
They have their own licenses, you should follow them.

Brian Hartvigsen's  Zendesk PHP Library:
http://code.google.com/p/zendesk-php-lib/ - r25 specifically

The official Twilio PHP Library:
https://github.com/twilio/twilio-php/ - v2.0.8 specifically

### Areas for Improvement

 *  Simplify the configuration on the Zendesk end of things, right now it's
probably more complicated than it needs to be;
 *  Use more of the incoming Twilio information to create a better customer
profile;
 *  Add a hook to allow for customer information lookup from third party systems;