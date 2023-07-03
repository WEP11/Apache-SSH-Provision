<?php
    try {
        // Undefined | Multiple Files | $_FILES Corruption Attack
        // If this request falls under any of them, treat it invalid.
        if (
            !isset($_FILES['keyfile']['error']) ||
            is_array($_FILES['keyfile']['error'])
        ) {
            throw new RuntimeException('Invalid parameters.');
        }
    
        // Check $_FILES['keyfile']['error'] value.
        switch ($_FILES['keyfile']['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                throw new RuntimeException('No file sent.');
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                throw new RuntimeException('Exceeded filesize limit.');
            default:
                throw new RuntimeException('Unknown errors.');
        }
    
        // You should also check filesize here. 
        if ($_FILES['keyfile']['size'] > 1000000) {
            throw new RuntimeException('Exceeded filesize limit.');
        }
    
        // DO NOT TRUST $_FILES['keyfile']['mime'] VALUE !!
        // Check MIME Type by yourself.
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        if (false === $ext = array_search(
            $finfo->file($_FILES['keyfile']['tmp_name']),
            array(
                'pub' => 'text/plain',
            ),
            true
        )) {
            throw new RuntimeException('Invalid file format.');
        }
    
        // You should name it uniquely.
        // DO NOT USE $_FILES['keyfile']['name'] WITHOUT ANY VALIDATION !!
        // On this example, obtain safe unique name from its binary data.
        $keyFile = sprintf(dirname(__FILE__) . '/../../data/%s.%s',
            sha1_file($_FILES['keyfile']['tmp_name']),
            $ext
        );
        if (!move_uploaded_file(
            $_FILES['keyfile']['tmp_name'],
            $keyFile
            )
        ) {
            throw new RuntimeException('Failed to move uploaded file.');
        }
    
    } catch (RuntimeException $e) {
    
        error_log($e->getMessage());
    
    }

    // Process uploaded keyfile
    exec('chmod 664 ' . $keyFile);

    // Get & verify hostname
    $hostName = escapeshellarg($_POST["host"]);
    exec("nslookup " . $hostName, $nsoutput, $retval);
    if ($retval > 0 || array_search('SERVFAIL', $nsoutput)) {
        error_log($hostName . " cannot be verified by DNS");
        shell_exec("rm ".$keyFile);
        exit();
    }

    // Sign keyfile
    shell_exec("ssh-keygen -h -s /opt/freezer/host_ca -I " . $hostName . 
        " -n " . $hostName .
        " -V +52w " . $keyFile);

    // Give signed certificate to host
    $certFile = pathinfo($keyFile, PATHINFO_FILENAME) . '-cert.pub';
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Cache-Control: public"); // needed for internet explorer
    header("Content-Type: text/plain");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Length:".filesize(dirname(__FILE__) . "/../../data/".$certFile));
    header("Content-Disposition: attachment; filename=".$certFile);
    readfile(dirname(__FILE__) . "/../../data/".$certFile);
    
    shell_exec("rm ".$keyFile);
    shell_exec("rm ".dirname(__FILE__) . "/../../data/".$certFile);
?>