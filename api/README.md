# Additional osTicket API endpoints.

The intent of these changes are to allow a public help desk to be embedded in a general purpose website, and for admin and staff to utilize the standard backend osTicket interface.

osTicket currently only allows the creation of a new ticket described by https://docs.osticket.com/en/latest/Developer%20Documentation/API/Tickets.html, and this repository extends osTickets with the following functionality:

1. Display a given users tickets.
2. Display available topics.
3. Display a ticket based on a given ticket ID.
4. Close a ticket.
5. Reopen a ticket.
6. Post a reply to an existing ticket.
7. Create new topic (minor modifications to core method).

In addition to GET, POST, and DELETE HTTP methods, PUT was added.

In addition, amalmagdy's code (https://github.com/osTicket/osTicket/pull/4361) whose purpose appears to be targeted for backend user needs may be desired to be added:
1. Retrieve ticket details.
2. Get list of tickets issued by one user.
3. Get list of tickets assigned to an agent (staff member).
4. Post a reply message to one ticket with updated status. i.e. change ticket status from open to closed.

## Tested With:
PHP Version 7.1.24, Apache/2.4.6, CentOS Linux release 7.5.1804
api/api_tester.html was just used to allow me to watch the script in a IDE.  Change the API key, email, and user name to reflect your installation.

## Setup
1. Made branch of b218896db6326420c7ebe42f3c06609b6618b906 => https://github.com/NotionCommotion/osTicket
2. Committed change to remove form errors.
3. Committed change to remove session errors.
4. Committed changes for new endpoints.

## TODO:
1. Confirm that posting a reply to an existing ticket with attachments work.
2. Figure out how to include attachments when retrieving ticket.
4. Clean up threads.
5. Changes to allow specific user name to be logged as making changes instead of "SYSTEM".
6. Better utilize exising osTicket methods where applicable.
7. TBD whether osTickets desiers camelcase or hypen property names (it is inconsistant)

## NOTES:
1. All ticket methods which change the database require either the user's email (email) or id (user_id).  Only adding a new ticket uses this information to directly insert into the database and the other's use it just to log who made the change.

