<div id="login_twofactor_modal">
    <div id="login_twofactor_modal_content">

        <div style="position: relative;">
            <div class="login_close" onclick="hideAuthTwoFactor()">
                <img src="{{ asset('images/close.png') }}" style="width: inherit; height: inherit">
            </div>
        </div>

        <div class="login_modal_info">
            <div style="width: 150px; height: 150px; margin: 0 auto">
                <img src="{{ asset('images/icon_telegram.png') }}" style="width: inherit; height: inherit">
            </div>
            <div style="margin-top: 15px">
                <p>Данный аккаунт привязан к <span style="color: #3cdffc">Telegram</span>!</p>
                <p>Введите код который был отправлен на ваш телеграм, чтобы авторизироваться на сайте!</p>
            </div>

            <div style="display: inline; width: 100%;">
                <div class="login_block_form">
                    <div style="padding: 5px">
                        <input id='code_twofactor' placeholder="Введите код">
                    </div>
                    <div style="padding: 5px; width: 100px; margin: 0 auto">
                        <button style="width: inherit;" onclick="sendCode()">Отправить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="login_modal">
    <div id="login_modal_content">

        <div style="position: relative;">
            <div class="login_close" onclick="hideAuthForm()">
                <img src="{{ asset('images/close.png') }}" style="width: inherit; height: inherit">
            </div>
        </div>

        <div class="login_modal_info" style="width: 100%; padding: 20px">

            <div style="width: 150px; height: 150px; margin: 0 auto">
                <img src="{{ asset('images/logo.png') }}" style="width: inherit; height: inherit">
            </div>
            <p>Укажите сервер на котором хотите авторизироваться, введите свой ник и пароль чтобы получить возможность
                загружать доказательства на свои наказания</p>


            <div style="display: inline; width: 100%;">
                <div class="login_block_form">
                    <div style="padding: 5px; display: flex; justify-content: center">
                        <select id="login_select_server">
                            @foreach(config('settings.ports') as $port => $data)
                                <option value="{{ $port }}">{{ $data['prefix'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div style="padding: 5px">
                        <input id='nick' placeholder="Введите свой ник">
                    </div>
                    <div style="padding: 5px">
                        <input id='password' placeholder="Введите пароль" type="password">
                    </div>
                    <div style="padding: 5px; width: 100px; margin: 0 auto">
                        <button style="width: inherit;" onclick="sendLogin()">Войти</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src='{{ asset('asset/account/auth.js') }}'></script>
