## Gilded Rose Inn Booking System
### Setup and Configuration

Core Requirements
- PHP7
- Composer
- MySQL Server

Clone the repository and navigate into the project.

Run
```
composer install
```

In the .env file, add database connection parameters
```
DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
```
Make sure to set the APP_ENV to prod

Create your database
```
bin/console doctrine:database:create
```
Update your database schema by running the following console command:
```
bin/console doctrine:schema:update --force
```
Load fixtures data (initial rooms with their parameters, cleaning crew)
```
bin/console app:fixtures:load
```
Execute
```
php -S 127.0.0.1:8000 -t public
```
### API Usage
Get Currently Available Rooms for Check-In
```
Content-Type: application/json
GET http://127.0.0.1:8000/api/v1/availability
```
Create new booking by providing a number of luggage items.
```
Content-Type: application/json
POST http://127.0.0.1:8000/api/v1/booking
{
	"luggage_items": 2
}
```
This will return the most profitable available room (room number) for the guest.

Get cleaning schedule for the day with calculated start and end times for each room based upon number of occupants and ordered by the net value of renting the room.
```
Content-Type: application/json
GET http://127.0.0.1:8000/api/v1/cleaning_schedule
```
Gnomes Cleaning Completion Method - when the gnomes finish with a room, PATCH the ticket to complete.
```
Content-Type: application/json
PATCH http://127.0.0.1:8000/api/v1/cleaning_schedule/{id}
```
### Functionality
The implementation is based around a MySQL relational database. The app:fixtures:load command loads the database with the rooms containing parameters from the specification, as well as a single cleaning crew (Gnome Cleaning Squad).

##### Entity Relationship Diagram
![alt text](https://raw.githubusercontent.com/k0bl/gilded-rose/master/extra/GildedEntities.jpg)

A room is booked by sending a POST request to /api/v1/booking with a JSON object containing the number of luggage items the guest is checking in with. There is a specialized query that checks against exisitng bookings, available rooms and cleaning availability. This query returns a single available room that will render the most profit for the business. For example, it would not make sense to book a guest with no luggage in a room with storage spaces. We could make more money if we choose a less equipped room and hold the rooms with storage space for guests who have luggage.

Once a room is booked, a cleaning "ticket" is created for that room. We create one cleaning ticket for each room for each night. If another guest is added to the room, we find the existing cleaning ticket and update the duration of the cleaning and occupants in the room accordingly.

Cleaning schedules can be generated for the day. We make sure to sort the cleaning tickets so that we are cleaning and making available the rooms that will render the most potential profit as quickly as possible. Cleaning tickets have a start and end time calculated by cleaning duration, which is based upon the number of guests in the room.

There is also a PATCH method to "complete" the cleaning ticket when the gnomes are done with each room. Perhaps they could have little gnome phones or something to run an app that allows them to see their schedule / complete cleaning tickets.

### Extensibility
You can already add more rooms to the inn with parameters by adding a room to the AppFixtures command. Additional rooms with parameters should work out of the box.

We could also add additional cleaning crews in AppFixtures but more work would need to be done (and more information gathered about the business intent) in order to make that work properly.

There are a number of ways we can add more business logic. The entities can be extended, modified or we can add new entities for brand new features. The code that is reponsible for returning query results resides in a repository class, we can simply add more repository functions with new queries to accomodate new requirements. Controller logic (the part that is handling the request data and reponse) can be extended with additional functions (ie if we needed to rewrite the logic for determining most profitable room, that is just a function that is called by the controller that calls a function on the repository) so all of that could be extended or easily replaced if need be.

### README Questions
* What third party libraries or other tools does your application use? How did you choose each library or framework you used?
	- The application uses the Symfony4 micro-skeleton. This gives us a bare bones project with the HTTP Foundation and HTTP kernel components.
	- FriendsOfSymfony REST Bundle. Provides a view layer, and tools for building RESTful APIs in Symfony.
	- Doctrine ORM - Object Relational Mapper that allows us to define database entites as objects (Classes). This is a very nice abstraction layer for dealing with relational database entities in an object oriented way. Also has a query builder tool so we don't have to write SQL queries from scratch.
	- JMS Serializer Bundle - this allows us to serialize data easily into JSON, XML or YAML. For this exercise we are using JSON.
* How long did you spend on this exercise? If you had unlimited time to work on this, how would you spend it and how would you prioritize each item?
	- I spent about 6 hours on this project. A little longer than expected, but I don't mind putting in the extra work. The additional couple of hours were mostly for testing, cleanup and writing this README.
	- If I had unlimited time, I would first start by implementing full support for HATEOAS including rel links for self and related objects. This will allow much smoother and more pragmatic development from a front-end standpoint.
	- There is no security of any kind, that is a major item that would need to be addressed. Especially some form of authentication and authorization.
	- Would love to develop an endpoint to allow management to create new rooms on the fly so they dont have to call up the devs to do it ;)
	- I could probably come up with something more algorithmic for making rooms available based upon the start and end times of the cleaning ticket instead of the completed patch method and completed timestamp it is using currently.
	- I could probably come up with something more algorithmic for determing most profitable room to book the guest in, database queries were the first thing that came to mind and seem to work nice enough for now.

* If you were to implement a level of automated testing to prepare this for a production server environment, how would you go about doing so?
	- I would probably use PHPUnit for Unit testing and Symfony WebTestCase for functional tests externally against the API. The first unit tests I would write would assert that the functions that execute queries to return a given available room returns the most profitable room checking against an array of pre-determined room booking patterns. (i.e) Book a room with 0 luggage, assert we get back a room with no storage space. Book a room with 1 luggage, assert we get back a room with 1 storage space, etc. There could be many permutations but that is the most critical part of the system. I would also write unit tests for asserting that the cleaning_schedule is returning the order of rooms to be cleaned that ensures the most potentially profitable rooms are cleaned first.
	- In terms of functional tests, they would be pretty similar in terms of scope but testing externally via the REST API to determine we are getting the intended behavior (ie try to book a room with 4 luggage, ensure a response code of 400).

