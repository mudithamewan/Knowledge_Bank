function ajax_action(link,type,data,csrf) {
        $('.VIEW_EMPTY').html('');

        const formData = new FormData();

        $('#'+type+'_VIEW').html('<center><i class="bx bx-loader bx-spin font-size-16 align-middle me-2"></i> Loading...</center>');
        formData.append('_token', csrf);
        formData.append('DATA', data);
      
        $.ajax({
            type: 'POST',
            url: link,
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(data) {

                if (data.success) {
                    Swal.fire(
                        'Success!',
                        data.success,
                        'success'
                    );
                }

                if (data.result) {
                    $('#' + type + '_VIEW').html(data.result);
                }

                if (data.error) {
                    Swal.fire(
                        'Error!',
                        data.error,
                        'error'
                    );
                }

            },
            error: function(xhr, status, error) {
                Swal.fire(
                    'Error!',
                    error,
                    'error'
                );
            }
        });
    }