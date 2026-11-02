document.addEventListener("DOMContentLoaded", () => {
const today = new Date().toISOString().split('T')[0];
const maxDate = new Date(new Date().getTime() + 2419200000).toISOString().split('T')[0];
const dobInput = document.getElementById("dob");
const prefferedDateInput = document.getElementById("preferred-date")
if (dobInput) {
    dobInput.setAttribute("max", today);
}
if (prefferedDateInput) {

    prefferedDateInput.setAttribute("min", today)
    prefferedDateInput.setAttribute("max", maxDate)
}
});
