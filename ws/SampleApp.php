<?php
	$qs = $_SERVER['QUERY_STRING'];
	$url = dirname('http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) . '/openbook-ws.php?' . $qs;

    require_once("SimpleRestClient.php");
    $xml=null;
    $restclient=null;
    $result=null;

    $cert_file=null;//Path to cert file
    $key_file=null;//Path to private key
    $key_password=null;//Private key passphrase
    $curl_opts=null;//Array to set additional CURL options or override the default options of the SimpleRestClient
    $post_data=null;//Array or string to set POST data
    $user_agent = "OpenBook Sample Rest Client";

    $restclient = new SimpleRestClient($cert_file, $key_file, $key_password, $user_agent, $curl_opts);

    if (!is_null($post_data))
    {
      $restclient->postWebRequest($url, $post_data);
    }
    else
    {
      $restclient->getWebRequest($url);
    }
?>

 <html>
 <head>
    <title>OpenBook Web Service (alpha) - Sample Application</title>
 </head>

<body>


<h2>OpenBook Web Service (alpha) - Sample Application</h2>

 <form action="#" method="GET">
 	<div><input type="text" name="booknumber" id="booknumber" value="" /> ISBN (10 or 13 digits, no symbols, e.g., 1896951422). You can also prefix with OLID (e.g., OLID:OL1397864M) or LCCN (e.g., LCCN:93005405)</div>
	<input type="submit" />
 </form>


    <span><b>Requested Url: </b><div><textarea rows="1" cols="150"><?php echo $url; ?></textarea> </span><br /></div>
    <br />

    <span><b>Status Code: </b></span>
    <span id="status_code">
        <?php
            if (!is_null($restclient))
            {
                //Get the Http_Status_Code
                echo 'Http Status Code: ' . $restclient->getStatusCode() . '<br />';
                $response = $restclient->getWebResponse();
                //Get the error message returned from web service
                $xml = simplexml_load_string($response);

                if (!is_null($xml))
                {
                    $result = $xml->xpath('//div[@class="status_description"]');
                    if (!is_null($result) && !empty($result))
                    {
                        echo 'Web Service Error Message: ' . $result[0] . '<br />';;
                    }
                }
            }
        ?>
    </span>
    <br />

    <span><b>Response: </b></span>
    <span>Copy/paste this HTML into any webpage to display the previewed book below.</span>
    <div id="response" >
        <textarea id="response_output" rows="10" cols="150" onclick="this.focus();this.select();"><?php
                if (!is_null($restclient))
                {
                  echo $restclient->getWebResponse();
                }
            ?></textarea>
    </div>

    <br />
    <span><b>Preview: </b></span>
    <div id="content">
        <?php
            if (!is_null($xml) && $restclient->getStatusCode() == 200 && is_null($post_data))
            {
            	$result = $xml->xpath('//openbook');
                echo $result[0];
            }
        ?>
    </div>
 </body>
 </html>