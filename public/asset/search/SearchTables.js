
let filterModal = document.getElementById("filter_modal");

function showFilterModal(){
    filterModal.style.display = "flex";
}

function hideFilterModal(){
    filterModal.style.display = "none";
}

function getSearchTables(){
    const checkboxes = document.querySelectorAll('#filters input[type="checkbox"]');
    let filters = {};
    checkboxes.forEach((checkbox) => {
        filters[checkbox.id] = (checkbox.checked ? 1 : 0);
    });
    return filters;
}
