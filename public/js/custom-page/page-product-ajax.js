import {table} from './applyDataTable.js'
import {editButton, deleteButton, addOnButton, paidOnButton, rowTextDangerOnOff} from './button-row-table.js';
import {bg} from '../data-tables/languageDataTable.js';
const path = '/product';
const currentPathname = window.location.pathname;

$(document).ready(function () {
    $.ajax({
        url:        path,
        type:       'POST',
        dataType:   'json',
        async:      true,
        success: function(data, status) {
            table.dataTable({
                data: data['products'],
                rowId: 'id',
                //pagingType: 'full_numbers', // "simple" option for 'Previous' and 'Next' buttons only
                columns: [
                    {
                        data: "student", // can be null or undefined
                        defaultContent: `<i></i>`,
                        title: 'Ученик'
                    },
                    {
                        data: "class", // can be null or undefined
                        defaultContent: `<i></i>`,
                        title: 'Клас'
                    },
                    {
                        data: 'price', // can be null or undefined
                        defaultContent: `<i></i>`,
                        title: 'Цена'
                    },
                    {
                        data: function (row, type, val, meta) {
                            if (row.isPaid) {
                                return 'Платено';
                            } else if (!row.isPaid) {
                                return 'Неплатено';
                            }
                            return `<i></i>`;
                        },
                        title: 'Статус'
                    },
                    {
                        data: function (row, type, val, meta) {
                            let number = Number(row.feeInDays);
                            if (number === 1) {
                                return number + ' ден'
                            } else if (number === 0 || number > 1) {
                                return number + ' дни'
                            }
                            return `<i></i>`;
                        },
                        title: 'Такси в дни'
                    },
                    {
                        data: 'forMonth', // can be null or undefined
                        defaultContent: `<i></i>`,
                        title: 'Такса за месец'
                    },
                    {
                        data: "dateCreate", // can be null or undefined
                        defaultContent: `<i></i>`,
                        title: 'Дата на създаване'
                    },
                    {
                        data: 'lastEdit', // can be null or undefined
                        defaultContent: `<i></i>`,
                        title: 'Последна редакция'
                    },
                    {
                        title: 'Плащане'
                    },
                    {
                        title: 'Редактиране'
                    },
                    {
                        title: 'Изтриване'
                    },
                ],
                columnDefs: [{
                    targets: -3,
                    data: function (row, type, val, meta) {
                        if (row.isPaid) {
                            return "<button id='paidBtn' class='btn-default' disabled>Плащане!</button>";
                        }
                        return  "<button id='paidBtn' class='btn-success'>Плащане!</button>";
                    },
                }, {
                    targets: -2,
                    data: function (row, type, val, meta) {
                        if (row.isPaid) {
                            return "<button id='editBtn' class='btn-danger'>Редактиране!</button>";
                        }
                        return "<button id='editBtn' class='btn-warning'>Редактиране!</button>";
                    },
                }, {
                    targets: -1,
                    data: function (row, type, val, meta) {
                        if (row.isPaid) {
                            return "<button id='deleteBtn' class='btn-default' disabled>Изтриване!</button>";
                        }
                        return "<button id='deleteBtn' class='btn-danger'>Изтриване!</button>"
                    },
                }],
                order: [
                    [1, 'asc'], [0, 'asc'], [3, 'des']
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
            productCreate(data);
            productEditId(data);

            },
            error : function(xhr, textStatus, errorThrown) {
                alert('Грешка в данните от сървъра.');
            },
    });

    $('#addOnBtn button').on('click', function () {
        addOnButton(`${path}/create`);
    });

    table.on('click', 'button', function () {
        let rowTable = $(this).parent().parent();
        rowTable.addClass("text-danger");
        let rowText = rowTable.children().map(function(){
            return $.trim($(this).text());
        }).get();

        let id = rowTable.attr('id');

        if (this.id === 'deleteBtn') {
            let studentFullName = rowText[0];
            let className = rowText[1];
            let forMonth = rowText[5];
            let price = rowText[3];
            let message = `Изтривате плащане на \"${studentFullName}\" от \"${className}\"\n` +
                `за месец \"${forMonth}\", сума ${price} лв.!!!`;

            deleteButton(`${path}/delete/${id}`, rowTable, message);
        } else if (this.id === 'editBtn') {
            editButton(`${path}/edit/${id}`);
        } else if (this.id === 'paidBtn') {
            paidOnButton(`/payment/product/${id}`);
        }
    });

    function productCreate(data) {
        if (currentPathname === `${path}/create`) {
            hiddenProductForMonthDay();
            selectedOption(data);

            $('#class #class_id').on('change', (event) => {
                selectedOption(data, 0, Number(event.target.value));
            });
        }
    }

    function productEditId(data) {
        hiddenProductForMonthDay();
        let res = currentPathname.split('/');
        let count = 0;
        for(let i = 0; i < res.length; i++) {
            if (res[i] === 'product' || res[i] === 'edit') {
                count++;
            }
        }
        if (count !== 2) {
            return false;
        }

        let id =  Number(res[res.length - 1]);
        $('#dataTable tbody #' + id).addClass("text-danger");

        selectedOption(data, id);
        $('#class #class_id').on('change',(event) => {
            selectedOption(data, 0, Number(event.target.value));
        });
    }

    function selectedOption(data, id = 0, classId = 0) {
        let i = 0;
        let tempFirstOption = '';
        let outputClass = [];
        let outputStudent = [];

        $.each(data['products'], function (key, value) {
            if (id === Number(value.id)) {
                classId = value.classId;
                return false;
            }
        });

        $.each(data['classes'], function(key, value) {
            if (classId > 0 && i === 0) {
                outputClass.push('');
            }

            if (value.id !== classId) {
                outputClass.push('<option value="' + value.id + '">' + value.name + '</option>');
            } else {
                tempFirstOption = '<option value="' + value.id + '">' + value.name + '</option>';
            }

            if (classId > 0) {
                if (value.id === classId) {
                    $.each(value.students, function (key, value) {
                        outputStudent.push('<option value="' + value.studentId + '">' + value.student + '</option>');
                    });
                }
            } else {
                $.each(value.students, function (key, value) {
                    outputStudent.push('<option value="' + value.studentId + '">' + value.student + '</option>');
                });
            }
            i++;
        });

        if (classId > 0) {
            outputClass[0] = tempFirstOption;
        }

        $('#class #class_id').html(outputClass.join(''));
        $('#student #student_id').html(outputStudent.join(''));
    }

    function hiddenProductForMonthDay() {
        let productForMonthDay = $('#product_forMonth_day');
        if (productForMonthDay) {
            productForMonthDay.hide();
        }
    }
});