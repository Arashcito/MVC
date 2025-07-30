# 🚀 Deployment Guide - Concordia Server

## Overview
This guide will help you deploy your Montréal Volleyball Club Management System to Concordia's server.

## 📋 Prerequisites

1. **Concordia VPN Access**
   - Connect to Concordia VPN: https://www.concordia.ca/ginacody/aits/support/faq/connect-from-home.html
   - Use your Concordia NETNAME and password

2. **File Transfer Tool**
   - Use SFTP/SCP or any file transfer tool
   - Or use Concordia's web interface if available

## 🔧 Server Information

- **Web Server**: ytc353.encs.concordia.ca
- **Web Directory**: `/www/groups/y/yt_comp353_1/`
- **Database Server**: ytc353.encs.concordia.ca:3306
- **Database**: ytc353_1
- **Username**: ytc353_1
- **Password**: Adm1n001

## 📁 Files to Upload

Upload these files to `/www/groups/y/yt_comp353_1/`:

```
📁 Your Project Files:
├── index.php          (Main application)
├── config.php         (Database configuration)
├── modals.php         (Modal forms)
├── script.php         (JavaScript)
├── test_concordia.php (Test file)
├── database.sql       (Database schema)
├── README.md          (Documentation)
└── DEPLOYMENT_GUIDE.md (This file)
```

## 🌐 Access URLs

After deployment, your application will be available at:

- **Main Application**: https://ytc353.encs.concordia.ca/index.php
- **Test Page**: https://ytc353.encs.concordia.ca/test_concordia.php

## 🔄 Deployment Steps

### Step 1: Connect to Concordia VPN
1. Follow the VPN setup guide
2. Connect using your Concordia credentials
3. Verify you can access Concordia resources

### Step 2: Upload Files
1. Use SFTP/SCP to connect to Concordia server
2. Navigate to `/www/groups/y/yt_comp353_1/`
3. Upload all your PHP files

### Step 3: Set Permissions
```bash
# Set proper permissions for web files
chmod 644 *.php
chmod 644 *.md
chmod 644 *.sql
```

### Step 4: Test the Application
1. Open https://ytc353.encs.concordia.ca/test_concordia.php
2. Verify database connection works
3. Test the main application at https://ytc353.encs.concordia.ca/index.php

## 🗄️ Database Setup

Your database is already set up with the correct credentials:
- **Host**: ytc353.encs.concordia.ca
- **Port**: 3306
- **Database**: ytc353_1
- **Username**: ytc353_1
- **Password**: Adm1n001

## 🔍 Troubleshooting

### Connection Issues
- Ensure VPN is connected
- Check if you can ping ytc353.encs.concordia.ca
- Verify your Concordia credentials

### Database Issues
- Test connection: `mysql -h ytc353.encs.concordia.ca -u ytc353_1 -p ytc353_1`
- Password: Adm1n001
- Check if tables exist: `SHOW TABLES;`

### Web Access Issues
- Verify files are in `/www/groups/y/yt_comp353_1/`
- Check file permissions (should be 644)
- Ensure HTTPS is used (not HTTP)

## 📞 Support

If you encounter issues:
1. Check Concordia's IT support
2. Verify VPN connection
3. Test database connection separately
4. Check file permissions

## ✅ Success Checklist

- [ ] VPN connected to Concordia
- [ ] Files uploaded to correct directory
- [ ] Test page shows database connection
- [ ] Main application loads without errors
- [ ] All tabs and modals work
- [ ] Can add/edit/delete records

## 🎉 You're Done!

Once deployed, your volleyball club management system will be accessible to anyone with the URL, and you can share it with your team members and instructors. 