<?php
    // Check user info from CAS
    $uname = escapeshellcmd($_SERVER['HTTP_CAS_EDUPERSONPRINCIPALNAME']);
    $auth_type = escapeshellcmd($_SERVER['HTTP_CAS_AUTHENTICATIONMETHOD']);
    //$auth_time = $_SERVER['HTTP_CAS_AUTHENTICATIONINSTANT'];
    if($auth_type != 'authn/MFA') {
        exit("E1: THERE WAS A PROBLEM PROCESSING YOUR REQUEST");
    }

    // Generate key for user
    $keyName = $uname . "_rsa";
    shell_exec("ssh-keygen -q -t rsa -N '' -C '' -f " . dirname(__FILE__) . "/../data/" . $keyName);
    shell_exec("chmod 664 " . dirname(__FILE__) . "/../data/".$keyName.".pub");

    // Move public key to public location
    shell_exec("mv " . dirname(__FILE__) . "/../data/" . $keyName.".pub users/".$keyName.".pub");

    // Sign public key
    shell_exec("ssh-keygen -N '' -s /opt/freezer/user_ca -I " . $uname . 
        " -n " . $uname .
        " -V +6h " .  dirname(__FILE__) . "/users/". $keyName.".pub");

    // Give private key to user
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Cache-Control: public"); // needed for internet explorer
    header("Content-Type: text/plain");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Length:".filesize(dirname(__FILE__) . "/../data/".$keyName));
    header("Content-Disposition: attachment; filename=".$keyName);
    readfile(dirname(__FILE__) . "/../data/".$keyName);

    // Remove private key
    shell_exec("rm " . dirname(__FILE__) . "/../data/" . $keyName);
?>
