param (
    $transferPath = "C:\COSAWIN\transfer\",
    $backupUploadedPath = "C:\COSAWIN\transfer\Uploaded\",
    $remotePath = "/websites/html/",
    $waitTime = 60
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

    # Create backup folder if not yet existent
    if (!(Test-Path $backupUploadedPath -PathType Container)) 
    {
        New-Item -ItemType Directory -Force -Path $backupUploadedPath
    }

    while($true)
    {
        try
        {   
            # Connect
            Write-Host "------------------------------------------------------------------------------"
            Write-Host (get-date).ToString('G')
            Write-Host "Check Files"
            $fileCount = ( Get-ChildItem -File -Path $transferPath | Measure-Object ).Count;
            Write-Host "$($fileCount) files to upload"

            if ($fileCount -gt 0)
            {
                Write-Host "Open Connection."
                $session.Open($sessionOptions)

                # No subdirectories
                $transferOptions = New-Object WinSCP.TransferOptions
                $transferOptions.FileMask = "|*/";

                # Upload files, collect results
                $transferResult = $session.PutFiles(($transferPath + "*"), $remotePath, $False, $transferOptions);

                # Iterate over every transfer
                foreach ($transfer in $transferResult.Transfers)
                {
                    # Success or error?
                    if ($transfer.Error -eq $Null)
                    {
                        $fileName = "{0,-28}" -f (Split-Path $transfer.FileName -leaf)
                        Write-Host "Upload of   || $($fileName) || succeeded."

                        # Upload succeeded, move source file to backup
                        Move-Item $transfer.FileName $backupUploadedPath -Force 
                    }
                    else
                    {
                        Write-Host "Upload of $($transfer.FileName) failed: $($transfer.Error.Message)"
                    }
                }
            }
        }
        catch 
        { 
            Write-Host "Error: $($_.Exception.Message)" 
        }
        finally 
        { 
            if ($session.Opened) 
            { 
                Write-Host "Close Connection."
                $session.Close() 
            }

            Write-Host "Waiting."
            For ($i=$waitTime; $i -gt 0; $i–-) 
            {  
                $percent = (100/$waitTime) * ($waitTime - $i)
                Write-Progress -Activity "Next Upload Check" -Status "Waiting Time" -SecondsRemaining $i -PercentComplete $percent
                Start-Sleep 1
            }
        }
    } # end while($true)    
}
catch
{
    Write-Host "Error: $($_.Exception.Message)"
}
finally
{
    # Disconnect, clean up
    $session.Dispose()
    exit 0
}
