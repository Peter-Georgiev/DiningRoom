import {table} from './applyDataTable.js'
import {editButton, deleteButton, addOnButton, rowTextDangerOnOff} from './button-row-table.js';
import {bg} from '../data-tables/languageDataTable.js';
const path = '/student';

$(document).ready(function () {
    table.dataTable({
        //pagingType: 'full_numbers', // "simple" option for 'Previous' and 'Next' buttons only
        columnDefs: [{
            targets: -2,
            data: null,
            defaultContent: "<button id='editBtn' class='text-warning'>Редактиране!</button>"
        }, {
            targets: -1,
            data: null,
            defaultContent: "<button id='deleteBtn' class='text-danger'>Изтриване!</button>"
        }],
        order: [
            [2, 'asc'], [0, 'asc'], [1, 'des']
        ],
        language: bg.language,
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
        drawCallback: function(){
            $('.paginate_button.next:not(.disabled)', this.api().table().container())
                .on('click', function(){
                    rowTextDangerOnOff();
                });
            $('.paginate_button.previous:not(.disabled)', this.api().table().container())
                .on('click', function(){
                    rowTextDangerOnOff();
                });
        }
    });

    rowTextDangerOnOff();

    table.on('click', 'button', function () {
        let rowTable = $(this).parent().parent();
        rowTable.addClass("text-danger");
        let rowText = rowTable.children().map(function(){
            return $.trim($(this).text());
        }).get();

        let id = rowTable.attr('id');

        if (this.id === 'deleteBtn') {
            let studentFullName = rowText[0];
            let className = rowText[2];
            let message = `Изтриване на ученик \"${studentFullName}\" \nот клас \"${className}\"!!!`;

            deleteButton(`${path}/delete/${id}`, rowTable, message);
        } else if (this.id === 'editBtn') {
            editButton(`${path}/edit/${id}`);
        }
    });

    $('#addOnBtn button').on('click', function () {
        addOnButton(`${path}/create`);
    });
    /*
    $('#dataTable tbody').on('click', 'button', function () {
        let btn = $(this);
        let trElement = btn.parents('tr');
        let tdElement = trElement.find('td');

        let data = {
            studentId: trElement.attr('value'),
            studentFullName: tdElement.get(0).textContent,
            className: tdElement.get(2).textContent,
            classId: tdElement.get(2).getAttribute('val'),
            teacherFullName: tdElement.get(3).textContent,
            teacherId: tdElement.get(3).getAttribute('val'),
            productsCount: tdElement.get(4).textContent
        };

        if (this.id === 'deleteBtn') {
            deleteButton(btn, data);
        } else if (this.id === 'editBtn') {
            editButton(btn, data);
        }
    });

        $('#addBtnStudent button').on('click', function () {
            window.location.href = '/student/create';
            //fetch('/student/create/', {
             //   method: 'GET'
            //}).then(res => window.location.reload());
        });

    
    function deleteButton(btn, data) {
        let promise = new Promise(function(resolve, reject) {
            let addStudent = $('div #addStudent');
            let editStudent = $('div #editStudent');
            let addBtnStudent = $('#addBtnStudent');

            if (addStudent.css('display') !== 'none') {
                addStudent.hide();
                addBtnStudent.show();
            }

            if (editStudent.css('display') !== 'none') {
                editStudent.hide();
                addBtnStudent.show();
            }

            setTimeout(function() {
                resolve(`${addStudent.css('display')}`);
            }, 300);
        });

        promise.then(function(value) {
            if (confirm(`!!! ВНИМАНИЕ !!!?\n` +
                `Изтриване на ученик \"${data.studentFullName}\"` +
                `\nот клас \"${data.className}\"!!!`)) {
                window.location.href = `/student/delete/${data.studentId}`;
                //fetch(`/student/delete/${data.studentId}`, {
                //    method: 'DELETE'
                //}).then(res => window.location.reload())
            }
            return false;
        })
    }

    function editButton(btn, data) {
        window.location.href = `/student/edit/${data.studentId}`;
        //fetch(`/student/edit/${data.studentId}`, {
          //  method: 'POST'
        //}).then(res => window.location.reload());
    }
*/
});