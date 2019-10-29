//payment/create - create.html.twig

$(document).ready(function () {

    $('#paymentDataTable').DataTable({
        "pagingType": "simple", // "simple" option for 'Previous' and 'Next' buttons only
        "columnDefs": [ {
            "targets": -1,
            "data": null,
            "defaultContent": "<button>Избор!</button>"
        } ]
    });

    $('#paymentDataTable tbody').on( 'click', 'button', function () {
        let trElement = $(this).parents('tr');
        let tdElement = trElement.find('td');

        $('#productId').val(trElement.attr('value'));
        $('#studentFullName').val(tdElement.get(0).textContent);
        $('#className').val(tdElement.get(1).textContent);
        $('#productForMonth').val(tdElement.get(3).textContent);
        $('#teacherFullName').val(tdElement.get(5).textContent);
        $('#payment_price').val(tdElement.get(2).textContent);

        $('div #paymentForPayer').show();
    });
});