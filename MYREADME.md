Hello,

This application is my code challenge for the Full Stack Developer position at Spartz.

I have been working in PHP through WordPress for the past year, building custom templates, plugins, and post types, and have been learning additional PHP skills outside of work as well.

This is the first time I have built a PHP API from scratch, and I had a great time! I look forward to learning from you how to improve and write cleaner, shorter code. One thing I will do for my next app is to design in OOP & MVC from the beginning, as it was difficult to integrate midstream.


SCHEMA
My schema can be found here: http://imgur.com/0N0Rqk2. I converted the CSVs to JSON with the headers as keys in order to use and pass the data. If I were to make this scalable, I would create databases and run database queries in addition to my current methods. For this challenge, I created a third CSV file as a join table of sorts, called 'visits'. This file receives user ids and city ids in order to report back which cities each user has visited.

FRAMEWORK
I decided to use Flight, as it was lightweight, easy to learn in a week, and the routing was reminiscent of Sinatra or Rails, which I have worked in before. Unlike Rails, it is not structured to be MVC, and I did not achieve the level of MVC I desired in this app. My file structure currently resembles my work in WordPress in that there an index file that calls on a functions file. I look forward to implementing more MVC PHP in the future.



HOW TO RUN IT
This application works through the browser. When you run it locally (I used MAMP), and you type in 'localhost:8888/flight_challenge/', you see a message saying hello.

ENDPOINT 1:
If you type '/flight_challenge/v1/states/VT/cities', you will see a list of all of the cities in Vermont (which is also where I am from). If you type in '/flight_challenge/v1/states/IL/cities', you will see all of the cities in Illinois. It is case-insensitive. If you type in an unrecognized state, you will get an error. 

ENDPOINT 2:
To find the cities nearest other cities, you enter '/flight_challenge/v1/states/VT/cities/9425?radius=100', '/flight_challenge/v1/states/VT/cities/Brattleboro?radius=100' or any city and radius combination. I tried both the Haversine and Vicenty formulas for distance, I stuck with Vicenty as it is said to be more accurate. It accepts city ids or city names and states, and is case-insensitive. 

ENDPOINT 3:
When you go to '/flight_challenge/v1/users/8/visits' you will see a JSON object containing visited cities. I originally had the URL able to accept the user's first name in addition to the ID, but that does not work for duplicates. I also originally used a form to submit the data, but ended up using cURL. If you enter http://localhost:8888/flight_challenge/v1/users/1/visits?name=rockford&state=IL and press enter, the page reloads and the new visited city is appended to the JSON array. If you enter an invalid city/state combination, you get an error.

ENDPOINT 4: When you return to the user page after submitting the form, you will see that '{"id":"2249","name":"Chicago","state":"IL","status":"verified","latitude":"41.884150000000000","longitude":"-87.632409000000000"}' is listed as a visited city. When you look at the 'visits.csv' file, you see that there is a new line 'id,8,2249'. There is currently no filter for duplicate entries and that would be something I would want to add.



TAKE-AWAYS
I am trained in Ruby on Rails, and I would love to make this application more MVC and more Object Oriented. This time working in PHP outside of WordPress makes me excited to learn Laravel. I loved this challenge, and I look forward to speaking with you further.

-- Ariel Diamond
