<html lang="en">

<head>
    <title>Pdf</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0,">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="x-apple-disable-message-reformatting">
    <meta name="keywords" content="Trendy Way Newsletter templates, Email Templates, Newsletters, Marketing  templates, Advertising templates, free Newsletter" />
    <link rel="icon" href="">

    <style type="text/css">
        @font-face {
            font-family: 'Public Sans';
            src: url("{{asset('fonts/PublicSans-Regular.ttf')}}") format('truetype');
            font-weight: 600;
            font-style: normal;
        }

        * {
            margin: 0;
            padding: 0;
            border: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: 'Public Sans', sans-serif;
            height: 100%;
            background-color: #394651;
            margin: 0px;
        }

        @media print {
            body {
                background-color: #394651 !important;
            }
        }
    </style>
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
    <table align="center" border="0" cellpadding="0" cellspacing="0" class="mobile" data-module="navigation" style="padding-left:20px;padding-right:20px;">
        <tbody>
            <tr>
                <td>
            <tr>
                <td style="padding:10px;">
                    <table align="center" border="0" cellpadding="0" cellspacing="0" class="scale" width="600">
                        <tr>
                            <td height="0"></td>
                        </tr>
                        <tr>
                            <td style="width:450px;background-color:#394651;padding-top:0px;">
                                <table align="center" border="0" ; cellpadding="0" class="scale" width="450" bgcolor="#394651">
                                    <tbody>
                                        <tr>
                                            <td valign="start" width="600">
                                                <table class="fullcenter fullcenter1" width="100%" align="left" border="0" cellpadding="0" cellspacing="0" width="150">
                                                    <tr>
                                                        <td height="20" width="350" valign="middle" align="left" class="center_res">
                                                            <table align="left" border="0" cellpadding="0" cellspacing="0" class="fullCenter" style="text-align:center; font-family: '';" width="150">
                                                                <tr>
                                                                    <td style="height:10px;">
                                                                        <!-- <img src="{{asset('assets/images/Logo.png')}}" style="width:200px;"> -->

                                                                        <img style="width:300px;" src="{{ $logoPath }}" />
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                        <td height="10" width="230" valign="right" align="right" class="center_res" style="padding-top:40px;">
                                                            <table align="right" border="0" cellpadding="0" cellspacing="0" class="fullCenter" style="text-align:center; font-family: ''; font-size:13px" width="">
                                                                <tr>
                                                                    <td style="padding-right:10px">
                                                                        <div style="width:250px; height:250px; border-radius:50%; overflow:hidden; 
                                                                        padding:5px;
                                                                        border:1px solid #394651;">
                                                                            <img src="{{ $profile_photo ?? url('images/default-profile.png') }}"
                                                                                style="width: 100%; height: 100%; border-radius: 50%; 
                                                                                object-fit:cover;
                                                                                max-height:100%;">
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding-left:20px">
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td height="0px"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td class="" style="color:#64f259;font-size:16px;">FULL NAME</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="public-sans-font" style="color:#52ac4c;text-transform:capitilize;font-size:24px;color:#fff;">{{ $name }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td height="20px"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td class="public-sans-font" style="color:#64f259;text-transform:Uppercase;font-size:16px;">Email</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="public-sans-font" style="color:#52ac4c;text-transform:normal;font-weight:600px;font-size:24px;color:#fff;">{{ $email }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td height="20px"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td class="public-sans-font" style="color:#64f259;font-size:16px;">MEMBERSHIP TYPE</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="color:#52ac4c;text-transform:capitalize;font-size:24px;color:#fff;"> {{ $membership_type }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td height="20px"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @if($membership_expiry!='')
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td class="public-sans-font" style="color:#64f259;text-transform:uppercase;font-size:16px;">MEMBERSHIP EXPIRY</td>
                                                        </tr>
                                                        <tr>
                                                            <td class="public-sans-font" style="font-size:24px;color:#fff;">{{ $membership_expiry }}</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                @endif
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td height="20px"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%">
                                                    <tbody>
                                                        <tr>
                                                            <td style="color:#FF0075;text-transform:uppercase;font-size:16px;padding-bottom:5px;">Profile page</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="height:100px;">
                                                                <img src="data:image/png;base64,{{ $qr_code }}" alt="QR Code" style="width:150px;">
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%" height="20px">
                                                    <tbody>
                                                        <tr>
                                                            <td height="5vh"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%" height="10px">
                                                    <tbody>
                                                        <tr>
                                                            <td height="2vh"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="100%" height="10px">
                                                    <tbody>
                                                        <tr>
                                                            <td height="2vh"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </td>
            </tr>

        </tbody>
    </table>
    <br><br><br>

    <table align="center" topmargin="0" border="0" cellpadding="0" class="mobile" width="700px">
        <tbody>
            <tr>
                <td style="color:#64f259;text-transform:normal;font-size:14px;padding-bottom:5px;text-align:right;">
                    <br><br><br><br><br><br><br><br><br>
                    <span>Generated {{ $generated_date }}</span>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>