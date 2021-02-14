<?php

namespace App\Http\Controllers;

use App\Stanje;
use Illuminate\Http\Request;
use App\Korisnik;
use App\Helper\CirilicaLatinicaHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $userId = Auth::id();
        if ($userId > 1) {
            return redirect()->route('pretraga');
        }

        $korisnici = Korisnik::sviKorisnici();
        
        $cirilicaLatinicaHelper = new CirilicaLatinicaHelper();
        $mesec = date('m');
        $godina = date('Y');
        foreach ($korisnici as &$korisnik) {
            $korisnik['stanje'] = Stanje::getStanje($korisnik['id'], $mesec, $godina);
            
            $idStanja = Stanje::getIdStanja($korisnik['id'], $mesec, $godina);
            $stanjeModel = Stanje::find($idStanja);
            
            //bazdaren vodomer
            if (isset($stanjeModel->prethodno_stanje_bazdaren_vodomer) && $stanjeModel->prethodno_stanje_bazdaren_vodomer >= 0) {
                $korisnik['prethodno_stanje'] = $stanjeModel->prethodno_stanje_bazdaren_vodomer;
            } else { 
                $prethodniMesec = $mesec;
                $prethodnaGodina = $godina;
                $this->getPrethodniMesec($prethodniMesec, $prethodnaGodina);
                $korisnik['prethodno_stanje'] = Stanje::getStanje($korisnik['id'], $prethodniMesec, $prethodnaGodina);
            }

            $korisnik['latinica'] = $cirilicaLatinicaHelper->stringToLat($korisnik['ime']) . ' ' . $cirilicaLatinicaHelper->stringToLat($korisnik['prezime']);
        }

        return view('home')->with(array('korisnici'=>$korisnici));
    }

    public function unesiStanje(Request $request)
    {
        $data = $request->input();
        $mesec = date('m');
        $godina = date('Y');

        $stanjeKorisnikaZaMesec = Stanje::getStanje($data['idKorisnik'], $mesec, $godina);
        $korisnikModel = Korisnik::find($data['idKorisnik']);
        
        if (isset($stanjeKorisnikaZaMesec)) {   //izmena 
            $idStanja = Stanje::getIdStanja($data['idKorisnik'], $mesec, $godina);
            $stanjeModel = Stanje::find($idStanja);
        } else {    //novo stanje
            $stanjeModel = new Stanje();
        }
        if (is_null($data['stanje'])) { //ako je prazno, obrisi
            $stanjeModel->delete();
            return 2;
        } else {
            $stanjeModel->korisnik_id = $data['idKorisnik'];
            $stanjeModel->mesec = $mesec;
            $stanjeModel->godina = $godina;
            $stanjeModel->vreme_citanja = date('Y-m-d H:i:s');
            $stanjeModel->stanje = $data['stanje'];
            $stanjeModel->broj_clanova_domacinstva = $korisnikModel->broj_clanova_domacinstva;
            $stanjeModel->save();

            return 1;
        }
    }

    public function unesiStanjeBazdaren(Request $request)
    {
        $data = $request->input();
        $mesec = date('m');
        $godina = date('Y');
        
        $stanjeKorisnikaZaMesec = Stanje::getStanje($data['idKorisnik'], $mesec, $godina);
        $korisnikModel = Korisnik::find($data['idKorisnik']);
        
        if (isset($stanjeKorisnikaZaMesec)) {   //izmena 
            $idStanja = Stanje::getIdStanja($data['idKorisnik'], $mesec, $godina);
            $stanjeModel = Stanje::find($idStanja);
        } else {    //novo stanje
            $stanjeModel = new Stanje();
        }
        
        if (is_null($data['stanje'])) { //ako je prazno, obrisi
            $stanjeModel->prethodno_stanje_bazdaren_vodomer = 0;
        } else {
            $stanjeModel->prethodno_stanje_bazdaren_vodomer = $data['stanje'];
            $stanjeModel->save();
        }
        return 1;
    }

    public function pretraga(Request $request) {
        $data = $request->input();
        
        $cirilicaLatinicaHelper = new CirilicaLatinicaHelper();

        $rezultat = [];
        if ($request->isMethod('post')) {
            $korisnici = Korisnik::korisnikPoImenuIPrezimenu($cirilicaLatinicaHelper->stringToCyr($data['ime']), $cirilicaLatinicaHelper->stringToCyr($data['prezime']));
            if (isset($data['pausalac']) && $data['pausalac']) {
                foreach ($korisnici as $korisnik) {
                    if ($korisnik['pausalac']) {
                        $rezultat[] = $korisnik;
                    }
                }
            } else {
                $rezultat = $korisnici;
            }
        }

        return view('pretraga')->with(array('korisnici'=>$rezultat));
    }

    public function korisnik(Request $request) {
        $data = $request->input();
        
        $korisnik = Korisnik::find($data['id'])->toArray();

        $potrosnje = Stanje::getStanjaKorisnika($data['id']);
        
        foreach($potrosnje as $i=>&$potrosnja) {

            $blokTarifa = 7 * $potrosnja['broj_clanova_domacinstva'];
            
            if (!$potrosnja['pausalac']) {

                if(isset($potrosnje[$i+1]['stanje'])) { //ako postoji prethodno stanje

                    $potrosnja['potroseno'] = $potrosnja['stanje'] - $potrosnje[$i+1]['stanje'];
                    
                    if ($potrosnja['potroseno'] > $blokTarifa) {
                        $prekoraceno = $potrosnja['potroseno'] - $blokTarifa;
                        $potrosnja['racun'] = 200 * $prekoraceno + 50 * $blokTarifa;
                    } else {
                        $potrosnja['racun'] = 50 * $potrosnja['potroseno'];
                    }

                } else {
                    $potrosnja['potroseno'] = '/';
                    $potrosnja['racun'] = 0;
                }

            } else {    //pausalac

                $potrosnja['potroseno'] = 0;
                if ($potrosnja['pausalac_kubika'] > $blokTarifa) {
                    $prekoraceno = $potrosnja['pausalac_kubika'] - $blokTarifa;
                    $potrosnja['racun'] = 200 * $prekoraceno + 50 * $blokTarifa;
                } else {
                    $potrosnja['racun'] = 50 * $potrosnja['pausalac_kubika'];
                }

            }

        }

        return view('korisnik')->with(array('korisnik'=>$korisnik, 'potrosnje'=>$potrosnje));
    }

    
    public function ocekivaniPrihod() {
        $trenutniMesec = date('m');
        $trenutnaGodina = date('Y');
        
        return view('ocekivani-prihod')->with(array('trenutniMesec'=>$trenutniMesec, 'trenutnaGodina'=>$trenutnaGodina));
    }

    
    public function ocekivaniPrihodRezultat(Request $request) {
        $data = $request->input();
        
        $korisnici = Korisnik::sviKorisnici();

        $rezultat = [];
        $br = 0;

        foreach ($korisnici as $korisnik) {
            $potrosnje = Stanje::getStanjaKorisnika($korisnik['id']);
            
            foreach($potrosnje as $i=>&$potrosnja) {

                if ($potrosnja['mesec'] == $data['mesec'] && $potrosnja['godina'] == $data['godina']) {  //za izabrani mesec

                    $blokTarifa = 7 * $potrosnja['broj_clanova_domacinstva'];
                    
                    if (!$potrosnja['pausalac']) {

                        if(isset($potrosnje[$i+1]['stanje'])) { //ako postoji prethodno stanje

                            $rezultat[$br]['potroseno'] = $potrosnja['stanje'] - $potrosnje[$i+1]['stanje'];
                            
                            if ($rezultat[$br]['potroseno'] > $blokTarifa) {
                                $prekoraceno = $rezultat[$br]['potroseno'] - $blokTarifa;
                                $rezultat[$br]['racun'] = 200 * $prekoraceno + 50 * $blokTarifa;
                            } else {
                                $rezultat[$br]['racun'] = 50 * $rezultat[$br]['potroseno'];
                            }

                        } else {
                            $rezultat[$br]['potroseno'] = '/';
                            $rezultat[$br]['racun'] = 0;
                        }

                    } else {    //pausalac

                        $rezultat[$br]['potroseno'] = 0;
                        if ($potrosnja['pausalac_kubika'] > $blokTarifa) {
                            $prekoraceno = $potrosnja['pausalac_kubika'] - $blokTarifa;
                            $rezultat[$br]['racun'] = 200 * $prekoraceno + 50 * $blokTarifa;
                        } else {
                            $rezultat[$br]['racun'] = 50 * $potrosnja['pausalac_kubika'];
                        }

                    }
                    $rezultat[$br]['korisnik_id'] = $korisnik['id'];
                    $rezultat[$br]['ime'] = $korisnik['ime'] . ' ' . $korisnik['prezime'];
                    $rezultat[$br]['broj_clanova_domacinstva'] = $korisnik['broj_clanova_domacinstva'];
                }
            }
            $br++;
        }

        array_multisort( array_column($rezultat, "racun"), SORT_DESC, array_column($rezultat, "broj_clanova_domacinstva"), SORT_DESC, $rezultat );
       
        $ukupno = 0;
        foreach ($rezultat as $rez) {
            $ukupno += $rez['racun'];
        }

        $kubika = 0;
        foreach ($rezultat as $rez) {
            $kubika += $rez['potroseno'];
        }
        
        return view('ocekivani-prihod-rezultat')->with(array('rezultat'=>$rezultat, 'ukupno'=>$ukupno, 'kubika'=>$kubika, 'mesec'=>$data['mesec'], 'godina'=>$data['godina']));
    }

    public function izvestaj() {
        $trenutniMesec = date('m');
        $trenutnaGodina = date('Y');
        
        return view('izvestaj')->with(array('trenutniMesec'=>$trenutniMesec, 'trenutnaGodina'=>$trenutnaGodina));
    }

    public function napraviIzvestaj(Request $request) {
        $data = $request->input();
        
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(8.6255);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(19.753);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20.225);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(16.307);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(8.307);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15.835);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(18.5);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(16.143);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10.502);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(13.169);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(9.876);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(18.338);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('W')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('X')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('Y')->setWidth(18.86);
        $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('Z')->setWidth(14.57);

        $spreadsheet->getActiveSheet()->getRowDimension('1')->setRowHeight(32.25);
        $spreadsheet->getActiveSheet()->getRowDimension('2')->setRowHeight(79.5);

        $styleFirstRow= [
            'font' => [
                'bold' => true,
                'size' => 16,
            ],
        ];
        $styleSecondRow= [
            'font' => [
                'bold' => true,
                'size' => 10,
            ],
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'rotation' => 90,
                'color' => [
                    'argb' => 'D9D9D9',
                ],
            ],
        ];
        $styleRedCols= [
            'alignment' => [
                'horizontal' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'rotation' => 90,
                'color' => [
                    'argb' => 'FDE9D9',
                ],
            ],
        ];
        $styleYellowCols= [
            'alignment' => [
                'horizontal' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'rotation' => 90,
                'color' => [
                    'argb' => 'FFFF00',
                ],
            ],
        ];
        $styleFirstCol= [
            'alignment' => [
                'horizontal' => 'center',
            ],
            'font' => [
                'bold' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $styleCenterCol= [
            'alignment' => [
                'horizontal' => 'center',
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        $styleBorder= [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['argb' => '00000000'],
                ],
            ],
        ];
        
        $spreadsheet->getActiveSheet()->getStyle('C1')->applyFromArray($styleFirstRow);
        $spreadsheet->getActiveSheet()->getStyle('A2:Z2')->applyFromArray($styleSecondRow);

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('C1', 'ЗАДУЖЕЊЕ ЗА ВОДУ МЕШТАНА СЕЛА МАЛЧА ЗА МЕСЕЦ ' . $data['mesec'] . '.' . $data['godina'] . '.');
        $sheet->setCellValue('A2', 'Р.бр');
        $sheet->setCellValue('B2', 'Име');
        $sheet->setCellValue('C2', 'Презиме');
        $sheet->setCellValue('D2', 'ШИФРА ОБЈЕКТА');
        $sheet->setCellValue('E2', 'бр. чл. домаћинства');
        $sheet->setCellValue('F2', 'Бр.водомера');
        $sheet->setCellValue('G2', 'Претходни датум читања');
        $sheet->setCellValue('H2', 'Датум читања');
        $sheet->setCellValue('I2', 'Претходно стање водомера');
        $sheet->setCellValue('J2', 'стање водомера');
        $sheet->setCellValue('K2', 'Количина утрошене воде (м3)');
        $sheet->setCellValue('L2', 'Утрошена количина воде по члану');
        $sheet->setCellValue('M2', 'Количина воде до 7m3');
        $sheet->setCellValue('N2', 'Количина воде преко 7m3');
        $sheet->setCellValue('O2', 'Цена утрошене воде до 7m3');
        $sheet->setCellValue('P2', 'Цена утрошене воде преко 7m3');
        $sheet->setCellValue('Q2', 'Износ за утрошену воду до 7 m3 по члану');
        $sheet->setCellValue('R2', 'Износ за утрошену воду преко 7 m3 по члану');
        $sheet->setCellValue('S2', 'Дуговање по текућој потрошњи');
        $sheet->setCellValue('T2', 'Очитавање');
        $sheet->setCellValue('U2', 'Укупно дуговање');
        $sheet->setCellValue('V2', 'индикатор 1 прекораченје преко 7 м3');
        $sheet->setCellValue('W2', 'идикатор 2 техничка вода ');
        $sheet->setCellValue('X2', 'тип водомера');
        $sheet->setCellValue('Y2', 'ЈМБГ');
        $sheet->setCellValue('Z2', 'НАПОМЕНА');

        $spreadsheet->getActiveSheet()->getStyle('Y2:Y2000')->getNumberFormat()->setFormatCode('0');

        $korisnici = Korisnik::sviKorisnici();
        
        foreach ($korisnici as &$korisnik) {
            //novo stanje
            $korisnik['stanje'] = Stanje::getStanje($korisnik['id'], $data['mesec'], $data['godina']);
            
            //prethodno stanje
            $prethodniMesec = $data['mesec'];
            $prethodnaGodina = $data['godina'];
            $this->getPrethodniMesec($prethodniMesec, $prethodnaGodina);
            $korisnik['prethodno_stanje'] = Stanje::getStanje($korisnik['id'], $prethodniMesec, $prethodnaGodina);
            $korisnik['bazdarenje_napomena'] = "";

            $idStanja = Stanje::getIdStanja($korisnik['id'], $data['mesec'], $data['godina']);
            if (isset($idStanja)) {
                $stanjeModel = Stanje::find($idStanja);

                //prethodno stanje ukoliko je bazdaren vodomer
                if (isset($stanjeModel->prethodno_stanje_bazdaren_vodomer) && $stanjeModel->prethodno_stanje_bazdaren_vodomer >= 0) {
                    $korisnik['prethodno_stanje'] = $stanjeModel->prethodno_stanje_bazdaren_vodomer;
                    $korisnik['bazdarenje_napomena'] = "баждарен водомер";
                }

                $korisnik['datum_citanja'] = date('d.m.Y.', strtotime($stanjeModel->vreme_citanja));
            } else {
                $korisnik['datum_citanja'] = '';
            }

            $idStanja = Stanje::getIdStanja($korisnik['id'], $prethodniMesec, $prethodnaGodina);
            if (isset($idStanja)) {
                $stanjePretModel = Stanje::find($idStanja);
                $korisnik['prethodni_datum_citanja'] = date('d.m.Y.', strtotime($stanjePretModel->vreme_citanja));
                $korisnik['prethodni_broj_clanova_domacinstva'] = $stanjePretModel->broj_clanova_domacinstva;
            } else {
                $korisnik['prethodni_datum_citanja'] = $korisnik['prethodni_broj_clanova_domacinstva'] = '';
            }
        }
        
        $brKorisnika = count($korisnici);
        $lastCol = $brKorisnika + 2;
        
        $spreadsheet->getActiveSheet()->getStyle('A3:A'.$lastCol)->applyFromArray($styleFirstCol);
        $spreadsheet->getActiveSheet()->getStyle('B3:B'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('C3:C'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('D3:D'.$lastCol)->applyFromArray($styleCenterCol);
        $spreadsheet->getActiveSheet()->getStyle('G3:G'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('E3:E'.$lastCol)->applyFromArray($styleCenterCol);
        $spreadsheet->getActiveSheet()->getStyle('F3:F'.$lastCol)->applyFromArray($styleCenterCol);
        $spreadsheet->getActiveSheet()->getStyle('H3:H'.$lastCol)->applyFromArray($styleRedCols);
        $spreadsheet->getActiveSheet()->getStyle('I3:I'.$lastCol)->applyFromArray($styleCenterCol);
        $spreadsheet->getActiveSheet()->getStyle('J3:J'.$lastCol)->applyFromArray($styleRedCols);
        $spreadsheet->getActiveSheet()->getStyle('K3:K'.$lastCol)->applyFromArray($styleRedCols);

        $spreadsheet->getActiveSheet()->getStyle('L3:L'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('M3:M'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('N3:N'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('O3:O'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('P3:P'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('Q3:Q'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('R3:R'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('S3:S'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('T3:T'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('U3:U'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('V3:V'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('W3:W'.$lastCol)->applyFromArray($styleBorder);
        $spreadsheet->getActiveSheet()->getStyle('X3:X'.$lastCol)->applyFromArray($styleBorder);

        $spreadsheet->getActiveSheet()->getStyle('Y3:Y'.$lastCol)->applyFromArray($styleCenterCol);
        $spreadsheet->getActiveSheet()->getStyle('Z3:Z'.$lastCol)->applyFromArray($styleBorder);

        $spreadsheet->getActiveSheet()->getStyle('A2:Z500')->getAlignment()->setWrapText(true); 
        
        foreach($korisnici as $j=>$kor) {
            $colBr = $j + 3;
            $napomena = [];
            
            if ($kor['bazdarenje_napomena'] != '') {
                $napomena[] = $kor['bazdarenje_napomena'];
            }
            $sheet->setCellValue('A'.$colBr, $kor['redni_broj']);
            $sheet->setCellValue('B'.$colBr, $kor['ime']);
            $sheet->setCellValue('C'.$colBr, $kor['prezime']);
            $sheet->setCellValue('D'.$colBr, $kor['sifra_objekta']);
            $sheet->setCellValue('E'.$colBr, $kor['broj_clanova_domacinstva']);
            if ($kor['prethodni_broj_clanova_domacinstva'] != "" && $kor['prethodni_broj_clanova_domacinstva'] != $kor['broj_clanova_domacinstva']) {
                $sheet->getStyle('E'.$colBr)->applyFromArray($styleYellowCols);
                $napomena[] = 'измењен број чланова домаћинства';
            }
            $sheet->setCellValue('F'.$colBr, $kor['broj_vodomera']);
            $sheet->setCellValue('G'.$colBr, $kor['prethodni_datum_citanja']);
            $sheet->setCellValue('H'.$colBr, $kor['datum_citanja']);
            $sheet->setCellValue('I'.$colBr, $kor['prethodno_stanje']);
            $sheet->setCellValue('J'.$colBr, $kor['stanje']);
            if ($kor['broj_clanova_domacinstva'] > 0) {
                $sheet->setCellValue('L'.$colBr, "=K".$colBr."/E".$colBr);
            } else {
                $sheet->setCellValue('L'.$colBr, "0");
            }
            $sheet->setCellValue('M'.$colBr, "=IF(L".$colBr."<=7,K".$colBr.",E".$colBr."*7)");
            $sheet->setCellValue('N'.$colBr, "=K".$colBr."-M".$colBr);
            if ($kor['broj_clanova_domacinstva'] > 0) {
                $sheet->setCellValue('O'.$colBr, "50.00");
                $sheet->setCellValue('P'.$colBr, "200.00");
                $sheet->setCellValue('Q'.$colBr, "=M".$colBr."*O".$colBr);
                $sheet->setCellValue('R'.$colBr, "=N".$colBr."*P".$colBr);
                $sheet->setCellValue('S'.$colBr, "=Q".$colBr."+R".$colBr);
            } else {
                $sheet->setCellValue('S'.$colBr, "=K".$colBr."*200");
            }
            $sheet->setCellValue('T'.$colBr, "60.00");
            $sheet->setCellValue('U'.$colBr, "=S".$colBr."+T".$colBr);
            $sheet->setCellValue('V'.$colBr, 0);
            $sheet->setCellValue('W'.$colBr, 0);
            $sheet->setCellValue('Z'.$colBr, implode(',', $napomena));
            
            $utroseno = 0;
            if ($kor['pausalac']) {
                $utroseno = $kor['pausalac_kubika'];
            } else {
                if (isset($kor['stanje']) && isset($kor['prethodno_stanje'])) {
                    $utroseno = $kor['stanje'] - $kor['prethodno_stanje'];
                }
            }
            $sheet->setCellValue('K'.$colBr, $utroseno);

            $sheet->setCellValue('Y'.$colBr, $kor['jmbg']);
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'. urlencode('Izvestaj.xlsx').'"');
        $writer->save('php://output');
    }

    private function getPrethodniMesec(&$mesec, &$godina) {
        if ($mesec > 1) {
            $mesec--;
        } else {
            $mesec = 12;
            $godina--;
        }
    }

    public function izmenaPodataka(Request $request) {
        $data = $request->input();
        
        $korisnik = Korisnik::find($data['id'])->toArray();

        return view('izmena-podataka')->with(array('korisnik'=>$korisnik));
    }

    public function upisPodataka(Request $request) {
        $data = $request->input();

        $korisnik = Korisnik::find($data['id']);
        $korisnik->ime = $data['ime'];
        $korisnik->prezime = $data['prezime'];
        $korisnik->jmbg = $data['jmbg'];
        $korisnik->sifra_objekta = $data['sifra_objekta'];
        $korisnik->broj_vodomera = $data['broj_vodomera'];
        $korisnik->broj_clanova_domacinstva = $data['broj_clanova_domacinstva'];
        if (isset($data['pausalac']) && $data['pausalac'] == 1) {
            $korisnik->pausalac = 1;
            $korisnik->pausalac_kubika = $data['pausalac_kubika'];
        } else {
            $korisnik->pausalac = 0;
            $korisnik->pausalac_kubika = 0;
        }
        $korisnik->save();

        return redirect()->route('korisnik', ['id' => $data['id']]);
    }
}
