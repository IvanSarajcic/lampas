<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lampaš sa stadiona JNA</title>
    <link rel="stylesheet" href="css/lampas.css?v=<?= filemtime(__DIR__ . '/css/lampas.css') ?>" type="text/css" media="screen">
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-6114758564581825"
     crossorigin="anonymous"></script>
</head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-QGN1HVLG4S"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-QGN1HVLG4S');
</script>
<body>

<form id="lampas-form" action="LampasSvg.php" method="post">
    <!-- Hidden inputs carry the form data -->
    <input type="hidden" name="row1" id="row1">
    <input type="hidden" name="row2" id="row2">
    <input type="hidden" name="row3" id="row3">
    <input type="hidden" name="row4" id="row4">
    <input type="hidden" name="row5" id="row5">
    <input type="hidden" name="row6" id="row6">
    <input type="hidden" name="row7" id="row7">
    <input type="hidden" name="row8" id="row8">
    <input type="hidden" name="bg_id" id="bg_id" value="none">
    <input type="hidden" name="malfunction" id="malfunction" value="0">
    <input type="hidden" name="brightness" id="brightness" value="100">
    <input type="hidden" name="glow" id="glow" value="0">
    <input type="hidden" name="warmth" id="warmth" value="0">
</form>

<div>
    <div id="lampas-hint"></div>
    <div id="lampas-controls-top">
        <select id="bg-select"></select>
        <select id="lang-select">
            <option value="sr">SRB</option>
            <option value="en">ENG</option>
        </select>
    </div>
</div>

<div id="lampas-wrap">
    <svg id="lampas-svg" xmlns="http://www.w3.org/2000/svg"></svg>
    <div id="screenshot-watermark"><span>partizan-histerical.rs/lampas</span><img src="img/misloe.svg" alt=""></div>
</div>

<input type="text" id="mobile-input" autocomplete="off" autocorrect="off" autocapitalize="characters" spellcheck="false">

<!-- Dev Tweaker Panel (Ctrl+Shift+D) -->
<div id="dev-tweaker" style="display:none;">
    <div id="dev-header">skew_data tweaker <kbd>Ctrl+Shift+D</kbd></div>
    <div id="dev-sliders"></div>
    <pre id="dev-json-out"></pre>
    <button id="dev-copy-btn">Copy JSON</button>
</div>

<div id="lampas-controls-bottom">
    <button type="button" id="btn-delete" title=""><svg class="btn" viewBox="0 -5 32 32" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M22.647,13.24 C23.039,13.63 23.039,14.27 22.647,14.66 C22.257,15.05 21.623,15.05 21.232,14.66 L18.993,12.42 L16.725,14.69 C16.331,15.08 15.692,15.08 15.298,14.69 C14.904,14.29 14.904,13.65 15.298,13.26 L17.566,10.99 L15.327,8.76 C14.936,8.37 14.936,7.73 15.327,7.34 C15.718,6.95 16.352,6.95 16.742,7.34 L18.981,9.58 L21.281,7.28 C21.676,6.89 22.314,6.89 22.708,7.28 C23.103,7.68 23.103,8.31 22.708,8.71 L20.408,11.01 L22.647,13.24 Z M27.996,0 L10.051,0 C9.771,-0.02 9.485,0.07 9.271,0.28 L0.285,10.22 C0.074,10.43 -0.017,10.71 -0.002,10.98 C-0.017,11.26 0.074,11.54 0.285,11.75 L9.271,21.69 C9.467,21.88 9.723,21.98 9.979,21.98 L9.979,22 L27.996,22 C30.207,22 32,20.21 32,18 L32,4 C32,1.79 30.207,0 27.996,0 Z"/></svg></button>
    <div id="sliders-wrap">
        <div class="slider-row"><span class="slider-tag">kvar</span><input type="range" class="slider" id="malfunction-slider" min="0" max="60" step="1" value="9"><span class="slider-val" id="malfunction-label">0.9%</span></div>
        <div class="slider-row"><span class="slider-tag">sjaj</span><input type="range" class="slider" id="brightness-slider" min="50" max="100" step="1" value="100"><span class="slider-val" id="brightness-label">100%</span></div>
        <div class="slider-row"><span class="slider-tag">aura</span><input type="range" class="slider" id="glow-slider" min="50" max="550" step="2" value="0"><span class="slider-val" id="glow-label">0</span></div>
        <div class="slider-row"><span class="slider-tag">boja</span><input type="range" class="slider" id="warmth-slider" min="0" max="100" step="1" value="0"><span class="slider-val" id="warmth-label">0%</span></div>
    </div>
    <button type="button" id="btn-submit" title=""><svg class="btn" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg" fill="currentColor"><path d="M15 30c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"/><path d="M35 20c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"/><path d="M35 40c-2.8 0-5-2.2-5-5s2.2-5 5-5 5 2.2 5 5-2.2 5-5 5zm0-8c-1.7 0-3 1.3-3 3s1.3 3 3 3 3-1.3 3-3-1.3-3-3-3z"/><path d="M19.007 25.885l12.88 6.44-.895 1.788-12.88-6.44z"/><path d="M30.993 15.885l.894 1.79-12.88 6.438-.894-1.79z"/></svg></button>
</div>



<script>
(function() {
    // ----- Settings, Font, Backgrounds loaded inline from PHP -----
    var S = <?= file_get_contents(__DIR__ . '/settings.svg.json') ?>;
    var FONT = <?= file_get_contents(__DIR__ . '/font.json') ?>;
    var BACKGROUNDS = <?= file_get_contents(__DIR__ . '/backgrounds.json') ?>;
    var LANG = <?= file_get_contents(__DIR__ . '/lang.json') ?>;
    var currentLang = 'sr';

    function applyLang(lang) {
        currentLang = lang;
        var t = LANG[lang];
        document.title = t['page-title'];
        document.getElementById('btn-submit').title = t['btn-share'];
        document.getElementById('btn-delete').title = t['btn-delete'];
        document.getElementById('lampas-hint').textContent = t['hint'];
        var noBgOpt = bgSelect.querySelector('option[value="none"]');
        if (noBgOpt) noBgOpt.textContent = t['no-bg'];
        // Slider labels
        var tags = document.querySelectorAll('.slider-tag');
        var tagKeys = ['slider-malfunction', 'slider-brightness', 'slider-glow', 'slider-warmth'];
        for (var i = 0; i < tags.length && i < tagKeys.length; i++) {
            tags[i].textContent = t[tagKeys[i]];
        }
    }

    var ROWS = S['total-rows'];  // 8
    var COLS = S['total-cols'];  // 26
    var SVG_W = S['margin-left'] + S['margin-right'] + COLS * S['segment-width'];
    var SVG_H = S['margin-top']  + S['margin-bottom'] + ROWS * S['segment-height'];

    // Text state: 8 rows, each up to 26 chars
    var text = [];
    for (var i = 0; i < ROWS; i++) text.push('');
    var activeRow = -1;

    var svgEl = document.getElementById('lampas-svg');
    var NS = 'http://www.w3.org/2000/svg';

    // References rebuilt on each background switch
    var rowGroups = [];
    var pixels = [];
    var cursorDots = [];
    var contentGroup = null;
    var styleEl = null;
    var currentBg = null;

    // ----- Preload background images -----
    var preloadedImages = {};
    for (var p = 0; p < BACKGROUNDS.length; p++) {
        if (BACKGROUNDS[p].filename) {
            var preImg = new Image();
            preImg.src = 'backgrounds/' + BACKGROUNDS[p].filename;
            preloadedImages[BACKGROUNDS[p].id] = preImg;
        }
    }

    // ----- Populate background dropdown -----
    var bgSelect = document.getElementById('bg-select');
    for (var b = 0; b < BACKGROUNDS.length; b++) {
        var opt = document.createElement('option');
        opt.value = BACKGROUNDS[b].id;
        opt.textContent = BACKGROUNDS[b].name;
        bgSelect.appendChild(opt);
    }

    function getBgById(id) {
        for (var i = 0; i < BACKGROUNDS.length; i++) {
            if (BACKGROUNDS[i].id === id) return BACKGROUNDS[i];
        }
        return BACKGROUNDS[0];
    }

    // ----- Build/rebuild entire SVG for a given background -----
    function buildSvg(bgEntry) {
        currentBg = bgEntry;
        var skew = bgEntry.skew_data || {};

        // Clear SVG
        while (svgEl.firstChild) svgEl.removeChild(svgEl.firstChild);
        rowGroups = [];
        pixels = [];
        cursorDots = [];

        var hasBg = bgEntry.filename && bgEntry.filename.length > 0;
        var usedW = SVG_W;
        var usedH = SVG_H;

        // If background photo, use preloaded image for dimensions
        if (hasBg) {
            var bgPath = 'backgrounds/' + bgEntry.filename;
            var probe = preloadedImages[bgEntry.id] || new Image();
            var onReady = function() {
                usedW = probe.naturalWidth;
                usedH = probe.naturalHeight;
                svgEl.setAttribute('viewBox', '0 0 ' + usedW + ' ' + usedH);
                // Update background rect & image sizes
                var bgRect = svgEl.querySelector('.bg');
                if (bgRect) { bgRect.setAttribute('width', usedW); bgRect.setAttribute('height', usedH); }
                var bgImgEl = svgEl.querySelector('.bg-photo');
                if (bgImgEl) { bgImgEl.setAttribute('width', usedW); bgImgEl.setAttribute('height', usedH); }

                // Auto-fit content to photo when no custom transform is set
                if (!contentGroup.getAttribute('transform')) {
                    var scaleX = usedW / SVG_W;
                    var scaleY = usedH / SVG_H;
                    var fitScale = Math.min(scaleX, scaleY);
                    var offX = (usedW - SVG_W * fitScale) / 2;
                    var offY = (usedH - SVG_H * fitScale) / 2;
                    contentGroup.setAttribute('transform',
                        'translate(' + offX + ',' + offY + ') scale(' + fitScale + ')');
                }
            };
            // If already loaded, apply immediately; otherwise wait
            if (probe.complete && probe.naturalWidth > 0) {
                onReady();
            } else {
                probe.onload = onReady;
                if (!probe.src) probe.src = bgPath;
            }
        }

        svgEl.setAttribute('viewBox', '0 0 ' + usedW + ' ' + usedH);
        svgEl.setAttribute('preserveAspectRatio', 'xMidYMid meet');

        // Defs + style
        var defs = document.createElementNS(NS, 'defs');
        styleEl = document.createElementNS(NS, 'style');
        defs.appendChild(styleEl);
        svgEl.appendChild(defs);
        updateSvgStyle();

        // Background rect
        var bgR = document.createElementNS(NS, 'rect');
        bgR.setAttribute('class', 'bg');
        bgR.setAttribute('width', usedW);
        bgR.setAttribute('height', usedH);
        svgEl.appendChild(bgR);

        // Background image
        if (hasBg) {
            var bgImg = document.createElementNS(NS, 'image');
            bgImg.setAttribute('class', 'bg-photo');
            bgImg.setAttribute('width', usedW);
            bgImg.setAttribute('height', usedH);
            var imgPath = 'backgrounds/' + bgEntry.filename;
            bgImg.setAttributeNS('http://www.w3.org/1999/xlink', 'href', imgPath);
            bgImg.setAttribute('href', imgPath);
            svgEl.appendChild(bgImg);
        }

        // Content group with transform from skew_data
        contentGroup = document.createElementNS(NS, 'g');
        var tp = [];
        var tx = skew['content-translate-x'] || 0, ty = skew['content-translate-y'] || 0;
        if (tx !== 0 || ty !== 0) tp.push('translate(' + tx + ',' + ty + ')');
        var rot = skew['content-rotate'] || 0;
        if (rot !== 0) tp.push('rotate(' + rot + ')');
        var sx = skew['content-scale-x'] || 1, sy = skew['content-scale-y'] || 1;
        if (sx !== 1 || sy !== 1) tp.push('scale(' + sx + ',' + sy + ')');
        var skx = skew['content-skew-x'] || 0;
        if (skx !== 0) tp.push('skewX(' + skx + ')');
        var sky = skew['content-skew-y'] || 0;
        if (sky !== 0) tp.push('skewY(' + sky + ')');
        if (tp.length > 0) contentGroup.setAttribute('transform', tp.join(' '));
        svgEl.appendChild(contentGroup);

        // Build grid
        for (var row = 0; row < ROWS; row++) {
            var rowG = document.createElementNS(NS, 'g');
            rowG.setAttribute('data-row', row);
            rowG.style.cursor = 'pointer';
            rowGroups.push(rowG);
            pixels.push([]);
            cursorDots.push([]);

            for (var col = 0; col < COLS; col++) {
                var x = S['margin-left'] + col * S['segment-width'];
                var y = S['margin-top']  + row * S['segment-height'];

                // Segment background rect
                var rect = document.createElementNS(NS, 'rect');
                rect.setAttribute('class', 'segment');
                rect.setAttribute('x', x + S['segment-rect-offset-x']);
                rect.setAttribute('y', y + S['segment-rect-offset-y']);
                rect.setAttribute('width',  S['segment-rect-width']);
                rect.setAttribute('height', S['segment-rect-height']);
                rowG.appendChild(rect);

                // Clickable hit area
                var hit = document.createElementNS(NS, 'rect');
                hit.setAttribute('x', x);
                hit.setAttribute('y', y);
                hit.setAttribute('width',  S['segment-width']);
                hit.setAttribute('height', S['segment-height']);
                hit.setAttribute('fill', 'transparent');
                hit.setAttribute('data-row', row);
                hit.setAttribute('data-col', col);
                rowG.appendChild(hit);

                // 7x5 lamp dots
                pixels[row].push([]);
                for (var dr = 0; dr < 7; dr++) {
                    pixels[row][col].push([]);
                    for (var dc = 0; dc < 5; dc++) {
                        var cx = x + S['segment-circle-start-x'] + dc * S['segment-circle-offset-x'];
                        var cy = y + S['segment-circle-start-y'] + dr * S['segment-circle-offset-y'];
                        var circle = document.createElementNS(NS, 'circle');
                        circle.setAttribute('class', 'pixel-off');
                        circle.setAttribute('cx', cx);
                        circle.setAttribute('cy', cy);
                        circle.setAttribute('r', S['segment-circle-radius']);
                        rowG.appendChild(circle);
                        pixels[row][col][dr].push(circle);
                    }
                }

                // Cursor indicator dots
                var cursorCircles = [];
                for (var dr = 0; dr < 7; dr++) {
                    var cx = x + S['segment-circle-start-x'] + 2 * S['segment-circle-offset-x'];
                    var cy = y + S['segment-circle-start-y'] + dr * S['segment-circle-offset-y'];
                    var cc = document.createElementNS(NS, 'circle');
                    cc.setAttribute('class', 'cursor-on');
                    cc.setAttribute('cx', cx);
                    cc.setAttribute('cy', cy);
                    cc.setAttribute('r', S['segment-circle-on-radius'] * 0.5);
                    cc.style.display = 'none';
                    rowG.appendChild(cc);
                    cursorCircles.push(cc);
                }
                cursorDots[row].push(cursorCircles);
            }

            contentGroup.appendChild(rowG);
        }

        // Re-render text & cursor
        for (var r = 0; r < ROWS; r++) renderRow(r);
        if (activeRow >= 0) {
            rowGroups[activeRow].setAttribute('class', 'row-active');
            startCursorBlink();
        }
    }

    // ----- Render a single character at [row][col] -----
    var malfunctionRate = 0.009;
    var brightnessVal = 1.0;
    var glowVal = 0;
    var warmthVal = 0;

    // Compute warm lamp color: #ffffff -> #ffff00 -> #ffd700
    function getWarmColor(baseHex, t) {
        if (t <= 0) return baseHex;
        var r0 = parseInt(baseHex.substr(1,2),16), g0 = parseInt(baseHex.substr(3,2),16), b0 = parseInt(baseHex.substr(5,2),16);
        var r1, g1, b1, lt;
        if (t <= 0.5) {
            // First half: base -> #ffff00
            r1 = 255; g1 = 255; b1 = 0;
            lt = t * 2;
        } else {
            // Second half: #ffff00 -> #ffd700
            r0 = 255; g0 = 255; b0 = 0;
            r1 = 255; g1 = 215; b1 = 0;
            lt = (t - 0.5) * 2;
        }
        var r = Math.round(r0 + (r1 - r0) * lt), g = Math.round(g0 + (g1 - g0) * lt), b = Math.round(b0 + (b1 - b0) * lt);
        return '#' + ((1<<24)|(r<<16)|(g<<8)|b).toString(16).slice(1);
    }

    function updateSvgStyle() {
        if (!styleEl || !currentBg) return;
        var skew = currentBg.skew_data || {};
        var segOp = (skew['segment-rect-opacity'] !== undefined) ? skew['segment-rect-opacity'] : 1;
        var baseLampColor = currentBg['lamp-color'] || S['segment-circle-on-bg-color'];
        var lampColor = getWarmColor(baseLampColor, warmthVal);
        var glowCss = glowVal > 0 ? 'filter:drop-shadow(0 0 ' + glowVal + 'px ' + lampColor + ');' : '';
        styleEl.textContent =
            '.bg{fill:' + S['bg-color'] + '}' +
            '.segment{fill:' + S['segment-rect-bg-color'] + ';opacity:' + segOp + '}' +
            '.pixel-off{fill:' + S['segment-circle-off-bg-color'] + '}' +
            '.pixel-on{fill:' + lampColor + ';opacity:' + brightnessVal + ';' + glowCss + '}' +
            '.row-active .segment{fill:#2d2729;stroke:' + lampColor + ';stroke-width:4;stroke-opacity:0.25}' +
            '.cursor-on{fill:' + lampColor + ';opacity:0.35}';
    }

    function renderChar(row, col, ch) {
        var upper = ch.toUpperCase();
        var fontData = FONT[upper] || FONT[' '];
        for (var dr = 0; dr < 7; dr++) {
            for (var dc = 0; dc < 5; dc++) {
                var on = fontData && fontData[dr] && fontData[dr][dc] ? true : false;
                // burnt-out lamp: randomly extinguish on-pixels
                if (on && malfunctionRate > 0 && Math.random() < malfunctionRate) on = false;
                var circle = pixels[row][col][dr][dc];
                circle.setAttribute('class', on ? 'pixel-on' : 'pixel-off');
                circle.setAttribute('r', on ? S['segment-circle-on-radius'] : S['segment-circle-radius']);
            }
        }
    }

    // ----- Centering helper -----
    function getRowOffset(row) {
        return Math.floor((COLS - text[row].length) / 2);
    }

    // ----- Render entire row -----
    function renderRow(row) {
        var str = text[row];
        var offset = getRowOffset(row);
        for (var col = 0; col < COLS; col++) {
            var textCol = col - offset;
            var ch = (textCol >= 0 && textCol < str.length) ? str[textCol] : ' ';
            renderChar(row, col, ch);
        }
        updateCursor(row);
    }

    // ----- Cursor blink -----
    var cursorVisible = true;
    var cursorInterval = null;

    function updateCursor(row) {
        for (var r = 0; r < ROWS; r++) {
            for (var c = 0; c < COLS; c++) {
                for (var d = 0; d < cursorDots[r][c].length; d++) {
                    cursorDots[r][c][d].style.display = 'none';
                }
            }
        }
        if (row === activeRow && row >= 0) {
            var offset = getRowOffset(row);
            var pos = offset + text[row].length;
            if (pos < COLS) {
                for (var d = 0; d < cursorDots[row][pos].length; d++) {
                    cursorDots[row][pos][d].style.display = cursorVisible ? '' : 'none';
                }
            }
        }
    }

    function startCursorBlink() {
        if (cursorInterval) clearInterval(cursorInterval);
        cursorVisible = true;
        updateCursor(activeRow);
        cursorInterval = setInterval(function() {
            cursorVisible = !cursorVisible;
            updateCursor(activeRow);
        }, 530);
    }

    // ----- Hidden input for mobile keyboard -----
    var mobileInput = document.getElementById('mobile-input');

    mobileInput.addEventListener('input', function() {
        if (activeRow < 0) return;
        var val = mobileInput.value;
        mobileInput.value = '';
        for (var i = 0; i < val.length; i++) {
            var ch = val[i].toUpperCase();
            if (text[activeRow].length < COLS) {
                text[activeRow] += ch;
                renderRow(activeRow);
                syncHiddenInputs();
                if (text[activeRow].length >= COLS && activeRow < ROWS - 1) {
                    selectRow(activeRow + 1);
                }
            }
        }
    });

    function focusMobileInput() {
        mobileInput.focus();
    }

    // ----- Row selection -----
    function selectRow(row) {
        if (activeRow >= 0 && rowGroups[activeRow]) rowGroups[activeRow].removeAttribute('class');
        activeRow = row;
        rowGroups[activeRow].setAttribute('class', 'row-active');
        startCursorBlink();
        focusMobileInput();
    }

    svgEl.addEventListener('click', function(e) {
        var target = e.target;
        while (target && target !== svgEl) {
            if (target.hasAttribute('data-row')) {
                selectRow(parseInt(target.getAttribute('data-row'), 10));
                return;
            }
            target = target.parentNode;
        }
    });

    // ----- Keyboard handling -----
    document.addEventListener('keydown', function(e) {
        if (activeRow < 0) return;

        var key = e.key;
        var row = activeRow;

        if (key === 'Backspace') {
            e.preventDefault();
            if (text[row].length > 0) {
                text[row] = text[row].slice(0, -1);
                renderRow(row);
            } else if (activeRow > 0) {
                selectRow(activeRow - 1);
            }
            syncHiddenInputs();
            return;
        }

        if (key === 'Enter') {
            e.preventDefault();
            if (activeRow < ROWS - 1) selectRow(activeRow + 1);
            return;
        }

        if (key === 'ArrowUp') {
            e.preventDefault();
            if (activeRow > 0) selectRow(activeRow - 1);
            return;
        }

        if (key === 'ArrowDown') {
            e.preventDefault();
            if (activeRow < ROWS - 1) selectRow(activeRow + 1);
            return;
        }

        if (key === 'Tab') {
            e.preventDefault();
            if (e.shiftKey && activeRow > 0) selectRow(activeRow - 1);
            else if (!e.shiftKey && activeRow < ROWS - 1) selectRow(activeRow + 1);
            return;
        }

        if (key === 'Delete') {
            e.preventDefault();
            text[row] = '';
            renderRow(row);
            syncHiddenInputs();
            return;
        }

        if (key === 'Escape') {
            if (activeRow >= 0) rowGroups[activeRow].removeAttribute('class');
            activeRow = -1;
            if (cursorInterval) clearInterval(cursorInterval);
            updateCursor(-1);
            return;
        }

        if (key.length > 1) return;

        if (text[row].length < COLS) {
            e.preventDefault();
            var ch = key.toUpperCase();
            text[row] += ch;
            renderRow(row);
            syncHiddenInputs();
            if (text[row].length >= COLS && activeRow < ROWS - 1) {
                selectRow(activeRow + 1);
            }
        }
    });

    // ----- Sync text state to hidden form inputs -----
    function syncHiddenInputs() {
        for (var i = 0; i < ROWS; i++) {
            document.getElementById('row' + (i + 1)).value = text[i];
        }
        document.getElementById('bg_id').value = currentBg ? currentBg.id : 'none';
        document.getElementById('malfunction').value = (malfunctionRate * 100).toFixed(1);
        document.getElementById('brightness').value = (brightnessVal * 100).toFixed(0);
        document.getElementById('glow').value = glowVal;
        document.getElementById('warmth').value = (warmthVal * 100).toFixed(0);
    }

    // ----- Sliders -----
    var malfSlider = document.getElementById('malfunction-slider');
    var malfLabel = document.getElementById('malfunction-label');
    malfSlider.addEventListener('input', function() {
        malfunctionRate = parseInt(malfSlider.value, 10) / 1000;
        malfLabel.textContent = (malfunctionRate * 100).toFixed(1) + '%';
        for (var i = 0; i < ROWS; i++) renderRow(i);
        syncHiddenInputs();
    });

    var brightSlider = document.getElementById('brightness-slider');
    var brightLabel = document.getElementById('brightness-label');
    brightSlider.addEventListener('input', function() {
        brightnessVal = parseInt(brightSlider.value, 10) / 100;
        brightLabel.textContent = Math.round(brightnessVal * 100) + '%';
        updateSvgStyle();
        syncHiddenInputs();
    });

    var glowSlider = document.getElementById('glow-slider');
    var glowLabel = document.getElementById('glow-label');
    glowSlider.addEventListener('input', function() {
        glowVal = parseInt(glowSlider.value, 10);
        glowLabel.textContent = glowVal + 'px';
        updateSvgStyle();
        syncHiddenInputs();
    });

    var warmSlider = document.getElementById('warmth-slider');
    var warmLabel = document.getElementById('warmth-label');
    warmSlider.addEventListener('input', function() {
        warmthVal = parseInt(warmSlider.value, 10) / 100;
        warmLabel.textContent = Math.round(warmthVal * 100) + '%';
        updateSvgStyle();
        syncHiddenInputs();
    });

    // ----- Background dropdown -----
    bgSelect.addEventListener('change', function() {
        var bg = getBgById(bgSelect.value);
        buildSvg(bg);
        syncHiddenInputs();
    });

    // ----- Language switch -----
    document.getElementById('lang-select').addEventListener('change', function() {
        applyLang(this.value);
    });

    // ----- Share as PNG -----
    // Convert an image URL to a data URL via canvas
    function toDataUrl(src, callback) {
        var img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = function() {
            var c = document.createElement('canvas');
            c.width = img.naturalWidth;
            c.height = img.naturalHeight;
            c.getContext('2d').drawImage(img, 0, 0);
            callback(c.toDataURL('image/png'));
        };
        img.onerror = function() { callback(null); };
        img.src = src;
    }

    // Preload misloe.svg as text for watermark embedding
    var misloeData = null;
    fetch('img/misloe.svg')
        .then(function(r) { return r.text(); })
        .then(function(svgText) {
            // Extract inner content and inline styles
            var m = svgText.match(/<svg[^>]*>([\s\S]*)<\/svg>/i);
            if (m) {
                var inner = m[1];
                inner = inner.replace(/<style[^>]*>[\s\S]*?<\/style>/g, '');
                inner = inner.replace(/class="st0"/g, 'style="fill-rule:evenodd;clip-rule:evenodd;fill:#AEB0B2"');
                inner = inner.replace(/class="st1"/g, 'style="fill-rule:evenodd;clip-rule:evenodd;fill:#FFFFFF"');
                inner = inner.replace(/class="st2"/g, 'style="fill:#FFF200"');
                inner = inner.replace(/class="st3"/g, 'style="fill-rule:evenodd;clip-rule:evenodd"');
                inner = inner.replace(/class="st4"/g, 'style="fill:#FFFFFF"');
                misloeData = inner;
            }
        })
        .catch(function() {});

    function svgToPng(callback) {
        var clone = svgEl.cloneNode(true);
        // Remove cursor dots and row-active styling for clean export
        var cursors = clone.querySelectorAll('.cursor-on');
        for (var i = 0; i < cursors.length; i++) cursors[i].parentNode.removeChild(cursors[i]);
        var activeRows = clone.querySelectorAll('.row-active');
        for (var i = 0; i < activeRows.length; i++) activeRows[i].removeAttribute('class');

        // Inline all CSS styles directly onto elements so blob URL rendering works
        var bgRect = clone.querySelector('.bg');
        if (bgRect) bgRect.setAttribute('fill', S['bg-color']);
        var segments = clone.querySelectorAll('.segment');
        var segOp = (currentBg && currentBg.skew_data && currentBg.skew_data['segment-rect-opacity'] !== undefined)
            ? currentBg.skew_data['segment-rect-opacity'] : 1;
        for (var i = 0; i < segments.length; i++) {
            segments[i].setAttribute('fill', S['segment-rect-bg-color']);
            segments[i].setAttribute('opacity', segOp);
        }
        var baseLampColor = (currentBg && currentBg['lamp-color']) || S['segment-circle-on-bg-color'];
        var lampColor = getWarmColor(baseLampColor, warmthVal);
        var pxOff = clone.querySelectorAll('.pixel-off');
        for (var i = 0; i < pxOff.length; i++) pxOff[i].setAttribute('fill', S['segment-circle-off-bg-color']);
        var pxOn = clone.querySelectorAll('.pixel-on');
        for (var i = 0; i < pxOn.length; i++) {
            pxOn[i].setAttribute('fill', lampColor);
            if (brightnessVal < 1) pxOn[i].setAttribute('opacity', brightnessVal);
            if (glowVal > 0) pxOn[i].style.filter = 'drop-shadow(0 0 ' + glowVal + 'px ' + lampColor + ')';
        }

        var vb = svgEl.getAttribute('viewBox').split(' ');
        var w = parseFloat(vb[2]);
        var h = parseFloat(vb[3]);

        // Add watermark if available
        if (misloeData) {
            var logoSize = Math.round(w * 0.06);
            var logoScale = (logoSize / 190).toFixed(6);
            var logoX = Math.round(w - logoSize - w * 0.015);
            var logoY = Math.round(h - logoSize - h * 0.015);
            var textSize = Math.max(5, Math.round(w * 0.012));
            var textX = logoX - Math.round(w * 0.005);
            var textY = Math.round(logoY + logoSize / 2 + textSize / 3);

            var wmNs = 'http://www.w3.org/2000/svg';
            var wmGroup = document.createElementNS(wmNs, 'g');
            wmGroup.setAttribute('opacity', '0.85');
            var padX = Math.round(textSize * 0.4);
            var padY = Math.round(textSize * 0.3);
            var estTextW = Math.round(textSize * 0.6 * 26); // approx width of 26 chars
            var rectX = textX - estTextW - padX;
            var rectY = textY - textSize + padY;
            var rectW = estTextW + padX * 2;
            var rectH = textSize + padY * 2;

            // Build watermark SVG as string and parse it properly
            var wmSvgStr = '<svg xmlns="http://www.w3.org/2000/svg">' +
                '<g opacity="0.85">' +
                '<g transform="translate(' + logoX + ',' + logoY + ') scale(' + logoScale + ')">' +
                misloeData +
                '</g>' +
                '<rect x="' + rectX + '" y="' + rectY + '" width="' + rectW + '" height="' + rectH + '" rx="' + Math.round(textSize * 0.3) + '" fill="#000000" opacity="0.5"/>' +
                '<text x="' + textX + '" y="' + textY + '" font-family="sans-serif" font-size="' + textSize + '" fill="#ffffff" text-anchor="end" opacity="0.8">partizan-histerical/lampas</text>' +
                '</g></svg>';
            var parser = new DOMParser();
            var wmDoc = parser.parseFromString(wmSvgStr, 'image/svg+xml');
            var wmParsed = wmDoc.documentElement.firstChild;
            clone.appendChild(clone.ownerDocument.importNode(wmParsed, true));
        }

        // Check if there's a background photo that needs data URL conversion
        var bgPhoto = clone.querySelector('.bg-photo');
        if (bgPhoto) {
            var bgHref = bgPhoto.getAttribute('href') || bgPhoto.getAttributeNS('http://www.w3.org/1999/xlink', 'href');
            if (bgHref && bgHref.indexOf('data:') !== 0) {
                // Convert relative URL to data URL so it works in blob context
                toDataUrl(bgHref, function(dataUrl) {
                    if (dataUrl) {
                        bgPhoto.setAttribute('href', dataUrl);
                        bgPhoto.setAttributeNS('http://www.w3.org/1999/xlink', 'href', dataUrl);
                    }
                    renderSvgToCanvas(clone, w, h, callback, null);
                });
                return;
            }
        }

        renderSvgToCanvas(clone, w, h, callback, S['bg-color']);
    }

    function renderSvgToCanvas(svgClone, w, h, callback, fillBg) {
        var svgData = new XMLSerializer().serializeToString(svgClone);
        var svgBlob = new Blob([svgData], {type: 'image/svg+xml;charset=utf-8'});
        var url = URL.createObjectURL(svgBlob);

        var img = new Image();
        img.onload = function() {
            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d');
            // Fill solid background color first if needed
            if (fillBg) {
                ctx.fillStyle = fillBg;
                ctx.fillRect(0, 0, w, h);
            }
            ctx.drawImage(img, 0, 0, w, h);
            URL.revokeObjectURL(url);
            canvas.toBlob(function(blob) {
                callback(blob);
            }, 'image/png');
        };
        img.src = url;
    }

    document.getElementById('btn-delete').addEventListener('click', function() {
        for (var i = 0; i < ROWS; i++) text[i] = '';
        for (var i = 0; i < ROWS; i++) renderRow(i);
        syncHiddenInputs();
        selectRow(0);
    });

    document.getElementById('btn-submit').addEventListener('click', function() {
        // Log submission
        try {
            fetch('log.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    text: text.slice(),
                    bg: currentBg ? currentBg.id : 'none'
                })
            }).catch(function() {});
        } catch(e) {}

        svgToPng(function(blob) {
            var file = new File([blob], 'lampas.png', {type: 'image/png'});
            if (navigator.share && navigator.canShare && navigator.canShare({files: [file]})) {
                navigator.share({
                    files: [file],
                    title: 'Lampa\u0161'
                }).catch(function() {});
            } else {
                // Fallback: download
                var a = document.createElement('a');
                a.href = URL.createObjectURL(blob);
                a.download = 'lampas.png';
                a.click();
                URL.revokeObjectURL(a.href);
            }
        });
    });

    // ----- Initial build -----
    buildSvg(BACKGROUNDS[0]);
    selectRow(0);
    applyLang(currentLang);

    // ===== DEV TWEAKER (Ctrl+Shift+D) =====
    (function() {
        var panel = document.getElementById('dev-tweaker');
        var jsonOut = document.getElementById('dev-json-out');
        var copyBtn = document.getElementById('dev-copy-btn');

        var fields = [
            { key: 'content-translate-x', label: 'translate-x', step: 1,     decimals: 0 },
            { key: 'content-translate-y', label: 'translate-y', step: 1,     decimals: 0 },
            { key: 'content-scale-x',     label: 'scale-x',     step: 0.001, decimals: 4 },
            { key: 'content-scale-y',     label: 'scale-y',     step: 0.001, decimals: 4 },
            { key: 'content-skew-x',      label: 'skew-x',      step: 0.1,   decimals: 1 },
            { key: 'content-skew-y',      label: 'skew-y',      step: 0.1,   decimals: 1 },
            { key: 'content-rotate',      label: 'rotate',      step: 0.1,   decimals: 1 },
            { key: 'segment-rect-opacity',label: 'seg-opacity',  step: 0.05,  decimals: 2 }
        ];

        var inputs = {};
        var container = document.getElementById('dev-sliders');

        function fmt(val, dec) {
            return parseFloat(parseFloat(val).toFixed(dec));
        }

        fields.forEach(function(f) {
            var row = document.createElement('div');
            row.className = 'dev-row';
            var lbl = document.createElement('label');
            lbl.textContent = f.label;

            var btnDown = document.createElement('button');
            btnDown.className = 'dev-arrow';
            btnDown.textContent = '\u25BC';
            btnDown.title = '-' + f.step;

            var inp = document.createElement('input');
            inp.type = 'text';
            inp.className = 'dev-num-input';
            inp.value = '0';

            var btnUp = document.createElement('button');
            btnUp.className = 'dev-arrow';
            btnUp.textContent = '\u25B2';
            btnUp.title = '+' + f.step;

            row.appendChild(lbl);
            row.appendChild(btnDown);
            row.appendChild(inp);
            row.appendChild(btnUp);
            container.appendChild(row);
            inputs[f.key] = { input: inp, step: f.step, decimals: f.decimals };

            btnUp.addEventListener('click', function() {
                var v = parseFloat(inp.value) || 0;
                inp.value = fmt(v + f.step, f.decimals);
                applyTweaker();
            });
            btnDown.addEventListener('click', function() {
                var v = parseFloat(inp.value) || 0;
                inp.value = fmt(v - f.step, f.decimals);
                applyTweaker();
            });
            inp.addEventListener('change', function() {
                inp.value = fmt(inp.value, f.decimals);
                applyTweaker();
            });
            inp.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowUp') { e.preventDefault(); btnUp.click(); }
                if (e.key === 'ArrowDown') { e.preventDefault(); btnDown.click(); }
            });
        });

        // Lamp color input
        var lampRow = document.createElement('div');
        lampRow.className = 'dev-row';
        var lampLbl = document.createElement('label');
        lampLbl.textContent = 'lamp-color';
        var lampInp = document.createElement('input');
        lampInp.type = 'text';
        lampInp.value = '#cecece';
        lampInp.className = 'dev-color-input';
        lampRow.appendChild(lampLbl);
        lampRow.appendChild(lampInp);
        container.appendChild(lampRow);

        function loadFromBg() {
            if (!currentBg) return;
            var sk = currentBg.skew_data || {};
            fields.forEach(function(f) {
                var v = sk[f.key];
                if (v === undefined) v = (f.key === 'content-scale-x' || f.key === 'content-scale-y') ? 1 : 0;
                inputs[f.key].input.value = fmt(v, f.decimals);
            });
            lampInp.value = currentBg['lamp-color'] || S['segment-circle-on-bg-color'];
            updateJson();
        }

        function applyTweaker() {
            if (!contentGroup) return;
            var tx = parseFloat(inputs['content-translate-x'].input.value) || 0;
            var ty = parseFloat(inputs['content-translate-y'].input.value) || 0;
            var rot = parseFloat(inputs['content-rotate'].input.value) || 0;
            var sx = parseFloat(inputs['content-scale-x'].input.value) || 1;
            var sy = parseFloat(inputs['content-scale-y'].input.value) || 1;
            var skx = parseFloat(inputs['content-skew-x'].input.value) || 0;
            var sky = parseFloat(inputs['content-skew-y'].input.value) || 0;
            var segOp = parseFloat(inputs['segment-rect-opacity'].input.value);
            if (isNaN(segOp)) segOp = 1;

            var tp = [];
            if (tx !== 0 || ty !== 0) tp.push('translate(' + tx + ',' + ty + ')');
            if (rot !== 0) tp.push('rotate(' + rot + ')');
            if (sx !== 1 || sy !== 1) tp.push('scale(' + sx + ',' + sy + ')');
            if (skx !== 0) tp.push('skewX(' + skx + ')');
            if (sky !== 0) tp.push('skewY(' + sky + ')');
            contentGroup.setAttribute('transform', tp.length ? tp.join(' ') : '');

            if (currentBg) {
                if (!currentBg.skew_data) currentBg.skew_data = {};
                currentBg.skew_data['segment-rect-opacity'] = segOp;
                updateSvgStyle();
            }
            updateJson();
        }

        function updateJson() {
            var obj = {};
            fields.forEach(function(f) {
                obj[f.key] = parseFloat(inputs[f.key].input.value) || 0;
            });
            var lc = lampInp.value.trim();
            var out = '"skew_data": ' + JSON.stringify(obj, null, 2) + ',\n"lamp-color": "' + lc + '"';
            jsonOut.textContent = out;
        }

        lampInp.addEventListener('input', updateJson);

        copyBtn.addEventListener('click', function() {
            navigator.clipboard.writeText(jsonOut.textContent).then(function() {
                copyBtn.textContent = 'Copied!';
                setTimeout(function() { copyBtn.textContent = 'Copy JSON'; }, 1500);
            });
        });

        // Toggle with Ctrl+Shift+D
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.shiftKey && e.key === 'D') {
                e.preventDefault();
                var vis = panel.style.display === 'block';
                panel.style.display = vis ? 'none' : 'block';
                if (!vis) loadFromBg();
            }
        });

        // Reload values when background changes
        bgSelect.addEventListener('change', function() {
            setTimeout(function() {
                if (panel.style.display === 'block') loadFromBg();
            }, 100);
        });
    })();

})();
</script>

</body>
</html>