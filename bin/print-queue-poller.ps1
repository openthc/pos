#
# OpenTHC Print Queue Poller
# @deprends on SumatraPDF https://www.sumatrapdfreader.org/

# Run with:
# `powershell -ExecutionPolicy Bypass -File .\openthc-print-queue-poller.ps1`

# Test With:
# "C:\Program Files\SumatraPDF\SumatraPDF.exe" -print-to "Your Printer Name" test.pdf

$queue_url = "{{OPENTHC_PRINT_QUEUE_URL}}"
$DownloadPath = "$env:TEMP\openthc-print-job.pdf"
$LastHashFile = "$env:TEMP\openthc-print-job-hash.txt"
$PrinterName = "{{OPENTHC_PRINT_QUEUE_PRINTER_NAME}}"
# $SumatraPath = "C:\Program Files\SumatraPDF\SumatraPDF.exe"
$SumatraPath = "C:\Users\root\AppData\Local\SumatraPDF\SumatraPDF.exe"

# Get-Command SumatraPDF.exe -ErrorAction SilentlyContinue
# C:\Program Files\SumatraPDF\
# C:\Program Files (x86)\SumatraPDF\
# %LOCALAPPDATA%\SumatraPDF\

$queue_req_head = @{
	Authorization = "Bearer v2018/print-queue/{{OPENTHC_PRINT_QUEUE_API_KEY}}"
}
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

			# & $SumatraPath -print-to "$PrinterName" -silent $DownloadPath

			$newHash | Out-File $LastHashFile
		} else {
			Write-Host "No change."
		}
	} catch {
		Write-Host "Error: $_"
	}

	Start-Sleep -Seconds 4

}
