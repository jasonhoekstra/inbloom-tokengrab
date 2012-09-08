<?php
session_start();

        const CLIENT_ID = '';
        const CLIENT_SECRET = '';

        const REDIRECT_URI = 'http://localhost/tokengrab/';
        const AUTHORIZATION_ENDPOINT = 'https://api.sandbox.slcedu.org/api/oauth/authorize';
        const TOKEN_ENDPOINT = 'https://api.sandbox.slcedu.org/api/oauth/token';


// If the session verification code is not set, redirect to the SLC Sandbox authorization endpoint
if (!isset($_GET['code'])) {
  $url = 'https://api.sandbox.slcedu.org/api/oauth/authorize?client_id=' . CLIENT_ID . '&redirect_uri=' . REDIRECT_URI;
  header('Location: ' . $url);
  die('Redirect');
}
else {

  $url = 'https://api.sandbox.slcedu.org/api/oauth/token?client_id=' . CLIENT_ID . '&client_secret=' . CLIENT_SECRET . '&grant_type=authorization_code&redirect_uri=' . REDIRECT_URI . '&code=' . $_GET['code'];

  $ch = curl_init();

//set the url, number of POST vars, POST data
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  curl_setopt($ch, CURLOPT_HEADER, 'Content-Type: application/vnd.slc+json');
  curl_setopt($ch, CURLOPT_HEADER, 'Accept: application/vnd.slc+json');

//execute post
  $result = curl_exec($ch);

//close connection
  curl_close($ch);

// de-serialize the result into an object
  $result = json_decode($result);

  if ($result == '') {
    header('Location: .');
  }


// set the session with the access_token and verification code
  $_SESSION['access_token'] = $result->access_token;
  $_SESSION['code'] = $_GET['code'];

  if ($result->error != '') {
    echo 'Error: ' . $result->error . '<br/><br/>';
    echo $result->error_description;
  }
  else {
    echo '<html><head></head><body>';
    echo '<h3>Your access token:</h3><h3 style="color: blue">' . $result->access_token . '</h3>';
    echo '<h3>REST Headers:</h3><form method="post" action=""><textarea name="comments" cols="80" rows="5">';
    echo 'Content-Type: application/vnd.slc+json' . "\n";
    echo 'Accept: application/vnd.slc+json' . "\n";
    echo 'Authorization: bearer ' . $result->access_token;
    echo '</textarea></form>';
    echo '<h3>Starting API endpoints:</h3>';
    echo '<ul>';
    echo '<li><a href="http://dev.slcedu.org/docs/adding-your-application-sli/adding-user-authentication-applications/checking-session" target="_new">https://api.sandbox.slcedu.org/api/rest/system/session/check</a></li>';
    echo '<li><a href="http://dev.slcedu.org/docs/sli-rest-api-resources/resource-uris/home" target="_new">https://api.sandbox.slcedu.org/api/rest/home</a></li>';
    echo '<li><a href="http://dev.slcedu.org/docs/sli-rest-api-resources/resource-uris/sections" target="_new">https://api.sandbox.slcedu.org/api/rest/sections</a></li>';
    echo '<li><a href="http://dev.slcedu.org/docs/sli-rest-api-resources/resource-uris/students" target="_new">https://api.sandbox.slcedu.org/api/rest/students</a></li>';
    echo '<li><a href="http://dev.slcedu.org/docs/sli-rest-api-resources/resource-uris/grades" target="_new">https://api.sandbox.slcedu.org/api/rest/grades</a></li>';
    echo '</ul>';
    echo '</body></html>';
  }
}
?>





