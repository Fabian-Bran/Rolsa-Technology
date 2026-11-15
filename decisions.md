STACK:
frontend: JS, CSS, HTML
backend: PHP
Database:MYSQL

Why: Very simple, There are no external packages so no need for npm, very capable stack that can produce proffesional looking websites.

userstory and functionality:
Users should be able to look access most website pages without loging in
-exceptions: admin page, user dashboard.

Users should be able to request quotes from services.
User should be able to have access to a CO2 calculator.
Users should

folder structure:
spliting up files in folders like Includes, DB, and Assets for organization.

Got rid of old practice folder because it doesnt need to be located inside of the main project

public and private folders added for security reasons now includes, utils and db are outside of the web root.

DB structure:
users: nullable password_hash because other authentication methods can be added later i.e auth O

services: info for specific services

quote_requests: users create these by requesting quotes on the website.

quotes: finalized quote after staff look into the request. accepted or rejected by user.

jobs: created by staff after user accepts the quote

UI decisions:
Monochrome - black and white UI

why: high contrast means adhearing to standard accessibility regulations like the WCAG

The logo is an otter.

why: we are a local green technology company and rolsa reminded me of Rosa the sea otter

both desktop and mobile accessibility

why: as a local green technology company we expect traffic from both mobile and desktop devices.
