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


$(document).ready(function() {
    startSetValues();
    $("select").change(function() {
        onChangeSetValues(this);
    });


});