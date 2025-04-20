#!/bin/bash

# Create the products directory if it doesn't exist
mkdir -p storage/app/public/products
echo "Created products directory"

# Move and rename the images
echo "Moving and renaming images..."

# Mapping of image names (note the typo fix from "speciality" to "specialty")
for img in public/images/speciality-*.jpg; do
  # Extract the pizza name from the filename
  base=$(basename "$img")
  # Replace "speciality" with "specialty"
  newname=$(echo "$base" | sed 's/speciality/specialty/')
  # Set the destination path
  dest="storage/app/public/products/$newname"
  
  # Copy the image to the new location
  cp "$img" "$dest"
  echo "Copied: $img -> $dest"
done

# Set permissions
chmod -R 755 storage/app/public/products
echo "Permissions set to 755"

# List the copied images
echo -e "\nImages in storage/app/public/products:"
ls -la storage/app/public/products/

echo -e "\nDone! Images are now in the correct location with correct names." 