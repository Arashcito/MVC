# Montréal Volleyball Club Management System

A comprehensive PHP-based management system for the Montréal Volleyball Club, built for COMP 353 Project.

## Features

- **Location Management**: Manage club locations, capacity, and general managers
- **Personnel Management**: Track staff, coaches, and administrators
- **Family Member Management**: Handle family associations for minor members
- **Member Management**: Complete member profiles with age-based requirements
- **Payment Tracking**: Record membership fees and donations
- **Team Management**: Create and manage teams with player assignments
- **Session Scheduling**: Schedule games and training sessions
- **Email Management**: Generate and track communication
- **Reports & Analytics**: Comprehensive reporting system

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- PDO MySQL extension

## Installation

1. **Clone or download the project files**
   ```bash
   git clone <repository-url>
   cd php-project
   ```

2. **Set up the database**
   - Create a MySQL database named `volleyball_club`
   - Import the database schema:
   ```bash
   mysql -u root -p volleyball_club < database.sql
   ```

3. **Configure database connection**
   - Edit `config.php` with your database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'volleyball_club';
   $username = 'your_username';
   $password = 'your_password';
   ```

4. **Set up web server**
   - Point your web server to the project directory
   - Ensure PHP has write permissions for session management

5. **Access the application**
   - Navigate to `http://localhost/php-project/` in your browser

## File Structure

```
php-project/
├── index.php          # Main application file
├── config.php         # Database and application configuration
├── modals.php         # Modal forms for data entry
├── script.php         # JavaScript functionality
├── database.sql       # Database schema and sample data
├── README.md          # This file
└── index.html         # Original HTML file (for reference)
```

## Database Schema

The system uses the following main tables:

- **locations**: Club facilities and their details
- **personnel**: Staff and coaches information
- **family_members**: Family associations for minor members
- **members**: Club member profiles
- **payments**: Financial transactions
- **teams**: Team information and assignments
- **sessions**: Games and training schedules
- **emails**: Communication tracking

## Key Features

### 1. Location Management
- Add/edit club locations (Head and Branch types)
- Track capacity and contact information
- Assign general managers

### 2. Personnel Management
- Complete staff profiles with SSN and Medicare validation
- Role-based assignments (Coach, Manager, etc.)
- Mandate tracking (Volunteer/Salaried)

### 3. Family Member Management
- Primary and Secondary family member types
- Required for minor members (under 18)
- Emergency contact information

### 4. Member Management
- Age-based validation (minors require family association)
- Physical attributes tracking (height, weight)
- Location assignments

### 5. Payment System
- Multiple payment methods (cash, check, credit, debit)
- Year-based tracking
- Membership and donation types

### 6. Team Management
- Gender-based team creation
- Player assignments
- Coach assignments

### 7. Session Scheduling
- Game and training session types
- Score tracking for games
- Coach assignments

## Usage

### Adding a New Location
1. Click on "Locations" tab
2. Click "Add Location" button
3. Fill in all required fields
4. Click "Save Location"

### Adding Personnel
1. Click on "Personnel" tab
2. Click "Add Personnel" button
3. Complete all required fields including SSN and Medicare
4. Select role and mandate
5. Click "Save Personnel"

### Adding Family Members
1. Click on "Family" tab
2. Click "Add Family Member" button
3. Select type (Primary/Secondary)
4. Fill in all required information
5. Click "Save Family Member"

### Adding Members
1. Click on "Members" tab
2. Click "Add Member" button
3. Fill in member details
4. For minors (under 18), family association is required
5. Click "Save Member"

### Recording Payments
1. Click on "Payments" tab
2. Click "Record Payment" button
3. Select member and payment details
4. Click "Record Payment"

### Creating Teams
1. Click on "Teams" tab
2. Click "Create Team" button
3. Enter team details and select players
4. Click "Save Team"

### Scheduling Sessions
1. Click on "Sessions" tab
2. Click "Schedule Session" button
3. Select session type and details
4. For games, select both teams
5. Click "Schedule Session"

## Security Features

- Input sanitization and validation
- SQL injection prevention using prepared statements
- XSS protection with htmlspecialchars()
- Session-based flash messages
- Form validation on both client and server side

## Customization

### Adding New Fields
1. Update the database schema in `database.sql`
2. Modify the corresponding form in `modals.php`
3. Update the PHP functions in `index.php`
4. Add validation as needed

### Styling
- All CSS is contained in the `<style>` section of `index.php`
- Responsive design for mobile devices
- Bootstrap-like styling system

### Database Configuration
- Edit `config.php` for database connection settings
- Modify helper functions as needed
- Add new validation rules

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Verify database credentials in `config.php`
   - Ensure MySQL service is running
   - Check database name exists

2. **Form Submission Issues**
   - Verify all required fields are filled
   - Check browser console for JavaScript errors
   - Ensure PHP error reporting is enabled

3. **Data Not Displaying**
   - Check database queries in `index.php`
   - Verify table structure matches schema
   - Check for SQL errors in logs

### Error Logging
- PHP errors are displayed by default (development mode)
- Check web server error logs for additional information
- Database errors are caught and displayed

## Development

### Adding New Features
1. Create database tables as needed
2. Add PHP functions for data operations
3. Create modal forms for data entry
4. Add JavaScript functionality
5. Update the main interface

### Testing
- Test all CRUD operations
- Verify form validations
- Check responsive design
- Test with different data scenarios

## License

This project is created for COMP 353 Database Systems course.

## Support

For issues or questions:
1. Check the troubleshooting section
2. Verify database setup
3. Review error logs
4. Test with sample data

## Future Enhancements

- User authentication and authorization
- Advanced reporting with charts
- Email integration
- Mobile app development
- API endpoints for external integrations
- Backup and restore functionality 