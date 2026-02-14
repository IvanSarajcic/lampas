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
            $this->log[] = "No font defined for: " . mb_strtoupper($char);
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

        $outDir = __DIR__ . '/out';
        if (!is_dir($outDir)) {
            mkdir($outDir, 0755, true);
        }
        file_put_contents($outDir . '/' . $filename . '.svg', $img);
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
        $this->settings = json_decode(file_get_contents(__DIR__ . '/settings.svg.json'), true);
        $this->font = json_decode(file_get_contents(__DIR__ . '/font.json'), true);
    }

    private $settings;
    private $font;
    private $text = [];
    private $log = [];
    private $segments = [];
}

// --- Handle form submission ---

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$rows = [];
for ($i = 1; $i <= 8; $i++) {
    $rows[] = isset($_POST['row' . $i]) ? mb_substr(trim($_POST['row' . $i]), 0, 26) : '';
}

$l = new LampasSvg();
$l->setText($rows);

// Generate a safe filename from text content
$svgOut = mb_strtoupper(implode('', array_map('trim', $rows)));
$svgOut = preg_replace('/[^A-Z0-9ČĆĐŠŽ\-]/u', '', $svgOut);
$svgOut = transliterator_transliterate('Any-Latin; Latin-ASCII', $svgOut);
$svgOut = preg_replace('/[^A-Z0-9\-]/', '', $svgOut);
if (strlen($svgOut) === 0) $svgOut = 'lampas_' . time();
$svgOut = substr($svgOut, 0, 64);

$l->makeImage($svgOut);
$logMessages = $l->getLog();
$svgFile = 'out/' . $svgOut . '.svg';

?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lampaš - Rezultat</title>
    <link rel="stylesheet" href="css/lampas.css" type="text/css" media="screen">
    <style>
        .result-wrap { text-align: center; margin: 2rem 0; }
        .result-wrap img { max-width: 100%; height: auto; border: 1px solid #333; }
        .actions { margin: 1.5rem 0; text-align: center; }
        .actions a {
            display: inline-block;
            padding: 0.5rem 1.5rem;
            margin: 0 0.5rem;
            color: #fff3a7;
            background-color: #404545;
            text-decoration: none;
            font-size: 1rem;
        }
        .actions a:hover { background-color: #575d5d; }
        .actions a.disabled { opacity: 0.4; pointer-events: none; }
        .log { color: #6d6b67; font-size: 0.85rem; margin-top: 1rem; }
        canvas { display: none; }
    </style>
</head>
<body>
    <div class="actions">
        <a href="index.php">&larr; NAZAD</a>
        <a href="<?= htmlspecialchars($svgFile) ?>" download="<?= htmlspecialchars($svgOut) ?>.svg">PREUZMI SVG</a>
        <a href="#" id="btn-png" class="disabled">PREUZMI PNG</a>
    </div>

    <div class="result-wrap">
        <img id="svg-img" src="<?= htmlspecialchars($svgFile) ?>" alt="Lampaš">
        <canvas id="png-canvas"></canvas>
    </div>

<?php if (!empty($logMessages)): ?>
    <div class="log">
        <?php foreach ($logMessages as $msg): ?>
            <p><?= htmlspecialchars($msg) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<script>
(function() {
    var MAX_PNG_WIDTH = 1000;
    var svgImg = document.getElementById('svg-img');
    var canvas  = document.getElementById('png-canvas');
    var btnPng  = document.getElementById('btn-png');
    var pngName = <?= json_encode($svgOut) ?>;
    var pngDataUrl = null;

    svgImg.addEventListener('load', function() {
        // Read intrinsic SVG dimensions from the loaded image
        var natW = svgImg.naturalWidth;
        var natH = svgImg.naturalHeight;
        if (!natW || !natH) return;

        // Scale to max width 1000px, keep aspect ratio
        var scale = Math.min(1, MAX_PNG_WIDTH / natW);
        var w = Math.round(natW * scale);
        var h = Math.round(natH * scale);

        canvas.width  = w;
        canvas.height = h;

        var ctx = canvas.getContext('2d');
        ctx.drawImage(svgImg, 0, 0, w, h);

        try {
            pngDataUrl = canvas.toDataURL('image/png');
            btnPng.classList.remove('disabled');
        } catch(e) {
            // CORS or tainted canvas — fall back gracefully
            console.warn('PNG export failed:', e);
        }
    });

    btnPng.addEventListener('click', function(e) {
        e.preventDefault();
        if (!pngDataUrl) return;
        var a = document.createElement('a');
        a.href = pngDataUrl;
        a.download = pngName + '.png';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });
})();
</script>

</body>
</html>
