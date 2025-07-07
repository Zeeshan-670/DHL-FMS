document.addEventListener("DOMContentLoaded", function () {
    let dateTimeInput = document.getElementById("selectDate");
    if (dateTimeInput) {
        let now = new Date();
        let offset = now.getTimezoneOffset() * 60000;
        let localTime = new Date(now - offset);
        let formattedDateTime = localTime.toISOString().slice(0, 16);
        dateTimeInput.value = formattedDateTime;
        dateTimeInput.disabled = true;
    }
});
