Remove-Item "$HOME\Downloads\$([Environment]::UserName)_rsa"
echo "AUTH CONNECT"
echo "Waiting for Private Key..."
Start-Process 'https://AUTH-URL/' -Wait
Write-Host "[" -NoNewline
while(!([System.IO.File]::Exists("$HOME\Downloads\$([Environment]::UserName)_rsa")))
{
    Start-Sleep -Seconds 1
    Write-Host "." -NoNewline
}
Write-Host "]"

Set-Location $HOME\.ssh
Remove-Item "$([Environment]::UserName)_rsa"
Remove-Item "$([Environment]::UserName)_rsa-cert.pub"
Invoke-WebRequest "AUTH-URL/users/$([Environment]::UserName)_rsa-cert.pub" -OutFile "$([Environment]::UserName)_rsa-cert.pub"
Move-Item -Path "$HOME\Downloads\$([Environment]::UserName)_rsa" -Destination "$HOME\.ssh\$([Environment]::UserName)_rsa"

echo "Ready to access resources!"
Write-Host -NoNewLine 'Press any key to continue...';
$null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown');