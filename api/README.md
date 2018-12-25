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

## DEMO:
api/api_tester.html is used to test endpoints using an IDE.  Change the API key, email, and user name to reflect your installation.  Output is as follows:

```
Create User
POST /api/scp/users.json

params:

{
  "phone": "4254441212X123",
  "notes": "Mynotes",
  "name": "john doe",
  "email": "johndoe66710@apitester.com",
  "password": "thepassword",
  "timezone": "America/Los_Angeles"
}
Status: Created (201)

Response:

{
  "id": 14,
  "name": "john doe",
  "email": "johndoe66710@apitester.com",
  "phone": "(425) 444-1212 x123"
}

Create Ticket using user email
POST /api/tickets.json

params:

{
  "email": "johndoe66710@apitester.com",
  "message": "My original message",
  "name": "John Doe",
  "subject": "Testing API",
  "topicId": 2
}
Status: Created (201)

Response:

{
  "ticket_number": "630051",
  "subject": "Testing API",
  "topic": {
    "id": 2,
    "name": "Feedback"
  },
  "status": {
    "id": 1,
    "name": "Open"
  },
  "priority": {
    "id": 1,
    "name": "low"
  },
  "department": "Support",
  "timestamps": {
    "create": "2018-12-25 17:47:42",
    "due": "2018-12-27 17:47:42",
    "close": null,
    "last_message": "2018-12-25 17:47:42",
    "last_response": null
  },
  "user": {
    "fullname": "john doe",
    "firstname": "john",
    "lastname": "doe",
    "email": "johndoe66710@apitester.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 40,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:42",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    }
  ]
}

Create Ticket using user ID
POST /api/tickets.json

params:

{
  "message": "My original message",
  "name": "John Doe",
  "subject": "Testing API",
  "topicId": 2,
  "userId": 14
}
Status: Created (201)

Response:

{
  "ticket_number": "174449",
  "subject": "Testing API",
  "topic": {
    "id": 2,
    "name": "Feedback"
  },
  "status": {
    "id": 1,
    "name": "Open"
  },
  "priority": {
    "id": 1,
    "name": "low"
  },
  "department": "Support",
  "timestamps": {
    "create": "2018-12-25 17:47:43",
    "due": "2018-12-27 17:47:43",
    "close": null,
    "last_message": "2018-12-25 17:47:43",
    "last_response": null
  },
  "user": {
    "fullname": "john doe",
    "firstname": "john",
    "lastname": "doe",
    "email": "johndoe66710@apitester.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 41,
      "pid": 0,
      "thread_id": 17,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:43",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    }
  ]
}

Close Ticket using user email
DELETE /api/tickets.json/630051

params:

{
  "email": "johndoe66710@apitester.com"
}
Status: No Content (204)

Response:

null

Reopen Ticket using user email
POST /api/tickets.json/630051

params:

{
  "email": "johndoe66710@apitester.com"
}
Status: OK (200)

Response:

{
  "ticket_number": "630051",
  "subject": "Testing API",
  "topic": {
    "id": 2,
    "name": "Feedback"
  },
  "status": {
    "id": 1,
    "name": "Open"
  },
  "priority": {
    "id": 1,
    "name": "low"
  },
  "department": "Support",
  "timestamps": {
    "create": "2018-12-25 17:47:42",
    "due": "2018-12-27 17:47:44",
    "close": "2018-12-25 17:47:44",
    "last_message": "2018-12-25 17:47:42",
    "last_response": null
  },
  "user": {
    "fullname": "john doe",
    "firstname": "john",
    "lastname": "doe",
    "email": "johndoe66710@apitester.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 40,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:42",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    },
    {
      "id": 42,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:43",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    }
  ]
}

Close Ticket using user ID
DELETE /api/tickets.json/630051

params:

{
  "userId": 14
}
Status: No Content (204)

Response:

null

Reopen Ticket using user ID
POST /api/tickets.json/630051

params:

{
  "userId": 14
}
Status: OK (200)

Response:

{
  "ticket_number": "630051",
  "subject": "Testing API",
  "topic": {
    "id": 2,
    "name": "Feedback"
  },
  "status": {
    "id": 1,
    "name": "Open"
  },
  "priority": {
    "id": 1,
    "name": "low"
  },
  "department": "Support",
  "timestamps": {
    "create": "2018-12-25 17:47:42",
    "due": "2018-12-27 17:47:44",
    "close": "2018-12-25 17:47:44",
    "last_message": "2018-12-25 17:47:42",
    "last_response": null
  },
  "user": {
    "fullname": "john doe",
    "firstname": "john",
    "lastname": "doe",
    "email": "johndoe66710@apitester.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 40,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:42",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    },
    {
      "id": 42,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:43",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    },
    {
      "id": 43,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:44",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    }
  ]
}

Update Ticket using user email
PUT /api/tickets.json/630051

params:

{
  "email": "johndoe66710@apitester.com",
  "message": "My updated message using email"
}
Status: OK (200)

Response:

{
  "ticket_number": "630051",
  "subject": "Testing API",
  "topic": {
    "id": 2,
    "name": "Feedback"
  },
  "status": {
    "id": 1,
    "name": "Open"
  },
  "priority": {
    "id": 1,
    "name": "low"
  },
  "department": "Support",
  "timestamps": {
    "create": "2018-12-25 17:47:42",
    "due": "2018-12-27 17:47:44",
    "close": "2018-12-25 17:47:44",
    "last_message": "2018-12-25 17:47:45",
    "last_response": null
  },
  "user": {
    "fullname": "john doe",
    "firstname": "john",
    "lastname": "doe",
    "email": "johndoe66710@apitester.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 40,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:42",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    },
    {
      "id": 42,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:43",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    },
    {
      "id": 43,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:44",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    },
    {
      "id": 44,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using email",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:45",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    }
  ]
}

Update Ticket using user ID
PUT /api/tickets.json/630051

params:

{
  "userId": 14,
  "message": "My updated message using userId"
}
Status: OK (200)

Response:

{
  "ticket_number": "630051",
  "subject": "Testing API",
  "topic": {
    "id": 2,
    "name": "Feedback"
  },
  "status": {
    "id": 1,
    "name": "Open"
  },
  "priority": {
    "id": 1,
    "name": "low"
  },
  "department": "Support",
  "timestamps": {
    "create": "2018-12-25 17:47:42",
    "due": "2018-12-27 17:47:44",
    "close": "2018-12-25 17:47:44",
    "last_message": {
      "alias": null,
      "func": "NOW",
      "args": []
    },
    "last_response": null
  },
  "user": {
    "fullname": "john doe",
    "firstname": "john",
    "lastname": "doe",
    "email": "johndoe66710@apitester.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 40,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:42",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    },
    {
      "id": 42,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:43",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    },
    {
      "id": 43,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:44",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    },
    {
      "id": 44,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using email",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:45",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    },
    {
      "id": 45,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using userId",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:45",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    }
  ]
}

Get Ticket using ticket ID and user email
GET /api/tickets.json/630051

params:

null
Status: OK (200)

Response:

{
  "ticket_number": "630051",
  "subject": "Testing API",
  "topic": {
    "id": 2,
    "name": "Feedback"
  },
  "status": {
    "id": 1,
    "name": "Open"
  },
  "priority": {
    "id": 1,
    "name": "low"
  },
  "department": "Support",
  "timestamps": {
    "create": "2018-12-25 17:47:42",
    "due": "2018-12-27 17:47:44",
    "close": "2018-12-25 17:47:44",
    "last_message": "2018-12-25 17:47:45",
    "last_response": null
  },
  "user": {
    "fullname": "john doe",
    "firstname": "john",
    "lastname": "doe",
    "email": "johndoe66710@apitester.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 40,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:42",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    },
    {
      "id": 42,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:43",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    },
    {
      "id": 43,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:44",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    },
    {
      "id": 44,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using email",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:45",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    },
    {
      "id": 45,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using userId",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:45",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    }
  ]
}

Get Ticket using ticket ID and user Id
GET /api/tickets.json/630051

params:

null
Status: OK (200)

Response:

{
  "ticket_number": "630051",
  "subject": "Testing API",
  "topic": {
    "id": 2,
    "name": "Feedback"
  },
  "status": {
    "id": 1,
    "name": "Open"
  },
  "priority": {
    "id": 1,
    "name": "low"
  },
  "department": "Support",
  "timestamps": {
    "create": "2018-12-25 17:47:42",
    "due": "2018-12-27 17:47:44",
    "close": "2018-12-25 17:47:44",
    "last_message": "2018-12-25 17:47:45",
    "last_response": null
  },
  "user": {
    "fullname": "john doe",
    "firstname": "john",
    "lastname": "doe",
    "email": "johndoe66710@apitester.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 40,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:42",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    },
    {
      "id": 42,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:43",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    },
    {
      "id": 43,
      "pid": 0,
      "thread_id": 16,
      "type": "N",
      "typeName": "note",
      "editor": null,
      "source": "",
      "format": "html",
      "title": "Status Changed",
      "message": "Closed by user",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:44",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "system",
        "id": null,
        "name": null
      }
    },
    {
      "id": 44,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using email",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:45",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    },
    {
      "id": 45,
      "pid": 0,
      "thread_id": 16,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using userId",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-25 17:47:45",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "john doe"
      }
    }
  ]
}

Get Topics
GET /api/topics.json

params:

null
Status: OK (200)

Response:

[
  {
    "id": 2,
    "value": "Feedback"
  },
  {
    "id": 1,
    "value": "General Inquiry"
  },
  {
    "id": 10,
    "value": "Report a Problem"
  },
  {
    "id": 11,
    "value": "Report a Problem / Access Issue"
  }
]
```