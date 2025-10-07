#!/bin/bash

# AutoScriptHub Asset Deployment Script
# This script copies theme assets to the public directory for production deployment

echo "Starting asset deployment..."

# Copy theme assets
echo "Copying theme assets..."
cp -r resources/views/theme public/

# Copy admin template assets
echo "Copying admin template assets..."
cp -r resources/views/admin/template public/admin

# Copy vendor assets (TinyMCE, etc.)
echo "Copying vendor assets..."
cp -r vendor public/

# Copy additional assets
echo "Copying additional assets..."
cp resources/views/assets/style.css public/assets/

# Run npm production build
echo "Running npm production build..."
npm run production

echo "Asset deployment completed successfully!"
echo "All assets are now available in the public directory."
