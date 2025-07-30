#!/bin/bash

# Concordia Server Deployment Script
# This script helps you deploy your PHP application to Concordia's server

echo "🚀 Concordia Server Deployment Script"
echo "======================================"

# Check if user is connected to Concordia VPN or network
echo "📡 Checking connection to Concordia server..."

# Test connection to Concordia server
if ping -c 1 ytc353.encs.concordia.ca > /dev/null 2>&1; then
    echo "✅ Connected to Concordia network"
else
    echo "❌ Not connected to Concordia network"
    echo "Please connect to Concordia VPN first:"
    echo "https://www.concordia.ca/ginacody/aits/support/faq/connect-from-home.html"
    exit 1
fi

echo ""
echo "📁 Files to upload:"
echo "- index.php"
echo "- config.php" 
echo "- modals.php"
echo "- script.php"
echo "- database.sql"
echo "- README.md"

echo ""
echo "🌐 Web URL will be:"
echo "https://ytc353.encs.concordia.ca/"

echo ""
echo "📋 Next steps:"
echo "1. Connect to Concordia VPN"
echo "2. Upload files to /www/groups/y/yt_comp353_1/"
echo "3. Test the application at https://ytc353.encs.concordia.ca/"
echo ""
echo "🔧 Database connection details:"
echo "- Host: ytc353.encs.concordia.ca"
echo "- Database: ytc353_1"
echo "- Username: ytc353_1"
echo "- Password: Adm1n001"
echo "- Port: 3306" 