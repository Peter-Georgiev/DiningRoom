$(document).ready(function () {
    $.ajax({
        url:        '/product/student/class',
        type:       'POST',
        dataType:   'json',
        async:      true,
        success: function(data, status) {
            $('#dataTable').dataTable({
                data: data['products'],
                rowId: 'id',
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
                        return "<button id='editBtn ' class='btn-warning'>Редактиране!</button>";
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
                order: [[1, 'asc'], [0, 'asc']],
                language: lang.language,
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

            clickButtonInRow(data);
            addBtnProduct(data);

            },
            error : function(xhr, textStatus, errorThrown) {
                alert('Грешка в данните от сървъра.');
            },
    });

    function addBtnProduct(data) {
        $('#addBtnProduct button').on('click', function () {
            window.location.href = '/product/create';
        });

        let output = [];
        $.each(data['student'], function(key, value) {
            output.push('<option value="'+ key +'">'+ value +'</option>');
        });
        $('#class #class_id').html(output.join(''));
    }

    function clickButtonInRow(data) {
        $('#dataTable').on('click', 'button', function () {
            let rowTable = $(this).parent().parent();
            rowTable.addClass("text-danger");
            let rowText = rowTable.children().map(function(){
                return $.trim($(this).text());
            }).get();

            let row = {
                'id': rowTable.attr('id'),
                'name': rowText[0],
                'class': rowText[1],
                'price': rowText[2],
                'status': rowText[3],
                'month': rowText[5],
                //'days': rowText[],

            };

            if (this.id === 'deleteBtn') {
                 deleteButton(row, rowTable);
            } else if (this.id === 'editBtn') {
                console.log([row, data]);
                //editButton(data);
            } else if (this.id === 'paidBtn') {
                console.log(data);
                //paidButton(data);
            }
        });
    }

    function deleteButton(row, rowTable) {
        let promise = new Promise(function(resolve, reject) {
            /*let addTeacher = $('div #addTeacher');
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
            */
            setTimeout(function() {
                //resolve(`${addTeacher.css('display')}`);
                resolve(`${rowTable.removeClass("text-danger")}`);
            }, 300);
        });

        promise.then(function(value) {
            if (confirm(`!!! ВНИМАНИЕ !!!\n` +
                `Изтривате плащане на \"${row.name}\" от \"${row.class}\"\n` +
                `за месец \"${row.month}\", сума ${row.price} лв.!!!`)) {
                window.location.href = `/product/delete/${row.id}`;
            }
            return false;
        })
    }


    function editButton(data) {
        window.location.href = `/product/edit/${data.teacherId}`;
        //fetch(`/student/edit/${data.studentId}`, {
        //  method: 'POST'
        //}).then(res => window.location.reload());
    }

    function paidButton(data) {
        console.log('Paid');
    }














    let lang = {
        "language": {
            "lengthMenu": "Показване на _MENU_ записи на страница",
            "zeroRecords": "Нищо не е намерено - съжалявам",
            "info": "Показване на страница _PAGE_ от _PAGES_",
            "infoEmpty": "Няма налични записи",
            "infoFiltered": "(филтрирана от _MAX_ общо записи)",
            "infoPostFix": "",
            "thousands": ",",
            "loadingRecords": "Зарежда...",
            "processing": "Обработка...",
            "search": "Търсене в таблицата по-долу:",
            "paginate": {
                "first": "Първа",
                "last": "Последна",
                "next": "Следваща",
                "previous": "Предишна"
            },
            "aria": {
                "sortAscending": ": Сортиране на колона възходящо",
                "sortDescending": ": Сортиране на колона низходящо"
            },
        },
        "null": "няма данни",
        "checkBox": "При избор на опцията: ",
        "emptyMessage": [
            {'message': "1. В поле 'Търсене' можеш да търсиш по: id, потребител, име, фамилия, име и фамилия, имейл или модул."},
            {'message': "2. При 'Търсене' по id трябва да въведете id: и номера. Например id:1234."},
            {'message': "3. При 'Търсене' по име, фамилия, име и фамилия - преобразува латиница в кирилица и обратно."},
            {'message': "4. При 'Търсене' по модул трябва да въведете името на модула. (Например: A1.1)"},
            {'message': "5. 'Изгледани проценти от модула' ти дават справка каква част от видеата курсистът е изгледал напълно."},
            {'message': "6. 'Само регистриран' - тук излизат всички регистрирани потребители, които са гледали безплатни видеа."},
            {'message': "7. 'С достъп до модул' - тук излизат само курсистите, които имат даден достъп до определен модул."},
            {'message': `8. 'Търсене в таблицата по-долу:' - търси по допълнителен критерий в изготвената вече справка.`}
        ],
    };
});