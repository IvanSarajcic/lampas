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

            $this->bgDataUrl = 'data:' . $mime . ';base64,' . $b64;
            $bgImage = '  <image width="' . $svgW . '" height="' . $svgH . '" href="' . $this->bgDataUrl . '" />';

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

        // Build watermark: misloe.svg + small credit text in bottom-right
        $watermark = '';
        $misloeFile = __DIR__ . '/img/misloe.svg';
        if (file_exists($misloeFile)) {
            $misloeSvg = file_get_contents($misloeFile);
            // Extract inner content between <svg ...> and </svg>
            $innerStart = strpos($misloeSvg, '>', strpos($misloeSvg, '<svg')) + 1;
            $innerEnd = strrpos($misloeSvg, '</svg>');
            $misloeInner = substr($misloeSvg, $innerStart, $innerEnd - $innerStart);
            // Remove <?xml, <!DOCTYPE, and <!-- comments -->
            $misloeInner = preg_replace('/<\?xml[^?]*\?>/', '', $misloeInner);

            // Inline the CSS class fills so it works when embedded inside a <g>
            // misloe.svg classes: st0=#AEB0B2, st1=#FFFFFF, st2=#FFF200, st3=#000000(default), st4=#FFFFFF
            $styleMap = [
                'st0' => 'fill-rule:evenodd;clip-rule:evenodd;fill:#AEB0B2',
                'st1' => 'fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF',
                'st2' => 'fill:#FFF200',
                'st3' => 'fill-rule:evenodd;clip-rule:evenodd',
                'st4' => 'fill:#FFFFFF',
            ];
            // Remove the <style> block entirely
            $misloeInner = preg_replace('/<style[^>]*>.*?<\/style>/s', '', $misloeInner);
            // Replace class="stX" with inline style="..."
            foreach ($styleMap as $cls => $style) {
                $misloeInner = str_replace('class="' . $cls . '"', 'style="' . $style . '"', $misloeInner);
            }

            // Size and position: small icon in bottom-right
            $logoSize = round($svgW * 0.06);  // ~6% of width
            $logoScale = $logoSize / 190;      // misloe.svg viewBox is 190x190
            $logoX = $svgW - $logoSize - round($svgW * 0.015);
            $logoY = $svgH - $logoSize - round($svgH * 0.015);

            // Credit text
            $textSize = round($svgW * 0.012);
            if ($textSize < 5) $textSize = 5;
            $textX = $logoX - round($svgW * 0.005);
            $textY = $logoY + $logoSize / 2 + $textSize / 3;

            $padX = round($textSize * 0.4);
            $padY = round($textSize * 0.3);
            $estTextW = round($textSize * 0.6 * 26);
            $rectX = $textX - $estTextW - $padX;
            $rectY = $textY - $textSize + $padY;
            $rectW = $estTextW + $padX * 2;
            $rectH = $textSize + $padY * 2;
            $rectR = round($textSize * 0.3);

            $watermark = '  <g opacity="0.85">' . "\n"
                       . '    <g transform="translate(' . $logoX . ',' . $logoY . ') scale(' . round($logoScale, 6) . ')">' . "\n"
                       . $misloeInner . "\n"
                       . '    </g>' . "\n"
                       . '    <rect x="' . $rectX . '" y="' . $rectY . '" width="' . $rectW . '" height="' . $rectH . '" rx="' . $rectR . '" fill="#000000" opacity="0.7"/>' . "\n"
                       . '    <text x="' . $textX . '" y="' . $textY . '" font-family="sans-serif" font-size="' . $textSize . '" fill="#ffffff" text-anchor="end" opacity="1">partizan-histerical/lampas</text>' . "\n"
                       . '  </g>';
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
                'watermark' => $watermark,
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

    public function getBackgroundDataUrl() {
        return $this->bgDataUrl;
    }

    private $settings;
    private $font;
    private $text = [];
    private $log = [];
    private $segments = [];
    private $bgDataUrl = '';
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
$bgDataUrl = $l->getBackgroundDataUrl();
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
        <a href="#" id="btn-png" class="disabled" title="Preuzmi PNG"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:middle;margin-right:4px"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>PNG</a>
        <a href="#" id="btn-share" class="disabled" title="Podeli"><svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:middle;margin-right:4px"><path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11A2.99 2.99 0 0 0 18 8a3 3 0 1 0-3-3c0 .24.04.47.09.7L8.04 9.81A2.99 2.99 0 0 0 6 9a3 3 0 1 0 0 6c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65a2.92 2.92 0 0 0 3 2.92 2.92 2.92 0 0 0 2.92-2.92A2.92 2.92 0 0 0 18 16.08z"/></svg>PODELI</a>
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
    var svgSrc  = <?= json_encode($svgFile) ?>;
    var bgDataUrl = <?= json_encode($bgDataUrl) ?>;
    var pngDataUrl = null;
    var pngBlob = null;
    var btnShare = document.getElementById('btn-share');

    function finalizePng(ctx, w, h) {
        try {
            pngDataUrl = canvas.toDataURL('image/png');
            btnPng.classList.remove('disabled');
            enableShareButton();
        } catch(e) {
            console.warn('PNG export failed:', e);
        }
    }

    function drawSvgOverlay(ctx, w, h) {
        // Fetch SVG, strip the <image> tag (bg already drawn), render as blob
        fetch(svgSrc)
            .then(function(r) { return r.text(); })
            .then(function(svgText) {
                // Remove the background <image> element so only lampas + watermark remain
                svgText = svgText.replace(/<image[^>]*href="data:[^"]*"[^>]*\/?>\s*/g, '');
                var blob = new Blob([svgText], { type: 'image/svg+xml;charset=utf-8' });
                var url = URL.createObjectURL(blob);
                var img = new Image();
                img.onload = function() {
                    ctx.drawImage(img, 0, 0, w, h);
                    URL.revokeObjectURL(url);
                    finalizePng(ctx, w, h);
                };
                img.src = url;
            })
            .catch(function(e) {
                console.warn('SVG overlay failed:', e);
                finalizePng(ctx, w, h);
            });
    }

    // Wait for the preview image to load to get dimensions
    function startRender() {
        var natW = svgImg.naturalWidth;
        var natH = svgImg.naturalHeight;
        if (!natW || !natH) return;

        var scale = Math.min(1, MAX_PNG_WIDTH / natW);
        var w = Math.round(natW * scale);
        var h = Math.round(natH * scale);

        canvas.width  = w;
        canvas.height = h;
        var ctx = canvas.getContext('2d');

        if (bgDataUrl) {
            // Layer 1: draw the background photo directly on canvas
            var bgImg = new Image();
            bgImg.onload = function() {
                ctx.drawImage(bgImg, 0, 0, w, h);
                // Layer 2: draw SVG overlay (lampas pixels + watermark, no bg image)
                drawSvgOverlay(ctx, w, h);
            };
            bgImg.src = bgDataUrl;
        } else {
            // No background photo — just render the full SVG
            drawSvgOverlay(ctx, w, h);
        }
    }

    if (svgImg.complete && svgImg.naturalWidth) {
        startRender();
    } else {
        svgImg.addEventListener('load', startRender);
    }

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

    // ----- Share button -----
    function enableShareButton() {
        if (!pngDataUrl) return;
        try {
            var byteString = atob(pngDataUrl.split(',')[1]);
            var ab = new ArrayBuffer(byteString.length);
            var ia = new Uint8Array(ab);
            for (var i = 0; i < byteString.length; i++) ia[i] = byteString.charCodeAt(i);
            pngBlob = new Blob([ab], { type: 'image/png' });
            // Always enable — we have fallback for non-share browsers
            btnShare.classList.remove('disabled');
        } catch(e) {
            console.warn('Share setup failed:', e);
        }
    }

    btnShare.addEventListener('click', function(e) {
        e.preventDefault();
        if (!pngBlob) return;

        // Try Web Share API with file (mobile)
        if (navigator.share) {
            var file = new File([pngBlob], pngName + '.png', { type: 'image/png' });
            var shareData = { title: 'Lampaš', text: 'Lampaš sa stadiona JNA' };

            if (navigator.canShare && navigator.canShare({ files: [file] })) {
                shareData.files = [file];
            }

            navigator.share(shareData).catch(function(err) {
                if (err.name !== 'AbortError') console.warn('Share failed:', err);
            });
            return;
        }

        // Fallback: copy PNG to clipboard (desktop)
        if (navigator.clipboard && navigator.clipboard.write) {
            var item = new ClipboardItem({ 'image/png': pngBlob });
            navigator.clipboard.write([item]).then(function() {
                var orig = btnShare.textContent;
                btnShare.innerHTML = btnShare.innerHTML.replace('PODELI', 'KOPIRANO!');
                setTimeout(function() { btnShare.innerHTML = btnShare.innerHTML.replace('KOPIRANO!', 'PODELI'); }, 1500);
            }).catch(function() {
                // Last fallback: open image in new tab
                window.open(pngDataUrl, '_blank');
            });
        } else {
            // Final fallback: open in new tab
            window.open(pngDataUrl, '_blank');
        }
    });
})();
</script>

</body>
</html>
