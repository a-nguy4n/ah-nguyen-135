# CSE135 Homework 4

# Team Members
- Haley Nguyen
- Allison Nguyen

## 'grader' password on Apache server
- iLuvGr@ding135

## Domain site link
- https://ah-nguyen.site

## IP Address
- 142.93.81.195

## Site Login 
- User: haley; Password: iLoverocks1!
- User: allison; Password: ahNguyenrock$

## Homework 4 Detailing 

- URL:  https://reporting.ah-nguyen.site/

- Username: grader

- Password: password 

- Part 1: MVC Scaffold 
  - Models are inside staticData.php, performanceData.php, and activityData.php 
  - Views are login.php and dashboard.php
  - Controllers are authController.php and dashboardController.php

  - Authentication Flow 
    - Inside authController.php is where we verify the username and password 
    against the database and store the user in session 
    - We have protected routes in dashboardController.php as it checks $_SESSION['user'] 
      and redirects to /login if not authenticated
    - When logging out, the session is destroyed from index.php with /logout route 
    - As such, forceful browsing is handled where access to /dashboard isn't allowed without logging in first. 


- Part 2: Connecting our Datastore to a Data Table / Grid
  - We decided to display the static, performance, and activity data in a table. We chose data we felt was most important and added it to the table. To accomplish this, we had to connect the MySQL database on our server to an HTML table on the dashboard page. dashboardController.php fetches data from our server from MySQL and passes it to dashboard.php which renders it as a raw HTML table. We then created php files for each type of data collected (staticData.php, performanceData.php, activityData.php) to query each respective table in MySQL and return all the necessary rows.
  

- Part 3: Connection our Datastore to a Chart
  -  We visualize our analytics data using Chart.js. 
  -  Our dashboard displays two interactive charts:
     1. **Performance Over Time (Line Chart)**: Shows page load times across all captured sessions. The data flows from MySQL → performanceData.php model → dashboardController.php → dashboard.php, where we take timestamps and `total_load_time` values, convert them to JSON with `json_encode()`, and pass them to Chart.js for rendering.
    
    2. **Activity Totals (Bar Chart)**: Displays user interaction counts (mouse moves, clicks, key presses, idle time). The activityData.php model queries the MySQL activity table, then we sum each interaction type using `array_sum()` and `array_column()`, pass the totals to Chart.js, and render as a grouped bar chart.
  - To simplify, our architecture is **MySQL Database → PHP Models (queries) → PHP Controller (data prep) → JavaScript (Chart.js rendering)**. 
