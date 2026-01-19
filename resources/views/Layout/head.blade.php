<meta charset="utf-8" />
<title>IntraKB</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
<meta content="Themesbrand" name="author" />
<!-- App favicon -->
<link rel="shortcut icon" href="{{url('/')}}/assets/images/favicon.png">

<!-- Bootstrap Css -->
<link href="{{url('/')}}/assets/css/bootstrap.min.css" id="bootstrap-style" rel="stylesheet" type="text/css" />
<!-- Icons Css -->
<link href="{{url('/')}}/assets/css/icons.min.css" rel="stylesheet" type="text/css" />
<!-- App Css-->
<link href="{{url('/')}}/assets/css/app.min.css" id="app-style" rel="stylesheet" type="text/css" />
<script src="{{url('/')}}/assets/libs/jquery/jquery.min.js"></script>

<script>
    function get_employee_details(EPF) {
        const formData = new FormData();
        formData.append('EPF', EPF);
        formData.append('_token', "<?= csrf_token() ?>");
        var arr = '';
        $.ajax({
            type: 'POST',
            url: "<?php echo url('/') . "/get_member_data_html" ?>",
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            async: false,
            success: function(data) {
                if (data) {
                    arr = data;
                }
            },
            error: function(xhr, textStatus, errorThrown) {

            }
        });
        return arr;
    }
</script>