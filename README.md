# Spartz Coding Challenge

## Build A Simple REST API

We have included a set of sample data in `cities.csv` and `users.csv`. Please implement a few basic operations on the data provided, including listing the cities in a given state, listing cities near another city, and registering a visit to a particular city by a user.  You may use whatever language or tools you wish to complete the exercise.  Package your solution so that it would be possible for another developer to get it up and running locally.  Keep in mind that you may be asked to extend your solution in an on-site interview.

**Required endpoints**

1. List all cities in a state

	`GET /v1/states/{state}/cities`

2. List cities within a 100 mile radius of a city

	`GET /v1/states/{state}/cities/{city}?radius=100`

3. Allow a user to update a row of data to indicate they have visited a particular city.

	`POST /v1/users/{user}/visits`

	```
	{
		"city": "Chicago",
		"state": "IL"
	}
	```

4. Return a list of cities the user has visited

	`GET /v1/users/{user}/visits`

## Things To Consider

- How to deal with bad requests
- Requests that could result in large data sets


## Deliverables

- The source code for your solution.
- The database schema you use to implement your solution.
- Any additional documentation you feel is necessary to explain how your application works, or describe your thought process and design decisions.



