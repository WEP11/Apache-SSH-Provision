# Overview
This is an idea I had for an SSH key provisioner using Apache for authentication (In my example mod-auth-cas for CAS authentication) and OpenSSH for generating keys and signing public keys. This provides a sort of independent way of providing two factor authentication for SSH based services. I don't know if this is suitable for serious use, but I welcome any ideas/feedback.

# How it works
We create a host and user CA, which is stored in a 700 directory for a signing user only. Users then login to the location and are issued a signed certificate and matching key they can use for a limited time period. The generated public certificates are publicly available on the server for any services that would like to use the keys, no private keys are stored other than the CA's, which are stored under the signing user.

Host keys are signed by sending a public key to hosts/onboard.php . A signed certificate is returned from that URL if a valid hostname is included as a comment in the key file. The CA certificate is available for users at the hosts/ location.

# Requirements
- PHP
- ssh-keygen

# Installation
- Clone repo
- Generate CA keys
- Store the CA private keys in a 700 directory for www-data
- Store the CA public keys in public/users/ and public/hosts/ respectively
- Link the public folder into your SSL protected Apache host. Protect the root location using mod-auth-cas, and open the users and hosts directories to appropriate addresses.

When you access the location, you should be required to login, and if successful you will get a private key for your user.

# Client Installation

All users will want to configure ssh to automatically use their <username>_rsa keys for the appropriate hosts. These scripts will automatically update the keys and certificates in the .ssh folder after executing and logging in. Users will need to add an @cert-authority entry for the host CA in their known_hosts file.

## Windows
Users need to download the auth-connect and auth-disconnect PowerShell scripts, saving them to a scripts folder. Simply run auth-connect, which opens the browser to the site for login. After logging in, the private key is downloaded by the browser. The script will move that into the .ssh folder and download the certificate.

To setup the auth-disconnect script you will need to open the Group Policy Editor:
1. Open User Configuration > Windows Settings > Scripts (Logon/Logoff)
2. Click Logoff
3. Add the auth-disconnect script to the PowerShell scripts tab
This script will clear the private key/certificate after you logoff.