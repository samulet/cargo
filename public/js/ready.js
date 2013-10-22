function startSetValues() {
    $( 'select[id^="parent-"]').each(function( index ) {
        setValuesSelect(this);
    });
}

function onChangeSetValues(element) {
    setValuesSelect(element);
}


function setValuesSelect(element) {
    if(typeof $(element).attr('id') === 'undefined') {}
    else {
        var array=$(element).attr('id').split("-");
        if(array[0]=='parent') {
            var parentOpt=$('select[name='+array[2]+'] option:selected').val();
            var thisSelect=$( 'select[id=child-'+array[2]+'-'+array[1]+']');
            $( 'select[id=child-'+array[2]+'-'+array[1]+'] option').each(function( index ) {
                var optionVal=$(this).val();
                optionVal=optionVal.split("-");
                if(optionVal[1]!=parentOpt) {
                    $(this).hide();

                } else {
                    $(this).show();
                    thisSelect.val($(this).val());
                }

            });
        }
    }
}


function fillTemplate() {
    $( ".template" ).each(function( index ) {
        //console.log( index + ": " + $( this ).text() );
        var firstFieldset=$(this).parent().find('fieldset').first();
        if(typeof firstFieldset.html() === 'undefined') {} else {
            var selectDataArray=new Array();
            firstFieldset.find('select').each(function( index ) {
                selectDataArray[index]=$(this).html();

            });
            $(this).append($(this).data('template'));

            $(this).find('select').each(function( index ) {
                $(this).html(selectDataArray[index]);
                $(this).find('option').removeAttr('selected');

            });
            $(this).attr('data-template', $(this).html());

            $(this).html('');
        }
    });
}

$(document).ready(function() {
    startSetValues();
    $("select").change(function() {
        onChangeSetValues(this);
    });

    $("#addListMenuSelect").change(function() {
        var optionVal=$("#addListMenuSelect option:checked").val();
        if(optionVal!='') {
            window.location.replace('/addList/my-fields/'+optionVal);
        }
    });
    var isDateInputSupported = function(){
        var elem = document.createElement('input');
        elem.setAttribute('type','date');
        elem.value = 'foo';
        return (elem.type == 'date' && elem.value != 'foo');
    }

    if (!isDateInputSupported()) {
        $('input[type="date"]').datepicker();
    }


});
$(window).load(function() {
    fillTemplate();
});
function time_control(element) {
    var numCount=$(element).val().length;
    var numVal=$(element).val();
    var lenCounter=numCount;
    if(numCount==2) {
        if(numVal.substring(1,2)==':') {
            numVal=new String('0'+numVal);
            $(element).val( numVal );
            numCount=3;
            lenCounter=numCount
        }
    }
    var regEx1=/[0-2]/;
    var regEx24=/[0-9]/;
    var regEx3=/[0-5]/;
    var regEx5=/[0-3]/;
    var error=0;
    if(lenCounter>5) {
        numVal=numVal.substring(0,5);
        numCount=5;
        lenCounter=5;
        $(element).val( numVal );
    }
    if(lenCounter==5) {
        if(regEx24.exec( numVal.substring(4,5) )==null) {
            numVal=numVal.substring(0,4);
            error++;
        }
        lenCounter--;

    }

    if(lenCounter==4) {
        if(regEx3.exec( numVal.substring(3,4) )==null) {
            numVal=numVal.substring(0,3);
            error++;
        }
        lenCounter--;
    }

    if(lenCounter==3) {
        if(numVal.substring(2,3)!=':') {
            numVal=numVal.substring(0,2);
            error++;
        }

        lenCounter--;
    }

    if(lenCounter==2) {
        if(regEx24.exec( numVal.substring(1,2) )==null) {
            numVal=numVal.substring(0,1);
            error++;
        } else {

            if(numVal.substring(0,1)==2) {

                if(regEx5.exec( numVal.substring(1,2) )==null) {
                    numVal=numVal.substring(0,1);
                    error++;
                }
            }
        }

        lenCounter--;
    }
    if(lenCounter==1) {
        if(regEx1.exec( numVal.substring(0,1) )==null) {
            numVal='';
            error++;
        }

        lenCounter--;
    }
    if((error==0)&&(numCount==2)) {
        numVal=new String(numVal+':');
        $(element).val( numVal );
    }
    if(error>0) {
        $(element).val( numVal );
    }
}

function showFilters() {
    var divFilter=$('.filters .fullFilter');
    if(divFilter.css('display')=='none') {
        $('.filters .fullFilter').show(500);
    } else {
        $('.filters .fullFilter').hide(500);
    }

}

function showMulti() {
    var divFilter=$('.filters .multiField');
    if(divFilter.css('display')=='none') {
        $('.filters .multiField').show(500);
    } else {
        $('.filters .multiField').hide(500);
    }

}