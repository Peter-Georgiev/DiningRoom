import {editButton, deleteButton, addOnButton, rowTextDangerOnOff} from './button-row-table.js';
const path = '/product';

$(document).ready(function () {
    $('#dataTable').dataTable({
        pagingType: 'simple', // "simple" option for 'Previous' and 'Next' buttons only
        columnDefs: [{
            targets: -3,
            data: null,
            defaultContent: "<button id='paidBtn' class='btn-success'>Плащане!</button>"
        }, {
            targets: -2,
            data: null,
            defaultContent: "<button id='editBtn' class='btn-warning'>Редактиране!</button>"
        }, {
            targets: -1,
            data: null,
            defaultContent: "<button id='deleteBtn' class='btn-danger'>Изтриване!</button>"
        }],
        order: [
            [1, 'asc'], [0, 'asc']
        ],
        dom: 'lfBSrtip',
        buttons: [
            {extend: 'copy'},
            //{extend: 'csv'},
            {extend: 'excel'},
            {
                //extend: 'pdfHtml5',
                extend: 'pdf',
                orientation: 'landscape',
                pageSize: 'LEGAL'
            },
            {extend: 'print'},
        ],
    });

    rowTextDangerOnOff();

    $('#addOnBtn button').on('click', function () {
        addOnButton(`${path}/create`);
    });

    $('#dataTable').on('click', 'button', function () {
        let rowTable = $(this).parent().parent();
        rowTable.addClass("text-danger");
        let rowText = rowTable.children().map(function(){
            return $.trim($(this).text());
        }).get();

        let id = rowTable.attr('id');
        let studentFullName = rowText[0];
        let productPrice = rowText[1];

        let message = `Изтриване на УЧИТЕЛ \"${studentFullName}\"!!!`;

        if (this.id === 'deleteBtn') {
            deleteButton(`${path}/delete/${id}`, rowTable, message);
        } else if (this.id === 'editBtn') {
            editButton(`${path}/edit/${id}`);
        }
    });

    /*

    $('#dataTable tbody').on('click', 'button', function () {
        let btn = $(this);
        let trElement = btn.parents('tr');
        let tdElement = trElement.find('td');

        let data = {
            productId: trElement.attr('value'),
            studentFullName: tdElement.get(0).textContent,
            productPrice: tdElement.get(1).textContent
        };

        if (this.id === 'deleteBtn') {
            deleteButton(data);
        } else if (this.id === 'editBtn') {
            editButton(data);
        } else if (this.id === 'paidBtn') {
            paidButton(data);
        }
    });


    try{
        $('#addBtnProduct button').on('click', function () {
            window.location.href = '/product/create';
            //fetch('/student/create/', {
            //   method: 'GET'
            //}).then(res => window.location.reload());
        });
    }catch (e) {

    }

    function deleteButton(data) {
        let promise = new Promise(function(resolve, reject) {
            let addTeacher = $('div #addTeacher');
            let editTeacher = $('div #editTeacher');
            let addBtnTeacher = $('#addBtnTeacher');

            if (addTeacher.css('display') !== 'none') {
                addTeacher.hide();
                addBtnTeacher.show();
            }

            if (editTeacher.css('display') !== 'none') {
                editTeacher.hide();
                addBtnTeacher.show();
            }

            setTimeout(function() {
                resolve(`${addTeacher.css('display')}`);
            }, 300);
        });

        promise.then(function(value) {
            if (confirm(`!!! ВНИМАНИЕ !!!\n` +
                `Изтриване на УЧИТЕЛ \"${data.teacherFullName}\"!!!`)) {
                window.location.href = `/teacher/delete/${data.teacherId}`;
            }
            return false;
        })
    }

    function editButton(data) {
        window.location.href = `/teacher/edit/${data.teacherId}`;
        //fetch(`/student/edit/${data.studentId}`, {
        //  method: 'POST'
        //}).then(res => window.location.reload());
    }

    function paidButton(data) {
        console.log('Paid');
    }
    */
});