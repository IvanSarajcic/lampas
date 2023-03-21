<?php
//Raster version
class Lampas {

    public function makeImage($filename) {
        $this->initImg();
        for ($row = 0; $row < $this->settings['total-rows']; $row++)
            for ($col = 0; $col < $this->settings['total-cols']; $col++)
                $this->drawChar(
                    $row,
                    $col,
                    isset($this->text[$row]) && mb_strlen($this->text[$row]) > $col
                        ? mb_substr($this->text[$row], $col, 1)
                        : " "
                );
        $this->saveImg($filename);
    }

    private function initImg() {
        $this->img = new Imagick();
        $this->img->newImage(
            $this->settings['total-cols'] * $this->settings['segment-width'],
            $this->settings['total-rows'] * $this->settings['segment-height'],
            new ImagickPixel('black')
        );
        $this->img->setImageFormat('png');
    }

    private function drawChar($startRow, $startCol, $char) {
        $x = $startCol * $this->settings['segment-width'];
        $y = $startRow * $this->settings['segment-height'];

        // draw empty segment
        $this->img->compositeImage(
            $this->segmentImg,
            imagick::COMPOSITE_ATOP,
            $x,
            $y
        );

        // draw pixels
        if (!isset($this->font[mb_strtoupper($char)])) {
            $this->log[] = "No font defined for: " . mb_strtoupper($char);
            return;
        } else {
            $font = $this->font[mb_strtoupper($char)];
            for ($row=0; $row<7; $row++)
                for ($col=0; $col<5; $col++) {
                    $pixel = isset($font[$row][$col]) ? $font[$row][$col] : 0;
                    if ($pixel)
                        $this->img->compositeImage(
                            $this->pixelImg,
                            imagick::COMPOSITE_ATOP,
                            $x + $this->settings['lamps-xcords'][$col],
                            $y + $this->settings['lamps-ycords'][$row]
                        );
                }
        }
    }

    private function saveImg($filename) {
        file_put_contents (__DIR__ . '/out/' . $filename . '.png', $this->img->getImageBlob());
    }

    public function setText($text = []) {
        $this->text = $text;
        return $this;
    }

    public function getLog() {
        return $this->log;
    }

    public function __construct() {
        $this->settings = json_decode(file_get_contents('settings.json'), true);
        $this->font = json_decode(file_get_contents('font.json'), true);
        $this->segmentImg = new Imagick();
        $this->segmentImg->readImage(__DIR__ . '/' . $this->settings['segment-img']);
        $this->pixelImg = new Imagick();
        $this->pixelImg->readImage(__DIR__ . '/' . $this->settings['lamp-img']);
    }

    /** @var Imagick */
    private $img;
    /** @var Imagick */
    private $segmentImg;
    /** @var Imagick */
    private $pixelImg;
    private $settings;
    private $font;
    private $text = [];
    private $log = [];
}

$l = new Lampas();
$l->setText([
    "", //"1234567890ABCDEFGHIJKL",
    "", //"  JA SAD ODOH U PENZIJU",
    "",//"", " A VI OSTAJTE UZ PARTIZAN",
    "", //"MNOPRSTUVYZČĆŠŽ,.*-",
    "", //"        VAŠ LAMPAŠ",
    "",
    "", //"  PARTIZAN - VARDAR 1957",
    "", //" PARTIZAN - RADNIČKI 2011",
]);
$l->makeImage('test1');
echo implode("\n", $l->getLog());
echo "\nOK.\n";
exit(0);
