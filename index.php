<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lampaš sa stadiona JNA</title>
    <link rel="stylesheet" href="css/lampas.css" type="text/css" media="screen">
</head>
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
</form>

<div id="lampas-wrap">
    <svg id="lampas-svg" xmlns="http://www.w3.org/2000/svg"></svg>
</div>

<div id="lampas-controls">
    <select id="bg-select"></select>
    <button type="button" id="btn-submit">ПОДЕЛИ</button>
</div>

<div id="lampas-hint">
    Klikni na red da počneš kucati. Enter za sledeći red. Backspace briše.
</div>

<script>
(function() {
    // ----- Settings, Font, Backgrounds loaded inline from PHP -----
    var S = <?= file_get_contents(__DIR__ . '/settings.svg.json') ?>;
    var FONT = <?= file_get_contents(__DIR__ . '/font.json') ?>;
    var BACKGROUNDS = <?= file_get_contents(__DIR__ . '/backgrounds.json') ?>;

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

        // If background photo, load its dimensions via an Image probe
        if (hasBg) {
            var bgPath = 'backgrounds/' + bgEntry.filename;
            // We'll set a temporary viewBox and update once image loads
            var probe = new Image();
            probe.onload = function() {
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
            probe.src = bgPath;
        }

        svgEl.setAttribute('viewBox', '0 0 ' + usedW + ' ' + usedH);
        svgEl.setAttribute('preserveAspectRatio', 'xMidYMid meet');

        // Defs + style
        var defs = document.createElementNS(NS, 'defs');
        styleEl = document.createElementNS(NS, 'style');
        var segOp = (skew['segment-rect-opacity'] !== undefined) ? skew['segment-rect-opacity'] : 1;
        var lampColor = bgEntry['lamp-color'] || S['segment-circle-on-bg-color'];
        styleEl.textContent =
            '.bg{fill:' + S['bg-color'] + '}' +
            '.segment{fill:' + S['segment-rect-bg-color'] + ';opacity:' + segOp + '}' +
            '.pixel-off{fill:' + S['segment-circle-off-bg-color'] + '}' +
            '.pixel-on{fill:' + lampColor + '}' +
            '.row-active .segment{fill:#2d2729;stroke:' + lampColor + ';stroke-width:4;stroke-opacity:0.25}' +
            '.cursor-on{fill:' + lampColor + ';opacity:0.35}';
        defs.appendChild(styleEl);
        svgEl.appendChild(defs);

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
    function renderChar(row, col, ch) {
        var upper = ch.toUpperCase();
        var fontData = FONT[upper] || FONT[' '];
        for (var dr = 0; dr < 7; dr++) {
            for (var dc = 0; dc < 5; dc++) {
                var on = fontData && fontData[dr] && fontData[dr][dc] ? true : false;
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

    // ----- Row selection -----
    function selectRow(row) {
        if (activeRow >= 0 && rowGroups[activeRow]) rowGroups[activeRow].removeAttribute('class');
        activeRow = row;
        rowGroups[activeRow].setAttribute('class', 'row-active');
        startCursorBlink();
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
    }

    // ----- Background dropdown -----
    bgSelect.addEventListener('change', function() {
        var bg = getBgById(bgSelect.value);
        buildSvg(bg);
        syncHiddenInputs();
    });

    // ----- Share as PNG -----
    function svgToPng(callback) {
        var clone = svgEl.cloneNode(true);
        // Remove cursor dots and row-active styling for clean export
        var cursors = clone.querySelectorAll('.cursor-on');
        for (var i = 0; i < cursors.length; i++) cursors[i].parentNode.removeChild(cursors[i]);
        var activeRows = clone.querySelectorAll('.row-active');
        for (var i = 0; i < activeRows.length; i++) activeRows[i].removeAttribute('class');

        var svgData = new XMLSerializer().serializeToString(clone);
        var svgBlob = new Blob([svgData], {type: 'image/svg+xml;charset=utf-8'});
        var url = URL.createObjectURL(svgBlob);

        var vb = svgEl.getAttribute('viewBox').split(' ');
        var w = parseFloat(vb[2]);
        var h = parseFloat(vb[3]);

        var img = new Image();
        img.onload = function() {
            var canvas = document.createElement('canvas');
            canvas.width = w;
            canvas.height = h;
            var ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, w, h);
            URL.revokeObjectURL(url);
            canvas.toBlob(function(blob) {
                callback(blob);
            }, 'image/png');
        };
        img.src = url;
    }

    document.getElementById('btn-submit').addEventListener('click', function() {
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

})();
</script>

</body>
</html>