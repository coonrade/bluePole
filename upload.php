<?php

    /*

        POST comes in,
        JSON goes out!
        
    */

    // default to error
    $reply = array();
    $reply['status'] = array(

        'code'  => '-1',
        'value' => 'Generic Error'
    );


    if(isset($_POST['submit'])) {

    	//TODO: global config
    	$to = 'akuznetsov3@ucmerced.edu';
    	$subject = 'a file for you';

    	$message = strip_tags($_POST['message']);

        //ASSUMES 1 file
    	$attachment = chunk_split(base64_encode(file_get_contents($_FILES['file']['tmp_name'])));
    	$filename = $_FILES['file']['name'];


    	$boundary =md5(date('r', time())); 

    	$headers = "From: webmaster@example.com\r\nReply-To: webmaster@example.com";
    	$headers .= "\r\nMIME-Version: 1.0\r\nContent-Type: multipart/mixed; boundary=\"_1_$boundary\"";

    	$message="This is a multi-part message in MIME format.

--_1_$boundary
Content-Type: multipart/alternative; boundary=\"_2_$boundary\"

--_2_$boundary
Content-Type: text/plain; charset=\"iso-8859-1\"
Content-Transfer-Encoding: 7bit

$message

--_2_$boundary--
--_1_$boundary
Content-Type: application/octet-stream; name=\"$filename\" 
Content-Transfer-Encoding: base64 
Content-Disposition: attachment 

$attachment
--_1_$boundary--";



        // built in mail is pretty basic
        // TODO: eventually switch over to something like swiftmailer 
        // to do the dirty work :).
        $result = mail($to, $subject, $message, $headers);

        if($result === true){

            // things went OK, so we deal with it

            $reply['status'] = array(

                'code'  => '0',
                'value' => 'Email sent OK!'
            );

        } else {
            // i dont know of any API to get the mail error, so we can only say there was a problem
            // not that we know what the problem was.  Oh well.

            $reply['status'] = array(

                'code'  => '1',
                'value' => 'Email NOT sent OK!'
            );
        }

    }
	

    // now we tell our story!
    $encoded = json_encode($reply);
    header('Content-type: application/json');
    exit($encoded);

?>