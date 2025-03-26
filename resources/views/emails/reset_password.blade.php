<!DOCTYPE html>
<html>
  <head>
    <title>Reach</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  </head>
  <body>
    <table align="center" width="100%" border="0" cellspacing="0" cellpadding="0" class="em_main_table" style="font-family: 'Public Sans', sans-serif; max-width: 800px; width: 100%;margin: 0px auto; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-table-lspace: 0px; mso-table-rspace: 0px; width: 100%; text-transform: initial;">
      <tbody>
        <tr style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box;">
          <td style="width: 100%; margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-line-height-rule: exactly;">
            <table cellpadding="0" cellspacing="0" border="0" style="padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-table-lspace: 0px; mso-table-rspace: 0px; margin: 0 auto; text-transform: initial; max-width:100%; width: 100%;">
              <tbody>
                <tr style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box;">
                  <td style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-line-height-rule: exactly;">
                    <table cellpadding="0" cellspacing="0" border="0" style="background:#283440; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-table-lspace: 0px; mso-table-rspace: 0px; width: 100%; margin: 0 auto; text-transform: initial;text-align: center;">
                      <tbody>
                        <tr style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box;">
                          <td style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-line-height-rule: exactly;">
                            <img src="{{ url('/') }}/assets/images/logo.png" alt="logo" style="width:200px; padding:20px 0px;">
                          </td>
                        </tr>
                        <tr style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; height: 5px; background:#ff0075;">
                          <td></td>
                        </tr>
                      </tbody>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="0" style="background:#fff; padding: 20px 15px; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-table-lspace: 0px; mso-table-rspace: 0px; width: 100%; margin: 0 auto; text-transform: initial;">
                      <tbody>
                        <tr style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box;">
                          <td style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-line-height-rule: exactly;">
                          </td>
                        </tr>
                        <tr style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box;">
                          <td style="font-family: 'Public Sans', sans-serif;margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-line-height-rule: exactly;">
                            <div style="font-size: 15px; padding: 20px; color:#283440; font-weight:300; line-height: 20px; font-family: 'Public Sans', sans-serif;">
                                <p>Hi {{ $data['first_name'] }},</p>
                                <p>We received a request to reset your password for your Reach account. Click the link below to reset your password:</p>
                                <p><a href="{{ url('reset-password?token=' . $data['token']) }}">Reset Your Password</a></p>
                                <p>If you did not request a password reset, please ignore this email or contact our support team if you have any questions.</p>
                                <p>Thank you,</p>
                                <p>The Reach Team</p>
                            </div>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <table cellpadding="0" cellspacing="0" border="0" width="100%" style="background:#283440; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-table-lspace: 0px; mso-table-rspace: 0px; width: 100%;text-transform: initial;text-align: center; height: 50px;">
                      <tbody>
                        <tr style="width:100%; margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box;">
                          <td colspan="1" style="width:100%; margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-line-height-rule: exactly; color: #fff; font-size:12px; height: 12px; background: #283440;"></td>
                        </tr>
                        <tr style="width:100%; margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box;">
                          <td colspan="1" style="font-family: 'Public Sans', sans-serif;;width:100%; margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; border-collapse: collapse; mso-line-height-rule: exactly; color: #fff; font-size:15px;"> © Copyright 2024 - All Rights Reserved </td>
                        </tr>
                        <tr style="margin: 0; padding: 0; -webkit-box-sizing: border-box; box-sizing: border-box; height: 5px; background:#ff0075;">
                          <td></td>
                        </tr>
                      </tbody>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </td>
        </tr>
      </tbody>
    </table>
  </body>
</html>
