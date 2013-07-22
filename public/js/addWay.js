function addWay(wayTr) {
    var way = $('.' + wayTr);
    var counter = $('#counter');
    var counterVal = counter.val();
    counterVal++;
    counter.val(counterVal);

    $('<tr class=' + wayTr + '-' + counterVal + '>' + way.html() + '</tr>').insertBefore('.addTr');

    $('.' + wayTr + '-' + counterVal + ' input').each(function (index) {
        if (typeof $(this).attr('name') === 'undefined') {
        } else {
            var splitEl = $(this).attr('name').split('_');
            var splitEl2='none';
            if (typeof splitEl[1] === 'undefined') {
            } else {

                splitEl2=splitEl[1];

            }
            if ( (splitEl2==0) || (typeof splitEl[1] === 'undefined') ){

                $(this).attr('name', $(this).attr('name') + '-' + counterVal);
                if (($(this).attr('class') != 'deleteWay') && ( $(this).attr('class') != 'deleteDocWay' ) && ( $(this).attr('class') != 'addDocWay' )) {
                    $(this).val('');
                } else {
                    alert($(this).attr('class'));
                    if( ( $(this).attr('class') == 'addDocWay' )) {
                        $(this).attr('onclick', "addDocWay('"+ wayTr + '-' + counterVal +"','docBox');");
                    }
                }
            } else {
                if(splitEl2 % 1 === 0) {
                    $(this).parent().parent().remove();
                }
            }
        }
    });
    $('.' + wayTr + '-' + counterVal + ' select').each(function (index) {
        if (typeof $(this).attr('name') === 'undefined') {
        } else {
            var splitEl = $(this).attr('name').split('_')[1];
            var splitEl2 = splitEl.split('-');
            if (typeof splitEl2[1] === 'undefined') {
                $(this).attr('name', $(this).attr('name') + '-' + counterVal);
                $(this).val('');
            }
        }
    });
    $('.' + wayTr + '-' + counterVal + ' textarea').each(function (index) {
        if (typeof $(this).attr('name') === 'undefined') {
        } else {
            var splitEl = $(this).attr('name').split('_')[1];
            var splitEl2 = splitEl.split('-');
            if (typeof splitEl2[1] === 'undefined') {
                $(this).attr('name', $(this).attr('name') + '-' + counterVal);
                $(this).val('');
            }
        }
    });
    $('.' + wayTr + '-' + counterVal + ' .deleteWay').attr('onclick', "delWay('" + wayTr + '-' + counterVal + "');");
}

function delWay(wayTr) {
    var conf = confirm('Вы действительно хотите удалить пункт выдачи?');
    if (!conf) {
        return false;
    }
    else {
        $('.' + wayTr).remove();
    }
}

function addDocWay(wayTr, docBox) {
    var wayDoc = $('.' + wayTr + ' .' + docBox);
    var counter = $('.' + wayTr + ' .docCounter');
    var counterVal = counter.val();
    $('<div class=' + docBox + '_' + counterVal + '>' + wayDoc.html() + '<div>').insertBefore('.addDocBefore');

    $('.' + wayTr + ' .' + docBox + '_' + counterVal + ' input').each(function (index) {
        if (typeof $(this).attr('name') === 'undefined') {
        } else {
            var splitName = $(this).attr('name').split('_');
            if (typeof splitName[0] === 'undefined') {
            } else {
                splitName = splitName[0];
            }
            var splitCounter = $(this).attr('name').split('-');
            var splitCounterLast = '';
            if (typeof splitCounter[1] === 'undefined') {
            } else {
                splitCounterLast = '-' + splitCounter;
            }
            $(this).attr('name', splitName + '_' + counterVal + splitCounterLast);
            if ($(this).attr('class') != 'deleteDocWay') {
                $(this).val('');
            }
        }
    });
    $('.' + wayTr + ' .' + docBox + '_' + counterVal + ' select').each(function (index) {
        $(this).attr('name', $(this).attr('name') + '-' + counterVal);
        $(this).val('');
    });
    $('.' + wayTr + ' .' + docBox + '_' + counterVal + ' textarea').each(function (index) {

        $(this).attr('name', $(this).attr('name') + '-' + counterVal);
        $(this).val('');
    });

    counterVal++;
    $('.' + wayTr + ' .docCounter').val(counterVal);
}

function delDocWay(wayTr, docBox) {

}