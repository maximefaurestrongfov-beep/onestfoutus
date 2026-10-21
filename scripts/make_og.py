"""Génère og.png (image d'aperçu 1200x630) à partir de data.json.
Usage : python3 scripts/make_og.py   (depuis la racine du site)
"""
import json
from pathlib import Path
from PIL import Image, ImageDraw, ImageFont

ROOT = Path(__file__).resolve().parent.parent
data = json.loads((ROOT / "data.json").read_text(encoding="utf-8"))
p = float(data["current"])

def font(size, bold=True):
    names = ["DejaVuSansMono-Bold.ttf" if bold else "DejaVuSansMono.ttf"]
    for d in ["/usr/share/fonts/truetype/dejavu/", "/Library/Fonts/", ""]:
        for n in names:
            try:
                return ImageFont.truetype(d + n, size)
            except OSError:
                pass
    return ImageFont.load_default()

W, H = 1200, 630
DESK, DOT = (11, 122, 117), (10, 107, 103)
WIN, HI, LO, DK = (198, 195, 189), (255, 255, 255), (125, 122, 117), (43, 42, 40)
NAVY, BLUE, RED, INK = (11, 31, 110), (47, 111, 192), (163, 18, 27), (20, 20, 20)

img = Image.new("RGB", (W, H), DESK)
d = ImageDraw.Draw(img)
for yy in range(0, H, 6):
    for xx in range(0, W, 6):
        d.point((xx, yy), fill=DOT)

# fenêtre
x0, y0, x1, y1 = 60, 60, W - 60, H - 60
d.rectangle([x0 + 8, y0 + 8, x1 + 8, y1 + 8], fill=(0, 0, 0))
d.rectangle([x0, y0, x1, y1], fill=WIN)
d.line([x0, y0, x1, y0], fill=HI, width=3); d.line([x0, y0, x0, y1], fill=HI, width=3)
d.line([x0, y1, x1, y1], fill=DK, width=3); d.line([x1, y0, x1, y1], fill=DK, width=3)

# barre de titre en dégradé
for i in range(x1 - x0 - 8):
    t = i / (x1 - x0 - 8)
    c = tuple(int(NAVY[k] + (BLUE[k] - NAVY[k]) * t) for k in range(3))
    d.line([x0 + 4 + i, y0 + 4, x0 + 4 + i, y0 + 52], fill=c)
d.text((x0 + 22, y0 + 14), "Installation de l'Apocalypse", font=font(28), fill=HI)

# titre et pourcentage
d.text((x0 + 40, y0 + 90), "On est foutus ?", font=font(56), fill=INK)
pct = f"{p:.1f}".replace(".", ",") + " %"
d.text((x0 + 40, y0 + 175), pct, font=font(120), fill=NAVY)

# barre de progression
bx0, by0, bx1, by1 = x0 + 40, y1 - 140, x1 - 40, y1 - 80
d.rectangle([bx0, by0, bx1, by1], fill=HI)
d.line([bx0, by0, bx1, by0], fill=LO, width=3); d.line([bx0, by0, bx0, by1], fill=LO, width=3)
n, gap = 50, 4
inner = (bx1 - bx0 - 12)
w = (inner - gap * (n - 1)) / n
lit = round(p / 100 * n)
for i in range(lit):
    cx = bx0 + 6 + i * (w + gap)
    d.rectangle([cx, by0 + 6, cx + w, by1 - 6], fill=NAVY)

d.text((x0 + 40, y1 - 58), "onestfoutus.strongfamily.fr · mise à jour chaque jour", font=font(24, False), fill=INK)

img.save(ROOT / "og.png", optimize=True)
print("og.png généré :", pct)
