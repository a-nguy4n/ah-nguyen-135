The GRADER.md file contains credentials for all levels of login (super admin, analyst, and viewer), a written scenario you would like the grader to try before they free-form use your project (step 1 do this, step 2 do that, ...), and a discussion of what areas you are concerned about from a bug or architecture point of view.  If you are clear about this, the grader will not deduct points at full consequence, only half consequence.  Acceptance of flaws is a good skill for a software engineer -- we can more easily fix the flaws we admit to, so aim for accountability over perfection for maximum leniency.

Credentials

- Super Admin:
  - Username: haley; password: iLovecse135
  - Username: allison; password: c0deR0ck$

- Analyst:
  - Username: grader; password: password

- Viewer: 
  - Username: guest; password: guest

Our Scenario: 
1. Visit https://reporting.ah-nguyen.site — you should see the login page
2. Try typing https://reporting.ah-nguyen.site/dashboard directly — you should be redirected to login
3. Log in as 'grader' which has the role of analyst, you should see the dashboard with links to all three reports
4. Visit the performance report 
5. Visit the behavior report 
6. Visit the engagement report 
7. Log out and log in as 'engagement' which has the role analyst but is restricted to the engagement report 
8. Click on the performance report, you should be redirected to a 403 page
9. Click on the engagement report, this should work
10. Log out and log in as 'guest' which has a viewer role 
11. Click on performance reports and verify that you cannot add a comment
12. Log out and log in as 'haley' which has a super admin role 
13. Visit the user management tab and verify that you could create a user if needed

- To Check out and Access 403 Error Page: 
  1. Log in with the Analyst or Viewer credentials listed above.
  2. In the browser, go to https://reporting.ah-nguyen.site/admin/users
  3. You should see the 403 Access Denied page.

- To Checkout and Access 404 Error Page: 
  1. Log in to the site.
  2. https://reporting.ah-nguyen.site/not-a-real-page
  3. You should see the 404 Page Not Found page.

Areas of Concern:

