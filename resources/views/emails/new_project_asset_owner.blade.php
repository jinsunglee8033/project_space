<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px;">
                <tr>
                    <td style="background-color: #4CAF50; padding: 15px; text-align: center; color: white;">
                        <h2>Welcome to Our Service</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 30px; text-align: left; font-family: Arial, sans-serif; line-height: 1.6;">
                        <p>Hello, {{ $details['who'] }}!</p>
                        <p>Thank you for joining us. We’re glad to have you!</p>

                        <p style="text-align: center;">
                            <a href="{{ $details['url'] }}"
                               style="
                                  background-color: #007bff;
                                  color: white;
                                  padding: 12px 20px;
                                  text-decoration: none;
                                  border-radius: 5px;
                                  display: inline-block;
                               ">
                                This project #{{ $details['c_id'] }} created by {{ $details['creator'] }}.
                            </a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="background-color: #f1f1f1; text-align: center; padding: 10px; font-size: 12px; color: #888;">
                        &copy; {{ date('Y') }} Your Company. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>