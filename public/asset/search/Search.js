let per_page = 5; //Кол-во элементов на странице
let currentPage = 1;

const search_nick = document.getElementById('search_nick');

search_nick.addEventListener('keypress', function (event){
   if(event.key === 'Enter'){
       updateSearchData(currentPage);
   }
});

const search_result = document.getElementById('search_result');
const paginationContainer = document.getElementById('paginate');

function setSearchResultContent(response) {
    search_result.innerHTML = "";
    paginationContainer.innerHTML = "";

    let success = response.success;
    if (success === true) {
        let data = response.response.data;

        if (typeof data === 'object'){
            data = Object.values(data);
        }

        data.forEach(function (element) {
            addFoundPost(element);
        });

        setPagination(response.response.countPages);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }else{
        sendPopupBlock('По заданным критериям ничего не найдено!', RED)
    }
}

function setPagination(totalPages) {
    const paginationBlock = document.createElement('div');
    paginationBlock.classList.add('pagination_block')

    const paginationList = document.createElement('ul');
    paginationList.classList.add('pagination');

    function createPaginationItem(pageNumber, text) {
        const listItem = document.createElement('li');
        const link = document.createElement('a');

        function selectPage(pageNumber) {
            return function (event) {
                updateSearchData(pageNumber);
                event.preventDefault();
            };
        }

        link.textContent = text;
        listItem.appendChild(link);

        if (pageNumber === currentPage) {
            link.classList.add('current-page');
        }else{
            link.classList.add('select-page');
            link.onclick = selectPage(pageNumber);
        }

        paginationList.appendChild(listItem);
    }

    if (currentPage > 1) {
        createPaginationItem(1, '1');
    }

    if (currentPage > 3) {
        createPaginationItem(currentPage - 2, '...');
        createPaginationItem(currentPage - 1, currentPage - 1);
    } else if (currentPage === 3) {
        createPaginationItem(currentPage - 1, currentPage - 1);
    }

    createPaginationItem(currentPage, currentPage);

    if (currentPage < totalPages - 2) {
        createPaginationItem(currentPage + 1, currentPage + 1);
        createPaginationItem(currentPage + 2, '...');
    } else if (currentPage === totalPages - 2) {
        createPaginationItem(currentPage + 1, currentPage + 1);
    }

    if (currentPage < totalPages) {
        createPaginationItem(totalPages, totalPages);
    }

    paginationBlock.appendChild(paginationList);
    paginationContainer.appendChild(paginationBlock);
}

function updateSearchData(page = 1, loadFilters = null) {
    SELECT_FORM_NAVIGATE = null;

    let deferred = $.Deferred();
    let data;
    if(loadFilters == null){
        data = {
            tables: getSearchTables(),
            nick: document.getElementById('search_nick').value,
            perPage: per_page,
            page: page
        };
    }else{
        let tables = {};
        tables[loadFilters.typePost] = true;

        let defaultTables = getSearchTables();

        Object.entries(defaultTables).forEach(function ([type, none]){
            if(!isNaN(type)){
                tables[type] = true;
            }
        });

        console.log(tables);

        data = {
            tables: tables,
            id: loadFilters.postId,
            perPage: per_page,
            page: page
        }
    }

    showLoading();
    $.ajax({
        url: '/punishments/data',
        type: 'GET',
        data: data,
        success: function (response) {
            hideLoading();

            deferred.resolve(response);

            currentPage = page;
            setSearchResultContent(response);
        },
        error: function (xhr, status, error) {
            hideLoading();
            deferred.reject(error);
        }
    });
    return deferred.promise();
}
