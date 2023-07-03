#/bin/bash
# Probably shouldn't run this, just use it as a reference
ssh-keygen -t rsa -b 4096 -f user_ca -C user_ca
ssh-keygen -t rsa -b 4096 -f host_ca -C host_ca

# This is a root/key manager only location
mv user_ca /opt/freezer/
mv host_ca /opt/freezer/