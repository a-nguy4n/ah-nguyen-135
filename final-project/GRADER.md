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



1. Log in as `grader` (analyst).
2. Open any live report page and click **Save Report**.
3. Open `/saved-reports` and verify a new saved entry appears.
4. Click **View PDF** and verify it opens the saved PDF.
5. Log out and log in as `guest` (viewer).
6. Verify viewer can access `/saved-reports`.
7. Verify viewer cannot open `/reports/performance` (should show 403).
8. Verify viewer cannot open `/reports/performance/export/pdf` (should show 403).

## Addendum: Credentials Note

- The scenario line that references username `engagement` may not apply if that account is not present in your current DB seed.
- If the `engagement` analyst account does not exist, please use `grader` for analyst flow and validate restricted-analyst behavior with any analyst account configured with limited `sections`.

## Addendum: Areas of Concern (Current)

- Saved reports are currently PDF snapshot based; publish/unpublish workflow is not implemented yet.
- Saved reports list currently has no search/filter/pagination.
- CSRF protections can be further improved for write actions.

