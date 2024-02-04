let blockLoad = document.getElementById('block_account_load_modal');
let button_send_files = document.getElementById('button_send_files');

let PROGRESS_LOADING = false;

function sendFormLoadFiles(lockType, id) {
    hideModalNeedConfirm();
    blockLoad.style.display = 'flex';
    button_send_files.setAttribute('onclick', "sendServerFiles(\'" + lockType + "\', \'" + id + "\')");
}

function hideFormLoadFiles() {
    if (PROGRESS_LOADING) {
        sendPopupBlock('Дождитесь загрузки файлов на сервер!', RED)
        return;
    }

    blockLoad.style.display = 'none';
    resetFormLoadFiles();
}

function resetFormLoadFiles() {
    $("#load_files_progress").css('width', '0');
    $('#input_load_file').val(null);

    $('#select_files_size').html('0КБ');
    $('#select_files_count').html('0');
}

$(document).ready(function () {

    $('#input_load_file').change(function () {

        if (window.FormData === undefined) {
            sendPopupBlock('В вашем браузере не поддерживается FormData, подгрузка файлов невозможна!')
        } else {
            let files = $('#input_load_file')[0].files;

            let countFiles = 0;
            let size = 0;
            Array.from(files).forEach(function (file) {
                size += file.size;
                countFiles++;
            });

            if (size >= 209715200) {
                sendPopupBlock('Вы превысили лимит в 200МБ!', RED)
            }

            if (countFiles >= 8) {
                sendPopupBlock('Вы можете загружать не больше 8 файлов!')
            }

            $('#select_files_size').html(formatBytes(size));
            $('#select_files_count').html(countFiles);
        }
    });
});

function sendServerFiles(lockType, id) {

    if (PROGRESS_LOADING) {
        sendPopupBlock('Вы уже загружаете файлы на сервер!', RED)
        return;
    }

    let formData = new FormData;
    let files = $('#input_load_file')[0].files;

    let countFiles = 0;
    let size = 0;
    Array.from(files).forEach(function (file) {
        formData.append(file.name, file);
        countFiles++;
        size += file.size;
    });

    formData.append('id', id);
    formData.append('typeLock', lockType);

    if (countFiles === 0) {
        sendPopupBlock('Выберите файлы перед отправкой!', RED);
        return;
    }

    if (countFiles >= 8) {
        sendPopupBlock('Вы можете загружать не больше 8 файлов!')
    }

    if (size >= 209715200) {
        sendPopupBlock('Вы превысили лимит в 200МБ!', RED)
        return;
    }

    function setProgress(e) {
        if (e.lengthComputable) {
            let complete = e.loaded / e.total;
            let progress = Math.floor(complete * 100) + "%"
            $("#load_files_progress").css('width', progress)
        }
    }

    PROGRESS_LOADING = true;
    $.ajax({
        xhr: function () {
            let xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (e) {
                setProgress(e);
            }, false);
            xhr.addEventListener("progress", function (e) {
                setProgress(e);
            }, false);
            return xhr;
        },
        url: '/api/load_file',
        data: formData,
        processData: false,
        contentType: false,
        type: 'POST',
        success: function (response) {
            PROGRESS_LOADING = false;

            if (response.success === true) {
                sendPopupBlock('Вы успешно отправили файлы!')
                updateSearchData(currentPage).done(function (none){
                    resetFormLoadFiles();
                    hideFormLoadFiles();
                    hideLoading();
                });
            } else {
                resetFormLoadFiles();
                hideFormLoadFiles();
                sendPopupBlock(response.error_message, RED);
            }
        },
        error: function () {
            PROGRESS_LOADING = false;
            hideFormLoadFiles();
            sendPopupBlock('Неудалось загрузить файлы, попробуйте позже!', RED)
        }
    });
}

function formatBytes(bytes) {
    if (bytes < 1024) {
        return bytes + ' B';
    } else if (bytes < 1024 * 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
    }
}
