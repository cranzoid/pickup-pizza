        <div class="footer">
            <p>&copy; {{ date('Y') }} PISA Pizza. All rights reserved.</p>
            <p>{{ App\Models\Setting::get('business_address', '55 Parkdale Ave North, Hamilton, ON L8H 5W7') }} | {{ App\Models\Setting::get('business_phone', '(905) 547-5777') }} | {{ App\Models\Setting::get('business_email', 'support@pisapizza.ca') }}</p>
            <p>This is an automated system notification. Please do not reply to this email.</p>
        </div> 