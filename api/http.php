<?php
/*********************************************************************
    http.php

    HTTP controller for the osTicket API

    Jared Hancock
    Copyright (c)  2006-2013 osTicket
    http://www.osticket.com

    Released under the GNU General Public License WITHOUT ANY WARRANTY.
    See LICENSE.TXT for details.

    vim: expandtab sw=4 ts=4 sts=4:
**********************************************************************/
// Use sessions — it's important for SSO authentication, which uses
// /api/auth/ext
define('DISABLE_SESSION', false);

require 'api.inc.php';

# Include the main api urls
require_once INCLUDE_DIR."class.dispatcher.php";

$dispatcher = patterns('',
        url_post("^/tickets\.(?P<format>xml|json|email)$", array('api.tickets.php:TicketApiController','create')),

        //Added Ticket Endpoints
        url_get("^/tickets\.(?P<format>xml|json)/(?P<tid>\d+)$", array('api.tickets.php:TicketApiController','getTicket')),  //Do first!
        url_get("^/tickets\.(?P<format>xml|json)", array('api.tickets.php:TicketApiController','getTickets')),
        url_post("^/tickets\.(?P<format>xml|json)/(?P<tid>\d+)$", array('api.tickets.php:TicketApiController','reopenTicket')),
        url_put("^/tickets\.(?P<format>xml|json)/(?P<tid>\d+)$", array('api.tickets.php:TicketApiController','updateTicket')),
        url_delete("^/tickets\.(?P<format>xml|json)/(?P<tid>\d+)$", array('api.tickets.php:TicketApiController','closeTicket')),
        url_post("^/tickets/reply\.(?P<format>json)$", array('api.tickets.php:TicketApiController','postReply')),
        url_get("^/topics\.(?P<format>xml|json)", array('api.tickets.php:TicketApiController','getTopics')),

        //Added User Endpoints
        url_post("^/scp/users\.(?P<format>xml|json)$", array('api.users.php:UserApiController','create')),
        url_delete("^/scp/users\.(?P<format>xml|json)/(?P<uid>\d+)$", array('api.users.php:UserApiController','delete')),

        url('^/tasks/', patterns('',
                url_post("^cron$", array('api.cron.php:CronApiController', 'execute'))
         ))
        );

Signal::send('api', $dispatcher);

//Change to only add staff for /scp API endpoints.  Needed to change ticket status so that user can be deleted.
$thisstaff = new ApiUser();

# Call the respective function
print $dispatcher->resolve($ost->get_path_info());

?>
