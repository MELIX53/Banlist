let accountData = null;

let blockAccount = document.getElementById('block_account');
let blockNeedConfirm = document.getElementById('block_account_need_confirm_modal');

document.addEventListener('DOMContentLoaded', function () {

    updateAccountData().done(function (none){
        let urlParams = new URLSearchParams(window.location.search);
        let params = {};
        urlParams.forEach(function(value, key) {
            params[key] = value;
        });

        updateSearchData(currentPage, (Object.keys(params).length === 0 ? null : params));
    });
});

function showModalNeedConfirm() {
    showLoading();
    let tables = {
        lock_nick: true,
        lock_os: true,
        kick: true,
        lock_chat: true,
    };
    tables[accountData.port] = true;

    $.ajax({
        url: '/punishments/data',
        type: 'GET',
        data: {
            tables: tables,
            punishedName: accountData.nick,
        },
        success: function (response) {
            hideLoading();

            if (response.success === false) {
                sendPopupBlock(response.error_message);
            } else {
                let countElements = 0;
                let content_account_load = document.getElementById('content_account_load');
                content_account_load.innerHTML = "";
                response.response.data.forEach(element => {
                    if(element.url === null) {

                        blockNeedConfirm.style.display = 'flex';

                        let elementId = element.id;
                        let elementType = element.type;

                        let buttonSendFormLoad = document.createElement('button');
                        buttonSendFormLoad.classList.add('buttonSendFormLoad');
                        buttonSendFormLoad.textContent = 'Загрузить..';
                        buttonSendFormLoad.setAttribute('onclick', "sendFormLoadFiles(\'" + elementType + "\', \'" + elementId + "\')");

                        let loadElementContent = document.createElement('div');
                        loadElementContent.style.display = 'flex';
                        loadElementContent.innerHTML =
                            '<div style="display: inline-block"><p>Ник: <span style="color: #3cdffc">' + element.opponentName + '</span></p>' +
                            '<p>Причина: <span style="color: #3cdffc">' + element.reason + '</span></p>' +
                            '<p>Время создания: <span style="color: #3cdffc">' + element.timeGenerated + '</span></p></div>'
                        ;
                        loadElementContent.appendChild(buttonSendFormLoad);

                        let loadElement = document.createElement('div');
                        loadElement.className = 'default_block default_block_n2';
                        loadElement.style.margin = '20px auto';
                        loadElement.appendChild(loadElementContent);

                        content_account_load.appendChild(loadElement);
                        countElements++;
                    }
                });
                if(countElements === 0){
                    sendPopupBlock('У вас нет наказаний которые требуют доказательств!')
                }
            }
        },
        error: function () {
        }
    });
}

function hideModalNeedConfirm() {
    blockNeedConfirm.style.display = 'none';
}

function updateAccountData() {
    showLoading();
    accountData = null;

    let deferred = $.Deferred();
    $.ajax({
        url: '/api/get_account_data',
        method: 'POST',
        success: function (response) {
            hideLoading();
            deferred.resolve(response.response.data);
            if (response.success === true) {
                accountData = response.response.data;
                setContentAuthorized();
            } else {
                setContentNotAuthorized();
            }
        }, error: function (xhr, status, error) {
            deferred.reject(error);
        }
    });
    return deferred.promise();
}

function setContentAuthorized() {
    blockAccount.innerHTML = "";

    let divContent = document.createElement('div');
    divContent.className = 'block_account_content'
    divContent.style.display = 'flex';

    let image = document.createElement('img');
    image.src = '/images/steve.png';
    image.style.height = 'calc(100% - 90%)'
    image.style.width = '30%'
    image.style.margin = 'auto';
    image.style.borderRadius = '50%'

    let divText = document.createElement('div');
    divText.className = 'block_account_content_text'

    let messageTitle = document.createElement('p');
    messageTitle.style.textAlign = 'center'
    messageTitle.innerText = accountData.nick;
    messageTitle.style.fontWeight = 'bold';
    messageTitle.style.fontSize = '30px';

    let messageContent = document.createElement('div');
    messageContent.innerHTML =
        "<p>Название сервера: <span style='color: #3cdffc'>" + accountData.portPrefix + "</span></p>" +
        "<p>Забанено по Нику: <span id='accountMessageContentBans'></span></p>" +
        "<p>Забанено по OS: <span id='accountMessageContentBansOs'></span></p>" +
        "<p>Заблокировано Чатов: <span id='accountMessageContentBansChat'></span></p>" +
        "<p>Кикнуто: <span id='accountMessageContentKicks'></span></p>" +
        "<br>" +
        "<p>❗ Все эти данные указывают на все ваши блокировки в течении <span>7 дней</span></p>";
    let paragraphs = messageContent.querySelectorAll('p');
    paragraphs.forEach(paragraph => {
        paragraph.style.margin = '0';
    });
    let spans = messageContent.querySelectorAll('span');
    spans.forEach(span => {
        span.style.color = '#3cdffc';
    });

    let buttonQuit = document.createElement('button');
    buttonQuit.innerText = 'Выйти';
    buttonQuit.className = 'button_account button_account_red';
    buttonQuit.style.float = 'right';
    buttonQuit.setAttribute('onclick', 'quit()');

    let buttonNeedConfirm = document.createElement('button');
    buttonNeedConfirm.innerText = 'Загрузить..';
    buttonNeedConfirm.className = 'button_account button_account_blue';
    buttonNeedConfirm.style.float = 'right';
    buttonNeedConfirm.style.marginRight = '10px';
    buttonNeedConfirm.setAttribute('onclick', 'showModalNeedConfirm()');

    divContent.appendChild(image);
    divText.appendChild(messageTitle);
    divText.appendChild(messageContent);
    divContent.appendChild(divText);
    blockAccount.appendChild(divContent);
    blockAccount.appendChild(buttonQuit);
    blockAccount.appendChild(buttonNeedConfirm);
    updateAccountMessageContent();
}

function updateAccountMessageContent() {
    let tables = {
        lock_nick: true,
        lock_os: true,
        kick: true,
        lock_chat: true,
    }
    tables[accountData.port] = true;

    $.ajax({
        url: '/punishments/data',
        type: 'GET',
        data: {
            tables: tables,
            punishedName: accountData.nick,
        },
        success: function (response) {

            if (response.response === null) response.response = {'countRecords': {}};

            let accountMessageContentBans = document.getElementById('accountMessageContentBans');
            let accountMessageContentBansOs = document.getElementById('accountMessageContentBansOs');
            let accountMessageContentBansChat = document.getElementById('accountMessageContentBansChat');
            let accountMessageContentKicks = document.getElementById('accountMessageContentKicks');

            let prefix = ' игроков';
            accountMessageContentBans.innerText = (response.response.countRecords['count_bans'] ?? 0) + prefix;
            accountMessageContentBansOs.innerText = (response.response.countRecords['count_bans_os'] ?? 0) + prefix;
            accountMessageContentBansChat.innerText = (response.response.countRecords['count_mutes'] ?? 0) + prefix;
            accountMessageContentKicks.innerText = (response.response.countRecords['count_kicks'] ?? 0) + prefix;
        },
        error: function (xhr, status, error) {
        }
    });
}

function setContentNotAuthorized() {
    blockAccount.innerHTML = "";

    let divContent = document.createElement('div');
    divContent.style.display = 'flex';

    let image = document.createElement('img');
    image.src = '/images/warn.png';
    image.style.height = 'calc(100% - 50%)'
    image.style.width = '30%'
    image.style.margin = '0 auto';

    let divText = document.createElement('div');
    divText.style.height = 'inherit';
    divText.style.width = '100%';

    let messageTitle = document.createElement('p');
    let messageContent = document.createElement('p');
    messageTitle.innerText = "Вы не авторизированы!";
    messageContent.innerText = 'Авторизируйтесь чтобы получить доступ к своему аккаунту и получить возможность загружать доказательства на свои наказания';

    let buttonLogin = document.createElement('button');
    buttonLogin.innerText = 'Войти';
    buttonLogin.className = 'button_account button_account_green';
    buttonLogin.style.float = 'right';
    buttonLogin.setAttribute('onclick', 'showAuthForm()')

    divContent.appendChild(image);
    divText.appendChild(messageTitle);
    divText.appendChild(messageContent);
    divContent.appendChild(divText);
    blockAccount.appendChild(divContent);
    blockAccount.appendChild(buttonLogin);
}
