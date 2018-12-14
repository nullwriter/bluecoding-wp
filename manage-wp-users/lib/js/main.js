(function( $ ) {
    'use strict';

    $( window ).load(function() {

        // $('#test_api_connection').on('click', function(){
        //     $('#result_test_api').text('');
        //     $('#loading-spinner').show();
        //
        //     $.ajax({
        //         url : admin_url.ajax_url,
        //         type : 'get',
        //         data : {
        //             action : 'simplefmTestConnection'
        //         },
        //         success : function( response ) {
        //             response = JSON.parse(response);
        //             $('#result_test_api').html(response.message);
        //         },
        //         complete : function() {
        //             $('#loading-spinner').hide();
        //         }
        //     });
        // });

        $('#mwpu-user-table').DataTable({
            'columnDefs': [
                {
                    'targets': 0,
                    'className': 'select-checkbox'
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