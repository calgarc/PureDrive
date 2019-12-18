PureDrive
----------------------------------------------------

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License, version 3,
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License, version 3,
along with this program.  If not, see <http://www.gnu.org/licenses/>

Copyright 2019-2020 by the PureDrive contributors


Pure Drive 
system architecture
Securely sync and access your data


Contents

1. Introduction

1.2 – System architecture
1.3 - Web client
1.4 - Mobile client
1.5 - Accessing files
1.6 – Security
1.7 – New features

2. Installation

2.1 - Files
2.2 - C-panel
2.3 - Home server
2.3 - Database
2.4 - Users

3. Plugins

3.1 - Installing plugins
3.2 - Plugin repository
3.3 - Creating plugins

4. Conclusion

4.1 - Conclusion




SYSTEM ARCHITECTURE

The PureDrive API is the core behind PureDrive’s architecture, enabling authorized users to control storage, manage users, enable or disable functionality, expand on the system, and much more. PureDrive is built around efficiency by providing users with a lightweight yet expandable system that runs on a fraction of the resources other systems use.

PureDrive is a php based system and runs in an encrypted environment on a Linux or Windows web server such as Apache or NGINX. All information including user details, file data, and plugin information is stored in a MySQL database.





WEB CLIENT

The web client is clean, simple, and reminiscent of modern day content management systems. The PureDrive interface is powerful and intuitive giving admins the ability to manage, customize and control all features of PureDrive.

For standard users the web client allows them to access, upload, manage, delete, and share their files and folders. Users also have assess to their profile and personal settings. Deleted files can be brought back from the trash as well as be permanently deleted. PureDrive has a powerful and ,modular search engine that not only allows you to search for files and folders but can also be setup to search for users, installed plugins.  The web client works on most major browsers and all operating systems.






MOBILE CLIENT

The web client will have all of the core features the web client has, as well as some features designed and optimized for mobile devices and tablets. The current development version allows you to upload and access your files on the go. The mobile version will provide an easy to use and excellent user experience for audio and video conferencing.



ACCESSING FILES

PureDrive offers a great deal of control over your data and the way its handled using a familiar and easy to use interface.. Files can easily be accessed and shared regardless of the device you use. You can share your files privately with other users of your system as well as publicly 
via public links. Data can also be accessed and maintained with plugins that 
add new functionality to the system such as a media player or video gallery 
plugin for example.





SECURITY

Security is a key factor for PureDrive users. PureDrive runs in a secure and encrypted platform aligned with industry standard security principles. Our product undergoes regular security scans and updates to ensure optimal security.



The PureDrive system is designed to be highly secure and provide a wide array of security measures including:

    • Brute force protection
    • 2 factor authentication
    • Unique user and password salts
    • Fully customize-able server side encryption
    • Multi layer sanitation and injection filters

Server settings have been implemented to allow admins to backup/restore the database and all important data files stored on the server. These features also allows you to modify and limit key server settings from an intuitive menu system.

NEW FEATURES

There are currently many feature and plugins in the development stage for future releases. Below are a few of the features currently under development.




Communication
	
Messaging
A multi channel chat and messaging system will be available allowing users to upload, share, and present their work with one another in real time. 

Document editing
Real time document editing will allow you to edit and modify existing documents with other users.

Plugin repository
PureDrive comes with plugin support allowing you to expand on the PureDrive system. PureDrive will feature a built in plugin repository making downloading and installing plugins a breeze.





Installation

Files
The PureDrive system files can be downloaded from www.puredrive.org. No account creation is needed to download and setup your own system, although some third party plugin manufacturers may ask you to make an account with them.  PureDrive comes zipped with all files needed to setup your own cloud based system, template files for designing your own plugins, and all documentation and installation instructions needed. However you will need your own server and or web hosting package to install the system.

Requirements
PureDrive’s does not require much to setup. In order to get a functioning system up and running you will need:

    • The latest version of PureDrive.
    • A server or web hosting package with a mySQL database.
    • Ftp access to upload files to your server.
    • Approx. 3mb of disk space for the system, more for file storage.
    • Php 7.x
    • MySQL 5.5+ or MariaDB 10.1+



C-Panel
C-Panel installation has been made easy and takes about 5 minutes to setup and run your own PureDrive system. Setting up PureDrive on your C-Panel based hosting is as simple as creating a database (with all privileges), uploading the files to a domain/subdomain on your web host, and following the on screen instructions.

Home Server
PureDrive can also be setup on your home server or servers in the workplace. PureDrive works on both windows and linux based servers, although the system was designed to run on Linux servers. Documentation and instructions on how to setup your own server running PureDrive is available at www.puredrive.org

Database
PureDrive is database driven and runs on either MySQL or MariaDB database. All data and information sent and retrieved from the database is sent through an encrypted environment. Its recommended you use an ssl certificate with a HAProxy load balancer

Users
Multiple users can be created and managed using a single instance of PureDrive. Admins can manage all users from the user menu as well as maintain how much space is allocated for each user. Custom user privileges can also be set to manage groups of users.




Plugins

Installing Plugins
PureDrive comes packaged with a plugin management system. Users can create, upload, and modify plugins adding new features that enhance the user experience and performance of the system. PureDrive supports both desktop and mobile plugins and plugin manufacturers can tailor their plugins for each device. Plugins are primarily written in php as well as javascript and css and are provided in zip format. All plugins have a meta.php file which contains all relative plugin information such as plugin name, author, version number and more. 

Plugin Repository
A plugin repository is available at www.puredrive.org as well as in the system itself which is where you can find many plugins currently available to the system. You may also find plugins from third party sources although you must exercise caution when installing plugins from third party sources.

Creating Plugins
Creating PureDrive plugins is easy thanks to our easy to understand api system. Each plugin has both a meta.php and an index.php. The meta.php has all plugin information which is stored in the database upon installation while the index.php contains the base plugin functionality. A template for creating plugins is both available in the plugins directory as well as at www.puredrive.org/creating-plugins/ 



Conclusion

Conclusion
PureDrive provides IT and users the flexibility and power to manage and maintain  their data in a secure and cost effective way. This gives its user base full control over its data and privacy rights while providing a pleasing and easy to use interface.

Visit www.puredrive.org for more information about PureDrive products, and support.















