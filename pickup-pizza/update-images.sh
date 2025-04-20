#!/bin/bash

# Ensure the images directory exists
mkdir -p storage/app/public/products

# Ensure storage link is created
php artisan storage:link

# Run the update script
php database/update-specialty-images.php

echo "Image paths updated successfully!" 