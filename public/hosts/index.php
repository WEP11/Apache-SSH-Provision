<?php
    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Cache-Control: public"); // needed for internet explorer
    header("Content-Type: text/plain");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Length:".filesize("host_ca.pub"));
    header("Content-Disposition: attachment; filename=host_ca.pub");
    readfile("host_ca.pub");
?>