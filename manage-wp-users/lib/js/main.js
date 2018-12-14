(function( $ ) {
    'use strict';

    $( window ).load(function() {

        $('.mwpu-change-status').on('click', function(){
            var user_id = $(this).data('id');
            var member_status_field = $(this).parent().parent().children('.member-status');

            $.ajax({
                url : admin_url.ajax_url,
                type : 'post',
                data : {
                    action : 'changeMemberStatus',
                    user_id: user_id
                },
                success : function( response ) {
                    response = JSON.parse(response);

                    if (response.result) {
                        alertify.success('User status has been updated successfully to: ' + response.status);
                        member_status_field.text(response.status);

                        // if (response.status === 'active') {
                        //     $(this).text('deactivate');
                        // } else {
                        //     $(this).text('activate');
                        // }
                    } else {
                        alertify.error('Something went wrong!');
                    }
                },
                error: function (xhr) {
                    console.log(xhr);
                }
            });
        });

        $('#mwpu-user-table').DataTable({
            'columnDefs': [
                {
                    'targets': 0,
                    'orderable': false,
                    'className': 'select-checkbox',
                    'checkboxes': {
                        'selectRow': true
                    }
                },
                {
                    'targets': 7,
                    'orderable': false
                }
            ],
            'select': {
                'style': 'multi',
                'selector': 'td:first-child'
            },
            order: [[ 1, 'asc' ]]
        });
    });

})( jQuery );