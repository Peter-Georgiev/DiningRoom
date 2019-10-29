import {table} from './applyDataTable.js'
import {editButton, deleteButton, addOnButton, rowTextDangerOnOff} from './button-row-table.js';
import {bg} from '../data-tables/languageDataTable.js';
const path = '/class';

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
            [0, 'asc']
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
        let className = rowText[0];
        let studentCount = rowText[1];

        let message = `Изтриване на КЛАС \"${className}\", обвързан с`;
        if (studentCount > 1) {
            message += ` ${studentCount} студента!!!`;
        } else {
            message += ` ${studentCount} студент!!!`;
        }

        if (this.id === 'deleteBtn') {
            deleteButton(`${path}/delete/${id}`, rowTable, message);
        } else if (this.id === 'editBtn') {
            editButton(`${path}/edit/${id}`);
        }
    });

    $('#addOnBtn button').on('click', function () {
        addOnButton(`${path}/create`);
    });
});
