function addWay(wayTr) {
    var way=$('.'+wayTr);
    var counter=$('#counter');
    var counterVal=counter.val();
    counterVal++;
    counter.val(counterVal);

    $('<tr class='+wayTr+'-'+counterVal+'>'+way.html()+'</tr>').insertBefore('.addTr');

    $('.'+wayTr+'-'+counterVal+' input').each(function( index ) {
     //   var splitEl = $(this).attr('name').split('_')[1];
    //    var splitEl2 = splitEl.split('-')[1];
    //    if(typeof splitEl2==='undefined') {
            $(this).attr('name',$(this).attr('name')+'-'+counterVal);
            if($(this).attr('class')!='deleteWay') {
                $(this).val('');
            }
      //  } else {
     //       alert(splitEl2);
      //  }
    });
    $('.'+wayTr+'-'+counterVal+' select').each(function( index ) {
        var splitEl = $(this).attr('name').split('_')[1];
        var splitEl2 = splitEl.split('-')[1];
        if(typeof splitEl2==='undefined') {
        $(this).attr('name',$(this).attr('name')+'-'+counterVal);
        $(this).val('');
    }
    });
    $('.'+wayTr+'-'+counterVal+' textarea').each(function( index ) {
        var splitEl = $(this).attr('name').split('_')[1];
        var splitEl2 = splitEl.split('-')[1];
        if(typeof splitEl2==='undefined') {
        $(this).attr('name',$(this).attr('name')+'-'+counterVal);
        $(this).val('');
        }
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

function addDocWay(wayTr,docBox) {
    var wayDoc=$('.'+wayTr+' .'+docBox);
    var counter=$('.'+wayTr+' .docCounter');
    var counterVal=counter.val();
    $('<div class='+docBox+'_'+counterVal+'>'+wayDoc.html()+'<div>').insertBefore('.addDocBefore');

    $('.'+wayTr+' .'+docBox+'_'+counterVal+' input').each(function( index ) {
            var splitName = $(this).attr('name').split('_')[0];
            var splitCounter = $(this).attr('name').split('-')[1];
        var splitCounterLast='';
            if(typeof splitCounter === 'undefined') { } else {
                splitCounterLast='-'+splitCounter;
            }
            $(this).attr('name',splitName+'_'+counterVal+splitCounterLast);
            if($(this).attr('class')!='deleteDocWay') {
                $(this).val('');
            }
    });
    $('.'+wayTr+' .'+docBox+'_'+counterVal+' select').each(function( index ) {
            $(this).attr('name',$(this).attr('name')+'-'+counterVal);
            $(this).val('');
    });
    $('.'+wayTr+' .'+docBox+'_'+counterVal+' textarea').each(function( index ) {

            $(this).attr('name',$(this).attr('name')+'-'+counterVal);
            $(this).val('');
    });

    counterVal++;
    $('.'+wayTr+' .docCounter').val(counterVal);
}

function delDocWay(wayTr,docBox) {

}