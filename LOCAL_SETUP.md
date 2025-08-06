# Local Development Setup

## 🏠 Local Environment Configuration

This setup allows you to run the Volleyball Club DBMS on your local machine using your MySQL database instead of the Concordia server.

### 📋 Prerequisites

✅ **Already Configured:**
- ✅ MySQL installed and running
- ✅ PHP installed
- ✅ Database `volleyball_club` created
- ✅ MySQL user: `root` with password: `Radio@33`

### 🚀 Quick Start

1. **Test your local environment:**
   ```bash
   # Open in browser: http://localhost/path/to/your/project/test_local_environment.php
   ```

2. **Switch to local configuration:**
   ```bash
   ./switch_environment.sh local
   ```

3. **Check current environment:**
   ```bash
   ./switch_environment.sh status
   ```

### 📁 Configuration Files

- `config.local.php` - Local MySQL configuration
- `config.concordia.php` - Original Concordia server configuration
- `config.php` - Active configuration (symlinked by switcher)

### 🔧 Environment Switcher Commands

```bash
# Switch to local environment
./switch_environment.sh local

# Switch to Concordia server
./switch_environment.sh concordia

# Check current status
./switch_environment.sh status

# Show help
./switch_environment.sh
```

### 🗄️ Database Setup

If you need to set up the database schema:

```bash
# Import the database schema
mysql -u root -p volleyball_club < database_schema.sql

# Add sample data (optional)
php add_sample_data.php
```

### 🔍 Troubleshooting

**Connection Issues:**
1. Ensure MySQL is running: `brew services start mysql` (if using Homebrew)
2. Verify database exists: `mysql -u root -p -e "SHOW DATABASES;"`
3. Check credentials in `config.local.php`

**PHP Issues:**
1. Start PHP built-in server: `php -S localhost:8000`
2. Check PHP extensions: `php -m | grep pdo_mysql`

### 🌐 Running the Application

**Option 1: PHP Built-in Server**
```bash
cd /Users/admin/Documents/Sutro/DBMS/MVC
php -S localhost:8000
# Visit: http://localhost:8000
```

**Option 2: Using XAMPP/MAMP**
- Place project in htdocs folder
- Visit: http://localhost/MVC

**Option 3: Using Local Apache/Nginx**
- Configure virtual host to point to project directory

### 📝 Development Tips

- The local config includes extra debugging features
- Check `debug.log` for development logs
- Connection status is shown when including config files
- Use `test_local_environment.php` to verify setup

### 🔄 Switching Back to Concordia

When you need to deploy or test on the Concordia server:

```bash
./switch_environment.sh concordia
```

This will restore the original Concordia configuration.
