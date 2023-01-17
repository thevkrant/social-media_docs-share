<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once('../library/tcpdf/tcpdf.php');
include('../config/config.php');
// Extend the TCPDF class to create custom Header and Footer

// create new PDF document

class MYPDF extends TCPDF
{
	public $_w = 840;
	public $_h = 595;

	//Page header
	public function Header()
	{
		// get the current page break margin
		$bMargin = $this->getBreakMargin();
		// get current auto-page-break mode
		$auto_page_break = $this->AutoPageBreak;
		// disable auto-page-break
		$this->SetAutoPageBreak(false, 0);
		// set bacground image
		//$img_file = K_PATH_IMAGES.'cert_bg_2.jpg';

		$img_file = K_PATH_IMAGES . 'cdpsap-new.jpg';

		$this->Image($img_file, 0, 0,  $this->_w, $this->_h, '', '', '', false, 300, '', false, false, 0);
		// restore auto-page-break status
		$this->SetAutoPageBreak($auto_page_break, $bMargin);
		// set the starting point for the page content
		$this->setPageMark();
	}
}

if (isset($_SERVER['HTTPS'])) {
	$http = 'https://';
} else {
	$http = 'http://';
}
$dirmName = dirname($_SERVER['PHP_SELF']);
$dirmName = explode('/', $dirmName);
if ($dirmName[1] != "") {
	$dirmName = '/' . $dirmName[1];
} else {
	$dirmName = '';
}

$globalLink = $http . $_SERVER['HTTP_HOST'];
$con = createConnection();
$cdate = Date("d-m-Y");

if (isset($_REQUEST['userRowId']) && $_REQUEST['userRowId'] != "") {

	$userRowId = base64_decode($_REQUEST['userRowId']);
	$courseName = 'CDP Course';

	$resultList2 = mysqli_query($con, "select first_name,last_name from user where user_id= $userRowId");
	$num2 = mysqli_num_rows($resultList2);
	if ($num2) {
		while ($row2 = mysqli_fetch_assoc($resultList2)) {
			$first_name = $row2['first_name'];
			$last_name = $row2['last_name'];
		}
	}

	if (empty($first_name)) {
		echo "Records not found.";
		exit;
	} else {

		////////////////////// Cert gen ///////////////

		$query = "select * from tbl_certificates where user_id=" . $userRowId . "";
		$certExists = mysqli_query($con, $query);
		$numExists = mysqli_num_rows($certExists);
		if ($numExists == 0) {

			$fileName = $userRowId . "-" . md5($first_name) . ".pdf";
			$cert_path = "https://" . $_SERVER['HTTP_HOST'] . "/user/certs/" . $fileName;

			$query = "INSERT INTO tbl_certificates (user_id, level_name,cert_path,created_date,modified_date) VALUES ($userRowId,'$courseName','$cert_path',NOW(),NOW())";
			//file_put_contents("cert1.txt",$query);
			mysqli_query($con, $query);
			$cert_path = getUserCertificateOnCourse($userRowId, $first_name, $last_name, $courseName, $cdate);
		} else {

			$queryDel = "delete from tbl_certificates where user_id=" . $userRowId . "";
			mysqli_query($con, $queryDel);

			$fileName = $userRowId . "-" . md5($first_name) . ".pdf";
			$cert_path = "https://" . $_SERVER['HTTP_HOST'] . "/user/certs/" . $fileName;
			$query = "INSERT INTO tbl_certificates (user_id, level_name,cert_path,created_date,modified_date) VALUES ($userRowId,'$courseName','$cert_path',NOW(),NOW())";
			//file_put_contents("cert2.txt",$query);
			mysqli_query($con, $query);
			$cert_path = getUserCertificateOnCourse($userRowId, $first_name, $last_name, $courseName, $cdate);
		}

		////header('Location: '.$cert_path);
		exit;

		///////////////////////////////////////////
	}
} else {
	echo "No arguments passed.";
	exit;
}

//getUserCertificateOnCourse('','','','','','');
function getUserCertificateOnCourse($userid, $firstname, $lastname, $level, $cdate)
{

	$fileName = $userid . "-" . md5($firstname) . ".pdf";
	//$destination=CERTIFICATE_DIR.$fileName;
	$destination = CERTIFICATE_DIR . $fileName;
	//echo $destination;exit;
	$pdflink = "https://" . $_SERVER['HTTP_HOST'] . "/user/certs/" . $fileName;

	if (file_exists($destination)) {
		@unlink($destination);
	}

	$width = $pdf->_w;
	$height = $pdf->_w;
	$pageLayout = array($width, $height); //  or array($height, $width) 
	$userfullname = $firstname . " " . $lastname;
	//echo $level;exit;

	$pdf = new MYPDF('p', 'pt', $pageLayout, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(PDF_CREATOR);
	//$pdf->SetAuthor('Nicola Asuni');
	$pdf->SetTitle('Certficate');
	//$pdf->SetSubject('TCPDF Tutorial');
	//$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

	// set header and footer fonts
	$pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));

	// set default monospaced font
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

	// set margins
	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(0);
	$pdf->SetFooterMargin(0);

	// remove default footer
	$pdf->setPrintFooter(false);

	// set auto page breaks
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	// set image scale factor
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

	// set some language-dependent strings (optional)
	if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
		require_once(dirname(__FILE__) . '/lang/eng.php');
		$pdf->setLanguageArray($l);
	}

	// ---------------------------------------------------------

	$pdf->AddPage('L');
	$html = '';

	//$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');
	//$utf8text = file_get_contents('data/utf8hindi.txt', false);

	$pdf->SetFont('Helvetica', 'B', 24);
	$pdf->SetFont('freesans', 'B', 24);

	//$pdf->SetTextColor(185,89,50);

	$pdf->SetXY(0, 290);
	draw_pdf_cell($pdf, $pdf->_w, $userfullname, 'C', '0', 1);

	//$pdf->SetXY(0,395);
	//draw_pdf_cell($pdf, $pdf->_w, $level, 'C', '0', 1);  
	//$pdf->Cell(190,10,$level,0,1);

	$pdf->SetFont('Helvetica', 'B', 15);
	$pdf->SetFont('freesans', 'B', 15);
	$pdf->SetXY(165, 429);

	$formatDate = date_format(date_create($cdate), 'jS F Y');
	draw_pdf_cell($pdf, $pdf->_w, $formatDate, 'L', '0', 1);

	//$pdf->Cell(190,10,$level,0,1);

	$pdf->SetFont('freeserif', 'B', 24);
	$pdf->SetFillColor(255, 235, 235);
	$pdf->Output();

	//$pdf->Output('example_051.pdf', 'I');
	//$pdf->Output($destination, 'D');

	$pdf->Output($destination, 'F');
	$certificate = new stdClass();
	$certificate->userId = strval($firstname);
	//$certificate->courseId = str_replace("CRS-","",$courseId);
	$certificate->webRedirectionLink = $pdflink;
	//$results=json_encode($finalArr);
	//return $results;
	return $pdflink;
}

function draw_pdf_cell(&$pdf, $w, $txt, $align = 'L', $border = '0', $pos = 0, $h = 3.5)
{

	//   $pos = 1 ; //documented as $ln, stands for position: 0 = next to (reading order) ; 1 = begiinning of next line ; 2 below
	//   $align = 'L' ; // (L)eft, (C)enter, (R)ight, (J)ustify
	$fill = FALSE; //	(boolean) Indicates if the cell background must be painted (true) or transparent (false). 
	$link = ''; //URL, works with AddLink()
	$ignore_min_height = FALSE; // if true ignore automatic minimum height value. 

	//    $border = '1' ; 
	/*Indicates if borders must be drawn around the cell. The value can be a number:

                0: no border (default)
                1: frame

            or a string containing some or all of the following characters (in any order):

                L: left
                T: top
                R: right
                B: bottom

            or an array of line styles for each border group - 
                 * for example: array('LTRB' => array('width' => 2, 'cap' => 'butt', 'join' => 'miter', 
                 * 'dash' => 0, 'color' => array(0, 0, 0))) 
                 * */

	$stretch = 3; /* 0 = disabled
                   * 1 = horizontal scaling only if text is larger than cell width
                   * 2 = forced horizontal scaling to fit cell width
                   * 3 = character spacing only if text is larger than cell width
                   * 4 = forced character spacing to fit cell width
                   */

	$calign = 'T'; /* cell vertical alignment relative to the specified Y value. Possible values are:
                    * T : cell top
                    * C : center
                    * B : cell bottom
                    * A : font top
                    * L : font baseline
                    * D : font bottom
                    */

	$valign = 'M';/* text vertical alignment inside the cell. Possible values are:
                    * T : top
                    * C : center
                    * B : bottom
                    */


	$pdf->Cell($w, $h, $txt, $border, $pos, $align, $fill, $link, $stretch, $ignore_min_height, $calign, $valign);
}
