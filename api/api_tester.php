<?php
session_start();
$orgNamePostfix=$_SESSION['orgNamePostfix']??rand(0,99999);
$emailPostfix=$_SESSION['emailPostfix']??rand(0,99999);
$_SESSION['orgNamePostfix']=$orgNamePostfix+1;
$_SESSION['emailPostfix']=$emailPostfix+1;
$orgName="Org Name {$orgNamePostfix}";
$email="johndoe{$emailPostfix}@apitester.com";
//$email="johndoe137@apitester.com";
?>
<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>OS Ticket API tester</title>
    </head>
    <body>
        <div>
            <ol id='output'></ol>
        </div>
        <script>
            var orgName='<?php echo($orgName);?>',
            email='<?php echo($email);?>',
            staffUserName='Michael',
            orgId,  //=112,             //Optional, else newly created user will be used for future tests.
            userId, //=112,             //Optional, else newly created user will be used for future tests.
            ticketNumber,   //=143514,  //Optional, else newly created ticket will be used for future tests.
            api='E5C00E92AC5D5EF8706E79201F6A0AAF';

            var output=document.getElementById('output');

            function testApi(test, method, url, data) {
                var msg='<h4>'+test+'</h4><p>'+method+' '+url+'</p><p>params:</p><pre><code>'+(data?JSON.stringify(data, null, 2):null)+'</code></pre>';
                if(data && ['POST','PUT'].indexOf(method)==-1) {
                    url=url+'?'+Object.keys(data).map(function(key) {return key + '=' + data[key]}).join('&');
                    data=null;
                }
                else data=JSON.stringify(data);
                var request = new XMLHttpRequest();
                request.open(method, url, false);
                request.setRequestHeader("X-API-Key", api);
                request.send(data);
                try {
                    var dataOut=JSON.parse(request.responseText);
                } catch (e) {
                    if(request.responseText) console.log('invalid response', request.responseText);
                    dataOut=null;
                }
                msg+='<p>Status: '+request.statusText+' ('+request.status+')</p><p>Response:</p><pre><code>'+JSON.stringify(dataOut, null, 2)+'</code></pre><br>';
                var li = document.createElement("li");
                li.innerHTML=msg;
                output.appendChild(li);
                return dataOut;
            }

            function startTest() {
                //user endpoints
                //Create first in this test so that new user can be used in future tests.
                var orgData={
                    name: orgName,
                    phone: '4254441212X123',
                    notes: 'Mynotes',
                    website: 'website.com',
                };
                var org=testApi('Create Organization', 'POST', '/api/scp/organizations.json', orgData);
                if(!orgId) {
                    if (typeof org.id === 'undefined') {
                        return 'org was not created';
                    }
                    orgId=org.id;
                }

                testApi('Get Organization', 'GET', '/api/scp/organizations.json/'+orgId);

                var userData={
                    email: email,
                    org_id: orgId,
                    phone: '4254441212X123',
                    notes: 'Mynotes',
                    name: 'john doe',
                    password: 'thepassword',
                    timezone: 'America/Los_Angeles',
                };
                var user=testApi('Create User', 'POST', '/api/scp/users.json', userData);
                if(!userId) {
                    if (typeof user.id === 'undefined') {
                        return 'user was not created';
                    }
                    userId=user.id;
                }

                userData.email='2_'+userData.email;
                testApi('Create Second User', 'POST', '/api/scp/users.json', userData);

                testApi('Get User', 'GET', '/api/scp/users.json/'+userId);

                //ticket endpoints
                //Create first in this test so that new ticket can be used in future tests.
                var ticketData={
                    email: email,
                    //message: "data:text/html, My original message",
                    message: "My original message",
                    name: "John Doe",
                    subject: "Testing API",
                    topicId: 2,
                };
                var ticket=testApi('Create Ticket using user email', 'POST', '/api/tickets.json', ticketData);
                if(!ticketNumber) {
                    if (typeof ticket.ticket_number === 'undefined') {
                        return 'ticket was not created';
                    }
                    ticketNumber=ticket.ticket_number;
                }

                ticketData.userId=userId;
                delete ticketData.email;
                testApi('Create Ticket using user ID', 'POST', '/api/tickets.json', ticketData);
                testApi('Close Ticket using user email', 'DELETE', '/api/tickets.json/'+ticketNumber, {email: email});
                testApi('Reopen Ticket using user email', 'POST', '/api/tickets.json/'+ticketNumber, {email: email});
                testApi('Close Ticket using user ID', 'DELETE', '/api/tickets.json/'+ticketNumber, {userId: userId});
                testApi('Reopen Ticket using user ID', 'POST', '/api/tickets.json/'+ticketNumber, {userId: userId});
                testApi('Update Ticket using user email', 'PUT', '/api/tickets.json/'+ticketNumber, {email: email, "message": "My updated message using email"});
                testApi('Update Ticket using user ID', 'PUT', '/api/tickets.json/'+ticketNumber, {userId: userId, "message": "My updated message using userId"});
                testApi('Get Ticket using ticket ID and user email', 'GET', '/api/tickets.json/'+ticketNumber);
                testApi('Get Ticket using ticket ID and user Id', 'GET', '/api/tickets.json/'+ticketNumber);
                testApi('Get Topics', 'GET', '/api/topics.json');
                testApi('Delete User', 'DELETE', '/api/scp/users.json/'+userId);
                testApi('Delete Organization', 'DELETE', '/api/scp/organizations.json/'+orgId);

            }

            if(error=startTest()){
                var li = document.createElement("li");
                li.appendChild(document.createTextNode("Test cannot continue since "+error))
                output.appendChild(li);
            }
        </script>
    </body>
</html>
