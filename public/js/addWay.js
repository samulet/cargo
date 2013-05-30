function addWay(wayTr) {
    var way=$('.'+wayTr);
    var counter=$('#counter');
    var counterVal=counter.val();
    counterVal++;
    counter.val(counterVal);

    $('<tr class='+wayTr+'-'+counterVal+'>'+way.html()+'</tr>').insertBefore('.addTr');

    $('.'+wayTr+'-'+counterVal+' input').each(function( index ) {
        $(this).attr('name',$(this).attr('name')+'-'+counterVal);
    });
    $('.'+wayTr+'-'+counterVal+' select').each(function( index ) {
        $(this).attr('name',$(this).attr('name')+'-'+counterVal);
    });
    $('.'+wayTr+'-'+counterVal+' textarea').each(function( index ) {
        $(this).attr('name',$(this).attr('name')+'-'+counterVal);
    });
    $('.'+wayTr+'-'+counterVal+' .deleteWay').attr('onclick',"delWay('"+wayTr+'-'+counterVal+"');");
}

function delWay(wayTr) {
    var conf = confirm('Вы действительно хотите удалить пункт выдачи?');
    if ( !conf ) {
        return false;
    }
    else {
        $('.'+wayTr).remove();
    }
}