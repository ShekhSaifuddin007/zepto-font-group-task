## Project Instruction

- clone repo

### Server Setup

- cd `server` folder run `composer install`
- set your db credentials from `server/configuration`
- import `sql` file from which have `server/zepto_font.sql`
- start server run `php -S localhost:8000 -t public`
- if you run server in different port than please change base url from `client/src/main.js` this `window.$http.defaults.baseURL = 'http://localhost:8000'`   

### Client Setup

- cd `client` folder and run `npm install`
- start client run `npm run dev`