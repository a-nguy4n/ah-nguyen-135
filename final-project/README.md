The README.md discusses the technical particulars of the project (link to the repo and link to the deployed sites), mentions the use of AI, if any, and your observation of its value or not, and provides a roadmap of things you would like to have done, time permitting, or in the future.  

Link to Repo: 

Link to Deployed Site: 

AI Usage Mentions 

Observation & Commentary on AI Usage 

Roadmap (Our Steps + Future Steps)

- Adding 3 Layers of Users: 
  -

- Setting up Export System:
    - Ideally when we click the PDF download button on the any of our pages for the reports:
        - Button sends to a new backend route like /reports/performance/export/pdf
        - The server reuses the same Performance/Engagment/Behavior data already show on-screen
        - It loads that data into a clean “print version” HTML template
        - A PDF library then converts that HTML into an actual PDF file
        - The server saves the PDF in an exports folder and returns:
            - either a download immediately, or a URL to open/share later

- Dashboard + Reports UI: 
    - First design the dashboard that gives access points to each report type 
        - Each report type is scoped to its own page and card on dash 
    - Ideally want to have shared styles for all pages 
        - keep it consistent 
        - use css tokens and data-attribs for easy, fast changes and adjustments
    - Have the PDF download buttons to each report type 