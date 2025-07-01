document.addEventListener("DOMContentLoaded", function () {
    let dateTimeInput = document.getElementById("selectDate");

    if (dateTimeInput) {
        // Get current local date and time
        let now = new Date();

        // Get local time offset in milliseconds and adjust time
        let offset = now.getTimezoneOffset() * 60000;
        let localTime = new Date(now - offset);

        // Format it as YYYY-MM-DDTHH:MM (required for datetime-local)
        let formattedDateTime = localTime.toISOString().slice(0, 16);

        // Set value and disable input
        dateTimeInput.value = formattedDateTime;
        dateTimeInput.disabled = true;
    }
});
