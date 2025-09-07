# FitFlex - Gym Booking System Documentation

## Project Overview

FitFlex is a comprehensive gym booking and personal trainer management system built with PHP and MySQL. It provides users with a smart platform to book gym slots, select personal trainers, and manage their fitness journey through a virtual wallet system.

## Features

### 🏋️ Core Functionality
- **Smart Slot Booking**: Real-time gym slot booking with capacity management
- **Personal Trainer Selection**: Browse and book sessions with qualified trainers
- **Virtual Wallet System**: Secure payment processing with transaction history
- **User Dashboard**: Comprehensive overview of bookings, transactions, and account stats
- **Booking History**: Complete history of past and upcoming sessions

### 🔐 Security Features
- Secure user authentication with password hashing
- Session management for user login state
- Input validation and sanitization
- SQL injection protection using prepared statements

### 💳 Payment System
- Virtual wallet with balance management
- Transaction tracking (credits and debits)
- Secure booking payments
- Top-up functionality with customizable amounts

## Technical Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL
- **Frontend**: HTML5, CSS3, JavaScript
- **Styling**: Custom CSS with modern glassmorphism design
- **Architecture**: MVC-inspired structure with modular components

## File Structure

```
fitflex/
├── assets/
│   └── style.css              # Main stylesheet with modern design
├── add_money.php              # Wallet top-up functionality
├── booking_history.php        # Complete booking history view
├── config.php                 # Database configuration and helpers
├── dashboard.php              # User dashboard with quick stats
├── header.php                 # Navigation header component
├── index.php                  # Landing page
├── login.php                  # User authentication
├── logout.php                 # Session termination
├── process_booking.php        # Booking transaction processing
├── register.php               # User registration
├── slots.php                  # Available slots and booking interface
├── trainers.php               # Trainer profiles and selection
├── wallet.php                 # Wallet management and transactions
└── README.md                  # Basic project description
```

## Database Schema

The system requires a MySQL database named `fitflex_gym` with the following tables:

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    wallet_balance DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Trainers Table
```sql
CREATE TABLE trainers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    qualifications TEXT NOT NULL,
    bio TEXT NOT NULL,
    hourly_rate DECIMAL(8,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Time Slots Table
```sql
CREATE TABLE time_slots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    max_capacity INT NOT NULL DEFAULT 20,
    current_bookings INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Bookings Table
```sql
CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    slot_id INT NOT NULL,
    trainer_id INT NULL,
    amount_paid DECIMAL(8,2) NOT NULL,
    booking_status ENUM('confirmed', 'cancelled', 'completed') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (slot_id) REFERENCES time_slots(id),
    FOREIGN KEY (trainer_id) REFERENCES trainers(id)
);
```

### Transactions Table
```sql
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    transaction_type ENUM('credit', 'debit') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

## Installation & Setup

### Prerequisites
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Git (for version control)

### Step-by-Step Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-username/fitflex.git
   cd fitflex
   ```

2. **Database Setup**
   - Create a MySQL database named `fitflex_gym`
   - Execute the SQL schema provided above
   - Insert sample data for trainers and time slots

3. **Configuration**
   - Edit `config.php` to update database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'fitflex_gym';
   $username = 'your_mysql_username';
   $password = 'your_mysql_password';
   ```

4. **Web Server Setup**
   - Place files in your web server's document root
   - Ensure PHP sessions are enabled
   - Set appropriate file permissions

5. **Sample Data (Optional)**
   ```sql
   -- Sample trainers
   INSERT INTO trainers (name, specialization, qualifications, bio, hourly_rate) VALUES
   ('John Smith', 'Strength Training', 'ACSM Certified Personal Trainer, 5 years experience', 'Specializes in powerlifting and muscle building', 25.00),
   ('Sarah Johnson', 'Cardio & Weight Loss', 'NASM Certified, Nutrition Specialist', 'Expert in fat loss and cardiovascular fitness', 30.00);
   
   -- Sample time slots (next 7 days)
   INSERT INTO time_slots (date, start_time, end_time, max_capacity) VALUES
   (CURDATE() + INTERVAL 1 DAY, '09:00:00', '10:00:00', 20),
   (CURDATE() + INTERVAL 1 DAY, '10:00:00', '11:00:00', 20),
   (CURDATE() + INTERVAL 1 DAY, '18:00:00', '19:00:00', 25);
   ```

## User Guide

### For Regular Users

#### 1. Registration & Login
- Visit the homepage and click "Get Started" or "Register"
- Fill in username, email, and password (minimum 6 characters)
- Login with username/email and password

#### 2. Dashboard Overview
- View wallet balance and account statistics
- See recent bookings and transactions
- Access quick action buttons for common tasks

#### 3. Booking Gym Slots
- Navigate to "Book Slots" from the menu
- Browse available time slots for the next 7 days
- Choose optional personal trainer for additional cost
- Confirm booking (requires sufficient wallet balance)

#### 4. Wallet Management
- Add money using preset amounts ($25, $50, $100, $200) or custom amount
- View transaction history with detailed descriptions
- Monitor available balance for future bookings

#### 5. Trainer Selection
- Browse trainer profiles with specializations and rates
- View qualifications and bio information
- Book sessions directly from trainer profiles

### For Administrators

#### Adding New Trainers
```sql
INSERT INTO trainers (name, specialization, qualifications, bio, hourly_rate) 
VALUES ('Trainer Name', 'Specialization', 'Qualifications', 'Bio', hourly_rate);
```

#### Creating Time Slots
```sql
INSERT INTO time_slots (date, start_time, end_time, max_capacity) 
VALUES ('YYYY-MM-DD', 'HH:MM:SS', 'HH:MM:SS', capacity_number);
```

## API Documentation

### Core Functions (config.php)

#### `isLoggedIn()`
- **Purpose**: Check if user is currently logged in
- **Returns**: Boolean
- **Usage**: Used throughout the application for conditional rendering

#### `requireLogin()`
- **Purpose**: Redirect to login page if user is not authenticated
- **Returns**: Void (redirects if not logged in)
- **Usage**: Used on protected pages to ensure authentication

### Database Operations

#### User Authentication
- Password hashing using `password_hash()` with `PASSWORD_DEFAULT`
- Verification using `password_verify()`
- Session management for persistent login state

#### Transaction Processing
- Atomic operations using database transactions
- Balance validation before processing bookings
- Comprehensive error handling with rollback capability

## Pricing Structure

- **Base Gym Slot**: $15.00 per session
- **Personal Trainer Add-on**: Variable rates ($20-$35/hour)
- **Total Cost**: Base fee + Trainer fee (if selected)
- **Wallet Top-up**: $1 - $1000 per transaction

## Security Considerations

### Input Validation
- All user inputs are sanitized using `htmlspecialchars()`
- Numeric inputs validated with `intval()` and `floatval()`
- Email validation on registration

### Database Security
- Prepared statements prevent SQL injection
- Password hashing protects user credentials
- Session management prevents unauthorized access

### Transaction Security
- Database transactions ensure data consistency
- Balance verification prevents overdrafts
- Comprehensive error handling and logging

## Responsive Design

The application features a modern, responsive design with:
- **Glassmorphism UI**: Translucent elements with backdrop filters
- **Mobile-First Approach**: Optimized for all device sizes
- **Modern Color Scheme**: Gradient backgrounds with accent colors
- **Accessibility**: High contrast and readable typography

## Browser Compatibility

- Chrome 80+
- Firefox 75+
- Safari 13+
- Edge 80+

## Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Verify MySQL server is running
   - Check database credentials in `config.php`
   - Ensure database exists and user has proper permissions

2. **Session Issues**
   - Verify PHP sessions are enabled
   - Check file permissions for session storage
   - Clear browser cookies and try again

3. **Booking Failures**
   - Ensure sufficient wallet balance
   - Check slot availability and capacity
   - Verify database foreign key constraints

4. **Styling Issues**
   - Confirm `assets/style.css` is accessible
   - Check browser compatibility for backdrop-filter
   - Clear browser cache

## Future Enhancements

### Planned Features
- Email notifications for bookings
- SMS reminders for upcoming sessions
- Advanced trainer scheduling system
- Mobile application development
- Payment gateway integration
- Membership plans and discounts
- Workout progress tracking
- Social features and community

### Technical Improvements
- RESTful API development
- Enhanced security measures
- Performance optimization
- Automated testing suite
- Docker containerization
- CI/CD pipeline setup

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit changes (`git commit -am 'Add new feature'`)
4. Push to branch (`git push origin feature/new-feature`)
5. Create Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For technical support or feature requests:
- Create an issue on GitHub
- Contact the development team
- Check the troubleshooting section above

## Version History

- **v1.0.0** - Initial release with core functionality
- **v1.1.0** - Enhanced UI and responsive design
- **v1.2.0** - Improved security and error handling

---

**Last Updated**: September 7, 2025
**Documentation Version**: 1.0
**Project Version**: 1.2.0
