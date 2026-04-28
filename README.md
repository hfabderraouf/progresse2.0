

# Gestion de la Scolarité - Web Application ( progresse2.0 )

## Project Overview
This project, titled **"Conception et Développement d'une Application Web de Gestion de la Scolarité,"** was developed as part of the **Programmation Web (PWEB)** module during the 2025/2026 academic year at **USTHB**. 

The application is a dynamic, full-stack solution designed to centralize and digitize academic data management.

## Key Features
* **Role-Based Access Control:** Distinct interfaces for **Administrators**, **Teachers**, and **Students**.
* **Authentication System:** Secure login using session management to protect user data.
* **Grade Management:** Automated calculation of averages and generation of digital transcripts (Relevé de notes).
* **Administrative Dashboard:** Tools to manage user accounts, modules, and view academic statistics.
* **Responsive Design:** A clean, intuitive user interface accessible across different devices.

## Technology Stack
* **Frontend:** HTML5, CSS3, JavaScript
* **Backend:** PHP (Server-side logic and session handling)
* **Database:** MySQL (Relational data storage)
* **Tools:** VS Code, Laragon/XAMPP, Git

## Project Structure
The application is built with a modular PHP architecture:
- `index.php`: The main landing page and entry point.
- `login.php` & `Auth.php`: Handles user authentication and security permissions.
- `database.php`: Manages the connection to the MySQL database.
- `App.php`: Functions as the central controller for application routing.
- `nav.php`: Common navigation component used across all pages.

## Database Schema
The system operates on a relational database consisting of five primary tables:
1.  **Admins**: For system management and oversight.
2.  **Teachers**: Associated with specific modules and grading.
3.  **Students**: Personal details and academic history.
4.  **Modules**: Subject details and coefficients.
5.  **Notes**: Records of grades linked to students and modules.




**Supervised by:** Dr. LAACHEMI | **Institution:** USTHB - Faculté d'Informatique
