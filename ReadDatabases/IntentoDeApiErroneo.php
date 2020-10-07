

<?php

#apt install php-ssh2

$serverIp = '192.168.0.15';
$serverPort = 22;
$serverUsername = 'User';
$publicKeyFile = '/home/agustin/.ssh/AgustinRSA.pub';
$privateKeyFile = '/home/agustin/.ssh/AgustinRSA';

#C:\trabajo\visual\OVEJA
$remoteFileName = 'C:\Clip.txt';
$localFileName = '/home/agustin/Desktop/test.txt';

$conn = ssh2_connect($serverIp, $serverPort);

if (ssh2_auth_pubkey_file($conn, $serverUsername, $publicKeyFile, $privateKeyFile)){
    echo "Connected";
    $sftp = @ssh2_sftp($conn);
    $stream = @fopen("ssh2.sftp://$sftp$remoteFileName", 'r');
    if (! $stream)
        echo 'No pudo abrir el file';
    $contents = fread($stream, filesize("ssh2.sftp://$sftp$remoteFileName"));
    file_put_contents ($localFileName, $contents);
    @fclose($stream);
    #$received = ssh2_scp_recv($conn, $remoteFileName, $localFileName);
    #$received = ssh2_scp_send($conn, $localFileName, $remoteFileName, 0644);
 
} else{
    echo 'Not connected bro';
}



?>



