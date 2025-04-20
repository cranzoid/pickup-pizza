                                @php
                                    $settings = new \App\Models\Setting();
                                    $store_address = $settings->get('business_address', '55 Parkdale Ave North, Hamilton, ON L8H 5W7');
                                    // Extract city and postal code from address if available
                                    $addressParts = explode(',', $store_address);
                                    $cityPostalParts = count($addressParts) > 1 ? explode(' ', trim($addressParts[1]), 2) : ['Hamilton', 'ON L8H 5W7'];
                                    $city = $cityPostalParts[0] ?? 'Hamilton';
                                    $postal = count($cityPostalParts) > 1 ? $cityPostalParts[1] : 'ON L8H 5W7';
                                @endphp 