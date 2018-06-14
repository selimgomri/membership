# Membership
Membership, a new project from [Chester-le-Street ASC](https://www.chesterlestreetasc.co.uk/) aims to make swimming club management simpler. It is available under the Apache open-source license, so you are free to use our code in any way possible.

This software is continuously developed in accordance with the business and operation needs to Chester-le-Street ASC. We use a version of this software for our day-to-day club management, though it is further specialised to our needs..

If you install this application on your own server, you must set up the SQL database tables yourself. You will need to modify the `config-template.php` file to connect to your database and rename it `config.php`. A Schema is provided for setting up your Database, though you will need to manually create an admin, where the password is a salted hash (preferrerably BCrypt). Use the PHP `password_hash()` functions for this.

Chester-le-Street ASC accept no liability for any issues, including all legal issues, with this software. As mentioned, we use a slightly different version of this software which is better suited to deployment environments.

## Features
### Automatic Member Management
The application is built on a database of club members. Members are assigned to squads and parents can link swimmers to their account. This allows us to automatically calculate monthly fees and other things.

### Online Gala Entries
Galas are added to the system by admins. Parents can enter their children into swims by selecting their name, gala and swims. This cuts down on duplicated data from existing arrangements. Parents recieve emails detailing their entries.

### Online Attendance Records
Attendance records are online, facilitating automatic attendance calculation.

### Direct Debit Payments
This application has been integrated with GoCardless and their APIs to allow Chester-le-Street ASC to bill members by Direct Debit. The GoCardless client library which is included in this software is copyright of GoCardless.

## Notice

This application contains third party client libraries. These are managed via Composer. They will come with the application, so we recommend that you do not update them via composer yourself, as this may cause issues.