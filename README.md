# CSE135 Homework 1

# Team Members
- Haley Nguyen
- Allison Nguyen

## 'grader' password on Apache server
- iLuvGr@ding135

## Domain site link
- https://ah-nguyen.site/

# Part 2
## Github deployment setup
The first thing we did was clone the repo onto our Apache server. We then created and updated our deployment script. We then created a deploy.php file under our root directory. After
creating the repo, we went into our Github account and then added a webhook. At one point we needed to set up a personal authentication token so that the auto-deployment could 
correctly auto-deploy without needed a password, as well. Our deployment script kept getting deleted every time we would push to our repo so we had to add the file to our actual repo so that our deployment script wouldn't keep deleting it. We also had a problem with auto-deployment not working for both users, so we had to edit the script a little to allow users allison and haley to make pushes to the repo. 

# Part 3
## Usernames and passwords
- user: haley; password: iLoverocks1!
- user: allison; password: ahNguyenrock$

## Step 5 (compress)
- After compressing the files, in the reponse headers section the Content-Encoding section now 
  contains the value gzip

## Step 6 (Obscuring server)
- Originally we tried to install and implement the mod_header module in Apache to accomplish this. Since it didn't work though, we 
  found a StackExchange post detailing how we could use the mod_security module to change the server name and this method worked.
  After downloading and implementing the mod_security module we just had to put the value of SecServerSignature to "CSE135 Server".

initial-index.jpg: <img width="2642" height="1706" alt="image" src="https://github.com/user-attachments/assets/f188cc4a-acbc-492e-b552-621c0ae232e1" />
modified-index.jpg: <img width="1124" height="336" alt="image" src="https://github.com/user-attachments/assets/5e4f7667-aed8-462f-a3ca-9e6b299a5679" />
validator-initial.jpg: <img width="1414" height="672" alt="image" src="https://github.com/user-attachments/assets/8ba2435f-ca3d-4e7a-b8ee-dca12c72d8e2" />
vhosts-verify.jpg: <img width="844" height="838" alt="image" src="https://github.com/user-attachments/assets/605127e2-71e7-4b9a-9edf-fa3f3b826290" />
ssl-verify.jpg: <img width="720" height="662" alt="image" src="https://github.com/user-attachments/assets/84d98806-42f4-4d68-8fe0-441dd0776aee" />
github-deploy.mpeg or github-deploy.gif: 
php-verification.jpg: <img width="1456" height="888" alt="image" src="https://github.com/user-attachments/assets/4f8c5013-0893-4baa-b86d-0a83382bd8ba" />
compress-verify.jpg: <img width="1240" height="754" alt="image" src="https://github.com/user-attachments/assets/f31107f8-84f9-43e9-9aeb-9e61cf13b3b9" />
header-verify.jpg - demonstration of 'server: cse135 server' response header
error-page.jpg: <img width="706" height="350" alt="image" src="https://github.com/user-attachments/assets/e73ff651-8b3b-490f-9a5f-3856d022a0f7" />
log-verification.jpg: <img width="1980" height="500" alt="image" src="https://github.com/user-attachments/assets/26198d28-babc-4302-b064-15d84fbe1085" />
report-verification.jpg: <img width="1244" height="760" alt="image" src="https://github.com/user-attachments/assets/3bd5ee14-6fa1-4548-a091-9763b5a1fe17" />

