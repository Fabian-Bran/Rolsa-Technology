// Appointment cost calculator - six bugs
const BASE_FEE = 30
let patientName = 'Ada';
const appointments = '3';
 
patientName = 'Grace Hopper';

let total = BASE_FEE * Number(appointments);
const discount = total > 50 ? 10 : 0
total = total - discount

console.log('Patient: ' + patientName);
console.log('Total: ' + String(total));
 
if (Number(appointments) === 3) {
  console.log('Three appointments booked')
}
 
const summary = `Charge for ${patientName} is ` + total;
console.log(summary);
