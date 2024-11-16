# Pet Care Connect Platform

## Project Overview

Pet Care Connect is a comprehensive web-based platform developed as part of the Software Engineering 1 and 2 courses. This platform aims to connect pet owners with various pet care services, including grooming and veterinary care. Built with Laravel, the platform provides a robust backend architecture supporting both admin dashboard and customer-facing interfaces.

## Features

- **User Authentication**: Secure login and registration system using Laravel's built-in authentication
- **Shop Management**: Complete shop registration and management system
- **Service Management**: Tools to manage and approve services offered by shops
- **Appointment Booking**: Integrated booking system for pet care services
- **Location-based Search**: Find nearby grooming and veterinary services using geolocation
- **User Profiles**: Pet owner profiles with pet management capabilities
- **Responsive Design**: Mobile-friendly interface using Tailwind CSS

## Technology Stack

- **Frontend**:
  - Blade templating engine
  - Tailwind CSS
  - Alpine.js for reactive components
  - JavaScript/jQuery
  - Leaflet.js for maps
- **Backend**:
  - Laravel 10.x
  - PHP 8.x
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Development**: Vite for asset bundling

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/pet-care-connect.git
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

6. Configure your database in `.env`

7. Run migrations:
   ```bash
   php artisan migrate
   ```

8. Start the development server:
   ```bash
   php artisan serve
   npm run dev
   ```

## Project Structure

- `app/Http/Controllers` - Contains all controllers
- `app/Models` - Contains Eloquent models
- `database/migrations` - Database migrations
- `resources/views` - Blade view templates
- `routes` - Application routes
- `public` - Publicly accessible files

## Future Development

- Implement payment gateway integration
- Add real-time notifications using Laravel WebSockets
- Develop API endpoints for future mobile app
- Enhance search functionality with advanced filters
- Add service analytics and reporting features

## Team Members

- Faminiano, Christian Jude
- Billedo, Dean Reight
- Valeros, Greybin
- Cawili, Herwayne
- Jimenez, Myke

## Acknowledgments

- Jaydee Ballaho
- RhamRhem Jaafar
- Marjorie Rojas
- Laravel team for the excellent framework
- Tailwind CSS team
- Alpine.js community
- Leaflet.js for mapping functionality

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
