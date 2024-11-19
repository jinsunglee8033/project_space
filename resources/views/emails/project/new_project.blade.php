<table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
    <tr>
        <td align="center">
            <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; padding: 20px;">
                <tr>
                    <td align="center" style="padding: 15px; background-color: #0BC27F; color: white;">
                        <h2 style="margin: 0;">Project Space</h2>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 20px;">
                        <h2 style="text-align: center;">{{ $details['receiver'] }}</h2>
                        <p style="text-align: center;">{{ $details['message'] }}</p>
                        <p style="color: #0BC27F; text-align: center;">{{ $details['title'] }}</p>

                        <table width="100%" cellpadding="8" cellspacing="0" style="margin-top: 20px; border-collapse: collapse;">
                            <tr>
                                <th align="left" style="border-bottom: 1px solid #ddd; padding: 10px;">Project</th>
                                <td align="left" style="border-bottom: 1px solid #ddd; padding: 10px;">{{ $details['project_title'] }}</td>
                            </tr>
                            <tr>
                                <th align="left" style="border-bottom: 1px solid #ddd; padding: 10px;">Project ID</th>
                                <td align="left" style="border-bottom: 1px solid #ddd; padding: 10px;">{{ $details['project_id'] }}</td>
                            </tr>
                        </table>

                        <p style="text-align: center; margin-top: 20px;">
                            <a href="{{ url($details['url']) }}" style="background-color: #0BC27F; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px;">Go to Project</a>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td align="center" style="padding: 10px; font-size: 12px; color: #888; background-color: #f1f1f1;">
                        &copy; {{ date('Y') }} Space Project. All rights reserved.
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>