const gridContainer = document.getElementById('imageGrid-container');
const figures = gridContainer.getElementsByTagName('figure');

for (let i=0; i < figures.length; i++) {
    const item = figures[i];
    const img = item.firstChild.firstChild;
    const figcaption = item.lastChild;
    const image = new Image();
    image.onload = () => {
        const imgHeight = img.offsetHeight;
        const figcaptionHeight = figcaption.offsetHeight + 10;
        item.style.gridRowEnd = 'span ' + (imgHeight + figcaptionHeight);
    }
    image.src = img.src;
}