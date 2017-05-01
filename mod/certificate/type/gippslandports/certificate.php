<?php
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from view.php in mod/tracker
}
// Date formatting - can be customized if necessary
$certificatedate = '';
if ($certrecord->certdate > 0) {
$certdate = $certrecord->certdate;
}else $certdate = certificate_generate_date($certificate, $course);
if($certificate->printdate > 0)    {
    if ($certificate->datefmt == 1)    {
    $certificatedate = str_replace(' 0', ' ', strftime('%B %d, %Y', $certdate));
}   if ($certificate->datefmt == 2) {
    $certificatedate = date('F jS, Y', $certdate);
}   if ($certificate->datefmt == 3) {
    $certificatedate = str_replace(' 0', '', strftime('%d %B %Y', $certdate));
}   if ($certificate->datefmt == 4) {
    $certificatedate = strftime('%B %Y', $certdate);
}   if ($certificate->datefmt == 5) {
    $timeformat = get_string('strftimedate');
    $certificatedate = userdate($certdate, $timeformat);
    }
}

//Grade formatting
$grade = '';
//Print the course grade
$coursegrade = certificate_print_course_grade($course);
if ($certificate->printgrade == 1 && $certrecord->reportgrade == !null) {
$reportgrade = $certrecord->reportgrade;
    $grade = $strcoursegrade.':  '.$reportgrade;
}else
    if($certificate->printgrade > 0) {
    if($certificate->printgrade == 1) {
    if($certificate->gradefmt == 1) {
    $grade = $strcoursegrade.':  '.$coursegrade->percentage;
}   if($certificate->gradefmt == 2) {
    $grade = $strcoursegrade.':  '.$coursegrade->points;
}   if($certificate->gradefmt == 3) {
    $grade = $strcoursegrade.':  '.$coursegrade->letter;

  }
} else {
//Print the mod grade
$modinfo = certificate_print_mod_grade($course, $certificate->printgrade);
if ($certrecord->reportgrade == !null) {
$modgrade = $certrecord->reportgrade;
    $grade = $modinfo->name.' '.$strgrade.': '.$modgrade;
}else
    if($certificate->printgrade > 1) {
    if ($certificate->gradefmt == 1) {
    $grade = $modinfo->name.' '.$strgrade.': '.$modinfo->percentage;
}
    if ($certificate->gradefmt == 2) {
    $grade = $modinfo->name.' '.$strgrade.': '.$modinfo->points;
}
    if($certificate->gradefmt == 3) {
    $grade = $modinfo->name.' '.$strgrade.': '.$modinfo->letter;
     }
	}
  }
}
//Print the outcome
$outcome = '';
$outcomeinfo = certificate_print_outcome($course, $certificate->printoutcome);
if($certificate->printoutcome > 0) {
    $outcome = $outcomeinfo->name.': '.$outcomeinfo->grade;
}

// Print the code number
$code = '';
if($certificate->printnumber) {
$code = $certrecord->code;
}
//Print the student name
$studentname = '';
// $studentname = $certrecord->studentname;
$studentname = fullname($USER);
//Print the credit hours
if($certificate->printhours) {
$credithours =  $strcredithours.': '.$certificate->printhours;
} else $credithours = '';

//Print the html text
$customtext = $certificate->customtext;

//Add pdf page
	$WIDTH = 210; $HEIGHT = 297;
    $orientation = "P";
    $pdf = new PDF($orientation, 'mm', 'A4');
    // $pdf->SetProtection(array('print'));
    $pdf->AddPage();
    if(ini_get('magic_quotes_gpc')=='1')
		$customtext=stripslashes($customtext);

// Add images and lines
    // print_border($certificate->borderstyle, $orientation);
	// draw_frame($certificate->bordercolor, $orientation);
    // print_watermark($certificate->printwmark, $orientation);
    print_seal($certificate->printseal, $orientation, 78, 30, '', '');
    print_seal($certificate->printseal, $orientation, 84, 220, 20, 20);
// certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, $sealx, $sealy, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, 78, 30, '', '');
certificate_print_image($pdf, $certificate, CERT_IMAGE_SEAL, 84, 220, 20, 20);
    // print_seal($certificate->printseal, $orientation, 164, 250, 20, 20);
    // print_signature($certificate->printsignature, $orientation, 85, 530, '', '');

// Add text
    // $pdf->SetTextColor(0,0,128);
	$pdf->Rect(25, 25, 160, 247);
    $pdf->SetTextColor(0,0,0);
	$pdf->SetFont("Helvetica", 'B');
	// $pdf->Cell(0,30,$customtext,1);
	$pdf->SetLeftMargin(30);
	$pdf->SetY(95);
	$pdf->SetFontSize(15);
	$str = utf8_decode($course->fullname);
	$len = $pdf->GetStringWidth($str);
	$pdf->SetX($WIDTH/2 - $len/2);
	$pdf->Write(20, $str);
	$pdf->Ln();
	$pdf->SetFontSize(12);
	// $str = utf8_decode($studentname);
	$str = $studentname;
	$len = $pdf->GetStringWidth($str);
	$pdf->SetX($WIDTH/2 - $len/2);
	$pdf->Write(20, $str);
	$pdf->Ln();
	$pdf->SetFontSize(10);
	$str = "Completed on: ".date("j F Y");
	$len = $pdf->GetStringWidth(utf8_decode($str));
	$pdf->SetX($WIDTH/2 - $len/2);
	$pdf->Write(10, $str);
	$pdf->Ln();
	$str = "Expires on: ".date("j F Y", mktime(0,0,0,date('m'),date('d'),date('Y')+1));
	$len = $pdf->GetStringWidth(utf8_decode($str));
	$pdf->SetX($WIDTH/2 - $len/2);
	$pdf->Write(10, $str);
	$pdf->Ln();
	$pdf->SetY(190);
	$pdf->Write(5, "Gippsland Ports Head Office");
	$pdf->Ln();
	$pdf->SetFontSize(8);
	$pdf->SetFont("Helvetica", '');
	$pdf->Write(5, "97 Main Street Bairnsdale VIC 3875");
	$pdf->Ln();
	$pdf->Write(5, "Phone: 03 5150 0500");
	$pdf->Ln();
	$pdf->Write(5, "Fax: 03 5150 0501");
	// $str = "Health & Safety Representative: Kristy Haley";
	// $pdf->SetXY($WIDTH - 30 - $pdf->GetStringWidth(utf8_decode($str)), 205);
	// $pdf->Write(5, $str);
	
	// Front of card
	$pdf->Rect(25, 219, 80, 53);
	// Gippsland Ports Head Office details
	$pdf->SetY(225);
	$pdf->SetFontSize(10);
	$pdf->SetFont("Helvetica", 'B');
	$pdf->Write(4, "Gippsland Ports Head Office");
	$pdf->Ln();
	$pdf->SetFontSize(8);
	$pdf->SetFont("Helvetica", '');
	$pdf->Write(4, "97 Main Street Bairnsdale VIC 3875");
	$pdf->Ln();
	$pdf->Write(4, "Phone: 03 5150 0500");
	$pdf->Ln();
	$pdf->Write(4, "Fax: 03 5150 0501");
	
	// Locations
	$pdf->SetY(245);
	$pdf->SetFontSize(7);
	$pdf->SetFont("Helvetica", 'B');
	$w = 25; $h = 4;
	$pdf->Cell($w, $h, "Office Location", 0, 0);
	$pdf->Cell($w, $h, "Office Number", 0, 0);
	$pdf->Cell($w, $h, "Office Manager", 0, 1);
	$pdf->SetFont("Helvetica", '');
	$pdf->Cell($w, $h, "Lakes Entrance Dep", 0, 0);
	$pdf->Cell($w, $h, "03 5155 6900", 0, 0);
	$pdf->Cell($w, $h, "0429 018 800", 0, 1);
	$pdf->Cell($w, $h, "Port Welshpool", 0, 0);
	$pdf->Cell($w, $h, "03 5688 1303", 0, 0);
	$pdf->Cell($w, $h, "0428 113 324", 0, 1);
	$pdf->Cell($w, $h, "Paynesville", 0, 0);
	$pdf->Cell($w, $h, "03 5156 6352", 0, 0);
	$pdf->Cell($w, $h, "0409 124 551", 0, 1);
	$pdf->Cell($w, $h, "Bullock Is. Boatyard", 0, 0);
	$pdf->Cell($w, $h, "03 5155 6950", 0, 0);
	$pdf->Cell($w, $h, "0409 124 551", 0, 0);
	
	// Back of card
	$pdf->Rect(105, 219, 80, 53);
	// Course n stuff
	$pdf->SetLeftMargin(110);
	$pdf->SetY(225);
	$pdf->SetFont("Helvetica", 'B');
	$pdf->SetFontSize(11);
	$pdf->MultiCell(60, 8, utf8_decode($course->fullname), 0, 'L');
	// $pdf->Ln();
	// $pdf->Write(10, utf8_decode($studentname));
	$pdf->Write(10, $studentname);
	$pdf->Ln();
	$pdf->SetFontSize(10);
	$str = "Completed on: ".date("j F Y");
	$len = $pdf->GetStringWidth(utf8_decode($str));
	$pdf->Write(8, $str);
	$pdf->Ln();
	$str = "Expires on: ".date("j F Y", mktime(0,0,0,date('m'),date('d'),date('Y')+1));
	$len = $pdf->GetStringWidth(utf8_decode($str));
	$pdf->Write(8, $str);
	
	
	//$pdf->WriteHTML($customtext);
    // cert_printtext(48, 170, 'C', 'Helvetica', 'B', 26, utf8_decode(get_string("titleportrait", "certificate")));
    // $pdf->SetTextColor(0,0,0);
    // cert_printtext(45, 230, 'C', 'Times', 'B', 20, utf8_decode(get_string("introportrait", "certificate")));
    // cert_printtext(45, 280, 'C', 'Helvetica', '', 30, utf8_decode($studentname));
    // cert_printtext(45, 330, 'C', 'Helvetica', '', 20, utf8_decode(get_string("statementportrait", "certificate")));
    // cert_printtext(45, 380, 'C', 'Helvetica', '', 20, utf8_decode($course->fullname));
    // cert_printtext(45, 420, 'C', 'Helvetica', '', 20, utf8_decode(get_string("ondayportrait", "certificate")));
    // cert_printtext(45, 460, 'C', 'Helvetica', '', 14, utf8_decode($certificatedate));
    // cert_printtext(45, 540, 'C', 'Times', '', 10, utf8_decode($grade));
    // cert_printtext(45, 551, 'C', 'Times', '', 10, utf8_decode($outcome));
    // cert_printtext(45, 562, 'C', 'Times', '', 10, utf8_decode($credithours));
    // cert_printtext(45, 720, 'C', 'Times', '', 10, utf8_decode($code));
    // $i = 0 ;
	// if($certificate->printteacher){
    // $context = get_context_instance(CONTEXT_MODULE, $cm->id);
    // if ($teachers = get_users_by_capability($context, 'mod/certificate:printteacher', '', $sort='u.lastname ASC','','','','',false)) {
		// foreach ($teachers as $teacher) {
			// $i++;
	// cert_printtext(85, 590+($i *12) , 'L', 'Times', '', 12, utf8_decode(fullname($teacher)));
// }}}
    // cert_printtext(0, 600, '', '', '', '', '');
	// $pdf->Cell(0,300,$pdf->WriteHTML($customtext),1);
	
	// $pdf->SetLeftMargin(85);
	// $pdf->WriteHTML($customtext);
?>
