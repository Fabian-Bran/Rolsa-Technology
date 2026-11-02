/* =========================================================
   MedBook - Northfield Surgery
   app.js - site-wide behaviour
 
   Week 6:  surgery details, opening status
   =========================================================  */
 
'use strict';
 
// ---------------------------------------------------------
// Surgery details
// ---------------------------------------------------------
 
const SURGERY_NAME  = 'Northfield Surgery';
const SURGERY_PHONE = '01752 000000';
const OPENING_HOUR  = 8;      // 24-hour clock
const CLOSING_HOUR  = 18.5;   // 6:30pm

const NOW = new Date() // holding today's date: const now = new Date();
const CURRENT_HOUR = NOW.getHours() // holding the current hour, from NOW.getHours().
const HOURS_OPENED = CLOSING_HOUR - OPENING_HOUR //holding the number of hours the surgery is open each weekday (calculate it from OPENING_HOUR and CLOSING_HOUR — do not type 10.5).
const ISWEEKDAY = NOW.getDay() > 0 && NOW.getDay() < 6 // a boolean. now.getDay() returns 0 for Sunday and 6 for Saturday.
const ISWITHINHOURS = CURRENT_HOUR >= OPENING_HOUR && CURRENT_HOUR <= CLOSING_HOUR // a boolean, true if currentHour is at or after opening and before closing.
const ISOPEN = ISWITHINHOURS && ISWEEKDAY // true only if it is within hours and not the weekend.

console.log('NOW: ' + NOW);
console.log('CURRENT_HOUR: ' + CURRENT_HOUR);
console.log('HOURS_OPENED: ' + HOURS_OPENED);
console.log('ISWEEKDAY: ' + ISWEEKDAY);
console.log('ISWITHINHOURS: ' + ISWITHINHOURS);
console.log('ISOPEN: ' + ISOPEN);

console.log(`${SURGERY_NAME} - script loaded`);

const STATUSMESSAGE = ISOPEN ? `Northfield Surgery is open now. Call 01752 000000.` : `Northfield Surgery is closed. We open at ${String(OPENING_HOUR).padStart(2,'0').concat(':').padEnd(5,'0')}.`;
console.log('STATUS: ' + STATUSMESSAGE);

/* 
const typedCount = '3';
console.log(typedCount + 1) = '31'
console.log(Number(typedCount) + 1) = 4
console.log(typeof typedCount) = string
console.log(typeof Number(typedCount)) = number

we need to be carefull because the errors produced by this are logic errors
*/