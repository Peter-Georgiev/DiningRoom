$(document).ready(function () {
    try {
        let month = 'Избери година и месец';
        let price = 'Въведи цана за месеца';
        $("#product_forMonth_day").hide();
        $('#product_forMonth').parent().children('label').text(month);
        $('#product_price').before('<div></div>');
        $('#product_price').parent().children('label').text(price);
    }catch (e) {

    }
});