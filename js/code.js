const urlBase = 'http://contacts.kite-keeper.com/API';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

//contact varibles
let allContacts = [];
let currentPage = 1;
let contactsPerPage = 5;

function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

/*function readCookie()
{
	userId = -1;
	let data = document.cookie;
	let splits = data.split(",");
	for(var i = 0; i < splits.length; i++) 
	{
		let thisOne = splits[i].trim();
		let tokens = thisOne.split("=");
		if( tokens[0] == "firstName" )
		{
			firstName = tokens[1];
		}
		else if( tokens[0] == "lastName" )
		{
			lastName = tokens[1];
		}
		else if( tokens[0] == "userId" )
		{
			userId = parseInt( tokens[1].trim() );
		}
	}
	
	if( userId < 0 )
	{
		window.location.href = "index.html";
	}
}*/

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addContact()
{
	let newColor = document.getElementById("colorText").value;
	document.getElementById("colorAddResult").innerHTML = "";

	let tmp = {color:newColor,userId,userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/Contacts/AddContact.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("colorAddResult").innerHTML = "Color has been added";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("colorAddResult").innerHTML = err.message;
	}
	
}

function doRegister()
{
	userId = 0;

    let firstName = document.getElementById("registerFirstName").value;
    let lastName = document.getElementById("registerLastName").value;
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
	var hash = md5( password );
	
	document.getElementById("registerResult").innerHTML = "";

    if(firstName == "" || lastName == "" || login == "" || password == "")
    {
        document.getElementById("registerResult").innerHTML = "Please make sure all fields are filled in!";
        return;
    }

	// let tmp = {login:login,password:password};
	var tmp = {firstName:firstName, lastName:lastName, userName:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Auth/Register.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.id;
		
				if( userId < 1 )
				{		
					document.getElementById("registerResult").innerHTML = "Register failed";
					return;
				}
		
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();
	
				window.location.href = "contacts.html";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}

function searchContacts()
{
	readCookie();
	let srch = document.getElementById("searchContacts").value;
	document.getElementById("contactSearchResult").innerHTML = "";
	
	let contactList = "";

	let tmp = {query:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/Contacts/Search.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				
				for( let i=0; i<jsonObject.results.length; i++ )
				{
					contactList += jsonObject.results[i].firstname;
					if( i < jsonObject.results.length - 1 )
					{
						contactList += "<br />\r\n";
					}
				}
				document.getElementById("contactSearchResult").innerHTML = contactList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactSearchResult").innerHTML = err.message;
	}
	
}

function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
	var hash = md5( password );
	
	document.getElementById("loginResult").innerHTML = "";

	// let tmp = {login:login,password:password};
	var tmp = {userName:login,password:hash};
	let jsonPayload = JSON.stringify( tmp );
	
	let url = urlBase + '/Auth/Login.' + extension;

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				let jsonObject = JSON.parse( xhr.responseText );
				userId = jsonObject.id;
		
				if( userId < 1 )
				{		
					document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
					return;
				}
		
				firstName = jsonObject.firstName;
				lastName = jsonObject.lastName;

				saveCookie();
	
				window.location.href = "contacts.html";
			}
			else if(this.status == 401)
			{
				document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("loginResult").innerHTML = err.message;
	}

}

//contact functions
function loadContacts() {
    let tmp = { 
        id: userId, 
        cacheSize: 100
    };
    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/Contacts/Fetch.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let jsonObject = JSON.parse(xhr.responseText);
            
            if (jsonObject.error === "") {
                allContacts = jsonObject.results || [];
                displayContacts();
            } else {
                document.getElementById("contactsList").innerHTML = 
                    '<p style="text-align:center;color:var(--error);">Error loading contacts</p>';
            }
        }
    };

    xhr.send(jsonPayload);
}

function displayContacts() {
    let filteredContacts = allContacts;
    let searchTerm = document.getElementById("searchContacts").value.toLowerCase();
    
    if (searchTerm) {
        filteredContacts = allContacts.filter(contact => 
            contact.FirstName.toLowerCase().includes(searchTerm) ||
            contact.LastName.toLowerCase().includes(searchTerm)
        );
    }

    let start = (currentPage - 1) * contactsPerPage;
    let end = start + contactsPerPage;
    let paginatedContacts = filteredContacts.slice(start, end);

    let html = '';
    
    if (paginatedContacts.length === 0) {
        html = '<p style="text-align:center;margin-top:40px;font-size:18px;">No contacts found</p>';
    } else {
        paginatedContacts.forEach(contact => {
            html += `
                <div class="contact-card">
                    <div class="contact-info">
                        <span class="contact-name">${contact.FirstName} ${contact.LastName}</span>
                        <span class="contact-separator">|</span>
                        <span class="contact-phone">${contact.Phone || 'No phone'}</span>
                        <span class="contact-separator">|</span>
                        <span class="contact-email">${contact.Email || 'No email'}</span>
                    </div>
                    <button class="icon-button edit-icon" onclick="editContact(${contact.ID})">Edit</button>
                    <button class="icon-button delete-icon" onclick="deleteContact(${contact.ID})">Delete</button>
                </div>
            `;
        });
    }

    document.getElementById("contactsList").innerHTML = html;
    displayPagination(filteredContacts.length);
}

function saveContact() {
    let firstName = document.getElementById("contactFirstName").value.trim();
    let lastName = document.getElementById("contactLastName").value.trim();
    let email = document.getElementById("contactEmail").value.trim();
    let phone = document.getElementById("contactPhone").value.trim();

    if (!firstName || !lastName) {
        document.getElementById("contactResult").innerHTML = "First and Last name are required";
        return;
    }

    let tmp = {
        userId: userId,
        firstname: firstName,
        lastname: lastName,
        email: email,
        phone: phone
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/Contacts/Create.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let jsonObject = JSON.parse(xhr.responseText);
            
            if (jsonObject.error === "") {
                window.location.href = 'contacts.html';
            } else {
                document.getElementById("contactResult").innerHTML = jsonObject.error;
            }
        }
    };

    xhr.send(jsonPayload);
}

function updateContact() {
    let contactId = localStorage.getItem('editContactId');
    
    let firstName = document.getElementById("contactFirstName").value.trim();
    let lastName = document.getElementById("contactLastName").value.trim();
    let email = document.getElementById("contactEmail").value.trim();
    let phone = document.getElementById("contactPhone").value.trim();

    if (!firstName || !lastName) {
        document.getElementById("contactResult").innerHTML = "First and Last name are required";
        return;
    }

    let tmp = {
        id: contactId,
        userId: userId,
        firstname: firstName,
        lastname: lastName,
        email: email,
        phone: phone
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/Contacts/Update.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let jsonObject = JSON.parse(xhr.responseText);
            
            if (jsonObject.error === "") {
                localStorage.removeItem('editContactId');
                window.location.href = 'contacts.html';
            } else {
                document.getElementById("contactResult").innerHTML = jsonObject.error;
            }
        }
    };

    xhr.send(jsonPayload);
}

function loadContactForEdit() {
    let contactId = localStorage.getItem('editContactId');
    
    if (!contactId) {
        window.location.href = 'contacts.html';
        return;
    }

    let tmp = { id: contactId };
    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/Contacts/Read.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            let jsonObject = JSON.parse(xhr.responseText);
            
            if (jsonObject.error === "") {
                document.getElementById("contactFirstName").value = jsonObject.FirstName || '';
                document.getElementById("contactLastName").value = jsonObject.LastName || '';
                document.getElementById("contactEmail").value = jsonObject.Email || '';
                document.getElementById("contactPhone").value = jsonObject.Phone || '';
            }
        }
    };

    xhr.send(jsonPayload);
}

function displayPagination(totalContacts) {
    let totalPages = Math.ceil(totalContacts / contactsPerPage);
    
    if (totalPages <= 1) {
        document.getElementById("pagination").innerHTML = '';
        return;
    }

    let html = '<button onclick="previousPage()" ' + (currentPage === 1 ? 'disabled' : '') + '>←</button>';
    
    for (let i = 1; i <= totalPages; i++) {
        html += `<span class="page-number ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</span>`;
    }
    
    html += '<button onclick="nextPage()" ' + (currentPage === totalPages ? 'disabled' : '') + '>→</button>';
    
    document.getElementById("pagination").innerHTML = html;
}

function searchContacts() {
    currentPage = 1;
    displayContacts();
}

function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        displayContacts();
    }
}

function goToPage(page) {
    currentPage = page;
    displayContacts();
}

function nextPage() {
    let filteredContacts = allContacts;
    let searchTerm = document.getElementById("searchContacts").value.toLowerCase();
    
    if (searchTerm) {
        filteredContacts = allContacts.filter(contact => 
            contact.FirstName.toLowerCase().includes(searchTerm) ||
            contact.LastName.toLowerCase().includes(searchTerm)
        );
    }
    
    let totalPages = Math.ceil(filteredContacts.length / contactsPerPage);
    
    if (currentPage < totalPages) {
        currentPage++;
        displayContacts();
    }
}

function editContact(id) {
    localStorage.setItem('editContactId', id);
    window.location.href = 'edit.html';
}

function deleteContact(id) {
    if (!confirm("Are you sure you want to delete this contact?")) {
        return;
    }

    let tmp = { id: id };
    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/Contacts/Delete.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            loadContacts();
        }
    };

    xhr.send(jsonPayload);
}