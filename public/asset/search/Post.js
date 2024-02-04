let post_data = {};

let POST_ID = 0;

let SELECT_FORM_NAVIGATE = null;

const TYPE_POST_ID = 'postID-';
const TYPE_SLIDER_ID = 'slider_ID-';

const TYPE_FORM_NAVIGATE = 'formID-';
const TYPE_FORM_NAVIGATE_BUTTON = 'formButtonID-';

function handleClick(event) {
    let target = event.target;

    let closest = target.closest('.ignoreClickAll');
    if (closest !== null) return;

    if (SELECT_FORM_NAVIGATE !== null && SELECT_FORM_NAVIGATE.style.display === 'flex') {

        let selectFormNavigateID = SELECT_FORM_NAVIGATE;
        selectFormNavigateID = selectFormNavigateID.id.split('-')[1];

        let targetId = target;
        targetId = (targetId.id ? (targetId.id.split('-')[1] ?? null) : null);

        if (target.className === 'ignoreClick' && selectFormNavigateID === targetId) return;

        if (target.id !== SELECT_FORM_NAVIGATE.id) {
            SELECT_FORM_NAVIGATE.style.display = 'none';
            SELECT_FORM_NAVIGATE = null;
            event.preventDefault();
        }
    }

}

document.addEventListener('click', handleClick);


function addFoundPost(element) {
    POST_ID++;
    let nextPostId = TYPE_POST_ID + POST_ID;

    let defaultBlock = document.createElement('div');
    defaultBlock.className = 'default_block';

    defaultBlock.appendChild(createPostNavigate(element));

    let blockContainer = document.createElement('div');

    let blockImage = document.createElement('div');
    blockImage.style.width = '100%';
    blockImage.style.height = '250px';
    blockImage.style.alignItems = 'center';
    blockImage.style.display = 'flex';
    blockImage.style.justifyContent = 'center';

    let blockImageContainer = document.createElement('div');
    blockImageContainer.className = 'block_slider_flex_center';
    blockImageContainer.id = nextPostId;

    blockImage.appendChild(blockImageContainer);
    blockContainer.appendChild(blockImage);
    defaultBlock.appendChild(blockContainer);
    search_result.appendChild(defaultBlock);

    if (element.url !== null && ((accountData !== null && accountData.is_admin) || element.confirmed === 1)) {
        post_data[nextPostId] = {'media': {}, 'selectID': {}}

        let blockCountMedia = document.createElement('div');
        blockCountMedia.id = TYPE_SLIDER_ID + POST_ID;
        blockCountMedia.style.display = 'flex';
        blockCountMedia.style.justifyContent = 'center';

        let urlData = JSON.parse(element.url);
        let SLIDER_ID = 1;
        urlData.forEach(function (urlData) {

            let media;
            switch (urlData['type']) {
                case 'video/mp4':
                    media = document.createElement('video');
                    media.setAttribute('src', urlData['url']);
                    media.style.maxWidth = '100%';
                    media.style.maxHeight = '250px';
                    media.style.borderRadius = '15px';
                    media.controls = true;
                    break;
                default:
                    media = document.createElement('img');
                    media.setAttribute('onclick', 'fullScreenImage(this)')
                    media.setAttribute('src', urlData['url']);
                    media.style.maxWidth = '100%';
                    media.style.maxHeight = '250px';
                    media.style.borderRadius = '10px';
                    break;
            }

            post_data[nextPostId]['media'][SLIDER_ID] = media;
            post_data[nextPostId]['selectID'] = 0

            let blockIconSelected = document.createElement('div');
            blockIconSelected.id = POST_ID + TYPE_SLIDER_ID + SLIDER_ID;
            blockIconSelected.style.borderRadius = '100%';
            blockIconSelected.setAttribute('onclick', "changeSlider(\'" + POST_ID + "\', \'" + SLIDER_ID + "\')");
            blockIconSelected.style.width = '15px';
            blockIconSelected.style.height = '15px';
            blockIconSelected.style.margin = '10px';
            blockIconSelected.style.backgroundColor = '#282727';
            blockCountMedia.appendChild(blockIconSelected);

            blockContainer.appendChild(blockCountMedia);
            changeSlider(POST_ID, 1);

            SLIDER_ID++;
        });
    } else {
        let image = document.createElement('img');
        image.setAttribute('src', '/images/nopruf.png');
        image.style.maxWidth = '100%';
        image.style.maxHeight = '250px';
        image.style.borderRadius = '10px';
        blockImage.appendChild(image);
        blockContainer.appendChild(blockImage);
    }

    let blockTextContent = document.createElement('div');
    blockTextContent.className = 'block_text_content_slider';

    let blockTextContentTitle = {
        lock_nick: 'Блокировка по нику',
        lock_os: 'Блокировка по OS',
        lock_chat: 'Блокировка по чату',
        kick: 'Выброшен с сервера'
    }

    blockTextContent.innerHTML += "<p>Тип наказания: <span style='color: gold'>" + blockTextContentTitle[element.type] + "</span></p>";
    blockTextContent.innerHTML += "<br>";

    switch (element.type) {
        case 'lock_nick':
        case 'lock_os':
            blockTextContent.innerHTML += "<p>Ник забаненого: <span style='color: gold'>" + element.opponentName + "</span></p>";
            blockTextContent.innerHTML += "<p>Забанил игрок: <span style='color: gold'>" + element.punishedName + "</span></p>";
            if (element.pardoned !== null) {
                blockTextContent.innerHTML += "<p>Разбанен игроком: <span style='color: gold'>" + element.pardoned + "</span>";
            }
            break;
        case 'lock_chat':
            blockTextContent.innerHTML += "<p>Ник замученого: <span style='color: gold'>" + element.opponentName + "</span></p>";
            blockTextContent.innerHTML += "<p>Замутил игрок: <span style='color: gold'>" + element.punishedName + "</span></p>";
            if (element.pardoned !== null) {
                blockTextContent.innerHTML += "<p>Размучен игроком: <span style='color: gold'>" + element.pardoned + "</span></p>";
            }
            break;
        case 'kick':
            blockTextContent.innerHTML += "<p>Ник кикнутого: <span style='color: gold'>" + element.opponentName + "</span></p>";
            blockTextContent.innerHTML += "<p>Кикнул игрок: <span style='color: gold'>" + element.punishedName + "</span></p>";
            break;
    }

    blockTextContent.innerHTML += "<br>";
    blockTextContent.innerHTML += "<p>Причина: <span style='color: gold'>" + element.reason + "</span></p>";
    blockTextContent.innerHTML += "<br>";

    blockTextContent.innerHTML += "<p>Время создания: <span style='color: gold'>" + element.timeGenerated + "</span></p>"
    if (element.type !== 'kick') {
        blockTextContent.innerHTML += "<p>Блокировка до: <span style='color: gold'>" + element.timeLocking + "</span></p>";
    }

    if (element.confirmed === 0 && element.url !== null) {
        blockTextContent.innerHTML += "<br><p style='color: red'>Доказательства не подтверждены Администратором!</p>"
    }

    blockTextContent.innerHTML += '<br>';
    blockTextContent.innerHTML += "<p style='color: gold; float: right'>" + element.portPrefix + "</p>";
    blockTextContent.innerHTML += '<br>';

    defaultBlock.appendChild(blockTextContent);
}

function createPostNavigate(element) {
    let line = document.createElement('div');
    line.style.width = '100%';
    line.style.height = '1px';
    line.style.backgroundColor = '#313131';

    let navigateContainerRelative = document.createElement('div');
    navigateContainerRelative.style.position = 'relative';
    navigateContainerRelative.style.zIndex = '1';

    let navigateAbsolute = document.createElement('div');
    navigateAbsolute.id = TYPE_FORM_NAVIGATE_BUTTON + POST_ID;
    navigateAbsolute.style.width = 'auto';
    navigateAbsolute.style.height = 'auto';
    navigateAbsolute.style.position = 'absolute';
    navigateAbsolute.style.fontSize = '30px';
    navigateAbsolute.style.fontWeight = 'bold';
    navigateAbsolute.style.textAlign = 'center';
    navigateAbsolute.style.color = 'rgb(59,57,57)';
    navigateAbsolute.style.right = '-10px';

    if (accountData !== null && accountData.is_admin) {
        navigateAbsolute.className = 'ignoreClick';
        navigateAbsolute.setAttribute('onclick', "sendFormPostNavigate(\'" + POST_ID + "\')");
        navigateAbsolute.style.top = '-30px';
        navigateAbsolute.innerText = '...';
    } else {
        navigateAbsolute.style.top = '-20px';
        navigateAbsolute.innerHTML =
            '<img src="images/copy.png" style="filter: invert(50%)" width="30px" height="30px" onclick="copyPostUrl(\'' + element.id + '\', \'' + element.type + '\')">';
    }

    navigateContainerRelative.appendChild(navigateAbsolute);

    if (accountData !== null && accountData.is_admin) {
        let navigateAbsoluteForm = document.createElement('div');
        navigateAbsoluteForm.id = TYPE_FORM_NAVIGATE + POST_ID;
        navigateAbsoluteForm.className = 'ignoreClickAll';
        navigateAbsoluteForm.style.display = 'none';
        navigateAbsoluteForm.style.flexDirection = 'column';
        navigateAbsoluteForm.style.position = 'absolute';
        navigateAbsoluteForm.style.borderRadius = '15px';
        navigateAbsoluteForm.style.backgroundColor = 'rgba(15, 15, 15, 0.7)';
        navigateAbsoluteForm.style.backdropFilter = 'blur(10px)';
        navigateAbsoluteForm.style.right = '10px';
        navigateAbsoluteForm.style.top = '15px';

        let buttonCopy = document.createElement('div');
        buttonCopy.setAttribute('onclick', 'copyPostUrl(\'' + element.id + '\', \'' + element.type + '\')');
        buttonCopy.className = 'button_post_form_navigate';
        buttonCopy.style.color = 'white';
        buttonCopy.innerHTML = 'Копировать ссылку';
        navigateAbsoluteForm.appendChild(buttonCopy);


        navigateAbsoluteForm.appendChild(line.cloneNode(true));

        let buttonAccept = document.createElement('div');
        buttonAccept.setAttribute('onclick', 'confirmPost(\'' + element.id + '\', \'' + element.type + '\')');
        buttonAccept.className = 'button_post_form_navigate';
        buttonAccept.style.color = '#55ff00';
        buttonAccept.innerHTML = 'Подтвердить доки.';
        navigateAbsoluteForm.appendChild(buttonAccept);

        navigateAbsoluteForm.appendChild(line.cloneNode(true));

        let buttonDelete = document.createElement('div');
        buttonDelete.setAttribute('onclick', 'removePost(\'' + element.id + '\', \'' + element.type + '\')');
        buttonDelete.className = 'button_post_form_navigate';
        buttonDelete.style.color = '#ff0000';
        buttonDelete.innerHTML = 'Удалить наказание';
        navigateAbsoluteForm.appendChild(buttonDelete);
        navigateContainerRelative.appendChild(navigateAbsoluteForm);
    }

    return navigateContainerRelative;
}

function sendFormPostNavigate(postID) {
    if (SELECT_FORM_NAVIGATE !== null) return;
    let formPostNavigate = document.getElementById(TYPE_FORM_NAVIGATE + postID);
    SELECT_FORM_NAVIGATE = formPostNavigate;
    formPostNavigate.style.display = 'flex';
}

function changeSlider(postId, sliderID) {
    let nextPostIdString = TYPE_POST_ID + postId
    let blockImage = document.getElementById(nextPostIdString);
    blockImage.innerHTML = "";
    resetPostSliders(postId);

    function validSelectId(nextSliderID, selectId) {
        if (selectId <= 0) {
            return 1;
        }
        if (!(selectId in post_data[nextSliderID]['media'])) {
            return 1;
        }
        return selectId;
    }

    let newSliderID = validSelectId(nextPostIdString, sliderID);
    let media = post_data[nextPostIdString]['media'][newSliderID];

    post_data[nextPostIdString]['selectID'] = newSliderID;
    blockImage.appendChild(media);

    let slider = document.getElementById(postId + TYPE_SLIDER_ID + newSliderID);
    slider.style.backgroundColor = '#fff';
}

function resetPostSliders(postId) {
    let post = document.getElementById(TYPE_SLIDER_ID + postId);
    let selectors = post.querySelectorAll('div');
    if (selectors !== null) {
        selectors.forEach(function (slider) {
            slider.style.backgroundColor = '#282727';
        });
    }
}

function fullScreenImage(fullscreenImage) {
    if (!document.fullscreenElement) {
        fullscreenImage.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

function copyPostUrl(postId, typePost) {
    function jsonToQueryString(json) {
        return Object.keys(json)
            .map(function (key) {
                return encodeURIComponent(key) + '=' + encodeURIComponent(json[key]);
            })
            .join('&');
    }

    let data = {
        postId: postId,
        typePost: typePost
    }

    navigator.clipboard.writeText((window.location.href.split('?')[0] ?? window.location.href) + "?" + jsonToQueryString(data)).then(function () {
        sendPopupBlock('Ссылка успешно копирована!');
    });
}

function confirmPost(postId, typeLock){
    showLoading();
    $.ajax({
        url: '/api/confirmed_post',
        method: 'POST',
        data: {
            postId: postId,
            typeLock: typeLock
        },
        success: function (response) {
            console.log(response);
            hideLoading();

            if (response.success) {
                sendPopupBlock(response.response.message);
                updateSearchData(currentPage);
            } else {
                sendPopupBlock(response.error_message, RED);
            }

        }, error: function (xhr, status, error) {
            hideLoading();
            sendPopupBlock('Неудалось отправить запрос!', RED);
        }
    });
}

function removePost(postId, typeLock) {
    showLoading();
    $.ajax({
        url: '/api/remove_post',
        method: 'POST',
        data: {
            postId: postId,
            typeLock: typeLock
        },
        success: function (response) {
            hideLoading();

            if (response.success) {
                sendPopupBlock(response.response.message);
                updateSearchData(currentPage);
            } else {
                sendPopupBlock(response.error_message, RED);
            }

        }, error: function (xhr, status, error) {
            hideLoading();
            sendPopupBlock('Неудалось отправить запрос!', RED);
        }
    });
}
