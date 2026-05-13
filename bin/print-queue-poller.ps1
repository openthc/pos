#
# OpenTHC Print Queue Poller
#
# SPDX-License-Identifier: GPL-3.0-only
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

$PrinterName = "{{OPENTHC_PRINT_QUEUE_PRINTER_NAME}}"

$queue_url = "{{OPENTHC_PRINT_QUEUE_URL}}"
$queue_req_auth = @{
	Authorization = "Bearer v2018/print-queue/{{OPENTHC_PRINT_QUEUE_API_KEY}}"
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

		Write-Host "Checking: $path"

		if (Test-Path $path) {
			return (Resolve-Path $path).Path
		}
	}

	return $null

}
$SumatraPath = Find-Sumatra

$DownloadPath = "$env:TEMP\openthc-print-job.pdf"
$LastHashFile = "$env:TEMP\openthc-print-job-hash.txt"


while ($true) {
	try {

		Invoke-WebRequest -Uri $queue_url -Headers $queue_req_head -OutFile $DownloadPath -UseBasicParsing

		$newHash = (Get-FileHash $DownloadPath).Hash

		if (Test-Path $LastHashFile) {
			$oldHash = Get-Content $LastHashFile
		} else {
			$oldHash = ""
		}

		if ($newHash -ne $oldHash) {
			Write-Host "New document detected. Printing..."

			& $SumatraPath -print-to "$PrinterName" -silent $DownloadPath

			$newHash | Out-File $LastHashFile

		} else {
			Write-Host "No change."
		}
	} catch {
		Write-Host "Error: $_"
	}

	Start-Sleep -Seconds 4

}
