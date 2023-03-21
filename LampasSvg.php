<?php

class LampasSvg {

    public function makeImage($filename) {
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

    private function drawChar($startRow, $startCol, $char) {
        $x = $this->settings['margin-left'] + $startCol * $this->settings['segment-width'];
        $y = $this->settings['margin-top'] + $startRow * $this->settings['segment-height'];

        $pixels = [];
        // draw pixels
        if (!isset($this->font[mb_strtoupper($char)])) {
            $this->log[] = "<p>No font defined for: " . mb_strtoupper($char) . "</p>";
        } else {
            $font = $this->font[mb_strtoupper($char)];
            for ($row=0; $row<7; $row++)
                for ($col=0; $col<5; $col++) {
                    $pixel = isset($font[$row][$col]) ? $font[$row][$col] : 0;
                    $pixels[] = $this->parse(
                        'svg.pattern.pixel',
                        [
                            'cls' => $pixel ? 'pixel-on' : 'pixel-off',
                            'x' => $x + $this->settings['segment-circle-start-x'] + $col * $this->settings['segment-circle-offset-x'],
                            'y' => $y + $this->settings['segment-circle-start-y'] + $row * $this->settings['segment-circle-offset-y'],
                            'r' => $pixel ? $this->settings['segment-circle-on-radius'] : $this->settings['segment-circle-radius']
                        ]
                    );
                }
        }

        $this->segments[] = $this->parse(
            'svg.pattern.segment',
            [
                //<rect class="segment" x="{{x}}" y="{{y}}" width="{{w}}" height="{{h}}" />
                'x' => $x + $this->settings['segment-rect-offset-x'],
                'y' => $y + $this->settings['segment-rect-offset-y'],
                'w' => $this->settings['segment-rect-width'],
                'h' => $this->settings['segment-rect-height'],
                'pixels' => implode("\n", $pixels),
            ]
        );
    }

    private function saveImg($filename) {
        $img = $this->parse(
            'svg.pattern.wrapper',
            [
                'w' => $this->settings['margin-left'] + $this->settings['margin-right'] + $this->settings['total-cols'] * $this->settings['segment-width'],
                'h' => $this->settings['margin-top'] + $this->settings['margin-bottom'] + $this->settings['total-rows'] * $this->settings['segment-height'],
                'bg' => $this->settings['bg-color'],
                'segment-bg' => $this->settings['segment-rect-bg-color'],
                'pixel-off-bg' => $this->settings['segment-circle-off-bg-color'],
                'pixel-on-bg' => $this->settings['segment-circle-on-bg-color'],
                'segments' => implode("\n", $this->segments),
            ]
        );
        file_put_contents (__DIR__ . '/out/' . $filename . '.svg', $img);
    }

    private function parse($filename, $replacements) {
        $txt = file_get_contents($filename);
        foreach ($replacements as $k => $v)
            $txt = str_replace('{{'.$k.'}}', $v, $txt);
        return $txt;
    }

    public function setText($text = []) {
        $this->text = $text;
        return $this;
    }

    public function getLog() {
        return $this->log;
    }

    public function __construct() {
        $this->settings = json_decode(file_get_contents('settings.svg.json'), true);
        $this->font = json_decode(file_get_contents('font.json'), true);
    }

    private $settings;
    private $font;
    private $text = [];
    private $log = [];
    private $segments = [];
}

$l = new LampasSvg();
$l->setText([
    $_POST['row1'],
    $_POST['row2'],
    $_POST['row3'],
    $_POST['row4'],
    $_POST['row5'],
    $_POST['row6'],
    $_POST['row7'],
    $_POST['row8']
]);

$svgOut = strtoupper(trim($_POST['row3'].$_POST['row4'].$_POST['row5'].$_POST['row6'].$_POST['row7'].$_POST['row8']));
$svgOut = preg_replace('/[^A-Z0-9ČĆĐŠŽ-]/', '', $svgOut);
$svgOut = iconv("UTF-8", "ASCII//TRANSLIT", $svgOut);
$svgOut = str_replace("'","",$svgOut);

if (strlen($svgOut)===0) $svgOut="empty_" . time();
echo($svgOut)."<br>";
$l->makeImage($svgOut);
echo implode("\n", $l->getLog());
echo "<img height=456 src=out/" .$svgOut. ".svg>";
exit(0);
