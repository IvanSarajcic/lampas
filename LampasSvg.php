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
        // Build content transform string from settings
        $transforms = [];
        $tx = $this->settings['content-translate-x'] ?? 0;
        $ty = $this->settings['content-translate-y'] ?? 0;
        if ($tx != 0 || $ty != 0) $transforms[] = "translate($tx,$ty)";
        $r = $this->settings['content-rotate'] ?? 0;
        if ($r != 0) $transforms[] = "rotate($r)";
        $sx = $this->settings['content-scale-x'] ?? 1;
        $sy = $this->settings['content-scale-y'] ?? 1;
        if ($sx != 1 || $sy != 1) $transforms[] = "scale($sx,$sy)";
        $skx = $this->settings['content-skew-x'] ?? 0;
        if ($skx != 0) $transforms[] = "skewX($skx)";
        $sky = $this->settings['content-skew-y'] ?? 0;
        if ($sky != 0) $transforms[] = "skewY($sky)";
        $contentTransform = !empty($transforms) ? implode(' ', $transforms) : '';

        // Determine SVG dimensions and background image
        $gridW = $this->settings['margin-left'] + $this->settings['margin-right'] + $this->settings['total-cols'] * $this->settings['segment-width'];
        $gridH = $this->settings['margin-top']  + $this->settings['margin-bottom'] + $this->settings['total-rows'] * $this->settings['segment-height'];
        $autoFitGrid = ($contentTransform === ''); // remember before we override

        $bgImage = '';
        $bgImagePath = $this->settings['background-image'] ?? '';
        $bgW = $this->settings['background-width'] ?? 0;
        $bgH = $this->settings['background-height'] ?? 0;

        if (!empty($bgImagePath) && file_exists(__DIR__ . '/' . $bgImagePath)) {
            $imgData = file_get_contents(__DIR__ . '/' . $bgImagePath);
            $ext = strtolower(pathinfo($bgImagePath, PATHINFO_EXTENSION));
            $mime = ($ext === 'png') ? 'image/png' : (($ext === 'webp') ? 'image/webp' : 'image/jpeg');
            $b64 = base64_encode($imgData);

            // Get actual image dimensions if not set
            if ($bgW <= 0 || $bgH <= 0) {
                $info = getimagesize(__DIR__ . '/' . $bgImagePath);
                if ($info) { $bgW = $info[0]; $bgH = $info[1]; }
            }

            // SVG matches photo dimensions when background is set
            $svgW = $bgW > 0 ? $bgW : $gridW;
            $svgH = $bgH > 0 ? $bgH : $gridH;

            $bgImage = '  <image width="' . $svgW . '" height="' . $svgH . '" href="data:' . $mime . ';base64,' . $b64 . '" />';

            // Auto-fit content to photo when no custom transform is set
            if ($autoFitGrid) {
                $scaleX = $svgW / $gridW;
                $scaleY = $svgH / $gridH;
                $fitScale = min($scaleX, $scaleY);
                $offX = ($svgW - $gridW * $fitScale) / 2;
                $offY = ($svgH - $gridH * $fitScale) / 2;
                $contentTransform = "translate($offX,$offY) scale($fitScale)";
            }
        } else {
            $svgW = $gridW;
            $svgH = $gridH;
        }

        $img = $this->parse(
            'svg.pattern.wrapper',
            [
                'w' => $svgW,
                'h' => $svgH,
                'bg' => $this->settings['bg-color'],
                'segment-bg' => $this->settings['segment-rect-bg-color'],
                'segment-opacity' => $this->settings['segment-rect-opacity'] ?? 1,
                'pixel-off-bg' => $this->settings['segment-circle-off-bg-color'],
                'pixel-on-bg' => $this->settings['segment-circle-on-bg-color'],
                'bg-image' => $bgImage,
                'content-transform' => $contentTransform,
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
        // Center each row horizontally
        $totalCols = $this->settings['total-cols'];
        foreach ($text as $i => $row) {
            $len = mb_strlen($row);
            if ($len < $totalCols) {
                $pad = intdiv($totalCols - $len, 2);
                $text[$i] = str_repeat(' ', $pad) . $row;
            }
        }
        $this->text = $text;
        return $this;
    }

    public function getLog() {
        return $this->log;
    }

    public function applyBackground($bgEntry) {
        if (!$bgEntry || empty($bgEntry['skew_data'])) return;
        $skew = $bgEntry['skew_data'];
        // Override transform and opacity settings with background-specific values
        foreach ($skew as $k => $v) {
            $this->settings[$k] = $v;
        }
        // Set background image from filename
        if (!empty($bgEntry['filename'])) {
            $this->settings['background-image'] = 'backgrounds/' . $bgEntry['filename'];
        } else {
            $this->settings['background-image'] = '';
        }
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

// Load background selection
$bgId = isset($_POST['bg_id']) ? $_POST['bg_id'] : 'none';
$backgrounds = json_decode(file_get_contents(__DIR__ . '/backgrounds.json'), true);
$selectedBg = null;
foreach ($backgrounds as $bg) {
    if ($bg['id'] === $bgId) { $selectedBg = $bg; break; }
}

$l = new LampasSvg();
if ($selectedBg) {
    $l->applyBackground($selectedBg);
}
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
    <style>canvas { display: none; }</style>
</head>
<body>
    <div class="actions">
        <a href="index.php">&larr; NAZAD</a>
        <a href="<?= htmlspecialchars($svgFile) ?>" download="<?= htmlspecialchars($svgOut) ?>.svg">PREUZMI SVG</a>
        <a href="#" id="btn-png" class="disabled">PREUZMI PNG</a>
        <a href="#" id="btn-share" class="disabled">PODELI</a>
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

    // ----- Share button (Web Share API) -----
    var btnShare = document.getElementById('btn-share');
    var pngBlob = null;

    function enableShareButton() {
        if (!pngDataUrl) return;
        // Convert data URL to Blob
        try {
            var byteString = atob(pngDataUrl.split(',')[1]);
            var ab = new ArrayBuffer(byteString.length);
            var ia = new Uint8Array(ab);
            for (var i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
            pngBlob = new Blob([ab], { type: 'image/png' });

            // Check if sharing files is supported
            if (navigator.canShare && navigator.canShare({ files: [new File([pngBlob], pngName + '.png', { type: 'image/png' })] })) {
                btnShare.classList.remove('disabled');
            } else if (navigator.share) {
                // Fallback: share without file (just text/url) on desktop
                btnShare.classList.remove('disabled');
            }
        } catch(e) {
            console.warn('Share setup failed:', e);
        }
    }

    // Call after PNG is ready
    var origOnLoad = svgImg.onload;
    svgImg.addEventListener('load', function() { enableShareButton(); });
    // If image already loaded (cached)
    if (pngDataUrl) enableShareButton();

    btnShare.addEventListener('click', function(e) {
        e.preventDefault();
        if (!pngBlob || !navigator.share) return;

        var file = new File([pngBlob], pngName + '.png', { type: 'image/png' });
        var shareData = { title: 'Lampaš', text: 'Lampaš sa stadiona JNA' };

        if (navigator.canShare && navigator.canShare({ files: [file] })) {
            shareData.files = [file];
        }

        navigator.share(shareData).catch(function(err) {
            if (err.name !== 'AbortError') console.warn('Share failed:', err);
        });
    });
})();
</script>

</body>
</html>
