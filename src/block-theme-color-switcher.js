

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





(function(window){
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

    function toggleColorSwitcherMenu() {
        var menu = document.getElementById('colorSwitcherMenu');
        menu.classList.toggle('open');
    }
    window.changeColorPalette = changeColorPalette;
    window.updatePageColors = updatePageColors;
    window.toggleColorSwitcherMenu = toggleColorSwitcherMenu;
})(window);