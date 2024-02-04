let POPUP_BLOCK = document.querySelector('#popup_block');
let POPUP_ID = 0;

const GREEN = "popup_content_block_green";
const RED = "popup_content_block_red";

window.addEventListener('resize', function (_) {
    resizePopupBlock();
});

document.addEventListener('DOMContentLoaded', function () {
    resizePopupBlock();
});

function resizePopupBlock() {
    let screenWidth = window.innerWidth;
    let blockPopup = POPUP_BLOCK.querySelector('.popup_content_block');

    if (blockPopup === null) return;

    if (screenWidth <= 555) {
        blockPopup.style.width = screenWidth - 40 + "px";
    } else {
        blockPopup.style.width = '500px'
    }
}

function sendPopupBlock(text, color = GREEN) {
    let nextId = 'popup_content_id_' + nextPopupId();

    let popupContent = document.createElement('div');
    popupContent.id = nextId;
    popupContent.className = 'popup_content_block ' + color;

    let popupImage = document.createElement('img');
    popupImage.src = 'images/popup_close.png';

    let popupImageBlock = document.createElement('div');
    popupImageBlock.className = 'popup_image_close';
    popupImageBlock.setAttribute('onclick', "closePopup(\'" + nextId + "\')");
    popupImageBlock.appendChild(popupImage)

    popupContent.appendChild(popupImageBlock);
    popupContent.innerHTML += "<p>" + text + "</p>";
    POPUP_BLOCK.appendChild(popupContent);

    window.setTimeout(function (){
        closePopup(nextId);
    }, 5000);

    resizePopupBlock();
}

function closePopup(popupId) {
    let popup = document.getElementById(popupId);
    if(popup === null) return;
    POPUP_BLOCK.removeChild(popup);
    resizePopupBlock();
}

function nextPopupId() {
    POPUP_ID++;
    return POPUP_ID
}