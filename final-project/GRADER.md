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
- For Analyst and going into Super Admin:
    1. Visit https://reporting.ah-nguyen.site — you should see the login page
    2. Try typing https://reporting.ah-nguyen.site/dashboard directly — you should be redirected to login
    3. Log in as 'grader' which has the role of analyst, you should see the dashboard with links to all three reports
    4. Visit the performance report 
    5. Visit the behavior report 
    6. Visit the engagement report 
    7. Log out and log in as 'engagement' which has the role analyst but is restricted to the engagement report 
    8. Click on the performance report, you should be redirected to a 403 page
    9. Click on the engagement report, this should work
    10. Log out and log in as 'haley' which has a super admin role 
    11. Visit the user management tab and verify that you could create a user if needed

- For Being the Viewer
    1. Log in as `guest` (viewer) (Also make sure there's no spaces after for the username and password).
    2. You should see the dashboard, 
    3. Verify viewer cannot open `/reports/performance` (should show 403)
    4. Verify viewer cannot open `/reports/performance/export/pdf` (should show 403)
    5.  Go back to Dashboard and click on Saved Reports.
    6. You should see Saved Reports.
    7. For other sections like Performance, Behavior, or Engagement you can also verify that viewer can't access those pages (should show 403). 

- To Check out and Access 403 Error Page: 
  1. Log in with the Analyst or Viewer credentials listed above.
  2. In the browser, go to https://reporting.ah-nguyen.site/admin/users
  3. You should see the 403 Access Denied page.

- To Checkout and Access 404 Error Page: 
  1. Log in to the site.
  2. https://reporting.ah-nguyen.site/not-a-real-page
  3. You should see the 404 Page Not Found page.

Areas of Concern:
- Saved reports are currently PDF snapshot based; publish/unpublish workflow is not implemented yet.
- Saved reports list currently has no search/filter/pagination.
- CSRF protections can be further improved for write actions.
- Saved report persistence depends on writable server storage directories; in locked-down hosting environments this may need manual permission configuration.
- Saved report titles are generated automatically and can become repetitive if many reports are saved in a short time window.
- Analyst section checks currently rely on comma-separated `sections` in session/database values; malformed spacing or unexpected values could cause access confusion.
- Error/edge handling on large PDF generation jobs is basic; very large datasets may produce slower export response times.
- There is limited user feedback messaging after some actions (like save/export failures), which can make troubleshooting less obvious to end users.
- Session security can be strengthened further with additional hardening (session regeneration timing, tighter cookie flags in all deployment contexts).
- Frontend table and chart views are optimized for current dataset sizes; significantly larger data volumes may require pagination/aggregation for better UX.










