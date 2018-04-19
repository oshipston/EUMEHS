var usrAuth;
////// HTML loading function ///////
function loadPage(page) {
    //var xhttp = new XMLHttpRequest();
    //xhttp.onreadystatechange = function() {
      //  if (this.readyState == 4 && this.status == 200) {
       //     document.getElementById("main1").innerHTML = this.responseText;
        //}
    //};
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("main1").innerHTML = this.responseText;
        }
    };
    HTfile = page + ".html";
    xmlhttp.open("GET", "lib/pages/" + HTfile, true);
    xmlhttp.send();
}

////// Display last modified time stamp from index.html ///////
function lastUpdated() {
    var date = document.lastModified;
    document.getElementById("lastUpdated").innerHTML = date;
}

////// Initialise Java sdk facebook connection ///////
function FBstart() {
    window.fbAsyncInit = function() {
        FB.init({
                appId            : '1908403759409286',
                autoLogAppEvents : true,
                xfbml            : true,
                version          : 'v2.10'
        });
        FB.AppEvents.logPageView();
    };

    (function(d, s, id){
     var js, fjs = d.getElementsByTagName(s)[0];
     if (d.getElementById(id)) {return;}
     js = d.createElement(s); js.id = id;
     js.src = "//connect.facebook.net/en_US/sdk.js";
     fjs.parentNode.insertBefore(js, fjs);
     }(document, 'script', 'facebook-jssdk'));
}

////// Pop up facebook login window ///////
function myFacebookLogin() {
    FB.login(function(response){
                 if (response.authResponse){
                 console.log("Receiving Profile info...");
                 FB.api('/me',function(response){
                        usrAuth = FB.getAuthResponse();
                        console.log(response.name + " logged in. With ID:" + usrAuth.userID);
                        })
                 } else {
                 console.log("User did not authorise log in..");
                 }
             }, {scope: 'user_likes', return_scopes: true});
}


////// Scrape facebook event image ///////
function pullImg() {
    FB.getLoginStatus(function(response) {
                      if (response.status === 'connected') {
                      /* make the API call */
                      FB.api(
                             "/1882259251987412/picture?fields=cover",
                             function (response) {
                                 if (response && !response.error) {
                             console.log(response.data);
                                 }
                             }
                             );
                      var uid = response.authResponse.userID;
                      var accessToken = response.authResponse.accessToken;
                      } else if (response.status === 'not_authorized') {
                      // the user is logged in to Facebook, 
                      // but has not authenticated your app
                      } else {
                      // the user isn't logged in to Facebook.
                      }
                      });
}


////// Construct and show events feed from events database   ///////

function showEvents(PorP) {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("main1").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET","lib/php/event_query.php?PorP=" + PorP,true);
    xmlhttp.send();
}


///// Function that class php to construct homefeed boxes //////
function showHomeFeed() {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("main1").innerHTML = this.responseText;
        }
    };
    xmlhttp.open("GET","lib/php/home_query.php",true);
    xmlhttp.send();
}

///// Function that class php to construct homefeed boxes //////
function showArticles(type) {
    if (window.XMLHttpRequest) {
        // code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp = new XMLHttpRequest();
    } else {
        // code for IE6, IE5
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("main1").innerHTML = this.responseText;
        }
    };
    console.log(type);
    xmlhttp.open("GET","lib/php/article_query.php?type=" + type,true);
    xmlhttp.send();
}


///// Validate and check form inputs //////
function validateForm() {
    var name = $("#name").val();
    var email = $("#email").val();
    var msg = $("#message").val();

    // Reset box border colour
    document.getElementById("name").style.borderColor = "#ccc";
    document.getElementById("email").style.borderColor = "#ccc";
    document.getElementById("message").style.borderColor = "#ccc";
    
    if (name==""){
        console.log("Name Ommitted");
        document.getElementById("name").style.borderColor= "red";
        document.getElementById("name").placeholder = "Please enter your name.";
    }
    if (email==""){
        console.log("Email Ommitted");
        document.getElementById("email").style.borderColor= "red";
        document.getElementById("email").placeholder = "Please enter your email.";
    }
    if (msg==""){
        console.log("Message Ommitted");
        document.getElementById("message").style.borderColor= "red";
        document.getElementById("message").placeholder = "Please enter your message.";
    }
    if (name && email && msg != "") {
        submitForm(name,email,msg);
        console.log(name+email+msg);
    }
}

///// Submit contact us form //////
function submitForm(name,email,msg) {
    $.ajax({
           type: "POST",
           url: "lib/php/form-process.php",
           data: "name=" + name + "&email=" + email + "&message=" + msg,
           success : function(text){
           if (text == "success") {
               console.log("Form submitted successfully");
               formSuccess();
           }
       }
   });
}
///// Show hidden form text on successful submit //////
function formSuccess() {
    $("#msgSubmit").removeClass("hidden");
}

////// Switch chevron from right to down and vice-versa in events feed ///////
function switch_chevron(FB_id){
    var list = document.getElementById('collapse_chevron_' + FB_id).classList;
    switch (list[1]){
        case "glyphicon-chevron-right":
            document.getElementById('collapse_chevron_' + FB_id).classList.remove("glyphicon-chevron-right");
            document.getElementById('collapse_chevron_' + FB_id).classList.add("glyphicon-chevron-down");
            break;
        case "glyphicon-chevron-down":
            document.getElementById('collapse_chevron_' + FB_id).classList.remove("glyphicon-chevron-down");
            document.getElementById('collapse_chevron_' + FB_id).classList.add("glyphicon-chevron-right");
            break;
    }
}

///// Reorganise page based on the suitability of the sidebar //////

function snv_open(){
    document.getElementById("sidenav_switch").classList.remove("glyphicon-chevron-right");
    document.getElementById("sidenav_switch").classList.add("glyphicon-chevron-left");
    document.getElementById("mySidenav").style.width = "150px";
    document.getElementById("main1").style.paddingLeft = "150px";
}
function snv_close(){
    document.getElementById("sidenav_switch").classList.remove("glyphicon-chevron-left");
    document.getElementById("sidenav_switch").classList.add("glyphicon-chevron-right");
    document.getElementById("mySidenav").style.width = "30px";
    document.getElementById("main1").style.paddingLeft = "30px";
}

function sidenav_openclose(){
    var list = document.getElementById("sidenav_switch").classList;
    switch (list[1]){
        case "glyphicon-chevron-right":
            snv_open();
            break;
        case "glyphicon-chevron-left":
            snv_close();
            break;
    }
}

function sidenav_setup(s){
    switch (s){
        case 0:
            document.getElementById("mySidenav").style.width = "0px";
            document.getElementById("main1").style.paddingLeft = "0px";
            break;
        case 1:
            snv_open();
            break;
    }
}

///// Principal nav-by-hash function //////
function loadRefresh(hash){
    //setHeader();
    switch (hash) {
        case "#Events":
            ga('set', 'page', '/#Events');
            ga('send', 'pageview');
            showEvents("Upcoming");
            sidenav_setup(1);
            var sno = '<a onclick="showEvents(&quot;Upcoming&quot;)">Upcoming</a><a onclick="showEvents(&quot;All&quot;)">All Events</a><a onclick="showEvents(&quot;Past&quot;)">Past</a>';
            document.getElementById("sidenav_opts").innerHTML = sno;
            break;
        case "#UniversityofEdinburgh":
            ga('set', 'page', '/#UniversityofEdinburgh');
            ga('send', 'pageview');
            loadPage("UofEAbout");
            sidenav_setup(0);
            break;
        case "#EUMEHSSoc":
            ga('set', 'page', '/#EUMEHSSoc');
            ga('send', 'pageview');
            loadPage("SocAbout");
            sidenav_setup(0);
            break;
        case "#EUMEHSCommittee":
            ga('set', 'page', '/#EUMEHSCommittee');
            ga('send', 'pageview');
            loadPage("CommitteeAbout");
            sidenav_setup(0);
            break;
        case "#ContactUs":
            ga('set', 'page', '/#ContactUs');
            ga('send', 'pageview');
            loadPage("contact");
            sidenav_setup(0);
            break;
        case "#Articles":
            ga('set', 'page', '/#Articles');
            ga('send', 'pageview');
            showArticles("all");
            sidenav_setup(1);
            var sno = '<a onclick="showArticles(&quot;all&quot;)">All</a><a onclick="showArticles(&quot;recent&quot;)">Recent</a><a onclick="showArticles(&quot;featured&quot;)">Featured</a><a onclick="showArticles(&quot;reviews&quot;)">Reviews</a><a onclick="showArticles(&quot;essays&quot;)">Essays</a><a onclick="showArticles(&quot;opinions&quot;)">Opinions</a><a onclick="showArticles(&quot;news&quot;)">News</a>';
            document.getElementById("sidenav_opts").innerHTML = sno;
            break;
        case "#EUMEHS":
            ga('set', 'page', '/#EUMEHS');
            ga('send', 'pageview');
            showHomeFeed();
            sidenav_setup(0);
            break;
        case "#EUMEHSBrand":
            ga('set', 'page', '/#EUMEHSBrand');
            ga('send', 'pageview');
            loadPage("eumehs_brand");
            sidenav_setup(0);
            break;
        case "#OurSponsors":
            ga('set', 'page', '/#OurSponsors');
            ga('send', 'pageview');
            loadPage("sponsors");
            sidenav_setup(0);
            break;
        case "#DialoguesOnAesthetics":
            ga('set', 'page', '/#DialoguesOnAesthetics');
            ga('send', 'pageview');
            loadPage("DialoguesOnAesthetics");
            sidenav_setup(0);
            break;
        default:
            location.hash = "#EUMEHS";
    }
}


////// ////// ////// AJAX function list ////// ////// //////

$(document).ready(function() {
                  loadRefresh(location.hash);
                  lastUpdated();
                  });

$(document).on('submit',"#contactForm",function(event){
               event.preventDefault();
               validateForm();
               });

// Manage homepage box navigation
$(document).on('click',".feed_link",function(){
               console.log(this.id);
               if (this.id[0].indexOf('#') > -1) {
                   location.hash = this.id;
               } else if (this.id.indexOf('Article') > -1) {
               
               } else {
                   window.location.href = this.id;
               }
               });

// Load content navigation using back bar etc.
$(window).on('hashchange', function(){
             loadRefresh(location.hash);
             });


/* Set the width of the side navigation to 250px */
$(document).on('click',"#sidenav_switch",function(){
               sidenav_openclose();
               });


//Image resizing scripts

/*$(window).resize(function() {
                 document.getElementById("OCC").style.marginLeft = (-0.4*document.getElementById("OCC").width + "px");
                 });
$(window).resize(function() {
                 document.getElementById("NCC").style.marginReft = (-0.2*document.getElementById("NCC").width + "px");
                 });*/

