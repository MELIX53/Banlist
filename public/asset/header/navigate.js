
let selectType = "none";

const TIKTOK = "Тик-Ток";
const YOU_TUBE = "Ютуб";
const DONATE = "Донат";
const VK = "ВКонтакте"
const TELEGRAM = "Телеграм"

const URL_NAVIGATE = {
    [TIKTOK]: "https://www.tiktok.com/@marsdygers",
    [YOU_TUBE]: "https://www.youtube.com/@marsdygers",
    [DONATE]: "https://dygers.fun/donate",
    [VK]: "https://dygers.fun/vk",
    [TELEGRAM]: "https://dygers.fun/telegram"
}

let modal = document.getElementById("navigate_modal");

function showModal(type, selectImage){
    let image = document.getElementById("navigate_image");
    let text = document.getElementById("navigate_modal_text");

    selectType = type;

    image.src = selectImage.src;
    text.textContent = type;
    modal.style.display = "flex";
}

function hideModal(){
    modal.style.display = "none";
}

function navigateTransfer(){
    if(selectType in URL_NAVIGATE) {
        hideModal();
        window.location.href = URL_NAVIGATE[selectType];
    }
}
