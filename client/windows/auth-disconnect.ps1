Set-Location $HOME\.ssh
Clear-Content "$([Environment]::UserName)_rsa"
Clear-Content "$([Environment]::UserName)_rsa-cert.pub"