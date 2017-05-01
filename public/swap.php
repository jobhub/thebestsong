<?php

/**

  @author Iain Mullan (@iainmullan)
  @date 14/05/2014

  This is an example token swap service written in PHP. This is required by
  the Spotify iOS SDK to authenticate a user.

  To run the service, enter your client ID, client
  secret and client callback URL below and place the file on a web server.

  Pass the full URL of this script (eg. http://localhost/swap.php) to the
  token swap method in the iOS SDK:

  NSURL *swapServiceURL = [NSURL urlWithString:@"http://localhost/swap.php"];

  -[SPAuth handleAuthCallbackWithTriggeredAuthURL:url
  tokenSwapServiceEndpointAtURL:swapServiceURL
  callback:callback];

  Note: For the beta 1 release of the iOS SDK Spotify provides the
  below beta values you can use in your Token Exchange Service code; later,
  these values will be invalidated and will need to be replaced by your
  own unique values.

 */
define('k_client_id', "a48fcd611b2b474e98e4d82584dc80f6");
define('k_client_secret', "37e65385e8e840d7a8d6e92c0a9536f7");
define('k_client_callback_url', "thebestsong-spotify-login://callback");

if (isset($_POST['code'])) {

    mail('ghiosip@gmail.com', 'Test spotify', print_r($_POST, true));

    $auth_code = $_POST['code'];

    $params = array(
        "grant_type" => "authorization_code",
        "client_id" => k_client_id,
        "client_secret" => k_client_secret,
        "redirect_uri" => k_client_callback_url,
        "code" => $auth_code
    );

    $ch = curl_init("https://ws.spotify.com/oauth/token");

    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch);

    curl_close($ch);

    mail('ghiosip@gmail.com', 'Test spotify response', $response);

    echo $response;
}
