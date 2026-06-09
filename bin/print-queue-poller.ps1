#
# OpenTHC Print Queue Poller
#
# SPDX-License-Identifier: MIT
#
# @depends on SumatraPDF https://www.sumatrapdfreader.org/
#

# Run with:
# `powershell -ExecutionPolicy Bypass -File .\openthc-print-queue.ps1`

# Find Printers
# `Get-Printer`
# `Get-Printer | Select-Object Name, PrinterStatus`
# `Rename-Printer -Name "HP LaserJet P1102" -NewName "Office Printer"`

# Test With:
# `"C:\Program Files\SumatraPDF\SumatraPDF.exe" -print-to "Printer Name" printme.pdf`

param(
	[string]$WorkPath = "$env:TEMP",
	[string]$PrintDeviceName = "{{OPENTHC_PRINT_QUEUE_PRINTER_NAME}}",
	[string]$PrintQueueID = "{{OPENTHC_PRINT_QUEUE_API_KEY}}",
	[string]$PrintQueueURL = "{{OPENTHC_PRINT_QUEUE_URL}}",
	[switch]$Register,
	[string]$RegisterMode,
	[int]$SleepTime = 4
)

$Host.UI.RawUI.WindowTitle = "Print Queue v420.26.160| OpenTHC"

# $PSBoundParameters.GetEnumerator() |
# 	Sort-Object Key |
# 	Format-Table Key, Value

# Monitor for Sleep or Shutdown to Exit Script
$null = Register-WmiEvent `
	-Class Win32_PowerManagementEvent `
	-SourceIdentifier PowerEvents `
	-Action {

		$eventType = $Event.SourceEventArgs.NewEvent.EventType

		switch ($eventType) {

			4 {
				Write-Host "QUIT: System going to sleep or off"
				Stop-Process -Id $PID
			}
			default {
				Write-Host "Power event: $eventType"
			}
		}
	}


#
# Registers with the Task Scheduler
# Either on Startup or on LogOn -- LogOn is probably the best choice
#
function Register-Auto-Start {

	$scriptPath = $MyInvocation.MyCommand.Path
	$taskName = "OpenTHC Print Queue"

	# Should This Return?
	Get-ScheduledTask -TaskName $taskName -ErrorAction SilentlyContinue


	$action = New-ScheduledTaskAction `
		-Execute "powershell.exe" `
		-Argument "-ExecutionPolicy Bypass -File `"$scriptPath`""

	# Pick the Best One for You
	# $trigger = New-ScheduledTaskTrigger -AtStartup
	$trigger = New-ScheduledTaskTrigger -AtLogOn

	$principal = New-ScheduledTaskPrincipal `
		-UserId "SYSTEM" `
		-LogonType ServiceAccount `
		-RunLevel Highest

	Register-ScheduledTask `
		-TaskName $taskName `
		-Action $action `
		-Trigger $trigger `
		-Principal $principal `
		-Force

}

# UnRegister?
# Unregister-ScheduledTask `
#     -TaskName "OpenTHC Print Queue" `
#     -Confirm:$false

#
# Register via Batch
#
function Register-Auto-Start-Via-Batch {

	$startup = [Environment]::GetFolderPath("Startup")

	# $bat = @"
	# powershell.exe -ExecutionPolicy Bypass -File "$PSCommandPath"
	# "@

	# Set-Content `
	# 	-Path "$startup\RunMyScript.bat" `
	# 	-Value $bat

}

function Register-Auto-Start-Via-Registry {

	$script = $PSCommandPath

	Set-ItemProperty `
		-Path "HKCU:\Software\Microsoft\Windows\CurrentVersion\Run" `
		-Name "OpenTHC Print Queue" `
		-Value "powershell.exe -ExecutionPolicy Bypass -File `"$script`""
}


# if [ Register-Auto-Start ] or Register Command Line
# // then
# Register-Auto-Start
# }

#
# Find the Printer
#
function Find-Printer {

	param(
		[Parameter(Mandatory)]
		[string]$PrinterName
	)

	$printer = Get-Printer -Name $PrinterName -ErrorAction SilentlyContinue

	if (-not $printer) {
		throw "Printer '$PrinterName' not found."
	}

	if ($printer.PrinterStatus -ne 'Normal') {
		throw "Printer '$PrinterName' status is '$($printer.PrinterStatus)'."
	}

	if ($printer.WorkOffline) {
		Write-Warning "Printer '$PrinterName' is configured to work offline."
	}

	return $printer

}

#
# Find the Executable
#
function Find-Sumatra {

	[CmdletBinding()]
	param()

	# In the Path?
	$cmd = Get-Command "SumatraPDF.exe" -ErrorAction SilentlyContinue
	if ($cmd) {
		return $cmd.Source
	}

	# Search
	$path_list = @(
		"$env:ProgramFiles\SumatraPDF\SumatraPDF.exe",
		"$env:ProgramFiles(x86)\SumatraPDF\SumatraPDF.exe",
		"$env:LOCALAPPDATA\SumatraPDF\SumatraPDF.exe",
		"$env:APPDATA\SumatraPDF\SumatraPDF.exe",
		"$PSScriptRoot\SumatraPDF.exe"
	)

	foreach ($path in $path_list) {

		# Write-Host "Checking: $path"

		if (Test-Path $path) {
			Write-Host "Found: $path"
			return (Resolve-Path $path).Path
		}
	}

	return $null

}

#
# Main
#
function Main {

	$SumatraPath = Find-Sumatra
	# try {
		$PrintDevice = Find-Printer $PrintDeviceName
	    # Write-Host "Ready to print to $($printer.Name)"
	# }
	# catch {
	    # Write-Error $_
	# }
	#

	$job_file = "$WorkPath\openthc-print-job-$PID.pdf"
	$job_hash_file = "$WorkPath\openthc-print-job-hash-$PID.txt"

	$req_head = @{
		Authorization = "Bearer v2018/print-queue/$PrintQueueID"
	}

	while ($true) {
		try {

			Invoke-WebRequest -Uri $PrintQueueURL -Headers $req_head -OutFile $DownloadPath -UseBasicParsing

			$oldHash = ""
			if (Test-Path $LastHashFile) {
				$oldHash = Get-Content $LastHashFile
			}

			$newHash = (Get-FileHash $DownloadPath).Hash

			if ($newHash -ne $oldHash) {
				Write-Host "New document detected. Printing..."

				& $SumatraPath -print-to "$PrintDeviceName" -silent $DownloadPath

				$newHash | Out-File $LastHashFile

			} else {
				Write-Host "No change."
			}
		} catch {
			Write-Host "Error: $_"
		}

		Start-Sleep -Seconds $SleepTime

	}

}

Main
