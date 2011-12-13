function ajaxSubmit(ajaxInput, ajaxOutput, waitMessage) {

var ajaxDisplay = document.getElementById(ajaxOutput);
ajaxDisplay.innerHTML = waitMessage;


var ajaxRequest;  // The variable that makes Ajax possible!

try{
 // Opera 8.0+, Firefox, Safari
 ajaxRequest = new XMLHttpRequest();
} catch (e){
 // Internet Explorer Browsers
 try{
  ajaxRequest = new ActiveXObject("Msxml2.XMLHTTP");
 } catch (e) {
  try{
   ajaxRequest = new ActiveXObject("Microsoft.XMLHTTP");
  } catch (e){
   // Something went wrong
   alert("Error - unable to initialize XML-HTTP. Please try again using Firefox or IE.");
   return false;
  }
 }
}

// Create a function that will receive data sent from the server
ajaxRequest.onreadystatechange = function(){
 if(ajaxRequest.readyState == 4){
  var ajaxDisplay = document.getElementById(ajaxOutput);
  ajaxDisplay.innerHTML = ajaxRequest.responseText;
 }
}

ajaxRequest.open("GET", ajaxInput, true);
ajaxRequest.send(null); 
}

