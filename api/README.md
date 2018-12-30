# Additional osTicket API endpoints.

The intent of these changes are to allow a public help desk to be embedded in a general purpose website, and for admin and staff to utilize the standard backend osTicket interface.

osTicket currently only allows the creation of a new ticket described by https://docs.osticket.com/en/latest/Developer%20Documentation/API/Tickets.html, and this repository extends osTickets with the following functionality:

Endpoints utilized by user:
1. Create ticket (minor modifications to core method).
2. View ticket.
3. View given user's tickets.
4. Close ticket.
5. Reopen ticket.
6. Update ticket
7. Display available topics.

Endpoints utilized by application:
1. Create user.
2. View user.
3. Delete user.
4. Create organization.
5. View organization.
6. Delete organization (and optionally associated users).
7. View organization's users.

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
api/api_tester.html is used to test endpoints using an IDE.  Change the API key to reflect your installation.  Output is as follows:

```
Create organization
POST /api/scp/organizations.json

params:

{
  "name": "ABC Company",
  "address": "123 main street",
  "phone": "4254441212X123",
  "notes": "Mynotes",
  "website": "website.com"
}
Status: Created (201)

Response:

{
  "id": 33,
  "name": "ABC Company"
}

Create organization with existing name
POST /api/scp/organizations.json

params:

{
  "name": "ABC Company",
  "address": "123 main street",
  "phone": "4254441212X123",
  "notes": "Mynotes",
  "website": "website.com"
}
Status: Bad Request (400)

Response:

{
  "message": "Organization name 'ABC Company' is already in use"
}

Get organization
GET /api/scp/organizations.json/33

params:

null
Status: OK (200)

Response:

{
  "id": 33,
  "name": "ABC Company"
}

Create user
POST /api/scp/users.json

params:

{
  "email": "John.Doe@gmail.com",
  "phone": "(425) 444-1212 X123",
  "notes": "Some Notes",
  "name": "John Doe",
  "password": "thepassword",
  "timezone": "America/Los_Angeles",
  "org_id": 33
}
Status: Created (201)

Response:

{
  "id": 101,
  "name": "John Doe",
  "email": "John.Doe@gmail.com",
  "phone": "(425) 444-1212 x123"
}

Create user with existing email
POST /api/scp/users.json

params:

{
  "email": "John.Doe@gmail.com",
  "phone": "(425) 444-1212 X123",
  "notes": "Some Notes",
  "name": "John Doe",
  "password": "thepassword",
  "timezone": "America/Los_Angeles",
  "org_id": 33
}
Status: Bad Request (400)

Response:

{
  "message": "Email John.Doe@gmail.com is already in use"
}

Create second user
POST /api/scp/users.json

params:

{
  "email": "John.Doe_2@gmail.com",
  "phone": "(425) 444-1212 X123",
  "notes": "Some Notes",
  "name": "John Doe",
  "password": "thepassword",
  "timezone": "America/Los_Angeles",
  "org_id": 33
}
Status: Created (201)

Response:

{
  "id": 102,
  "name": "John Doe",
  "email": "John.Doe_2@gmail.com",
  "phone": "(425) 444-1212 x123"
}

Get organization users
GET /api/scp/organizations.json/users/33

params:

null
Status: OK (200)

Response:

[
  {
    "id": 101,
    "name": "John Doe",
    "email": "John.Doe@gmail.com",
    "phone": "(425) 444-1212 x123"
  },
  {
    "id": 102,
    "name": "John Doe",
    "email": "John.Doe_2@gmail.com",
    "phone": "(425) 444-1212 x123"
  }
]

Delete organization and delete users
DELETE /api/scp/organizations.json/33

params:

{
  "deleteUsers": 1
}
Status: No Content (204)

Response:

null

Delete organization with invalid ID
DELETE /api/scp/organizations.json/33

params:

null
Status: Bad Request (400)

Response:

{
  "message": "Organization ID '33' does not exist"
}

Get user with invalid ID
GET /api/scp/users.json/101

params:

null
Status: Bad Request (400)

Response:

{
  "message": "User ID '101' does not exist"
}

Delete user with invalid ID
DELETE /api/scp/users.json/101

params:

null
Status: Bad Request (400)

Response:

{
  "message": "User ID '101' does not exist"
}

Create user without an organization
POST /api/scp/users.json

params:

{
  "email": "John.Doe@gmail.com",
  "phone": "(425) 444-1212 X123",
  "notes": "Some Notes",
  "name": "John Doe",
  "password": "thepassword",
  "timezone": "America/Los_Angeles"
}
Status: Created (201)

Response:

{
  "id": 103,
  "name": "John Doe",
  "email": "John.Doe@gmail.com",
  "phone": "(425) 444-1212 x123"
}

Delete user
DELETE /api/scp/users.json/103

params:

null
Status: No Content (204)

Response:

null

Create organization
POST /api/scp/organizations.json

params:

{
  "name": "ABC Company",
  "address": "123 main street",
  "phone": "4254441212X123",
  "notes": "Mynotes",
  "website": "website.com"
}
Status: Created (201)

Response:

{
  "id": 34,
  "name": "ABC Company"
}

Create user
POST /api/scp/users.json

params:

{
  "email": "John.Doe@gmail.com",
  "phone": "(425) 444-1212 X123",
  "notes": "Some Notes",
  "name": "John Doe",
  "password": "thepassword",
  "timezone": "America/Los_Angeles",
  "org_id": 34
}
Status: Created (201)

Response:

{
  "id": 104,
  "name": "John Doe",
  "email": "John.Doe@gmail.com",
  "phone": "(425) 444-1212 x123"
}

Delete organization but do not delete users
DELETE /api/scp/organizations.json/34

params:

null
Status: No Content (204)

Response:

null

Get organization with invalid ID
GET /api/scp/organizations.json/34

params:

null
Status: Bad Request (400)

Response:

{
  "message": "Organization ID '34' does not exist"
}

Get user
GET /api/scp/users.json/104

params:

null
Status: OK (200)

Response:

{
  "id": 104,
  "name": "John Doe",
  "email": "John.Doe@gmail.com",
  "phone": "(425) 444-1212 x123"
}

Create Ticket using user email
POST /api/tickets.json

params:

{
  "message": "My original message",
  "name": "John Doe",
  "subject": "Testing API",
  "topicId": 2,
  "userId": 104
}
Status: Created (201)

Response:

{
  "id": "784332",
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
    "create": "2018-12-30 15:27:05",
    "due": "2019-01-01 15:27:05",
    "close": null,
    "last_message": "2018-12-30 15:27:05",
    "last_response": null
  },
  "user": {
    "fullname": "John Doe",
    "firstname": "John",
    "lastname": "Doe",
    "email": "John.Doe@gmail.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 139,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:05",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
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
  "userId": 104
}
Status: Created (201)

Response:

{
  "id": "342966",
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
    "create": "2018-12-30 15:27:05",
    "due": "2019-01-01 15:27:05",
    "close": null,
    "last_message": "2018-12-30 15:27:05",
    "last_response": null
  },
  "user": {
    "fullname": "John Doe",
    "firstname": "John",
    "lastname": "Doe",
    "email": "John.Doe@gmail.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 140,
      "pid": 0,
      "thread_id": 87,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:05",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    }
  ]
}

Create Ticket using invalid user email
POST /api/tickets.json

params:

{
  "message": "My original message",
  "name": "John Doe",
  "subject": "Testing API",
  "topicId": 2,
  "userId": 102
}
Status: Bad Request (400)

Response:

{
  "message": "Invalid user.  User ID does not exist."
}

Create Ticket using invalid user ID
POST /api/tickets.json

params:

{
  "message": "My original message",
  "name": "John Doe",
  "subject": "Testing API",
  "topicId": 2,
  "userId": 102
}
Status: Bad Request (400)

Response:

{
  "message": "Invalid user.  User ID does not exist."
}

Close Ticket using user email
DELETE /api/tickets.json/784332

params:

{
  "email": "John.Doe@gmail.com"
}
Status: No Content (204)

Response:

null

Reopen Ticket using user email
POST /api/tickets.json/784332

params:

{
  "email": "John.Doe@gmail.com"
}
Status: OK (200)

Response:

{
  "id": "784332",
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
    "create": "2018-12-30 15:27:05",
    "due": "2019-01-01 15:27:05",
    "close": null,
    "last_message": "2018-12-30 15:27:05",
    "last_response": null
  },
  "user": {
    "fullname": "John Doe",
    "firstname": "John",
    "lastname": "Doe",
    "email": "John.Doe@gmail.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 139,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:05",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    }
  ]
}

Close Ticket using user ID
DELETE /api/tickets.json/784332

params:

{
  "userId": 104
}
Status: No Content (204)

Response:

null

Reopen Ticket using user ID
POST /api/tickets.json/784332

params:

{
  "userId": 104
}
Status: OK (200)

Response:

{
  "id": "784332",
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
    "create": "2018-12-30 15:27:05",
    "due": "2019-01-01 15:27:05",
    "close": null,
    "last_message": "2018-12-30 15:27:05",
    "last_response": null
  },
  "user": {
    "fullname": "John Doe",
    "firstname": "John",
    "lastname": "Doe",
    "email": "John.Doe@gmail.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 139,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:05",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    }
  ]
}

Update Ticket using user email
PUT /api/tickets.json/784332

params:

{
  "email": "John.Doe@gmail.com",
  "message": "My updated message using email"
}
Status: OK (200)

Response:

{
  "id": "784332",
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
    "create": "2018-12-30 15:27:05",
    "due": "2019-01-01 15:27:05",
    "close": null,
    "last_message": "2018-12-30 15:27:08",
    "last_response": null
  },
  "user": {
    "fullname": "John Doe",
    "firstname": "John",
    "lastname": "Doe",
    "email": "John.Doe@gmail.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 139,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:05",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    },
    {
      "id": 141,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using email",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:08",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    }
  ]
}

Update Ticket using user ID
PUT /api/tickets.json/784332

params:

{
  "userId": 104,
  "message": "My updated message using userId"
}
Status: OK (200)

Response:

{
  "id": "784332",
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
    "create": "2018-12-30 15:27:05",
    "due": "2019-01-01 15:27:05",
    "close": null,
    "last_message": {
      "alias": null,
      "func": "NOW",
      "args": []
    },
    "last_response": null
  },
  "user": {
    "fullname": "John Doe",
    "firstname": "John",
    "lastname": "Doe",
    "email": "John.Doe@gmail.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 139,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:05",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    },
    {
      "id": 141,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using email",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:08",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    },
    {
      "id": 142,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using userId",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:08",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    }
  ]
}

Update Ticket using invalid user email
PUT /api/tickets.json/784332

params:

{
  "email": "John.Doe_2@gmail.com",
  "message": "My updated message using email"
}
Status: Bad Request (400)

Response:

{
  "message": "Invalid user.  User email does not exist."
}

Update Ticket using invalid user user ID
PUT /api/tickets.json/784332

params:

{
  "userId": 102,
  "message": "My updated message using userId"
}
Status: Bad Request (400)

Response:

{
  "message": "Invalid user.  User ID does not exist."
}

Get Tickets using user email
GET /api/tickets.json

params:

{
  "email": "John.Doe@gmail.com"
}
Status: OK (200)

Response:

[
  {
    "id": "784332",
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
      "create": "2018-12-30 15:27:05",
      "due": "2019-01-01 15:27:05",
      "close": null,
      "last_message": "2018-12-30 15:27:08",
      "last_response": null
    },
    "user": {
      "fullname": "John Doe",
      "firstname": "John",
      "lastname": "Doe",
      "email": "John.Doe@gmail.com",
      "phone": "(425) 444-1212 x123"
    },
    "source": "API",
    "assigned_to": [],
    "threads": [
      {
        "id": 139,
        "pid": 0,
        "thread_id": 86,
        "type": "M",
        "typeName": "message",
        "editor": null,
        "source": "api",
        "format": "text",
        "title": "Testing API",
        "message": "My original message",
        "attachmentUrls": [],
        "timestampes": {
          "created": "2018-12-30 15:27:05",
          "updated": "0000-00-00 00:00:00"
        },
        "user": {
          "type": "user",
          "id": null,
          "name": "John Doe"
        }
      },
      {
        "id": 141,
        "pid": 0,
        "thread_id": 86,
        "type": "M",
        "typeName": "message",
        "editor": null,
        "source": "",
        "format": "text",
        "title": null,
        "message": "My updated message using email",
        "attachmentUrls": [],
        "timestampes": {
          "created": "2018-12-30 15:27:08",
          "updated": "0000-00-00 00:00:00"
        },
        "user": {
          "type": "user",
          "id": null,
          "name": "John Doe"
        }
      },
      {
        "id": 142,
        "pid": 0,
        "thread_id": 86,
        "type": "M",
        "typeName": "message",
        "editor": null,
        "source": "",
        "format": "text",
        "title": null,
        "message": "My updated message using userId",
        "attachmentUrls": [],
        "timestampes": {
          "created": "2018-12-30 15:27:08",
          "updated": "0000-00-00 00:00:00"
        },
        "user": {
          "type": "user",
          "id": null,
          "name": "John Doe"
        }
      }
    ]
  },
  {
    "id": "342966",
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
      "create": "2018-12-30 15:27:05",
      "due": "2019-01-01 15:27:05",
      "close": null,
      "last_message": "2018-12-30 15:27:05",
      "last_response": null
    },
    "user": {
      "fullname": "John Doe",
      "firstname": "John",
      "lastname": "Doe",
      "email": "John.Doe@gmail.com",
      "phone": "(425) 444-1212 x123"
    },
    "source": "API",
    "assigned_to": [],
    "threads": [
      {
        "id": 140,
        "pid": 0,
        "thread_id": 87,
        "type": "M",
        "typeName": "message",
        "editor": null,
        "source": "api",
        "format": "text",
        "title": "Testing API",
        "message": "My original message",
        "attachmentUrls": [],
        "timestampes": {
          "created": "2018-12-30 15:27:05",
          "updated": "0000-00-00 00:00:00"
        },
        "user": {
          "type": "user",
          "id": null,
          "name": "John Doe"
        }
      }
    ]
  }
]

Get Tickets using user ID
GET /api/tickets.json

params:

{
  "userId": 104
}
Status: OK (200)

Response:

[
  {
    "id": "784332",
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
      "create": "2018-12-30 15:27:05",
      "due": "2019-01-01 15:27:05",
      "close": null,
      "last_message": "2018-12-30 15:27:08",
      "last_response": null
    },
    "user": {
      "fullname": "John Doe",
      "firstname": "John",
      "lastname": "Doe",
      "email": "John.Doe@gmail.com",
      "phone": "(425) 444-1212 x123"
    },
    "source": "API",
    "assigned_to": [],
    "threads": [
      {
        "id": 139,
        "pid": 0,
        "thread_id": 86,
        "type": "M",
        "typeName": "message",
        "editor": null,
        "source": "api",
        "format": "text",
        "title": "Testing API",
        "message": "My original message",
        "attachmentUrls": [],
        "timestampes": {
          "created": "2018-12-30 15:27:05",
          "updated": "0000-00-00 00:00:00"
        },
        "user": {
          "type": "user",
          "id": null,
          "name": "John Doe"
        }
      },
      {
        "id": 141,
        "pid": 0,
        "thread_id": 86,
        "type": "M",
        "typeName": "message",
        "editor": null,
        "source": "",
        "format": "text",
        "title": null,
        "message": "My updated message using email",
        "attachmentUrls": [],
        "timestampes": {
          "created": "2018-12-30 15:27:08",
          "updated": "0000-00-00 00:00:00"
        },
        "user": {
          "type": "user",
          "id": null,
          "name": "John Doe"
        }
      },
      {
        "id": 142,
        "pid": 0,
        "thread_id": 86,
        "type": "M",
        "typeName": "message",
        "editor": null,
        "source": "",
        "format": "text",
        "title": null,
        "message": "My updated message using userId",
        "attachmentUrls": [],
        "timestampes": {
          "created": "2018-12-30 15:27:08",
          "updated": "0000-00-00 00:00:00"
        },
        "user": {
          "type": "user",
          "id": null,
          "name": "John Doe"
        }
      }
    ]
  },
  {
    "id": "342966",
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
      "create": "2018-12-30 15:27:05",
      "due": "2019-01-01 15:27:05",
      "close": null,
      "last_message": "2018-12-30 15:27:05",
      "last_response": null
    },
    "user": {
      "fullname": "John Doe",
      "firstname": "John",
      "lastname": "Doe",
      "email": "John.Doe@gmail.com",
      "phone": "(425) 444-1212 x123"
    },
    "source": "API",
    "assigned_to": [],
    "threads": [
      {
        "id": 140,
        "pid": 0,
        "thread_id": 87,
        "type": "M",
        "typeName": "message",
        "editor": null,
        "source": "api",
        "format": "text",
        "title": "Testing API",
        "message": "My original message",
        "attachmentUrls": [],
        "timestampes": {
          "created": "2018-12-30 15:27:05",
          "updated": "0000-00-00 00:00:00"
        },
        "user": {
          "type": "user",
          "id": null,
          "name": "John Doe"
        }
      }
    ]
  }
]

Get Ticket
GET /api/tickets.json/784332

params:

null
Status: OK (200)

Response:

{
  "id": "784332",
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
    "create": "2018-12-30 15:27:05",
    "due": "2019-01-01 15:27:05",
    "close": null,
    "last_message": "2018-12-30 15:27:08",
    "last_response": null
  },
  "user": {
    "fullname": "John Doe",
    "firstname": "John",
    "lastname": "Doe",
    "email": "John.Doe@gmail.com",
    "phone": "(425) 444-1212 x123"
  },
  "source": "API",
  "assigned_to": [],
  "threads": [
    {
      "id": 139,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "api",
      "format": "text",
      "title": "Testing API",
      "message": "My original message",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:05",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    },
    {
      "id": 141,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using email",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:08",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    },
    {
      "id": 142,
      "pid": 0,
      "thread_id": 86,
      "type": "M",
      "typeName": "message",
      "editor": null,
      "source": "",
      "format": "text",
      "title": null,
      "message": "My updated message using userId",
      "attachmentUrls": [],
      "timestampes": {
        "created": "2018-12-30 15:27:08",
        "updated": "0000-00-00 00:00:00"
      },
      "user": {
        "type": "user",
        "id": null,
        "name": "John Doe"
      }
    }
  ]
}

Delete user
DELETE /api/scp/users.json/104

params:

null
Status: No Content (204)

Response:

null

Get Ticket with invalid ID
GET /api/tickets.json/784332

params:

null
Status: Bad Request (400)

Response:

{
  "message": "Ticket Number '784332' does not exist"
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