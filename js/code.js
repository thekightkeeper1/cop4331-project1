const urlBase = 'http://localhost/API';
const extension = 'php';

let userId = 0;
let firstName = "";
let lastName = "";

function saveCookie()
{
	let minutes = 20;
	let date = new Date();
	date.setTime(date.getTime()+(minutes*60*1000));	
	document.cookie = "firstName=" + firstName + ",lastName=" + lastName + ",userId=" + userId + ";expires=" + date.toGMTString();
}

function readCookie()
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
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
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

const CONTACTS_PER_PAGE = 5;
let currentPage = 1;
let currentResults = [];

function searchContacts()
{
	readCookie();
	let srch = document.getElementById("searchContacts").value;
	document.getElementById("contactSearchResult").innerHTML = "";

	if(srch.toLowerCase() === "banana")
	{
		bananaRain();
		document.getElementById("contactSearchResult").innerHTML = "It's banana time!";
		currentResults = [];
		renderPagination();
		return;
	}

	let tmp = {query:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/Contacts/SearchContacts.' + extension;
	
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

				if(jsonObject.results.length == 0)
				{
					document.getElementById("contactSearchResult").innerHTML = "No contacts found with that name!"
					currentResults = [];
					renderPagination();
					return;
				}
				
				currentResults = jsonObject.results;
				currentPage = 1;
				renderPage();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactSearchResult").innerHTML = err.message;
	}
}

function renderPage()
{
	let start = (currentPage - 1) * CONTACTS_PER_PAGE;
	let end = Math.min(start + CONTACTS_PER_PAGE, currentResults.length);
	let contactList = '<div class="contact-row contact-header">'
		+ '<span class="contact-name">Full Name</span>'
		+ '<span class="contact-phone">Phone</span>'
		+ '<span class="contact-email">Email</span>'
		+ '<span class="contact-actions"></span>'
		+ '</div>\r\n';

	for( let i = start; i < end; i++ )
	{
		contactList += '<div class="contact-row">'
			+ '<span class="contact-name">' + currentResults[i].firstname + ' ' + currentResults[i].lastname + '</span>'
			+ '<span class="contact-phone">' + currentResults[i].phone + '</span>'
			+ '<span class="contact-email">' + currentResults[i].email + '</span>'
			+ '<span class="contact-actions">'
			+ '<i class="fa-solid fa-pen-to-square" onclick="editContact(' + currentResults[i].ID + ', \'' + currentResults[i].firstname + '\', \'' + currentResults[i].lastname + '\', \'' + currentResults[i].email + '\', \'' + currentResults[i].phone + '\')"></i>'
			+ '<i class="fa-solid fa-trash" onclick="deleteContact(' + currentResults[i].ID + ')"></i>'
			+ '</span>'
			+ '</div>\r\n';
	}

	document.getElementById("contactSearchResult").innerHTML = contactList;
	renderPagination();
}

function renderPagination()
{
	let pagination = document.getElementById("pagination");
	let totalPages = Math.ceil(currentResults.length / CONTACTS_PER_PAGE);

	if(totalPages <= 1)
	{
		pagination.innerHTML = "";
		return;
	}

	let html = '<button class="page-btn" aria-label="Previous Page" onclick="goToPage(' + (currentPage - 1) + ')" ' + (currentPage === 1 ? 'disabled' : '') + '><i class="fa-solid fa-arrow-left"></i></button>';

	for(let p = 1; p <= totalPages; p++)
	{
		html += '<button class="page-btn' + (p === currentPage ? ' active' : '') + '" onclick="goToPage(' + p + ')">' + p + '</button>';
	}

	html += '<button class="page-btn" aria-label="Next Page" onclick="goToPage(' + (currentPage + 1) + ')" ' + (currentPage === totalPages ? 'disabled' : '') + '><i class="fa-solid fa-arrow-right"></i></button>';

	pagination.innerHTML = html;
}

function goToPage(page)
{
	let totalPages = Math.ceil(currentResults.length / CONTACTS_PER_PAGE);
	if(page < 1 || page > totalPages) return;
	currentPage = page;
	renderPage();
}

function doLogin()
{
	userId = 0;
	firstName = "";
	lastName = "";
	
	let login = document.getElementById("loginName").value;
	let password = document.getElementById("loginPassword").value;
	var hash = md5( password );

	if(login == "" || password == "")
	{
		document.getElementById("loginResult").innerHTML = "Please make sure all fields are filled in!";
		return;
	}

	
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

function addContact()
{
	window.location.href = 'add.html';
}

function editContact(id, firstname, lastname, email, phone)
{
	// storing all contact data in localStorage
	let contactData = {
		id: id,
		firstname: firstname,
		lastname: lastname,
		email: email,
		phone: phone
	};
	localStorage.setItem('editContactData', JSON.stringify(contactData));
	window.location.href = 'edit.html';
}

function deleteContact(id)
{
	let confirmed = window.confirm("Are you sure you want to delete this contact?");
	if(confirmed)
	{
		let tmp = {id:id};
		let jsonPayload = JSON.stringify( tmp );
		
		let url = urlBase + '/Contacts/DeleteContact.' + extension;  // Fixed

		let xhr = new XMLHttpRequest();
		xhr.open("POST", url, true);
		xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
		try
		{
			xhr.onreadystatechange = function() 
			{
				if (this.readyState == 4 && this.status == 200) 
				{
					searchContacts();
				}
			};
			xhr.send(jsonPayload);
		}
		catch(err)
		{
			window.alert("Error deleting contact. Please try again.");
		}
	}
}

document.addEventListener("DOMContentLoaded", function() {
    let searchInput = document.getElementById("searchContacts");
    if (searchInput) {
        searchInput.addEventListener("keyup", function(event) {
            if (event.key === "Enter") {
                searchContacts();
            }
        });
    }
});

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
        firstName: firstName,  
        lastName: lastName,    
        email: email,
        phone: phone
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/Contacts/AddContact.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
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
    } catch(err) {
        document.getElementById("contactResult").innerHTML = err.message;
    }
}

function loadContactForEdit() {
    let contactData = localStorage.getItem('editContactData');
    
    if (!contactData) {
        window.location.href = 'contacts.html';
        return;
    }

    let contact = JSON.parse(contactData);
    
    document.getElementById("contactFirstName").value = contact.firstname || '';
    document.getElementById("contactLastName").value = contact.lastname || '';
    document.getElementById("contactEmail").value = contact.email || '';
    document.getElementById("contactPhone").value = contact.phone || '';
}

function updateContact() {
    let contactData = localStorage.getItem('editContactData');
    let contact = JSON.parse(contactData);
    let contactId = contact.id;
    
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
        firstName: firstName,  
        lastName: lastName,   
        email: email,
        phone: phone
    };

    let jsonPayload = JSON.stringify(tmp);
    let url = urlBase + '/Contacts/EditContact.' + extension; 

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let jsonObject = JSON.parse(xhr.responseText);
                
                if (jsonObject.error === "") {
                    localStorage.removeItem('editContactData');  // Clean up
                    window.location.href = 'contacts.html';
                } else {
                    document.getElementById("contactResult").innerHTML = jsonObject.error;
                }
            }
        };
        xhr.send(jsonPayload);
    } catch(err) {
        document.getElementById("contactResult").innerHTML = err.message;
    }
}

function loadContacts()
{
    document.getElementById("searchContacts").value = "";
    searchContacts();
}

function bananaRain()
{
	// Create 20 bananas that fall down the screen
	for(let i = 0; i < 20; i++)
	{
		// Create a banana element
		let banana = document.createElement("div");
		banana.textContent = "🍌";
		banana.style.position = "fixed";
		banana.style.left = Math.random() * 100 + "vw";
		banana.style.top = "-50px";
		banana.style.fontSize = "30px";
		banana.style.zIndex = "9999";
		document.body.appendChild(banana);

		setTimeout(function() {
			banana.style.transition = "top 3s ease-in";
			banana.style.top = "110vh";

			setTimeout(function() {
				banana.remove();
			}, 3000);
		}, i * 100);
	}
}
