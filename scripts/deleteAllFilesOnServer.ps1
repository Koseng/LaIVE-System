param (
    $remotePath = "/websites/html/"
)

try
{
    # Load WinSCP .NET assembly
    Add-Type -Path "WinSCPnet.dll"
 
    # Setup session options
    $sessionOptions = New-Object WinSCP.SessionOptions -Property @{
        Protocol = [WinSCP.Protocol]::Sftp
        HostName = "myServer.de"
        PortNumber = 22
        UserName = "myUser"
        Password = "myPassword"
        SshHostKeyFingerprint = "ssh-ed25519 256 xxxxxxxxxxxxxxxxMyKexxxxxxxxxxxxxxxxxxxxxxx"
    }
 
    $session = New-Object WinSCP.Session

    # Delete all files on remote server
    $session.Open($sessionOptions)
    Write-Host "Delete all files on remote server"
    $session.RemoveFiles($remotePath + "*.*")
    $session.Close() 
}
catch
{
    Write-Host "Error: $($_.Exception.Message)"
}
finally
{
    # Disconnect, clean up
    $session.Dispose()
    exit 1
}