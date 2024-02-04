<header>
    <div style="display: flex; justify-content: space-evenly">
        <div class="nav_buttons nav_button_telegram">
            <img src='{{ asset('images/icon_telegram.png') }}' title="Телеграм сервера" onclick="showModal(TELEGRAM, this)">
        </div>
        <div class="nav_buttons nav_button_vk">
            <img src='{{ asset('images/icon_vk.png') }}' title="Вконтакте сервера" onclick="showModal(VK, this)">
        </div>
        <div class="nav_buttons nav_button_donate">
            <img src='{{ asset('images/icon_donate.png') }}' title="Донат сервера" onclick="showModal(DONATE, this)">
        </div>
        <div class="nav_buttons nav_button_youtube">
            <img src='{{ asset('images/icon_youtube.png') }}' title="Ютуб сервера" onclick="showModal(YOU_TUBE, this)">
        </div>
        <div class="nav_buttons nav_button_tiktok">
            <img src='{{ asset('images/icon_tiktok.png') }}' title="ТикТок сервера" onclick="showModal(TIKTOK, this)">
        </div>
    </div>
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" onclick="window.location.href = window.location.href.split('?')[0];">
    </div>

    <div id="navigate_modal">
        <div id="navigate_modal_content">
            <div class="navigate_modal_info">
                <div class="block_navigate_image">
                    <img id="navigate_image" src="" alt="">
                </div>
                <p>Вы действительно хотите перейти в <span id="navigate_modal_text" style="color: aqua"></span> сервера?</p>
            </div>
            <div class="navigate_modal_buttons">
                <button style="border-bottom-right-radius: 0; background-color: rgb(248,63,63);" onclick="hideModal()">Нет</button>
                <button style="border-bottom-left-radius: 0; background-color: rgb(128,194,4)" onclick="navigateTransfer()">Да</button>
            </div>
        </div>
    </div>
</header>

<script src='{{ asset('asset/header/navigate.js') }}'></script>
