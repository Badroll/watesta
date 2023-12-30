/** by eji  **/
$(document).ready(function () {
    $(document).on('click','.check-all',function(){
        $(".check-id").prop("checked" , this.checked);
        if($('.check-id').prop('checked')){
            $('.check-id').closest("tr").addClass("selected");
        }else{
            $('.check-id').closest("tr").removeClass("selected");
        }
    }); 
    $(document).on('click','.check-id',function(){
        if($(this).prop('checked')){
            $(this).closest("tr").addClass("selected");
        }else{
            $(this).closest("tr").removeClass("selected");
        }
    });
    //close notive
    $(document).on('click','.ui-pnotify', function(){
        $(this).remove();
    });

    // single delete
    $(document).on('click','.single-delete', function(){
        var url = $(this).data('url');
        swal_alert(url);
        return false;
    });
    // multiple delete
    $(document).on('click','.remove-all-data', function(){
          dell_all_item('remove-all');
          return false;
    });
    // $(".select2_single").select2({
    //     placeholder: "Select one "
    // });
    // $(".select2_multiple").select2({
    //     allowClear: true
    // });
    // $('.tags_input').tagsInput({
    //     width: 'auto'
    // });
    $('.form-valid').validate();


    // Add placeholder to the datatable filter option
    $('.dataTables_filter input[type=search]').attr('placeholder','Type to filter...');


    // Enable Select2 select for the length option
    $('.dataTables_length select').select2({
        minimumResultsForSearch: "-1"
    });


    //Enable Select2 select for individual column searching
    $('.filter-select').select2({
        width: '100%'
    });

    //change status
    $(document).on('click','.up-status', function(){
            var url = $(this).data("url");
            swal({   
                title: "Are you sure?",   
                text: "You want to update this status!",   
                type: "warning",   
                showCancelButton: true,   
                confirmButtonColor: "#DD6B55",   
                confirmButtonText: "Yes, update it!",   
                closeOnConfirm: true 
            }, function(isConfirm){
                    if(isConfirm){
                        location.replace(url);   
                    }
            });
        });
    
    
});

function notif_success(msg)
{
    new PNotify({
        title: 'Success Message',
        text: msg,
        type: 'success'
    });
}

function notif_error(msg)
{
    new PNotify({
        title: 'Error Message',
        text: msg,
        type: 'error'
    });
}

function notif_info(msg)
{
    new PNotify({
        title: 'Information Message',
        text: msg,
        type: 'info'
    });
}

function notif_warning(msg)
{
    new PNotify({
        title: 'Warning Message',
        text: msg
    });
}

function swal_alert(el, custom_msg){
    custom_msg = custom_msg || "Are you sure want to delete ?";

    msg = custom_msg;
    swal({
        title: msg,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes!',
        closeOnConfirm: true
    },
    function(isConfirm){  
        if (isConfirm) {     
            location.replace(el);  
        }
    });
}

function sweetsuccess(msg){
    console.log(msg);
    swal("Success!", msg, "success");
}
function sweeterror(msg){
    swal("Error!", msg, "error");
}
function sweetinfo(msg){
    swal("Information!", msg, "info");
}


function dell_all_item(el, custom_msg){
    custom_msg = custom_msg || "Are you sure want to delete this data selected ?";
    msg = custom_msg;
    swal({
        title: msg,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#DD6B55',
        confirmButtonText: 'Yes!',
        closeOnConfirm: true
    },
    function(isConfirm){  
        if (isConfirm) {
            if(el==''){
                location.replace(el);
            }else{
                $('.'+el).submit(); 
            }    
        }
    });
}