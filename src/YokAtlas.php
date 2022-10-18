<?php
/**
 * Developer: ONUR KAYA
 * Contact: empatisoft@gmail.com
 */

namespace Empatisoft;

use PHPHtmlParser\Dom;
use PHPHtmlParser\Exceptions\ChildNotFoundException;
use PHPHtmlParser\Exceptions\CircularException;
use PHPHtmlParser\Exceptions\ContentLengthException;
use PHPHtmlParser\Exceptions\LogicalException;
use PHPHtmlParser\Exceptions\NotLoadedException;
use PHPHtmlParser\Exceptions\StrictException;

class YokAtlas {

    /**
     * @var string
     */
    private string $url = 'https://yokatlas.yok.gov.tr/';

    /**
     * @var int
     * ÖSYM Program Kodu
     */
    private int $program;

    /**
     * @var int
     * Raporu alınacak olan yıl
     */
    private int $year;

    /**
     * @var int
     * ÖSYM Üniversite Kodu
     */
    private int $university;


    private function getPath() {
        return ($this->year == (date('Y') - 1)) || $this->year >= date('Y') ? '' : $this->year.'/';
    }

    /**
     * @return array
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Üniversiteye ait olan tüm programları listeler.
     */
    public function getPrograms(): array {
        $result = [];
        $programs = getRequest($this->url.'lisans-univ.php?u='.$this->university, [], false);
        $dom = new Dom;
        $dom->loadStr($programs);
        $links = $dom->find('a');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos($href, 'lisans.php?y=') !== false) {
                $code = str_replace('lisans.php?y=', '', $href);
                preg_match('/<div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;width:80%">(.*)<\/div> /', $link->innerHtml, $program);
                $result[$code] = $program[1];
            }
        }
        return $result;
    }

    /**
     * @return array
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * ÖSYM Program Kodu belirlenen, programa ait tüm verileri çeker.
     */
    public function getProgram(): array
    {
        return array_merge(
            $this->getProgramInfo(),
            $this->getGenders(),
            $this->getArea(),
            $this->getCities(),
            $this->getEducationStatus(),
            $this->getGraduationYears(),
            $this->getGraduationArea(),
            $this->getSchoolTypes(),
            $this->getSchools(),
            $this->getBasePointStatistics(),
            $this->getLastStudent(),
            $this->getYksNetAverages(),
            $this->getYksPoints(),
            $this->getYksPositions(),
            $this->getPreferredOnCountry(),
            $this->getAveragePreferencePositions(),
            $this->getPreferenceTrends()
        );
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Yerleşenlerin Tercih Eğilimleri
     */
    private function getPreferenceTrends(): array
    {
        // Tercih Eğilimleri - Genel
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1300.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $row) {
                    $result['GENEL'][$row[0]] = $row[2] ?? null;
                    unset($value);
                }
            }
        }
        unset($program, $dom, $table1);

        // Tercih Eğilimleri - Üniversite Türleri
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1310.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $row) {
                    $result['UNIVERSITE_TIPLERI'][$row[0]] = isset($row[2]) ? (int)$row[2] : 0;
                    unset($value);
                }
            }
        }
        unset($program, $dom, $table1);

        // Tercih Eğilimleri - Üniversiteler
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1320.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                $type = $table1[0][0] ?? 'DEVLET';
                foreach ($table1 as $index => $row) {
                    if($index > 1) {
                        $result['UNIVERSITELER'][$type][$row[0]] = isset($row[2]) ? (int)$row[2] : 0;
                    }
                }
                unset($type);
            }
        }
        unset($table1);

        $table1 = $dom->find('table')[1]->innerHtml;
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                $type = $table1[0][0] ?? 'DEVLET';
                foreach ($table1 as $index => $row) {
                    if($index > 1) {
                        $result['UNIVERSITELER'][$type][$row[0]] = isset($row[2]) ? (int)$row[2] : 0;
                    }
                }
                unset($type);
            }
        }
        unset($table1);

        $table1 = $dom->find('table')[2]->innerHtml;
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                $type = $table1[0][0] ?? 'DEVLET';
                foreach ($table1 as $index => $row) {
                    if($index > 1) {
                        $result['UNIVERSITELER'][$type][$row[0]] = isset($row[2]) ? (int)$row[2] : 0;
                    }
                }
                unset($type);
            }
        }
        unset($table1);

        $table1 = $dom->find('table')[3]->innerHtml;
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                $type = $table1[0][0] ?? 'DEVLET';
                foreach ($table1 as $index => $row) {
                    if($index > 1) {
                        $result['UNIVERSITELER'][$type][$row[0]] = isset($row[2]) ? (int)$row[2] : 0;
                    }
                }
                unset($type);
            }
        }
        unset($table1);

        // Tercih Eğilimleri - İller
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1330.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 0) {
                        $result['ILLER'][$row[0]] = isset($row[2]) ? (int)$row[2] : 0;
                    }
                }
                unset($type);
            }
        }
        unset($table1);

        // Tercih Eğilimleri - Aynı/Farklı Program
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1340a.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 0) {
                        $result['PROGRAM'][str_replace('"', '', $row[0])] = isset($row[2]) ? (int)$row[2] : 0;
                    }
                }
                unset($type);
            }
        }
        unset($table1);

        // Tercih Eğilimleri - Meslekler
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1340b.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 0) {
                        $result['MESLEKLER'][str_replace('"', '', $row[0])] = isset($row[2]) ? (int)$row[2] : 0;
                    }
                }
                unset($type);
            }
        }
        unset($table1);

        unset($program, $dom);

        return [
            'YERLESENLERIN_TERCIH_EGILIMLERI' => $result
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Yerleşenler ortalama kaçıncı tercihlerine yerleşti?
     */
    private function getAveragePreferencePositions(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1040.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $table2 = $dom->find('table')[1]->innerHtml;

        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $row) {
                    $value = $row[2] ?? null;
                    if(isset($row[4]))
                        $value .= ' ('.$row[4].')';

                    $result['BILGI'][$row[0]] = $value;
                    unset($value);
                }
            }
        }
        if($table2 != null) {
            $table2 = parseTable($table2);
            if(!empty($table2)) {
                foreach ($table2 as $index => $row) {
                    if($index > 1 && $index != 14) {
                        $result['TERCIH_SIRASI'][$row[0]] = isset($row[2]) ? (int)$row[2] : 0;
                    }
                }
            }
        }
        return [
            'YERLESENLER_ORTALAMA_KACINCI_TERCIHLERINE_YERLESTI' => $result
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Ülke Genelinde Tercih Edilme
     */
    private function getPreferredOnCountry(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1080.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $table2 = $dom->find('table')[1]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $row) {
                    $value = $row[2] ?? null;
                    if(isset($row[4]))
                        $value .= ' ('.$row[4].')';

                    $result['BILGI'][$row[0]] = $value;
                    unset($value);
                }
            }
        }
        if($table2 != null) {
            $table2 = parseTable($table2);
            if(!empty($table2)) {
                foreach ($table2 as $index => $row) {
                    if($index > 0) {
                        $result['TERCIH_SIRASI'][$row[0]] = isset($row[2]) ? (int)$row[2] : 0;
                    }
                }
            }
        }
        return [
            'ULKE_GENELINDE_TERCIH_EDILME' => $result
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Yerleşenlerin YKS Başarı Sıraları
     */
    private function getYksPositions(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1230.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $table2 = $dom->find('table')[1]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 0) {
                        $result['ORTALAMA'][] = [
                            'ACIKLAMA' => $row[0] ?? null,
                            '0_12_KATSAYI_ILE' => $row[2] ?? null,
                            '0_12_0_06_KATSAYI_ILE' => $row[4] ?? null
                        ];
                    }
                }
            }
        }
        if($table2 != null) {
            $table2 = parseTable($table2);
            if(!empty($table2)) {
                foreach ($table2 as $index => $row) {
                    if($index > 0) {
                        $result['EN_DUSUK'][] = [
                            'ACIKLAMA' => $row[0] ?? null,
                            '0_12_KATSAYI_ILE' => $row[2] ?? null,
                            '0_12_0_06_KATSAYI_ILE' => $row[4] ?? null
                        ];
                    }
                }
            }
        }
        return [
            'YKS_BASARI_SIRALARI' => $result
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Yerleşenlerin YKS Puanları
     */
    private function getYksPoints(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1220.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $table2 = $dom->find('table')[1]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 0) {
                        $result['ORTALAMA_PUAN'][] = [
                            'ACIKLAMA' => $row[0] ?? null,
                            '0_12_KATSAYI_ILE' => $row[2] ?? null,
                            '0_12_0_06_KATSAYI_ILE' => $row[4] ?? null
                        ];
                    }
                }
            }
        }
        if($table2 != null) {
            $table2 = parseTable($table2);
            if(!empty($table2)) {
                foreach ($table2 as $index => $row) {
                    if($index > 0) {
                        $result['EN_DUSUK_PUANLAR'][] = [
                            'ACIKLAMA' => $row[0] ?? null,
                            '0_12_KATSAYI_ILE' => $row[2] ?? null,
                            '0_12_0_06_KATSAYI_ILE' => $row[4] ?? null
                        ];
                    }
                }
            }
        }
        return [
            'YKS_PUANLARI' => $result
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * YKS Net Ortalamaları
     */
    private function getYksNetAverages(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1210a.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 0) {
                        $result[] = [
                            'ACIKLAMA' => $row[0] ?? null,
                            '0_12_KATSAYI_ILE' => $row[2] ?? null,
                            '0_12_0_06_KATSAYI_ILE' => $row[4] ?? null
                        ];
                    }
                }
            }
        }
        return [
            'YKS_NET_ORTALAMALARI' => $result
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Yerleşen Son Kişi Profili
     */
    private function getLastStudent(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1070.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $row) {
                    $result[$row[0]] = $row[2] ?? null;
                }
            }
        }
        return [
            'YERLESEN_SON_KISI_PROFILI' => $result
        ];
    }

    /**
     * @return array
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Taban Puan ve Başarı Sırası İstatistikleri
     */
    private function getBasePointStatistics(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1000_3.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $table2 = $dom->find('table')[1]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 0) {
                        $result['TABAN_PUAN'][] = [
                            'TIP' => $row[0] ?? null,
                            'KONTENJAN' => isset($row[2]) ? (int)$row[2] : 0,
                            'YERLESEN_SAYISI' => isset($row[4]) ? (int)$row[4] : 0,
                            'SON_KISI_PUANI' => $row[6] ?? null
                        ];
                    }
                }
            }
        }
        if($table2 != null) {
            $table2 = parseTable($table2);
            if(!empty($table2)) {
                foreach ($table2 as $index => $row) {
                    if($index > 1) {
                        $result['BASARI_SIRASI'][] = [
                            'TIP' => $row[0] ?? null,
                            'KONTENJAN' => isset($row[2]) ? (int)$row[2] : 0,
                            'YERLESEN_SAYISI' => isset($row[4]) ? (int)$row[4] : 0,
                            'SON_KISI_SIRASI_0_12' => $row[6] ?? null,
                            'SON_KISI_SIRASI_0_12_0_06' => $row[8] ?? null
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Mezun olunan okullar
     */
    private function getSchools(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1060.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 2) {
                        $result[] = [
                            'OKUL' => $row[0] ?? null,
                            'TOPLAM' => isset($row[2]) ? (int)$row[2] : 0,
                            'YENI_MEZUN' => isset($row[4]) ? (int)$row[4] : 0,
                            'ONCEKI_MEZUN' => isset($row[6]) ? (int)$row[6] : 0
                        ];
                    }
                }
            }
        }

        return [
            'MEZUN_OLDUKLARI_OKULLAR' => $result
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Mezun olunan okul türleri
     */
    private function getSchoolTypes(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1050a.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $table2 = $dom->find('table')[1]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 1) {
                        $result['GENEL_LISELER'][] = [
                            'TIP' => $row[0] ?? null,
                            'SAYI' => isset($row[2]) ? (int)$row[2] : 0,
                            'ORAN' => $row[4] ?? 0
                        ];
                    }
                }
            }
        }
        if($table2 != null) {
            $table2 = parseTable($table2);
            if(!empty($table2)) {
                foreach ($table2 as $index => $row) {
                    if($index > 1) {
                        $result['MESLEK_LISELERI'][] = [
                            'TIP' => $row[0] ?? null,
                            'SAYI' => isset($row[2]) ? (int)$row[2] : 0,
                            'ORAN' => $row[4] ?? 0
                        ];
                    }
                }
            }
        }

        return [
            'LISE_TIPLERI' => $result
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Liseden mezun oldukları bölümlere göre
     */
    private function getGraduationArea(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1050b.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 1) {
                        $result[] = [
                            'ALAN' => $row[0] ?? null,
                            'SAYI' => isset($row[2]) ? (int)$row[2] : 0,
                            'ORAN' => $row[4] ?? 0
                        ];
                    }
                }
            }
        }

        return [
            'LISEDEN_MEZUNIYET_ALANLARI' => $result
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Yerleşenlerin Liseden mezuniyet yılları
     */
    private function getGraduationYears(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1030b.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 1) {
                        $result[] = [
                            'YIL' => isset($row[0]) ? (int)$row[0] : 0,
                            'SAYI' => isset($row[2]) ? (int)$row[2] : 0,
                            'ORAN' => $row[4] ?? 0
                        ];
                    }
                }
            }
        }

        return [
            'LISEDEN_MEZUNIYET_YILLARI' => $result
        ];
    }

    /**
     * @return array
     * Genel Program Bilgisi
     */
    private function getProgramInfo(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1000_1.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $tables = $dom->find('table');

        $table1 = $tables[0]->getChildren();
        $table2 = $tables[1]->getChildren();
        $table3 = $tables[2]->getChildren();

        preg_match('/<tr><td class="thb text-left" width="50%">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> /', $table1[3]->innerHtml, $table1Properties);

        preg_match('/<tr><td class="thb text-left" width="50%">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left"><strong>(.*)<\/strong><\/td><td class="text-center vert-align"><strong>(.*)<\/strong><\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left"><strong>(.*)<\/strong><\/td><td class="text-center vert-align"><strong>(.*)<\/strong><\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> /', $table2[1]->innerHtml, $table2Properties);

        preg_match('/<tr><td class="thb text-left" width="50%">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left" width="50%">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr> <tr><td class="thb text-left">(.*)<\/td><td class="text-center vert-align">(.*)<\/td><\/tr>/', $table3[1]->innerHtml, $table3Properties);

        return [
            'PROGRAM_ADI' => trim(preg_replace('/ <tr> <th class="thb text-center" colspan="2"><big>(.*)<\/big><\/th> <\/tr>/', '$1', $table1[1]->innerHtml)),
            'PROGRAM_OSYM_KODU' => $table1Properties[2],
            'UNIVERSITE_TURU' => $table1Properties[4],
            'UNIVERSITE' => $table1Properties[6],
            'FAKULTE' => $table1Properties[8],
            'PUAN_TURU' => $table1Properties[10],
            'BURS_TURU' => $table1Properties[12],
            'GENEL_KONTENJAN' => $table2Properties[2],
            'OKUL_BIRINCISI_KONTENJANI' => $table2Properties[4],
            'TOPLAM_KONTENJAN' => $table2Properties[6],
            'GENEL_KONTENJANA_YERLESEN' => $table2Properties[8],
            'OKUL_BIRINCISI_KONTENJANINA_YERLESEN' => $table2Properties[10],
            'TOPLAM_YERLESEN' => $table2Properties[12],
            'BOS_KALAN_KONTENJAN' => $table2Properties[14],
            'ILK_YERLESME_ORANI' => $table2Properties[16],
            'YERLESIP_KAYIT_YAPTIRMAYAN' => $table2Properties[18],
            'EK_YERLESEN' => $table2Properties[20],
            '0_12_KATSAYI_ILE_YERLESEN_SON_KISININ_PUANI' => $table3Properties[2],
            '0_12_0_06_KATSAYI_ILE_YERLESEN_SON_KISININ_PUANI' => $table3Properties[4],
            '0_12_KATSAYI_ILE_YERLESEN_SON_KISININ_BASARI_SIRASI' => $table3Properties[6],
            '0_12_0_06_KATSAYI_ILE_YERLESEN_SON_KISININ_BASARI_SIRASI' => $table3Properties[8],
            '0_12_TAVAN_PUAN' => $table3Properties[10],
            '0_12_TAVAN_BASARI_SIRASI' => $table3Properties[12],
            'ONCEKI_YIL_YERLESIP_OBP_KIRILARAK_YERLESEN_SAYISI' => $table3Properties[14],
            'YERLESENLERIN_ORTALAMA_OBP' => $table3Properties[16],
            'YERLESENLERIN_ORTALAMA_DIPLOMA_NOTU' => $table3Properties[18]
        ];
    }

    /**
     * @return array
     * Cinsiyet Dağılımı
     */
    private function getGenders(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1010.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $data = $dom->find('table')[0]->innerHtml;
        preg_match('/<thead> <tr> <th style="border:none;"><\/th> <th class="thb text-center" width="33%">Sayı<\/th> <th class="thb text-center" width="33%">% Oran<\/th> <\/tr> <\/thead> <tbody> <tr> <td class="thb text-center">&nbsp;(.*)<\/td> <td class="text-center vert-align">(.*)<\/td> <td class="text-center vert-align">(.*)<\/td> <\/tr> <tr> <td class="thb text-center">&nbsp;(.*)<\/td> <td class="text-center vert-align">(.*)<\/td> <td class="text-center vert-align">(.*)<\/td> <\/tr> <\/tbody> /', $data, $genders);
        return [
            'KIZ_OGRENCI_SAYISI' => isset($genders[2]) ? (int)$genders[2] : 0,
            'KIZ_OGRENCI_ORANI' => $genders[3],
            'ERKEK_OGRENCI_SAYISI' => isset($genders[5]) ? (int)$genders[5] : 0,
            'ERKEK_OGRENCI_ORANI' => $genders[6]
        ];
    }

    /**
     * @return array
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Bölge Raporu
     */
    private function getArea(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1020ab.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);

        $table1 = $dom->find('table')[0]->innerHtml;
        $table2 = $dom->find('table')[1]->innerHtml;

        preg_match('/<thead> <tr> <th style="border:none;"><\/th> <th class="thb" width="20%" style="text-align:center;">(.*)<\/th> <th class="thb" width="20%" style="text-align:center;">% Oran<\/th> <th class="thb" width="30%" style="text-align:center;">(.*)<\/th> <\/tr> <\/thead> <tbody> <tr> <td class="thr text-left">(.*)<\/td> <td class="tdr text-center">(.*)<\/td> <td class="tdr text-center">(.*)<\/td> <td class="tdr text-center">(.*)<\/td> <\/tr> <tr> <td class="thb text-left">(.*)<\/td> <td class="text-center">(.*)<\/td> <td class="text-center">(.*)<\/td> <td class="text-center">(.*)<\/td> <\/tr> <tr> <td class="thb text-left">(.*)<\/td> <td class="text-center">(.*)<\/td> <td class="text-center">(.*)<\/td> <td class="text-center">(.*)<\/td> <\/tr> <\/tbody>/', $table1, $city);

        $area = [];
        if($table2 != null) {
            $table2 = parseTable($table2);
            if(!empty($table2)) {
                foreach ($table2 as $index => $row) {
                    if($index > 1) {
                        $area[] = [
                            'BOLGE' => $row[0] ?? null,
                            'SAYI' => isset($row[2]) ? (int)$row[2] : 0,
                            'ORAN' => $row[4] ?? 0
                        ];
                    }
                }
            }
        }

        return [
            'AYNI_SEHIR' => [
                'TOPLAM_YERLESEN_SAYISI' => isset($city[4]) ? (int)$city[4] : 0,
                'TOPLAM_YERLESEN_ORANI' => $city[5],
                'TOPLAM_YERLESEN_CINSIYET' => $city[6],
                'AYNI_SEHIR_YERLESEN_SAYISI' => isset($city[8]) ? (int)$city[8] : 0,
                'AYNI_SEHIR_YERLESEN_ORANI' => $city[9],
                'AYNI_SEHIR_YERLESEN_CINSIYET' => $city[10],
                'FARKLI_SEHIR_YERLESEN_SAYISI' => isset($city[12]) ? (int)$city[12] : 0,
                'FARKLI_SEHIR_YERLESEN_ORANI' => $city[13],
                'FARKLI_SEHIR_YERLESEN_CINSIYET' => $city[14]
            ],
            'BOLGELER' => $area
        ];
    }

    /**
     * @return array
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Şehir Raporu
     */
    private function getCities(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1020c.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);

        $table1 = $dom->find('table')[0]->innerHtml;

        $cities = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 1) {
                        $cities[] = [
                            'SEHIR' => $row[0] ?? null,
                            'SAYI' => isset($row[2]) ? (int)$row[2] : 0,
                            'ORAN' => $row[4] ?? 0
                        ];
                    }
                }
            }
        }

        return [
            'SEHIRLER' => $cities
        ];
    }

    /**
     * @return array[]
     * @throws ChildNotFoundException
     * @throws CircularException
     * @throws ContentLengthException
     * @throws LogicalException
     * @throws NotLoadedException
     * @throws StrictException
     * Yerleşenlerin Öğrenim Durumları
     */
    private function getEducationStatus(): array
    {
        $program = getRequest($this->url.$this->getPath().'content/lisans-dynamic/1030a.php?y='.$this->program, [], false);
        $dom = new Dom;
        $dom->loadStr($program);
        $table1 = $dom->find('table')[0]->innerHtml;
        $result = [];
        if($table1 != null) {
            $table1 = parseTable($table1);
            if(!empty($table1)) {
                foreach ($table1 as $index => $row) {
                    if($index > 1) {
                        $result[] = [
                            'DURUM' => $row[0] ?? null,
                            'SAYI' => isset($row[2]) ? (int)$row[2] : 0,
                            'ORAN' => $row[4] ?? 0
                        ];
                    }
                }
            }
        }

        return [
            'OGRENIM_DURUMLARI' => $result
        ];
    }

    /**
     * @param int $program
     */
    public function setProgram(int $program): void
    {
        $this->program = $program;
    }

    /**
     * @param int $year
     */
    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    /**
     * @param int $university
     */
    public function setUniversity(int $university): void
    {
        $this->university = $university;
    }

}
