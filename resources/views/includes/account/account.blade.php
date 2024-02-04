<div id='block_account' class="default_block"></div>

<div id="block_account_need_confirm_modal">
    <div class="block_account_need_confirm_modal_content">
        <div style="position: relative;">
            <div class="login_close" onclick="hideModalNeedConfirm()">
                <img src="{{ asset('images/close.png') }}" style="width: inherit; height: inherit">
            </div>
        </div>
        <div style="width: 100%; margin-top: 25px">
            <div style="text-align: center">
                <h3>Загрузите доказательства!</h3>
            </div>
            <div style="margin-top: 30px">
                <ul>
                    <li>Все доказательства не должны превышать отметку <span style="color: #3cdffc">200МБ</span></li>
                    <li>На доказательствах должно быть четко видно <span style="color: #3cdffc">НИК</span> нарушителя
                    </li>
                    <li>Запрещено загружать файлы не типов <span style="color: #3cdffc">МЕДИА</span></li>
                </ul>
            </div>
        </div>
        <div id="content_account_load" style="padding: 0 15px"></div>
    </div>
</div>

<div id="block_account_load_modal">
    <div class="block_account_load_modal_content">
        <div style="position: relative;">
            <div class="login_close" onclick="hideFormLoadFiles()">
                <img src="{{ asset('images/close.png') }}" style="width: inherit; height: inherit">
            </div>
        </div>
        <div style="padding: 15px">
            <div style="text-align: center; margin-top: 20px">
                <h4>Выберите файлы</h4>
            </div>
            <div style="display: flex; justify-content: center">
                <div style="width: 90%; height: 15px; background-color: rgba(42,41,41,0.9); border-radius: 10px; margin-top: 20px">
                    <div id="load_files_progress" style="height: inherit; width: 0; background-color: #17e017; border-radius: 10px"></div>
                </div>
            </div>
            <div style="margin-top: 20px">

                <div style="display: flex">
                    <div style="margin: auto 0; overflow: auto; width: 50%">
                        <p style="margin: 0">Вес файлов: <span id="select_files_size">0КБ</span>.</p>
                        <p style="margin: 0">Выбрано файлов: <span id="select_files_count">0</span>.</p>
                    </div>
                    <div style="margin-left: auto">
                        <label class="input-file">
                            <input id='input_load_file' type="file" name="files[]" accept="image/*,video/*,.gif" multiple>
                            <span>Выберите файлы</span>
                        </label>
                    </div>
                </div>
                <div style="display:flex;">
                <button id="button_send_files" class="button_send_files" style=" margin: 50px auto 20px auto" >Отправить</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src='{{ asset('asset/account/account.js') }}'></script>
<script src='{{ asset('asset/account/loadProcess.js') }}'></script>
