let login = document.getElementById('login_modal');
let loginTwoFactor = document.getElementById('login_twofactor_modal');

function showAuthTwoFactor() {
    loginTwoFactor.style.display = "flex";
}

function hideAuthTwoFactor() {
    loginTwoFactor.style.display = "none";
}

function showAuthForm() {
    login.style.display = "flex";
}

function hideAuthForm() {
    login.style.display = "none";
}

function quit() {
    showLoading();
    $.ajax({
        url: '/api/logout',
        method: 'POST',
        success: function (_) {
            setContentNotAuthorized();
            sendPopupBlock('Вы успешно вышли с своего аккаунта ' + accountData.nick);
            showLoading()
            updateAccountData().done(function (none){
                updateSearchData(currentPage);
            });
            hideLoading();
        },
        error: function (xhr, status, error) {
            hideLoading();
        }
    });
}

function sendCode() {
    let code = document.getElementById('code_twofactor').value;
    showLoading();
    $.ajax({
        url: '/api/login',
        method: 'POST',
        data: {
            code: code
        },
        success: function (response) {
            hideLoading();
            if (response.success === false) {
                if (response.error_message == null) {
                    sendPopupBlock('Вы не ввели код!', RED);
                    return;
                } else {
                    sendPopupBlock(response.error_message, RED)
                }
            } else {
                sendPopupBlock(response.response.message);
                showLoading()
                updateAccountData().done(function (none){
                    updateSearchData(currentPage);
                });
            }
            hideAuthTwoFactor();
        },
        error: function (xhr, status, error) {
            hideLoading();
        }
    });
}

function sendLogin() {
    let nick = document.getElementById('nick').value;
    let password = document.getElementById('password').value;
    let selectServer = document.getElementById('login_select_server');
    let port = selectServer.options[selectServer.selectedIndex].value;
    showLoading();
    $.ajax({
            url: '/api/login',
            method: 'POST',
            data: {
                nick: nick,
                password: password,
                port: port
            },
            success: function (response) {
                hideLoading();
                if (response.success === false) {
                    if (response.error_message !== null) {
                        sendPopupBlock(response.error_message, RED);
                        return;
                    }
                    if (response.response['two-factor'] === true) {
                        hideAuthForm();
                        showAuthTwoFactor();
                    }
                    return;
                }

                if (response.error_message !== null) {
                    sendPopupBlock(response.error_message, RED);
                } else {
                    sendPopupBlock(response.response.message, GREEN);
                    showLoading()
                    updateAccountData().done(function (none){
                        updateSearchData(currentPage);
                    });
                }
                hideAuthForm();
            },
            error:
                function (xhr, status, error) {
                    hideLoading();
                }
        }
    );
}
