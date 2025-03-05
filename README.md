# Pet Care Connect Platform

<p align="center">
  <img src="public/images/logo.png" alt="Pet Care Connect Logo" width="200">
</p>

<p align="center">
  <strong>Connecting Pet Owners with Quality Care Services</strong>
</p>

## Project Overview

Pet Care Connect is a comprehensive web-based platform developed as part of the Software Engineering 1 and 2 courses. This platform aims to connect pet owners with various pet care services, including grooming and veterinary care. Built with Laravel, the platform provides a robust backend architecture supporting both admin dashboard and customer-facing interfaces.

The system facilitates seamless communication between pet owners and service providers, streamlining the process of finding, booking, and managing pet care appointments. With an intuitive user interface and powerful search capabilities, Pet Care Connect serves as a one-stop solution for all pet care needs.


## Features

- **User Authentication & Authorization**
  - Secure login and registration system using Laravel's built-in authentication
  - Role-based access control for pet owners, shop owners, and administrators
  - Password reset functionality and account management

- **Shop Management**
  - Complete shop registration and management system
  - Shop profile customization with logo, description, and contact information
  - Operating hours and service availability configuration
  - Shop verification and approval process

- **Service Management**
  - Tools to manage and approve services offered by shops
  - Service categorization and tagging
  - Pricing and duration settings
  - Service availability scheduling

- **Appointment Booking**
  - Integrated booking system for pet care services
  - Real-time availability checking
  - Appointment confirmation and reminders
  - Rescheduling and cancellation capabilities

- **Location-based Search**
  - Find nearby grooming and veterinary services using geolocation
  - Advanced filtering by service type, rating, and availability
  - Interactive map interface using Leaflet.js

- **User Profiles**
  - Pet owner profiles with pet management capabilities
  - Multiple pet registration with detailed information
  - Appointment history and upcoming bookings
  - Favorite shops and services

- **Reviews and Ratings**
  - Post-service review and rating system
  - Photo attachments for reviews
  - Shop response capabilities

- **Responsive Design**
  - Mobile-friendly interface using Tailwind CSS
  - Cross-browser compatibility
  - Progressive enhancement for various device capabilities

## Technology Stack

- **Frontend**:
  - Blade templating engine
  - Tailwind CSS for responsive design
  - Alpine.js for reactive components
  - JavaScript/jQuery for enhanced interactivity
  - Leaflet.js for maps and location services
  
- **Backend**:
  - Laravel 10.x framework
  - PHP 8.x
  - MVC architecture
  - RESTful API design
  
- **Database**:
  - MySQL relational database
  - Eloquent ORM
  - Database migrations and seeders
  
- **Authentication**: 
  - Laravel Sanctum for API authentication
  - Session-based authentication for web interface
  
- **Development**: 
  - Vite for asset bundling
  - Laravel Mix for asset compilation
  - Git for version control
  - Laravel Artisan CLI

## Installation

### Prerequisites
- PHP 8.0 or higher
- Composer
- Node.js and NPM
- MySQL
- Git

### Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/deanbilledo/PetCareConnect.git
   cd PetCareConnect
   ```

2. Install PHP dependencies:
   ```bash
   composer install
   ```

3. Install NPM dependencies:
   ```bash
   npm install
   ```

4. Create environment file:
   ```bash
   cp .env.example .env
   ```

5. Generate application key:
   ```bash
   php artisan key:generate
   ```

6. Configure your database in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=petcareconnect
   DB_USERNAME=root
   DB_PASSWORD=
   ```

7. Run migrations and seed the database:
   ```bash
   php artisan migrate --seed
   ```

8. Create symbolic link for storage:
   ```bash
   php artisan storage:link
   ```

9. Start the development server:
   ```bash
   php artisan serve
   npm run dev
   ```

10. Access the application at `http://localhost:8000`

## Project Structure

- `app/Http/Controllers` - Contains all controllers for handling requests
- `app/Models` - Contains Eloquent models for database interaction
- `app/Services` - Contains business logic services
- `database/migrations` - Database migrations for schema definition
- `database/seeders` - Seeders for populating the database with initial data
- `resources/views` - Blade view templates for the frontend
- `resources/js` - JavaScript assets and components
- `resources/css` - CSS and Tailwind styling
- `routes` - Application routes definition
- `public` - Publicly accessible files
- `tests` - Application test suite

## Testing

Run the test suite with:
```bash
php artisan test
```

For frontend testing:
```bash
npm run test
```

## Contributing

1. Fork the repository
2. Create a new branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Commit your changes (`git commit -m 'Add some amazing feature'`)
5. Push to the branch (`git push origin feature/amazing-feature`)
6. Open a Pull Request

## Future Development

- Implement payment gateway integration with multiple providers
- Add real-time notifications using Laravel WebSockets
- Develop API endpoints for future mobile app integration
- Enhance search functionality with advanced filters and AI recommendations
- Add service analytics and reporting features for business insights
- Implement multi-language support for international users

## Team Members

- Faminiano, Christian Jude
- Billedo, Dean Reight
- Valeros, Greybin
- Cawili, Herwayne
- Jimenez, Myke

## Acknowledgments

- Jaydee Ballaho - Project Advisor
- RhamRhem Jaafar - Technical Consultant
- Marjorie Rojas - UI/UX Design Consultant
- Laravel team for the excellent framework
- Tailwind CSS team for the utility-first CSS framework
- Alpine.js community for the lightweight JavaScript framework
- Leaflet.js for mapping functionality

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
