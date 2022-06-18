<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PruebasController extends Controller
{
    public function fpdf(){

    	$fpdf = new Fpdf('P','mm', 'A4');
    	$fpdf->AddPage();
    	$fpdf->SetFont('Arial', '', 8);
    	$fpdf->SetFillColor(255,255,255);

    	// Organizacion
    	$fpdf->SetXY(10,15);
    	$fpdf->SetFont('Arial', 'B', 10);
    	$fpdf->Cell(105, 4, utf8_decode("RESTAURANT CAMPESTRE LOS GIRASOLES S.A.C"),0,1,"L");
    	$fpdf->Ln(2);
    	$fpdf->SetFont('Arial', '', 10);
    	$fpdf->Cell(105, 4, utf8_decode("CAL.NOVA NRO. 109 URB. SOL DE VITARTE (A ESP. COLEGIO 1226-ALT.GRF.VISTA ALEGRE) LIMA - LIMA - ATE CAL.NOVA NRO. 109 URB. SOL DE VITARTE (A ESP. COLEGIO 1226-ALT.GRF.VISTA ALEGRE) LIMA - LIMA - ATE"),0,0,"L");
    	$fpdf->Cell(95, 4, utf8_decode(""),0,0,"L",1);
        
        // Numero de pedido
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->RoundedRect(120, 10, 80, 10, 1, '12', 'D');
        $fpdf->SetXY(120,10);
        $fpdf->Cell(80, 10, utf8_decode("Orden N° 0001 - 0019"),0,0,"C");

        // Fecha pedido
        $fpdf->SetXY(120,20);
        $fpdf->Cell(40, 10, utf8_decode("Fecha: 22/10/2018"),1,0,"C");

        // Hora pedido
        $fpdf->SetXY(160,20);
        $fpdf->Cell(40, 10, utf8_decode("Hora: 09:41"),1,0,"C");

        // Informacion de cliente
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->SetXY(10,37);
        $fpdf->Cell(15, 4, utf8_decode("Señor(es):"),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(115, 4, utf8_decode("SISTEMAS & S E.I.R.L. Lorem ipsum dolor sit amet, consectetur adipisicing elit. Modi, laudantium iure molestias repudiandae laboriosam inventore id magni ducimus, pariatur provident magnam tempore excepturi ab sequi quas placeat quaerat fuga est!"),"B",0,"L");
        $fpdf->Cell(70, 4, utf8_decode(""),0,0,"L",1);

        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->SetXY(10,45);
        $fpdf->Cell(14, 4, utf8_decode("Dirección:"),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(116, 4, utf8_decode("JR. BOLOGNESI NRO. 103 SEC. CHILCA SECTOR 01 Lorem ipsum dolor sit amet, consectetur adipisicing elit. Adipisci consectetur est beatae ratione eveniet, sequi saepe accusantium eaque illum incidunt et repudiandae esse pariatur possimus enim, unde quisquam quae? Neque!"),"B",0,"L");
        $fpdf->Cell(70, 4, utf8_decode(""),0,0,"L",1);

        $fpdf->SetXY(10,53);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(10, 4, utf8_decode("R.U.C:"),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(55, 4, utf8_decode("20600226631"),"B",0,"L");

        $fpdf->SetXY(75,53);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(10, 4, utf8_decode("D.N.I:"),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(55, 4, utf8_decode(""),"B",0,"L");

        // Comprobante
        $fpdf->SetXY(150,37);
        $fpdf->Cell(5, 5, utf8_decode(""),1,0,"C");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(20, 5, utf8_decode("Boleta"),0,0,"L");

        $fpdf->Cell(5, 5, utf8_decode("X"),1,0,"C");
        $fpdf->Cell(20, 5, utf8_decode("Venta Interna"),0,0,"L");

        $fpdf->SetXY(150,44.5);
        $fpdf->Cell(5, 5, utf8_decode(""),1,0,"C");
        $fpdf->Cell(20, 5, utf8_decode("Factura"),0,0,"L");

        $fpdf->SetXY(150,52.5);
        $fpdf->Cell(5, 5, utf8_decode(""),1,0,"C");
        $fpdf->Cell(20, 5, utf8_decode("Ticket"),0,1,"L");

        // Detalle pedido
        $fpdf->Ln(3);
        $fpdf->SetFillColor(60,60,60);
        $fpdf->SetTextColor(255,255,255);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(20, 5, utf8_decode("CODIGO"),1,0,"C",1);
        $fpdf->Cell(15, 5, utf8_decode("SERIE"),1,0,"C",1);
        // w = 120
        $fpdf->Cell(15, 5, utf8_decode("37"),1,0,"C",1);
        $fpdf->Cell(15, 5, utf8_decode("38"),1,0,"C",1);
        $fpdf->Cell(15, 5, utf8_decode("39"),1,0,"C",1);
        $fpdf->Cell(15, 5, utf8_decode("40"),1,0,"C",1);
        $fpdf->Cell(15, 5, utf8_decode("41"),1,0,"C",1);
        $fpdf->Cell(15, 5, utf8_decode("42"),1,0,"C",1);
        $fpdf->Cell(15, 5, utf8_decode("43"),1,0,"C",1);
        $fpdf->Cell(15, 5, utf8_decode("44"),1,0,"C",1);

        $fpdf->Cell(15, 5, utf8_decode("PARES"),1,0,"C",1);
        $fpdf->Cell(20, 5, utf8_decode("TOTAL"),1,1,"C",1);

        $fpdf->RoundedRect(10, 65.5, 190, 170, 1, '34', 'D');

        $fpdf->SetFont('Arial', '', 8);
        $fpdf->SetTextColor(0,0,0);
        $i = 1;
        while ($i <= 34) {
        	$fpdf->SetFont('Arial', 'B', 8);
	        $fpdf->Cell(5, 5, utf8_decode($i),($i == 34 ? 0 : 1),0,"C");
	        $fpdf->SetFont('Arial', '', 8);
	        $fpdf->Cell(15, 5, utf8_decode("3702"),1,0,"C");
	        $fpdf->Cell(15, 5, utf8_decode("F8"),1,0,"C");
	        $fpdf->Cell(15, 5, utf8_decode(""),1,0,"C");
	        $fpdf->Cell(15, 5, utf8_decode(""),1,0,"C");
	        $fpdf->Cell(15, 5, utf8_decode("1"),1,0,"C");
	        $fpdf->Cell(15, 5, utf8_decode(""),1,0,"C");
	        $fpdf->Cell(15, 5, utf8_decode(""),1,0,"C");
	        $fpdf->Cell(15, 5, utf8_decode("4"),1,0,"C");
	        $fpdf->Cell(15, 5, utf8_decode(""),1,0,"C");
	        $fpdf->Cell(15, 5, utf8_decode(""),1,0,"C");
	        $fpdf->Cell(15, 5, "5",1,0,"C");
	        $fpdf->Cell(20, 5, number_format("63240",2,'.',','),($i == 34 ? 0 : 1),1,"R");
        	$i++;
	    }

	    // Observación
	    $fpdf->RoundedRect(10, 237, 85, 30, 1, '12', 'D');

	    $fpdf->SetXY(10,237.5);
	    $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(20, 4, utf8_decode("Observación: "),0,0,"L");

		$fpdf->SetXY(30,237.5);
		$fpdf->SetFont('Arial', '', 8);
        $fpdf->MultiCell(65, 4, utf8_decode("IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISICING ELIT. VOLUPTAS NEMO SIMILIQUE DOLOREM ET DOLORUM DOLOR IPSAM SEQUI AUT."),0,"L");

        // Vendedor
        $fpdf->RoundedRect(10, 267, 85, 4, 1, '34', 'D');
        $fpdf->SetXY(10,267);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(14, 4, utf8_decode("Vendedor: "),0,0,"L");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(71, 4, utf8_decode("JECHABAUDIS"),0,0,"L");

        // Total Pares
        $fpdf->RoundedRect(97.5, 237, 50, 10, 1, '1234', 'D');
        $fpdf->SetXY(97.5, 237);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(25, 10, utf8_decode("TOTAL PARES:"),"R",0,"R");
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(25, 10, utf8_decode("20"),0,0,"L");

        // SUB TOTAL
        $fpdf->SetFillColor(0,0,0);
        $fpdf->RoundedRect(150, 237, 25, 10, 1, '14', 'FD');
        $fpdf->RoundedRect(175, 237, 25, 10, 1, '23', 'D');
        $fpdf->SetXY(150, 237);

        $fpdf->SetTextColor(255,255,255);
        $fpdf->SetFont('Arial', 'B', 8);
        $fpdf->Cell(25, 10, utf8_decode("S-TOTAL"),0,0,"C");

        $fpdf->SetTextColor(0,0,0);
        $fpdf->SetFont('Arial', '', 8);
        $fpdf->Cell(25, 10, number_format("609786",2,'.',','),0,0,"C");

        $fpdf->output();
        exit();

    }
}
