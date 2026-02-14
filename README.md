# Lampaš Maker

Web tool that generates SVG replicas of the iconic **Lampaš** — the electronic scoreboard from FK Partizan's JNA Stadium in Belgrade.

Users type up to 8 rows × 26 characters. Each character is rendered as a grid of lit/unlit lamps on a dark background, faithfully recreating the retro scoreboard aesthetic.

## Features

- SVG output — scalable, no dependencies, downloadable
- 5×7 pixel font with full Serbian/Croatian alphabet (Č, Ć, Đ, Š, Ž)
- Numbers 0–9, punctuation (`,` `.` `*` `-` `>` `<` `:`)
- Template-based SVG generation (easy to customize colors/sizes)
- Retro terminal-style web form

## Requirements

- PHP 7.4+ with `mbstring` and `intl` extensions
- A web server (Apache, Nginx, or `php -S localhost:8000`)

## Quick Start

```bash
# Clone and serve
git clone https://github.com/IvanSarajcic/lampas.git
cd lampas
php -S localhost:8000
```

Open [http://localhost:8000](http://localhost:8000) in your browser.

## Project Structure

```
index.php              – Input form (8 rows × 26 chars)
LampasSvg.php          – SVG rendering engine + result page
font.json              – 5×7 pixel font definitions
settings.svg.json      – SVG layout & color configuration
svg.pattern.wrapper    – SVG document template
svg.pattern.segment    – Character cell template
svg.pattern.pixel      – Individual lamp circle template
css/lampas.scss        – Styles (SCSS source)
css/lampas.css         – Compiled CSS
out/                   – Generated SVG output directory
```

## Customization

Edit `settings.svg.json` to change colors, sizes, and margins. Key settings:

| Setting | Description |
|---------|-------------|
| `bg-color` | Overall background color |
| `segment-rect-bg-color` | Character cell background |
| `segment-circle-on-bg-color` | Lit lamp color |
| `segment-circle-off-bg-color` | Unlit lamp color |
| `total-rows` / `total-cols` | Grid size (default 8×26) |

## Adding Characters

Edit `font.json`. Each character is a 7×5 matrix of `0` (off) and `1` (on):

```json
"!": [
  [0,0,1,0,0],
  [0,0,1,0,0],
  [0,0,1,0,0],
  [0,0,1,0,0],
  [0,0,0,0,0],
  [0,0,1,0,0],
  [0,0,1,0,0]
]
```
