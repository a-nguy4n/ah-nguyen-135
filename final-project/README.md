The README.md discusses the technical particulars of the project (link to the repo and link to the deployed sites), mentions the use of AI, if any, and your observation of its value or not, and provides a roadmap of things you would like to have done, time permitting, or in the future.  

Link to Repo: 
- https://github.com/a-nguy4n/ah-nguyen-135 

Link to Deployed Site: 
- https://ah-nguyen.site/ 

Link to Reporting Site (fast access):
- https://reporting.ah-nguyen.site/

AI Usage Mentions:
Observation & Commentary on AI Usage
- Claude.ai was used to help set up the boilerplate for analyst comments. It was not used as a final solution, but rather as a starting point that was then applied across the controller pages.
- Setting up the Export System:
  - ChatGPT-5 helped with the more complex export/save-report workflow
- UI
  - AI was used to help debug certain CSS errors, but about 90% of the CSS for the UI and layouts was done by hand
  - AI also helped check for inconsistencies
- AI (Claude and ChatGPT) was also used to assist with several PHP files involving repeated controller patterns and more complex backend logic, especially the export/saved-reports flow in reportExportController.php, savedReportsController.php, and savedReportModel.php. It was also used as support when refining repeated authorization logic in the report controllers.
- Benefit of usage: 
    -  AI was useful for speeding up repetitive boilerplate, cross-file updates, and helping reason through more complex backend logic such as the PDF export and saved-reports workflow.
- Downside of usage:
    - AI-generated code still needed careful manual review because it could introduce logic mismatches and terrible styling inconsistencies
- Any AI output generated was reviewed, edited, and integrated manually

Roadmap (Our Steps + Future Steps)
- Adding 3 Layers of Users:
    - Originally, we set up a users table on our server to keep track of logins. We then added a `role` column to account for the 3 layers of users. Users can have the `super_admin`, `analyst`, or `viewer` role.
    - On login, the role is stored in the PHP session alongside the username. Each controller checks `$_SESSION['role']` before rendering any page and unauthorized access redirects to a custom 403 page. The comment form is conditionally hidden from viewers in the view layer, and a server-side check in the controller prevents viewers from submitting comments even if they bypass the UI.
    - We then expanded the checks to ensure that only certain analysts could view certain reports. We did this by including another column in the database called `sections`, which stores access such as `performance`, `engagement`, or `behavior`. If a user with the analyst role does not have the required section listed, they are redirected to a 403 page when trying to access that report.

- Authentication + Authorization:
    - The system currently supports three user roles: `super_admin`, `analyst`, and `viewer`.
    - `super_admin` can manage users and access all report areas.
    - `analyst` access can be limited by backend-defined `sections` values such as `performance`, `behavior`, and `engagement`.
    - `viewer` can access the dashboard and Saved Reports, but is blocked from live report routes and export/save routes.

- Setting up Export System:
    - Ideally, when we click the PDF download button on any of our report pages:
        - The button sends the request to a backend route such as `/reports/performance/export/pdf`.
        - The server reuses the same Performance/Engagement/Behavior data already shown on-screen.
        - It loads that data into a clean “print version” HTML template
        - A PDF library then converts that HTML into an actual PDF file.
        - The server saves the PDF in an exports folder and returns:
            - either an immediate download, or a URL to open/share later
    - Sources:
        - https://github.com/dompdf/dompdf as the HTML-to-PDF converter
    - Implementation:
        - PDF export is implemented with `dompdf`.
        - Each report has an export route and a save route.
        - If the normal public exports directory is not writable, the system attempts fallback storage paths so report saving/exporting is more resilient.

- Dashboard + Reports UI:
    - First, design the dashboard so that it provides access points to each report type.
        - Each report type is scoped to its own page and card on the dashboard.
    - Ideally, we wanted to have shared styles for all pages:
        - keep the design consistent,
        - and use CSS tokens and data attributes for easier and faster changes.
    - Include PDF download buttons for each report type.
    - Shared styling is used across the dashboard, reports, user management, and saved reports pages.

- Saved Reports:
    - A Saved Reports was added so analysts and super admins can save report snapshots.
    - Clicking `Save Report` generates a PDF snapshot and records it in the Saved Reports list.
    - Saved reports can be opened from `/saved-reports` through a `View PDF` link.

- Future Steps:
    - Add clearer success/error flash messages for actions such as saving a report.
    - Add filtering/search/pagination for Saved Reports.
    - Improve CSRF protection for all write actions.
    - Add a cleanup/retention policy for stored saved-report PDFs.










