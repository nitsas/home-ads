Description
===========

This is a simple real estate ads website, created as my final project for the 
*Programming and Systems in the World Wide Web* class at the Computer 
Engineering and Informatics Department, University of Patras.

The system supports adding and managing real estate ads, which only registered
users can create and modify. Guests can view all ads but not modify them or
create new ones. 

Technologies used
-----------------

PHP, HTML, CSS, JavaScript, AJAX, MySQL, RSS, Google Maps API, Google 
Geocoding API

Features / Requirements
-----------------------

The system supports three types of users: guests, registered users and
administrators.

- *Guests* can browse the various property categories, only seeing ads that
    have been approved by and admin, as well as search for ads using features
    such as: property category, cost, size, facilities (e.g. pool, garden,
    sauna), whether the property is for rent or sale
- *Registered users* can do everything guests can, plus add/remove ads in
    their favorites list, create new ads, and delete their own ads that are no
    longer relevant (property rented/sold). Users can register by choosing a
    username and password, and providing an email address and at least one
    phone number, and optionally a name and surname. Registered users can
    change their password and all their contact info except their username.
    Newly created ads must first be approved by an admin before they show up
    for other users.
- *Admins* can do everything registered users can. In addition, they have the
    power to approve and disapprove ads, they can create, modify and delete 
    property categories (e.g. studio, 2-bedroom), change an individual
    property's category and other info. Admins also manage a list with all 
    possible property facilities (e.g. garage, garden, central heating).
    Lastly, admins have the power to manage registered users, i.e. change
    all their info and their password.

Some of the features of the site are:

- The main page shows the 5 most recent and top 5 most viewed ads, as well as
    their locations on a map.
- Registered users can *create* new ads and *delete* any of their own ads.
- Each registered user can add and remove adds to their favorites list.
- All users can search for ads meeting any of the following criteria: 
    availability for rent or sale, property cost, size, year of construction,
    address, property category and facilities, as well as whether the ad is 
    approved or not (only for admins).
- The search page displays the number of ads that match the selected criteria.
    This number is updated dynamically (AJAX) as the user adds or removes 
    search criteria. Of course the user can choose to see a list of all ads 
    that match the criteria.
- Property locations on the map are determined automatically using Google's 
    Geocoding API, together with the property's address. For the maps 
    themselves, we used Google's Maps API.
- The site supports an RSS feed with the 10 most recent approved ads.
