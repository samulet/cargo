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
                if (($(this).attr('class') != 'deleteWay') && ( $(this).attr('class') != 'deleteDocWay' ) && ( $(this).attr('class') != 'addDocWay' ) && ($(this).attr('class') != 'docCounter') ) {
                    $(this).val('');
                } else {
                    if( ( $(this).attr('class') == 'addDocWay' )) {
                        $(this).attr('onclick', "addDocWay('"+ wayTr + '-' + counterVal +"','docBox');");
                    }
                    if( ( $(this).attr('class') == 'docCounter' )) {
                        $(this).val(1);
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
            var splitEl = $(this).attr('name').split('_');
            var splitEl2='none';
            if (typeof splitEl[1] === 'undefined') {
            } else {

                splitEl2=splitEl[1];

            }
            if ( (splitEl2==0) || (typeof splitEl[1] === 'undefined') ){
                $(this).attr('name', $(this).attr('name') + '-' + counterVal);
                $(this).val('');
            } else {
                if(splitEl2 % 1 === 0) {
                    $(this).parent().parent().remove();
                }
            }
        }
    });
    $('.' + wayTr + '-' + counterVal + ' textarea').each(function (index) {
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
                $(this).val('');
            } else {
                if(splitEl2 % 1 === 0) {
                    $(this).parent().parent().remove();
                }
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
    $('<div class=' + docBox + '_' + counterVal + '>' + wayDoc.html() + '<div>').insertBefore('.' + wayTr +' .addDocBefore')

    $('.' + wayTr + ' .' + docBox + '_' + counterVal + ' .docName').html('<b>Документ '+counterVal+'</b>');

    $('.' + wayTr + ' .' + docBox + '_' + counterVal + ' input'+','+'.' + wayTr + ' .' + docBox + '_' + counterVal + ' select'+', '+'.' + wayTr + ' .' + docBox + '_' + counterVal + ' textarea').each(function (index) {
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
                splitCounterLast = '-' + splitCounter[1];
            }
            $(this).attr('name', splitName + '_' + counterVal + splitCounterLast);
            if ($(this).attr('class') != 'deleteDocWay') {
                $(this).val('');
            }
        }
    });


    counterVal++;
    $('.' + wayTr + ' .docCounter').val(counterVal);
}

function delDocWay(wayTr, docBox) {

}