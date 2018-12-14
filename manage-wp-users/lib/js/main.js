(function( $ ) {
    'use strict';

    $( window ).load(function() {

        $('.mwpu-change-status').on('click', function(){
            var user_id = $(this).data('id');
            var button = $(this);
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
                        alertify.success('User status has been updated successfully to: ' + response.status.toUpperCase());
                        updateLabels(response.status, member_status_field, button);
                    } else {
                        alertify.error('Something went wrong!');
                    }
                },
                error: function (xhr) {
                    alertify.error('Something went wrong: '+xhr);
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
                    'targets': 6,
                    'orderable': false
                }
            ],
            'select': {
                'style': 'multi',
                'selector': 'td:first-child'
            },
            order: [[ 1, 'asc' ]]
        });

        function updateLabels(new_status, status_field, button) {

            // update member status in list
            status_field.text(new_status);

            // update button label
            if (new_status === 'active') {
                button.text('deactivate');
            } else {
                button.text('activate');
            }
        }
    });

})( jQuery );