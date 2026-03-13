The README.md discusses the technical particulars of the project (link to the repo and link to the deployed sites), mentions the use of AI, if any, and your observation of its value or not, and provides a roadmap of things you would like to have done, time permitting, or in the future.  

Link to Repo: 
- https://github.com/a-nguy4n/ah-nguyen-135 

Link to Deployed Site: 
- https://ah-nguyen.site/ 

Link to Reporting Site (fast access):
- https://reporting.ah-nguyen.site/

AI Usage Mentions 
Observation & Commentary on AI Usage 
- Claude.ai was used to help set up the boilerplate for analyst comments. It was not used as a final solution but more of a starting point which was then applied to all controller pages. 

Roadmap (Our Steps + Future Steps)

- Adding 3 Layers of Users:
    - Originally we had set up a users table on our server to keep track of logins. We had to add a 'role' row in the table to take into account the 3 layers of users. Users could either have the super_admin, analyst, or viewer role. 
    - On login, the role is stored in the PHP session alongside the username. Each controller checks `$_SESSION['role']` before rendering any page and unauthorized access redirects to a custom 403 page. The comment form is conditionally hidden from viewers in the view layer, and a server-side check in the controller prevents viewers from submitting comments even if they bypass the UI.
    - We then expanded the check to ensure only certain analysts could view certain reports. We did this by including another column in the database called sections which would check for performance, engagement, or behavior. If a certain user with the analyst role did not have all 3 of the sections specified in their user creation, they would be redirected to a 403 page when trying to access a specific report.

- Authentication + Authorization:
    - The system currently supports three user roles: `super_admin`, `analyst`, and `viewer`
    - `super_admin` can manage users and access all report areas
    - `analyst` access can be limited by backend-defined `sections` values such as `performance`,`behavior`, and `engagement`
    - `viewer` can access the dashboard and Saved Reports, but is blocked from live report routes and export/save routes

- Setting up Export System:
    - Ideally when we click the PDF download button on the any of our pages for the reports:
        - Button sends to a new backend route like /reports/performance/export/pdf
        - The server reuses the same Performance/Engagment/Behavior data already show on-screen
        - It loads that data into a clean “print version” HTML template
        - A PDF library then converts that HTML into an actual PDF file
        - The server saves the PDF in an exports folder and returns:
            - either a download immediately, or a URL to open/share later
    - Sources:
        - https://github.com/dompdf/dompdf for HTML to PDF converter
    - Implementation: 
        - PDF export is implemented with `dompdf`.
        - Each report has an export route and a save route.
        - If the normal public exports directory is not writable, the system attempts fallback storage paths so report saving/exporting is more resilient.

- Dashboard + Reports UI: 
    - First design the dashboard that gives access points to each report type 
        - Each report type is scoped to its own page and card on dash 
    - Ideally want to have shared styles for all pages 
        - keep it consistent 
        - use css tokens and data-attribs for easy, fast changes and adjustments
    - Have the PDF download buttons to each report type 
    - Shared styling is used across dashboard, reports, user management, and saved reports pages

- Saved Reports:
    - A Saved Reports workflow was added so analysts and super admins can save report snapshots
    - Clicking `Save Report` generates a PDF snapshot and records it in the Saved Reports list
    - Saved reports can be opened from `/saved-reports` through a `View PDF` link

- Future Steps: 
    - Add clearer success/error flash messages for actions such as saving a report
    - Add filtering/search/pagination for Saved Reports
    - Improve CSRF protection for all write actions
    - Add cleanup/retention policy for stored saved-report PDFs










