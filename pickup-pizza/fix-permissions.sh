#!/bin/bash

# Fix permissions for the products directory
chmod -R 755 storage/app/public/products
echo "Permissions fixed!"

# List all specialty pizza images to verify they exist
echo "Checking for specialty pizza images:"
ls -la storage/app/public/products/specialty-*.jpg || echo "No specialty pizza images found!" 