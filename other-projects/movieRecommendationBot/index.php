<!DOCTYPE html>
<meta charset="utf-8">
<title>Chatbot System</title>
<script src="https://code.jquery.com/jquery-1.11.2.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
<link rel="stylesheet" href="style.css">
<?php 
include './operator.php';
?>


<body>
<h1 class="center" id="headline">= Movie Recommendation Bot =</h1>

<!--this is the middle view seperated into 2 div, left=> human input , right => bot respond-->
<div id="grid-container">

  <div id="humanInput">

    <div id="info">
        <p id="info_start">Click on the microphone icon and begin speaking.</p>
        <p id="info_speak_now">Speak now.</p>
        <p id="info_no_speech">No speech was detected. You may need to adjust your
          <a href="//support.google.com/chrome/bin/answer.py?hl=en&amp;answer=1407892">
            microphone settings</a>.</p>
        <p id="info_no_microphone" style="display:none">
          No microphone was found. Ensure that a microphone is installed and that
          <a href="//support.google.com/chrome/bin/answer.py?hl=en&amp;answer=1407892">
          microphone settings</a> are configured correctly.</p>
        <p id="info_allow">Click the "Allow" button above to enable your microphone.</p>
        <p id="info_denied">Permission to use microphone was denied.</p>
        <p id="info_blocked">Permission to use microphone is blocked. To change,
          go to chrome://settings/contentExceptions#media-stream</p>
        <p id="info_upgrade">Web Speech API is not supported by this browser.
           Upgrade to <a href="//www.google.com/chrome">Chrome</a>
           version 25 or later.</p>
    </div>
    <div style="margin-left: 5%; margin-bottom: 8px;">
      <input type="text" id="say" style="width:80%; font-size:15pt" placeholder="insert speech" />
      <input class="text-center rounded-pill float-right" style="width:15%; margin-bottom: 7px;" type="button" id="btn" onclick="directShow();" value="I say"/>
    </div>
      <div style="margin-top: 15px;"class="float-right">
        <button id="start_button" onclick="startButton(event)">
          <img id="start_img" src="mic.gif" alt="Start"></button>
      </div>
      <div id="results">
        <span id="final_span" class="final"></span>
        <span id="interim_span" class="interim"></span>
        <p></p>
      </div>
  </div>

  <div id="botOutcome">

      <div id="news"></div>
      <p id="demo"></p>
      <p id="movieList"></p>
      <div id="googleMap"style="width:100%px;height:380px;"></div> <!--Map div-->
  </div>
</div>
<!--not using this. hidden-->
  <div style="display: none;" id="div_language">
    <select id="select_language" onchange="updateCountry()"></select>
    &nbsp;&nbsp;
    <select id="select_dialect"></select>
  </div>






<script>

      function directShow(){
        var str = document.getElementById("say");
        texttospeech(str.value);
      
      }
    </script>



<script>

var langs =
[['Afrikaans',       ['af-ZA']],
 ['Bahasa Indonesia',['id-ID']],
 ['Bahasa Melayu',   ['ms-MY']],
 ['Català',          ['ca-ES']],
 ['Čeština',         ['cs-CZ']],
 ['Deutsch',         ['de-DE']],
 ['English',         ['en-AU', 'Australia'],
                     ['en-CA', 'Canada'],
                     ['en-IN', 'India'],
                     ['en-NZ', 'New Zealand'],
                     ['en-ZA', 'South Africa'],
                     ['en-GB', 'United Kingdom'],
                     ['en-US', 'United States']],
 ['Español',         ['es-AR', 'Argentina'],
                     ['es-BO', 'Bolivia'],
                     ['es-CL', 'Chile'],
                     ['es-CO', 'Colombia'],
                     ['es-CR', 'Costa Rica'],
                     ['es-EC', 'Ecuador'],
                     ['es-SV', 'El Salvador'],
                     ['es-ES', 'España'],
                     ['es-US', 'Estados Unidos'],
                     ['es-GT', 'Guatemala'],
                     ['es-HN', 'Honduras'],
                     ['es-MX', 'México'],
                     ['es-NI', 'Nicaragua'],
                     ['es-PA', 'Panamá'],
                     ['es-PY', 'Paraguay'],
                     ['es-PE', 'Perú'],
                     ['es-PR', 'Puerto Rico'],
                     ['es-DO', 'República Dominicana'],
                     ['es-UY', 'Uruguay'],
                     ['es-VE', 'Venezuela']],
 ['Euskara',         ['eu-ES']],
 ['Français',        ['fr-FR']],
 ['Galego',          ['gl-ES']],
 ['Hrvatski',        ['hr_HR']],
 ['IsiZulu',         ['zu-ZA']],
 ['Íslenska',        ['is-IS']],
 ['Italiano',        ['it-IT', 'Italia'],
                     ['it-CH', 'Svizzera']],
 ['Magyar',          ['hu-HU']],
 ['Nederlands',      ['nl-NL']],
 ['Norsk bokmål',    ['nb-NO']],
 ['Polski',          ['pl-PL']],
 ['Português',       ['pt-BR', 'Brasil'],
                     ['pt-PT', 'Portugal']],
 ['Română',          ['ro-RO']],
 ['Slovenčina',      ['sk-SK']],
 ['Suomi',           ['fi-FI']],
 ['Svenska',         ['sv-SE']],
 ['Türkçe',          ['tr-TR']],
 ['български',       ['bg-BG']],
 ['Pусский',         ['ru-RU']],
 ['Српски',          ['sr-RS']],
 ['한국어',            ['ko-KR']],
 ['中文',             ['cmn-Hans-CN', '普通话 (中国大陆)'],
                     ['cmn-Hans-HK', '普通话 (香港)'],
                     ['cmn-Hant-TW', '中文 (台灣)'],
                     ['yue-Hant-HK', '粵語 (香港)']],
 ['日本語',           ['ja-JP']],
 ['Lingua latīna',   ['la']]];
for (var i = 0; i < langs.length; i++) {
  select_language.options[i] = new Option(langs[i][0], i);
}
select_language.selectedIndex = 6;
updateCountry();
select_dialect.selectedIndex = 6;
showInfo('info_start');
function updateCountry() {
  for (var i = select_dialect.options.length - 1; i >= 0; i--) {
    select_dialect.remove(i);
  }
  var list = langs[select_language.selectedIndex];
  for (var i = 1; i < list.length; i++) {
    select_dialect.options.add(new Option(list[i][1], list[i][0]));
  }
  select_dialect.style.visibility = list[1].length == 1 ? 'hidden' : 'visible';
}

var create_email = false;
var final_transcript = '';
var recognizing = false;
var ignore_onend;
var start_timestamp;
var speakingStatus;
rate=1;
volume=1;
  



if (!('webkitSpeechRecognition' in window)) {
  upgrade();
} else {
      
    start_button.style.display = 'inline-block';
    var recognition = new webkitSpeechRecognition();


    recognition.continuous = true;
    recognition.interimResults = true;
    
    recognition.onstart = function() {
      recognizing = true;
      showInfo('info_speak_now');
      start_img.src = 'mic-animate.gif';
    
    };
    recognition.onerror = function(event) {
      if (event.error == 'no-speech') {
        start_img.src = 'mic.gif';
        showInfo('info_no_speech');
        ignore_onend = true;
      }
      if (event.error == 'audio-capture') {
        start_img.src = 'mic.gif';
        showInfo('info_no_microphone');
        ignore_onend = true;
      }
      if (event.error == 'not-allowed') {
        if (event.timeStamp - start_timestamp < 100) {
          showInfo('info_blocked');
        } else {
          showInfo('info_denied');
        }
        ignore_onend = true;
      }
    };
    recognition.onend = function() {
      recognizing = false;
      if (ignore_onend) {
        return;
      }
      start_img.src = 'mic.gif';
      if (!final_transcript) {
        showInfo('info_start');
        return;
      }
      showInfo('');
      if (window.getSelection) {
        window.getSelection().removeAllRanges();
        var range = document.createRange();
        range.selectNode(document.getElementById('final_span'));
        window.getSelection().addRange(range);
      }
      if (create_email) {
        create_email = false;
        createEmail();
      }
    };
    recognition.onresult = function(event) {
      var interim_transcript = '';
      for (var i = event.resultIndex; i < event.results.length; ++i) {
        if (event.results[i].isFinal) {
          final_transcript = event.results[i][0].transcript;

      texttospeech(final_transcript);

      speakingStatus = "stopped";
      
      
        } else {
          interim_transcript = event.results[i][0].transcript;
        }
      }
      final_transcript = capitalize(final_transcript);
      final_span.innerHTML = linebreak(final_transcript);
      interim_span.innerHTML = linebreak(interim_transcript);

    };
}


function upgrade() {
  start_button.style.visibility = 'hidden';
  showInfo('info_upgrade');
}
  
var two_line = /\n\n/g;
var one_line = /\n/g;
function linebreak(s) {
  return s.replace(two_line, '<p></p>').replace(one_line, '<br>');
}
var first_char = /\S/;
function capitalize(s) {
  return s.replace(first_char, function(m) { return m.toUpperCase(); });
}
  


function startButton(event) {

  speakingStatus = "stopped";
  if (recognizing) {
    recognition.stop();
    return;
  }
  final_transcript = '';
  recognition.lang = select_dialect.value;
  recognition.start();
  ignore_onend = false;
  final_span.innerHTML = '';
  interim_span.innerHTML = '';
  start_img.src = 'mic-slash.gif';
  showInfo('info_allow');
  start_timestamp = event.timeStamp;
}
function showInfo(s) {
  if (s) {
    for (var child = info.firstChild; child; child = child.nextSibling) {
      if (child.style) {
        child.style.display = child.id == s ? 'inline' : 'none';
      }
    }
    info.style.visibility = 'visible';
  } else {
    info.style.visibility = 'hidden';
  }
}
var current_style;

//create var for 3 randomly selected movies
var movie1 = "<?php echo $movieCsv[$test1][1]; ?>";
var movie2 = "<?php echo $movieCsv[$test2][1]; ?>";
var movie3 = "<?php echo $movieCsv[$test3][1]; ?>";

function texttospeech(dialog)
{
    voices = window.speechSynthesis.getVoices();
    console.log('Get voices ' + voices.length.toString());
    for(var i = 0; i < voices.length; i++ ) {
       console.log("Voice " + i.toString() + ' ' + voices[i].name);
    
  }
     
     var showText ="Sorry, user. I don't understand";
     var sayText ="Sorry, user. I don't understand";
     document.getElementById("news").innerHTML="";
     
//Introduction  
//Default answer
      if(dialog=="hi"||dialog=="hello")
      {
        showText = sayText ="Hello, user.";
      }
      else if (dialog=="what does this system do" || dialog=="help")
      {  
        showText = sayText = "This is a chatbot that recommend you movies you will like in cinema"; 
      }
      else if (dialog=="ask me about movies")
      {
        var x = document.getElementById("movieList");
        x.innerHTML = "<strong> First Movie:  </strong> " +movie1+ "<br>" + 
        				"<strong> Second Movie:  </strong> " +movie2 + "<br>" + 
        				"<strong> Third Movie:  </strong> " +movie3;
        sayText = showText = "Tell us what you think of either movie."
      }     
      else if (dialog.indexOf("first movie")!=-1 ){ 
        $.ajaxSetup({async: false}); 
        showText="";
        sayText="";
        $.ajax({
          url: "./operator.php",                 
          data: {text1: dialog, index: 1},                 
          success: function (data) {
            nlp = data;
            if (nlp == "more information needed")
            {
              showText = sayText = "Please elaborate your thoughts on " +movie1;
            }
            else 
            {
              showText =  nlp;
              sayText = "This movie is recommended for you and this is the location of cinema closest to you";
              getMyLocation();
              google.maps.event.addDomListener(window, 'load', getMyLocation);
            }
          } 
        });  
      }

      else if (dialog.indexOf("second movie")!=-1 ){ 
        $.ajaxSetup({async: false}); 
        showText="";
        sayText="";
        $.ajax({
          url: "./operator.php",                 
          data: {text1: dialog, index: 2},                 
          success: function (data) {
            nlp = data;
            if (nlp == "more information needed")
            {
              showText = sayText = "Please elaborate your thoughts on " +movie2;
            }
            else 
            {
              showText = nlp;
              sayText = "This movie is recommended for you and this is the location of cinema closest to you";
              getMyLocation();
              google.maps.event.addDomListener(window, 'load', getMyLocation);
            }
          } 
        });  
      }
      else if (dialog.indexOf("third movie")!=-1 ){ 
        $.ajaxSetup({async: false}); 
        showText="";
        sayText="";
        $.ajax({
          url: "./operator.php",                 
          data: {text1: dialog, index: 3},                 
          success: function (data) {
          	nlp = data;
              if (nlp == "more information needed")
            {
              showText = sayText = "Please elaborate your thoughts on " +movie3;
            }
            else 
            {
              showText = nlp;
              sayText = "This movie is recommended for you and this is the location of cinema closest to you";
              getMyLocation();
              google.maps.event.addDomListener(window, 'load', getMyLocation);
            }
          } 
        });  
      }
      
 

      

//Give Answer with a speech
    //saysomething(sayText);
    showsomething(showText);
    
    //if the computer is responding, the system stops recognising speech.
    var u1 = new SpeechSynthesisUtterance(sayText);
        u1.lang = 'en-US';
        u1.pitch = 1;
        u1.rate = 1;
        u1.voice = voices[0];
        u1.voiceURI = 'native';
        u1.volume = 6;
    
    
    //if the computer is responding, the system stops recognising speech.
    u1.onstart = function(){

      recognition.stop();
      reset();
    }
    //if the computer finishes responding, the system restarts recognising speech.
    u1.onend = function(event) {
      recognition.start();
      recognizing = true;
     }
    
        speechSynthesis.speak(u1);
        console.log("Voice " + u1.voice.name);
    
     
}
</script>

<script>
//This is inicial functions
//Say some welcome speech and show this place
var x = document.getElementById("demo");

window.onload=function(){
  
//Welcome Speech
  var welcomeStr = "Hello user, welcome to use the chatbot system.";
  showAndsay(welcomeStr);
    
}
  
</script>

<script>

function showAndsay(str){
  showsomething(str);
  saysomething(str);
}



//This is help user to look what the system said, like a Console
function showsomething(str){
    document.getElementById("demo").innerHTML = str;
}

function saysomething(str) {
  var u = new SpeechSynthesisUtterance();
        u.text = str ;
        u.lang = 'en-AU';
        u.rate = window.rate;
        u.volume=window.volume;
        speechSynthesis.speak(u);
}



</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB1aHLIO964_MbuSGGsWROkZ93cO0htvS0&libraries=places"></script>
<script>
  // THIS IS FUNCTION TO GET LOCATION

  var infowindow;
  var map;
  function getMyLocation() {
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(showMyPosition);
    } else {
     x.innerHTML = "Geolocation is not supported by this browser.";
    }
  }

  function showMyPosition(position) {
    var y = document.getElementById("movieList");
    y.innerHTML = "This is cinemas around you";
    var Lat = position.coords.latitude;
    var Long = position.coords.longitude;
    //-42.882391,147.328591
    city = new google.maps.LatLng(Lat,Long);
    map = new google.maps.Map(document.getElementById('googleMap'), 
      {
        center: city,
        zoom: 15
      });

      var request= 
      {
        location: city,
        radius: 1000,
        types: ['movie_theater']
      };

      infowindow = new google.maps.InfoWindow();
      var service = new google.maps.places.PlacesService(map);
      service.nearbySearch(request, callback);
  }

  function callback(results, status) 
  {
    if (status== google.maps.places.PlacesServiceStatus.OK) 
    {
      for (var i= 0; i< results.length; i++) 
      {
        createMarker(results[i]);
      }
    }
  }

    function createMarker(place)
    {
      var placeLoc = place.geometry.location;
      var marker = new google.maps.Marker(
      {
        map: map,
        position: place.geometry.location
      });

      google.maps.event.addListener(marker, 'click', function()
      {
        infowindow.setContent(place.name);
        infowindow.open(map, this);
      });

    }


    console.log(movie1);
    console.log(movie2);
    console.log(movie3);
    console.log("possible commands");
    console.log("hello");
    console.log("ask me about movies");
    console.log("first movie / second movie / third movie");
</script>

</body>
