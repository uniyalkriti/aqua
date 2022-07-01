<?php
error_reporting(0);
require_once('./widgets/html2fpdf/html2fpdf.php');
$pdf = new HTML2FPDF();
$pdf->SetMargins(5,0,0);
$pdf->AddPage();
//$pdf->DisplayPreferences('HideWindowUI');
$pdfbody = '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<link rel="stylesheet" href="../admin/css/print.css" type="text/css" />
</head>

<body>';
if(isset($_POST['printcontent']))
	$pdfbody .= $_POST['printcontent'];
$pdfbody .= '
</body>
</html>';
$pdf->WriteHTML($pdfbody);
$file = $pdf->Output('prioritypass.pdf','S');
header("Content-Type: application/pdf");
header("Cache-Control: no-cache");
header("Accept-Ranges: none");
header("Content-Disposition: inline; filename=\"abc.pdf\"");
echo $file;
//echo $pdfbody;
?>