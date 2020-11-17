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
            $prethodniMesec = $mesec;
            $prethodnaGodina = $godina;
            $this->getPrethodniMesec($prethodniMesec, $prethodnaGodina);
            $korisnik['prethodno_stanje'] = Stanje::getStanje($korisnik['id'], $prethodniMesec, $prethodnaGodina);

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

    public function pretraga(Request $request) {
        $data = $request->input();

        $cirilicaLatinicaHelper = new CirilicaLatinicaHelper();

        $korisnici = [];
        if ($request->isMethod('post')) {
            $korisnici = Korisnik::korisnikPoImenuIPrezimenu($cirilicaLatinicaHelper->stringToCyr($data['ime']), $cirilicaLatinicaHelper->stringToCyr($data['prezime']));
        }

        return view('pretraga')->with(array('korisnici'=>$korisnici));
    }

    public function korisnik(Request $request) {
        $data = $request->input();
        
        $korisnik = Korisnik::find($data['id'])->toArray();

        $potrosnje = Stanje::getStanjaKorisnika($data['id']);

        foreach($potrosnje as $i=>&$potrosnja) {
            $blokTarifa = 7 * $potrosnja['broj_clanova_domacinstva'];
            if(isset($potrosnje[$i+1]['stanje'])) {
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
        }

        return view('korisnik')->with(array('korisnik'=>$korisnik, 'potrosnje'=>$potrosnje));
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
        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(14.57);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15.86);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(11.86);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(12.14);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10.71);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(15.86);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(7.29);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(14.14);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(12.71);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(2.29);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(1.86);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(1.29);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(1.71);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('P')->setWidth(1.86);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setWidth(1.71);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('R')->setWidth(1.57);
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('S')->setWidth(2);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(1.86);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(2);
        $spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('V')->setWidth(1.71);
        $spreadsheet->getActiveSheet()->getColumnDimension('W')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('W')->setWidth(2);
        $spreadsheet->getActiveSheet()->getColumnDimension('X')->setAutoSize(false);
        $spreadsheet->getActiveSheet()->getColumnDimension('X')->setWidth(2.29);
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
            $idStanja = Stanje::getIdStanja($korisnik['id'], $data['mesec'], $data['godina']);
            if (isset($idStanja)) {
                $stanjeModel = Stanje::find($idStanja);
                $korisnik['datum_citanja'] = date('d.m.Y.', strtotime($stanjeModel->vreme_citanja));
            } else {
                $korisnik['datum_citanja'] = '';
            }

            //prethodno stanje
            $prethodniMesec = $data['mesec'];
            $prethodnaGodina = $data['godina'];
            $this->getPrethodniMesec($prethodniMesec, $prethodnaGodina);
            $korisnik['prethodno_stanje'] = Stanje::getStanje($korisnik['id'], $prethodniMesec, $prethodnaGodina);
            $idStanja = Stanje::getIdStanja($korisnik['id'], $prethodniMesec, $prethodnaGodina);
            if (isset($idStanja)) {
                $stanjePretModel = Stanje::find($idStanja);
                $korisnik['prethodni_datum_citanja'] = date('d.m.Y.', strtotime($stanjePretModel->vreme_citanja));
            } else {
                $korisnik['prethodni_datum_citanja'] = '';
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

        foreach($korisnici as $i=>$korisnik) {
            $colBr = $i + 3;
            $sheet->setCellValue('A'.$colBr, $i+1);
            $sheet->setCellValue('B'.$colBr, $korisnik['ime']);
            $sheet->setCellValue('C'.$colBr, $korisnik['prezime']);
            $sheet->setCellValue('D'.$colBr, $korisnik['sifra_objekta']);
            $sheet->setCellValue('E'.$colBr, $korisnik['broj_clanova_domacinstva']);
            $sheet->setCellValue('F'.$colBr, $korisnik['broj_vodomera']);
            $sheet->setCellValue('G'.$colBr, $korisnik['prethodni_datum_citanja']);
            $sheet->setCellValue('H'.$colBr, $korisnik['datum_citanja']);
            $sheet->setCellValue('I'.$colBr, $korisnik['prethodno_stanje']);
            $sheet->setCellValue('J'.$colBr, $korisnik['stanje']);
            $utroseno = 0;
            if ($korisnik['pausalac']) {
                $sheet->setCellValue('K'.$colBr, $korisnik['pausalac_kubika']);
            } else {
                if (isset($korisnik['stanje']) && isset($korisnik['prethodno_stanje'])) {
                    $utroseno = $korisnik['stanje'] - $korisnik['prethodno_stanje'];
                    $sheet->setCellValue('K'.$colBr, $utroseno);
                }
            }
            $sheet->setCellValue('Y'.$colBr, $korisnik['jmbg']);
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
