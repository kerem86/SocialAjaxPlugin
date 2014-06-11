/*************************************************************************************
*
* JQUERY (CODE ABSTRACTISATION)
*
*************************************************************************************/
//Globals
window.base_url = 'http://www.slots4play.com/'
window.socialAuth = new SocialAuth(); //proxy object to handle site auth

//Facebook variables
window.fbAsyncInit = {}; //for facebook initialization

//Google variables
var OAUTHURL    =   'https://accounts.google.com/o/oauth2/auth?';
var VALIDURL    =   'https://www.googleapis.com/oauth2/v1/tokeninfo?access_token=AIzaSyAjS_qEPAghkiSKsUimz0Rc49IeFuIL6fM';
var SCOPE       =   'https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email';
var CLIENTID    =   '319546066624-f1jhbmfki4p7uv56ijda5f4vrq7onr38.apps.googleusercontent.com';
var REDIRECT    =   'http://www.slots4play.com/AuthAPI/'
var LOGOUT      =   'https://accounts.google.com/Logout';
var TYPE        =   'token';
var _url        =   OAUTHURL + 'scope=' + SCOPE + '&client_id=' + CLIENTID + '&redirect_uri=' + REDIRECT + '&response_type=' + TYPE;
var acToken;
var tokenType;
var expiresIn;
var user;

$(document).ready(function(){
	socialAuth.init();
});

function SocialAuth()
{
	this.loggedIn = false;
	this.authProvider = '';

	this.init = function()
	{
		//Facbook Initialisation
		window.fbAsyncInit = function() {
			FB.init({
				appId      : '237836593012930',
				cookie     : true,  // enable cookies to allow the server to access the session
				xfbml      : true,  // parse social plugins on this page
				version    : 'v2.0' // use version 2.0
			});

			FB.getLoginStatus(function(response) {
				statusChangeCallback(response);
			});
		};
	};

	this.submitData = function(data, type)
	{
		var self = this;
		var user_info = data;
		$.ajax(
		{
			type: "POST",
			url: base_url + 'AuthAPI/login.php?s=' + self.authProvider,
			data: data,
			success: function(data)
			{
				data = JSON.parse(data);
				if (data.response == "OK")
				{
					self.loggedIn = true;
					self.showWellcomeMessage(user_info);
					self.checkCookie('postOnFacebook');
				}
				else
				{
					self.loggedIn = false;
					showLoginButtons();
				}
			},
		});
	};

	this.showWellcomeMessage = function(data)
	{
		$('#facebookLoginButton').remove();
		$('#googleLoginButton').remove();
		if (data.last_name != undefined) {
			var username = data.last_name;
		} else {
			var username = data.email.split('@')[0];
		}
		$('#socialLogin').append('<p id="wellcomeMessage"> Welcome <span id="user_first_name">' + username + '</span></p>')
	};

	this.showLoginButtons = function()
	{
		if ($('#facebookButton').length == 0)
		{
			var facebookButton = '<div class="facebookButton" style="display: inline-block"><button type="button" id="facebookLoginButton">Facebook Login</button></div>';
			var googleButton ='<div class="googleButton" style="display: inline-block"><button type="button" id="googleLoginButton">Google Login</button></div>';
			$('#wellcomeMessage').remove();
			$('#socialLogin').append(facebookButton);
			$('#socialLogin').append(googleButton);

			$('#googleLoginButton').click(function() { googleLogin(); });
			$('#facebookLoginButton').click(function() { facebookShowLoginWindow(); });
		}
	};

	this.setCookie = function(cname, cvalue, exdays)
	{
		var d = new Date();
		d.setTime(d.getTime() + (exdays*24*60*60*1000));
		var expires = "expires="+d.toGMTString();
		document.cookie = cname + "=" + cvalue + "; " + expires;
	};

	this.getCookie = function(cname)
	{
		var name = cname + "=";
		var ca = document.cookie.split(';');
		for(var i=0; i<ca.length; i++) {
			var c = ca[i].trim();
			if (c.indexOf(name) == 0) return c.substring(name.length,c.length);
		}
		return "";
	};

	this.checkCookie = function(cname)
	{
		var self = this;
		var post = self.getCookie(cname);
		if (post == "")
		{
			self.setCookie(cname, 'postOnFacebook', 1);
			postOnFacebook();
		}
	};
};

/*************************************************************************************
*
* FACEBOOK LOGIN
*
*************************************************************************************/

// Load the SDK asynchronously
(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

// This function is called when someone finishes with the Login
// Button.  See the onlogin handler attached to it in the sample
// code below.
function statusChangeCallback (response)
{
	if (response.status === 'connected')
	{
		facebookGetUserInfo();
	} else {
		socialAuth.showLoginButtons();
	}
}

function facebookShowLoginWindow()
{

	FB.login(function(response) {
		if (response.status == 'connected') {
			facebookGetUserInfo();
		} else {
			//console.log('User cancelled login or did not fully authorize.');
		}
	}, {scope: 'email, publish_actions'});
}

//creaza un obiect cu informatiile userului si apeleaza SocialAuth
function facebookGetUserInfo()
{
	FB.api('/me', function(response)
	{
		data = {
			'id': response.id,
			'email': response.email,
		}
		if (response.country) data.country = response.country;
		if (response.first_name) data.first_name = response.first_name;
		if (response.last_name) data.last_name = response.last_name;
		socialAuth.authProvider = 'facebook';
		socialAuth.submitData(data);
		//socialAuth.redirectToRegister(data, 'facebook');
	});
}

//function to post on facebook wall
function postOnFacebook()
{
	if (socialAuth.authProvider == 'facebook')
	{
		var url = document.URL;
		var gameTitle = $('.reverse-color h1').text()
		var username = $('#user_first_name').text();
		if (username == 'null') return 0;
		var body = 'Just played ' + gameTitle + ' on Slots4Play.com:' + url;

		var publish = {
			method: 'feed',
			message: 'Just played ' + gameTitle + ' on Slots4Play.com:' + url,
			name: 'Slots4Play',
			caption: 'Casino Mobile Games',
			description: $("meta[name=description]").attr('content'),
			link: url,
			picture: $('#playGameButton img').attr('src'),
			actions: [{ name: gameTitle, link: url }],
			user_message_prompt: 'Share your thoughts about ' + gameTitle
		};

		FB.api('/me/feed', 'post', publish, function(response)
		{
			if (!response || response.error)
			{
				console.log('Error occured');
			} else {
				console.log('Post ID: ' + response.id);
			}
		});
	}
}

/*************************************************************************************
*
* GOOGLE LOGIN
*
*************************************************************************************/

function googleLogin()
{
	var win         =   window.open(_url, "windowname1", 'width=800, height=600');

	var pollTimer   =   window.setInterval(function()
	{
		try
		{
			if (win.document.URL.indexOf(REDIRECT) != -1)
			{
				window.clearInterval(pollTimer);
				var url =   win.document.URL;
				acToken =   gup(url, 'access_token');
				tokenType = gup(url, 'token_type');
				expiresIn = gup(url, 'expires_in');
				win.close();

				googleValidateToken(acToken);
			}
		}
		catch(e)
		{
		}
	}, 500);
}

function googleValidateToken(token)
{
	$.ajax({
		url: VALIDURL + token,
		data: null,
		success: function(responseText){
			googleGetUserInfo();
			loggedIn = true;
			$('#loginText').hide();
			$('#logoutText').show();
		},
		dataType: "jsonp"
	});
}

function googleGetUserInfo()
{
	$.ajax({
		url: 'https://www.googleapis.com/oauth2/v1/userinfo?access_token=' + acToken,
		data: null,
		success: function(resp) {
			user    =   resp;
			googleSubmitData(user)	;
			console.log(user);
		},
		dataType: "jsonp"
	});
}

function gup(url, name)
{
	name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
	var regexS = "[\\#&]"+name+"=([^&#]*)";
	var regex = new RegExp( regexS );
	var results = regex.exec( url );
	if( results == null )
		return "";
	else
		return results[1];
}

function startLogoutPolling()
{
	$('#loginText').show();
	$('#logoutText').hide();
	loggedIn = false;
	$('#uName').text('Welcome ');
	$('#imgHolder').attr('src', 'none.jpg');
}

function googleSubmitData(response)
{
	data = {
		'id': response.id,
		'email': response.email,
	}
	if (response.country) data.country = response.country;
	if (response.first_name) data.first_name = response.family_name;
	if (response.last_name) data.last_name = response.given_name;
	socialAuth.authProvider = 'google';
	socialAuth.submitData(data);
}