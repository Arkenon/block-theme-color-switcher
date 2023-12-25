import chroma from "chroma-js";

function selectColors(palette) {
    // Renkleri analiz edin
    let analyzedColors = Object.values(palette).map(color => ({
        color,
        brightness: chroma(color).get('hsl.l')
    }));

    // Renkleri aydınlık seviyelerine göre sıralayın
    analyzedColors.sort((a, b) => a.brightness - b.brightness);

    // Arka plan ve yazı rengini seçin (en karanlık ve en aydınlık)
    let color_bg = analyzedColors[0].color;
    let color_text = analyzedColors[analyzedColors.length - 1].color;

    // Ana ve ikincil renkleri seçin (orta tonlar)
    let color_1 = analyzedColors[Math.floor(analyzedColors.length / 3)].color;
    let color_2 = analyzedColors[Math.floor(2 * analyzedColors.length / 3)].color;

    return { color_bg, color_text, color_1, color_2 };
}

let finalColors = {};
// Tüm paletleri işleyin ve konsola yazdırın

for (const paletteName in palettes) {
    const palette = palettes[paletteName];
    finalColors[paletteName] = selectColors(palette); // finalColors objesine anahtar-değer çifti olarak ekleyin
}

console.log(finalColors)
// color-switcher.js
// Tüm paletleri işleyin ve konsola yazdırın


document.addEventListener('DOMContentLoaded', function() {

    const paletteContainer = document.querySelector('.palette-container');

    Object.entries(palettes).forEach(([paletteName, colors],index) => {
        const paletteBox = document.createElement('div');
        paletteBox.className = 'palette-box';
        paletteBox.onclick = function() { changeColorPalette(paletteName); };
        paletteBox.style.backgroundColor = finalColors[paletteName]['color_bg'];

        const colorPrimary = document.createElement('div');
        colorPrimary.className = 'color-circle primary';
        colorPrimary.style.backgroundColor = finalColors[paletteName]['color_1'];;

        const colorHover = document.createElement('div');
        colorHover.className = 'color-circle hover';
        colorHover.style.backgroundColor = finalColors[paletteName]['color_2'];;

        const text = document.createElement('h6');
        text.style.color = finalColors[paletteName]['color_text'];;
        text.className = 'color-circle text';
        text.textContent="A";

        paletteBox.appendChild(text);
        paletteBox.appendChild(colorPrimary);
        paletteBox.appendChild(colorHover);

        paletteContainer.appendChild(paletteBox);
    });
});

// Sayfa yüklendiğinde olay dinleyicilerini ayarla
document.addEventListener('DOMContentLoaded', function() {

    var button = document.querySelector('.off-canvas-button');
    var menu = document.querySelector('.off-canvas-menu');

    // Menü dışına tıklandığında menüyü kapat
    document.addEventListener('click', function(event) {
        if (!menu.contains(event.target) && !button.contains(event.target)) {
            menu.classList.remove('open');
        }
    });


    /*    const defaultColors = {
            '--wp--preset--color--base': '#FFFEFD',
            '--wp--preset--color--contrast': '#18191a',
            '--wp--preset--color--primary': '#009E66',
            '--wp--preset--color--hover': '#00B380',
            '--wp--preset--color--soft': '#f0f9ee'
        };*/

    const bodyElement = document.body; // body etiketini al

    Object.keys(defaultColors).forEach(key => {
        const storedColor = localStorage.getItem(key) || defaultColors[key];
        bodyElement.style.setProperty(key, storedColor);
        if (!localStorage.getItem(key)) {
            localStorage.setItem(key, defaultColors[key]);
        }
    });

});

function toggleColorSwitcherMenu() {
    var menu = document.getElementById('colorSwitcherMenu');
    menu.classList.toggle('open');
}

function changeColorPalette(paletteName) {

    // Seçilen renk paletini al ve uygula
    const selectedPalette = palettes[paletteName];
    for (const [key, value] of Object.entries(selectedPalette)) {
        document.documentElement.style.setProperty(key, value);
        localStorage.setItem(key, value); // Renkleri local storage'a kaydet
    }
    // Tüm sayfayı güncelle
    updatePageColors(selectedPalette);
}

function updatePageColors(palette) {
    const bodyElement = document.body; // body etiketini al

    for (const [key, value] of Object.entries(palette)) {
        // CSS değişkenlerini güncelle
        bodyElement.style.setProperty(key, value);
    }
}


