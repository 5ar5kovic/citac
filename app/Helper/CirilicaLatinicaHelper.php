<?php


namespace App\Helper;


class CirilicaLatinicaHelper
{

    private $latSaNasimSlovima;
    private $latBezNasihSlova;

    public $conv_array;
    public $conv_array_sms;


    function __construct(){
        $this->latSaNasimSlovima=array(
            "Џ"=>"Dž",
            "Њ"=>"Nj",
            "Љ"=>"Lj",
            "А"=>"A",
            "Б"=>"B",
            "Ц"=>"C",
            "Д"=>"D",
            "Ђ"=>"Đ",
            "Е"=>"E",
            "Ф"=>"F",
            "Г"=>"G",
            "Х"=>"H",
            "И"=>"I",
            "Ј"=>"J",
            "К"=>"K",
            "Л"=>"L",
            "М"=>"M",
            "Н"=>"N",
            "О"=>"O",
            "П"=>"P",
            "Р"=>"R",
            "С"=>"S",
            "Т"=>"T",
            "Ћ"=>"Ć",
            "У"=>"U",
            "В"=>"V",
            "Ч"=>"Č",
            "Ш"=>"Š",
            "З"=>"Z",
            "Ж"=>"Ž",
            "љ"=>"lj",
            "њ"=>"nj",
            "џ"=>"dž",
            "а"=>"a",
            "б"=>"b",
            "ц"=>"c",
            "д"=>"d",
            "ђ"=>"đ",
            "е"=>"e",
            "ф"=>"f",
            "г"=>"g",
            "х"=>"h",
            "и"=>"i",
            "ј"=>"j",
            "к"=>"k",
            "л"=>"l",
            "м"=>"m",
            "н"=>"n",
            "о"=>"o",
            "п"=>"p",
            "р"=>"r",
            "с"=>"s",
            "ћ"=>"ć",
            "т"=>"t",
            "у"=>"u",
            "в"=>"v",
            "з"=>"z",
            "ж"=>"ž",
            "ч"=>"č",
            "ш"=>"š",
        );

        $this->latBezNasihSlova=array(
            "Џ"=>"Dz",
            "Њ"=>"Nj",
            "Љ"=>"Lj",
            "А"=>"A",
            "Б"=>"B",
            "Ц"=>"C",
            "Д"=>"D",
            "Ђ"=>"Dj",
            "Е"=>"E",
            "Ф"=>"F",
            "Г"=>"G",
            "Х"=>"H",
            "И"=>"I",
            "Ј"=>"J",
            "К"=>"K",
            "Л"=>"L",
            "М"=>"M",
            "Н"=>"N",
            "О"=>"O",
            "П"=>"P",
            "Р"=>"R",
            "С"=>"S",
            "Т"=>"T",
            "Ћ"=>"C",
            "У"=>"U",
            "В"=>"V",
            "Ч"=>"C",
            "Ш"=>"S",
            "З"=>"Z",
            "Ж"=>"Z",
            "љ"=>"lj",
            "њ"=>"nj",
            "џ"=>"dz",
            "а"=>"a",
            "б"=>"b",
            "ц"=>"c",
            "д"=>"d",
            "ђ"=>"dj",
            "е"=>"e",
            "ф"=>"f",
            "г"=>"g",
            "х"=>"h",
            "и"=>"i",
            "ј"=>"j",
            "к"=>"k",
            "л"=>"l",
            "м"=>"m",
            "н"=>"n",
            "о"=>"o",
            "п"=>"p",
            "р"=>"r",
            "с"=>"s",
            "ћ"=>"c",
            "т"=>"t",
            "у"=>"u",
            "в"=>"v",
            "з"=>"z",
            "ж"=>"z",
            "ч"=>"c",
            "ш"=>"s",
        );
    }

    public function stringToLat($str){
        $newstr=$str;
        foreach ($this->latBezNasihSlova as $cyrchar=>$latchar)
            $newstr=str_replace($cyrchar,$latchar,$newstr);
        return $newstr;
    }

    public function stringToCyr($str){
        $newstr=$str;//substr($str,1,strlen($str)-2);
        foreach ($this->latSaNasimSlovima as $cyrchar=>$latchar)
            $newstr=str_replace($latchar,$cyrchar,$newstr);
        return $newstr;
    }


}
