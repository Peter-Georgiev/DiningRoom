
function rowTextDangerOnOff(isOn = true) {
    let token = (window.location.pathname)
        .trim()
        .split('/');

    let id = Number(token[token.length - 1]);
    if (id <= 0) {
        return false;
    }
    let trToken = $('#dataTable tbody tr');

    $(trToken).each(function(){
        if (Number($(this).attr('id')) === id) {
            if (isOn) {
                $(this).addClass('text-danger');
            } else {
                $(this).removeClass('text-danger');
            }
        }
    });
}

function deleteButton(pathname, rowTable, messages) {
    let promise = new Promise(function(resolve, reject) {
        rowTextDangerOnOff(false);

        let add = $('div #add');
        let edit = $('div #edit');
        let addOnBtn = $('#addOnBtn');

        if (add.css('display') !== 'none') {
            add.hide();
            addOnBtn.show();
        }

        if (edit.css('display') !== 'none') {
            edit.hide();
            addOnBtn.show();
        }

        setTimeout(function() { resolve([
                `${add.css('display')}`,
                `${rowTable.removeClass("text-danger")}`
            ]);
        }, 300);
    });

    promise.then(function(value) {
        if (confirm(`!!! ВНИМАНИЕ !!!\n` + messages)) {
            window.location.href = pathname;
        }
        return false;
    })
}

function editButton(pathname) {
    window.location.href = pathname;
    //fetch(`/student/edit/${data.studentId}`, {
    //  method: 'POST'
    //}).then(res => window.location.reload());
}

function addOnButton(pathname) {
    window.location.href = pathname;
}

function paidOnButton(pathname) {
    window.location.href = pathname;
}

export {editButton, deleteButton, addOnButton, rowTextDangerOnOff, paidOnButton};