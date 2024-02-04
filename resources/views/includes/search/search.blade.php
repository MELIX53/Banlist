<div id="filter_modal">
    <div class="filter_modal_content">
        <div style="position:relative;">
            <div style="position: absolute; right: 10px; width: 15px; height: 15px">
                <img src="{{ asset('images/close.png') }}" style="width: inherit; height: inherit"
                     onclick="hideFilterModal()">
            </div>
        </div>
        <div style="padding: 20px">
            <div id='filters' style="display: flex; justify-content: space-between">
                <div style="width: 100%; margin: auto 0;">
                    @foreach(config('settings.ports') as $port => $data)
                        <div>
                            <input type="checkbox" id="{{ $port }}" checked>
                            <label>{{ $data['prefix'] }}</label>
                        </div>
                    @endforeach
                </div>
                <div style="width: 100%">
                    <div>
                        <input type="checkbox" id="lock_chat" checked>
                        <label>Блокировки чата</label>
                    </div>
                    <div>
                        <input type="checkbox" id="lock_nick" checked>
                        <label>Баны по нику</label>
                    </div>
                    <div>
                        <input type="checkbox" id="lock_os" checked>
                        <label>Баны по OS</label>
                    </div>
                    <div>
                        <input type="checkbox" id="kick" checked>
                        <label>Выброшены с сервера</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="default_block">
    <div class="block_search" style="display: flex">
        <input id='search_nick' placeholder="Введите ник игрока" style="width: 100%;">
        <img src='{{ asset('images/filter.png') }}' onclick="showFilterModal()">
        <img src='{{ asset('images/search.png') }}' onclick="updateSearchData()">
    </div>
</div>

<div id="search_result"></div>
<div id="paginate"></div>

<script src="{{ asset('asset/search/SearchTables.js') }}"></script>
<script src="{{ asset('asset/search/Search.js') }}"></script>
<script src="{{ asset('asset/search/Post.js') }}"></script>

